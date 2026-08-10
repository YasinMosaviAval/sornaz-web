<?php

namespace Modules\Analytics\Services;

use Core\database\DB;
use RuntimeException;

class AdminTestDataService {
    private const USERNAME_PREFIX = 'test_academy_manager_';
    private const TOTAL = 50;

    public function seedAcademyManagers(): array {
        return transaction(function () {
            $passwordHash = password_hash('123456789', PASSWORD_DEFAULT);
            $created = 0;
            $updated = 0;

            foreach ($this->people() as $index => $person) {
                $number = $index + 1;
                $username = self::USERNAME_PREFIX . sprintf('%02d', $number);
                $existing = DB::table('users')->where('username', $username)->first();
                $createdAt = $existing['created_at'] ?? date('Y-m-d H:i:s', strtotime('-' . (540 - $index * 9) . ' days'));
                $values = $this->userValues($person, $index, $createdAt, $passwordHash);

                if ($existing) {
                    $userId = (int)$existing['user_id'];
                    DB::table('users')->where('user_id', $userId)->update($values);
                    $updated++;
                } else {
                    $userId = DB::table('users')->insertGetId(['username' => $username] + $values);
                    if (!$userId) throw new RuntimeException('ایجاد کاربر آزمایشی شماره ' . $number . ' ناموفق بود.');
                    $created++;
                }

                DB::table('users')->where('user_id', $userId)->update([
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
                $this->setFullNameTranslation($userId, $person['name'], $createdAt);
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'total' => self::TOTAL,
                'message' => "تست مدیران آموزشگاه تکمیل شد: {$created} کاربر ایجاد و {$updated} کاربر آزمایشی همگام‌سازی شد.",
            ];
        });
    }

    public function statistics(): array {
        $users = DB::table('users')->whereRaw("username LIKE '" . self::USERNAME_PREFIX . "%'")->get();
        $stats = ['total' => count($users), 'pending' => 0, 'approved' => 0, 'other' => 0];
        foreach ($users as $user) {
            $status = $user['status'] ?? '';
            if ($status === 'pending') $stats['pending']++;
            elseif ($status === 'approved') $stats['approved']++;
            else $stats['other']++;
        }
        return $stats;
    }

    public function deleteAcademyManagers(): array {
        return transaction(function () {
            $users = DB::table('users')->whereRaw("username LIKE '" . self::USERNAME_PREFIX . "%'")->get();
            $userIds = array_map(fn(array $user) => (int)$user['user_id'], $users);
            if (!$userIds) {
                return ['deleted' => 0, 'message' => 'هیچ مدیر آموزشگاه آزمایشی برای حذف وجود ندارد.'];
            }

            DB::table('translations')->where('table_name', 'users')->whereIn('table_id', $userIds)->delete();
            DB::table('users')->whereIn('user_id', $userIds)->delete();

            return [
                'deleted' => count($userIds),
                'message' => count($userIds) . ' مدیر آموزشگاه آزمایشی و ترجمه‌های مرتبط با موفقیت حذف شدند.',
            ];
        });
    }

    private function userValues(array $person, int $index, string $createdAt, string $passwordHash): array {
        $createdTimestamp = strtotime($createdAt);
        $status = $this->statusFor($index);
        $approvedBySiteAdmin = $status === 'approved' && $index >= 20 && $index < 32;
        $phoneVerifiedAt = date('Y-m-d H:i:s', $createdTimestamp + (($index % 48) + 1) * 3600);
        $lastLoginAt = date('Y-m-d H:i:s', min(time(), $createdTimestamp + (($index % 90) + 4) * 86400));
        $updatedAt = date('Y-m-d H:i:s', min(time(), $createdTimestamp + (($index % 72) + 1) * 3600));
        $approvedAt = $approvedBySiteAdmin
            ? date('Y-m-d H:i:s', $createdTimestamp + (5 * 3600) + (($index * 19) % 235) * 3600)
            : null;

        return [
            'email' => sprintf('sornaz.academy.manager%02d@gmail.com', $index + 1),
            'phone' => sprintf('0991%07d', 1000000 + $index + 1),
            'phone_verified_at' => $phoneVerifiedAt,
            'last_login_at' => $lastLoginAt,
            'last_login_ip' => sprintf('185.51.%d.%d', 20 + ($index % 20), 10 + $index),
            'password' => $passwordHash,
            'national_code' => $this->nationalCode(730000001 + $index),
            'gender' => $person['gender'],
            'type' => 'human',
            'status' => $status,
            'locale' => 'fa',
            'timezone' => 'Asia/Tehran',
            'avatar_file_id' => null,
            'visibility' => $index < 30 ? 'public' : ($index < 40 ? 'private' : 'unlisted'),
            'birthday' => $this->birthdayFor($index),
            'register_time' => $createdAt,
            'register_method' => ['phone', 'email', 'google'][$index % 3],
            'created_at' => $createdAt,
            'created_by' => null,
            'updated_at' => $updatedAt,
            'updated_by' => null,
            'approved_at' => $approvedAt,
            'approved_by' => $approvedBySiteAdmin ? 1 : null,
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }

    private function setFullNameTranslation(int $userId, string $fullName, string $createdAt): void {
        $translation = DB::table('translations')
            ->where('table_name', 'users')->where('table_id', $userId)
            ->where('locale', 'fa')->where('field', 'full_name')->first();
        $values = [
            'code' => null,
            'value' => $fullName,
            'version' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => $userId,
            'deleted_at' => null,
            'deleted_by' => null,
        ];
        if ($translation) {
            DB::table('translations')->where('translation_id', (int)$translation['translation_id'])->update($values + [
                'created_by' => $userId,
            ]);
            return;
        }
        $translationId = DB::table('translations')->insertGetId([
            'table_name' => 'users', 'table_id' => $userId, 'locale' => 'fa', 'field' => 'full_name',
            'created_at' => $createdAt, 'created_by' => $userId,
        ] + $values);
        if (!$translationId) throw new RuntimeException('ثبت ترجمه نام کاربر آزمایشی ناموفق بود.');
    }

    private function statusFor(int $index): string {
        if ($index < 20) return 'pending';
        if ($index < 40) return 'approved';
        if ($index < 44) return 'rejected';
        if ($index < 47) return 'inactive';
        return 'banned';
    }

    private function birthdayFor(int $index): string {
        $ages = [25, 27, 29, 61, 65, 70, 75, 80, 63, 68];
        $age = $index < 40 ? 30 + (($index * 7) % 31) : $ages[$index - 40];
        return date('Y-m-d', strtotime('-' . $age . ' years -' . (($index * 17) % 330) . ' days'));
    }

    private function nationalCode(int $base): string {
        $digits = str_split(sprintf('%09d', $base));
        $sum = 0;
        foreach ($digits as $index => $digit) $sum += (int)$digit * (10 - $index);
        $remainder = $sum % 11;
        $checkDigit = $remainder < 2 ? $remainder : 11 - $remainder;
        return implode('', $digits) . $checkDigit;
    }

    private function people(): array {
        $maleFirstNames = ['علی', 'رضا', 'امیر', 'حسین', 'مهدی', 'محمد', 'سعید', 'آرش', 'پویان', 'کیوان', 'فرهاد', 'بهرام', 'نوید', 'بابک', 'کامران', 'کوروش', 'داریوش', 'سامان', 'رامین', 'شهاب', 'میلاد', 'محسن', 'مسعود', 'یاسر', 'جواد'];
        $femaleFirstNames = ['مریم', 'سارا', 'نگار', 'الهام', 'نرگس', 'لیلا', 'مهسا', 'نازنین', 'شبنم', 'پرستو', 'سپیده', 'ترانه', 'آزاده', 'سمیرا', 'بهاره', 'غزل', 'حدیث', 'رویا', 'مینا', 'شیوا', 'الهه', 'زهرا', 'فاطمه', 'ریحانه', 'هانیه'];
        $lastNames = ['محمدی', 'احمدی', 'رضایی', 'کریمی', 'حسینی', 'مرادی', 'قاسمی', 'اکبری', 'صادقی', 'نوری'];
        $people = [];
        for ($i = 0; $i < 25; $i++) {
            $people[] = ['name' => $maleFirstNames[$i] . ' ' . $lastNames[$i % 10], 'gender' => 'male'];
            $people[] = ['name' => $femaleFirstNames[$i] . ' ' . $lastNames[($i + 5) % 10], 'gender' => 'female'];
        }
        return $people;
    }
}
