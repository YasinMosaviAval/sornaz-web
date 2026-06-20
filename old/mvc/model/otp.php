<?php

/**
 * mvc/model/otp.php
 *
 * مدیریت کدهای OTP
 * جدول: otp_codes
 *
 * purpose: register | login | reset
 * type:    email    | phone
 */
class OtpModel extends BaseModel {

    protected string $table = 'otp_codes';

    const EXPIRE_MINUTES = 10;
    const CODE_LENGTH    = 6;
    const MAX_ATTEMPTS   = 5; // حداکثر درخواست در یک بازه


  // ── ساخت و ذخیره OTP ─────────────────────────────

    /**
     * ساخت کد جدید و ذخیره در DB
     * کدهای قبلی استفاده‌نشده رو ابطال می‌کنه
     */
    public function generate(string $target, string $type, string $purpose): string {
        // بررسی rate limit — حداکثر MAX_ATTEMPTS در یک ساعت
        $recentCount = (int) $this->db->value(
            "SELECT COUNT(*) FROM otp_codes
            WHERE target = :target AND purpose = :purpose
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            ['target' => $target, 'purpose' => $purpose]
        );

        if ($recentCount >= self::MAX_ATTEMPTS) {
            throw new RuntimeException('too_many_requests');
        }

        // ابطال کدهای قبلی
        $this->db->modify(
            "UPDATE otp_codes SET used_at = NOW()
                WHERE target = :target AND purpose = :purpose AND used_at IS NULL",
            ['target' => $target, 'purpose' => $purpose]
        );

        // تولید کد
        $code      = str_pad((string) random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . self::EXPIRE_MINUTES . ' minutes'));

        $this->db->insert(
            "INSERT INTO otp_codes (target, type, code, purpose, expires_at)
                VALUES (:target, :type, :code, :purpose, :expires_at)",
                [
                    'target'     => $target,
                    'type'       => $type,
                    'code'       => $code,
                    'purpose'    => $purpose,
                    'expires_at' => $expiresAt,
                ]
        );

        return $code;
    }


  // ── تأیید OTP ────────────────────────────────────

    /**
     * بررسی و mark کردن OTP به عنوان استفاده‌شده
     * برمی‌گردونه: true اگه معتبر بود
     */
    public function verify(string $target, string $code, string $purpose): bool {
        $otp = $this->db->first(
            "SELECT * FROM otp_codes
                WHERE target   = :target
                    AND code     = :code
                    AND purpose  = :purpose
                    AND used_at  IS NULL
                    AND expires_at > NOW()
                ORDER BY id DESC
                LIMIT 1",
            ['target' => $target, 'code' => $code, 'purpose' => $purpose]
        );

        if (!$otp) return false;

        // mark as used
        $this->db->modify(
            "UPDATE otp_codes SET used_at = NOW() WHERE id = :id",
            ['id' => $otp['id']]
        );

        return true;
    }


  // ── بررسی وجود OTP فعال ─────────────────────────

    public function hasActive(string $target, string $purpose): bool {
        return $this->db->exists(
            "SELECT id FROM otp_codes
            WHERE target = :target AND purpose = :purpose
                AND used_at IS NULL AND expires_at > NOW()",
            ['target' => $target, 'purpose' => $purpose]
        );
    }


  // ── پاکسازی کدهای منقضی ─────────────────────────

    public function cleanup(): void {
        $this->db->modify("DELETE FROM otp_codes WHERE expires_at < DATE_SUB(NOW(), INTERVAL 1 DAY)");
    }
}