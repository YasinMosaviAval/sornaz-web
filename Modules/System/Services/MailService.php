<?php

namespace Modules\System\Services;

use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

class MailService {
    public function sendRegistrationOtp(string $email, string $code, int $validMinutes): bool {
        return $this->sendOtpTemplate($email, $code, $validMinutes, 'کد فعال‌سازی حساب', 'کد تأیید ثبت‌نام شما:');
    }

    public function sendPasswordResetOtp(string $email, string $code, int $validMinutes): bool {
        return $this->sendOtpTemplate($email, $code, $validMinutes, 'کد بازیابی رمز عبور', 'کد بازیابی رمز عبور شما:');
    }

    private function sendOtpTemplate(string $email, string $code, int $validMinutes, string $title, string $description): bool {
        $appName = 'برنامه آموزشی سرناز';
        $safeCode = e($code);
        $subject = "{$title} — {$appName}";
        $body = <<<HTML
<!doctype html><html lang="fa" dir="rtl"><head><meta charset="UTF-8"></head>
<body style="margin:0;background:#f4f4f8;font-family:Tahoma,Arial,sans-serif;direction:rtl">
<div style="max-width:560px;margin:40px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.08)">
<div style="background:#4f46e5;padding:32px 40px;text-align:center;color:#fff;font-size:22px;font-weight:bold">{$appName}</div>
<div style="padding:40px;color:#333;line-height:1.9;font-size:15px">
<h2 style="color:#4f46e5;margin-top:0">{$title}</h2><p>{$description}</p>
<div style="text-align:center;margin:32px 0;font-size:42px;font-weight:900;letter-spacing:12px;color:#4f46e5;font-family:monospace">{$safeCode}</div>
<p style="color:#888;font-size:13px">این کد تا {$validMinutes} دقیقه معتبر است.</p>
<p style="color:#888;font-size:13px">اگر این درخواست را شما ارسال نکرده‌اید، این ایمیل را نادیده بگیرید.</p>
</div></div></body></html>
HTML;
        return $this->send($email, $subject, $body);
    }

    private function send(string $recipient, string $subject, string $html): bool {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = (string)config('system.mail.host');
            $mail->Port = (int)config('system.mail.port', 587);
            $mail->SMTPAuth = config('system.mail.username', '') !== '';
            $mail->Username = (string)config('system.mail.username', '');
            $mail->Password = (string)config('system.mail.password', '');
            $encryption = strtolower((string)config('system.mail.encryption', 'tls'));
            if ($encryption === 'ssl' || $encryption === 'smtps') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls' || $encryption === 'starttls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = '';
                $mail->SMTPAutoTLS = false;
            }
            $verifyPeer = (bool)config('system.mail.verify_peer', true);
            $mail->SMTPOptions = ['ssl' => [
                'verify_peer' => $verifyPeer,
                'verify_peer_name' => $verifyPeer,
                'allow_self_signed' => !$verifyPeer,
            ]];
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->setFrom((string)config('system.mail.from'), (string)config('system.mail.from_name'));
            $mail->addAddress($recipient);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return $mail->send();
        } catch (Throwable $exception) {
            error_log('[Registration Mail Error] ' . $exception->getMessage());
            return false;
        }
    }
}
