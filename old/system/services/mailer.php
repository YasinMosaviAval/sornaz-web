<?php

/**
 * system/services/mailer.php
 *
 * سرویس ارسال ایمیل با PHPMailer
 *
 * نصب: composer require phpmailer/phpmailer
 *
 * تنظیمات در .env:
 *   MAIL_HOST, MAIL_PORT, MAIL_USER, MAIL_PASS
 *   MAIL_FROM, MAIL_FROM_NAME, MAIL_ENCRYPTION
 */
class Mailer {

  /**
   * ارسال ایمیل تأیید حساب (لینک)
   */
  public static function sendVerificationLink(string $toEmail, string $token, string $fullname = ''): bool {
    global $config;
    $base = fullBaseUrl();
    $link = "$base/account/verify-email?token=" . urlencode($token);
    $name = $fullname ?: 'کاربر گرامی';

    $subject = 'تأیید ایمیل — ' . ($config['page']['title'] ?? 'سرناز');

    $body = self::emailTemplate(
      title:   'تأیید آدرس ایمیل',
      content: "
        <p>$name عزیز،</p>
        <p>برای فعال‌سازی حساب کاربری خود روی دکمه زیر کلیک کنید:</p>
        <p style='text-align:center; margin:32px 0'>
          <a href='$link'
             style='background:#4f46e5;color:#fff;padding:14px 32px;
                    border-radius:8px;text-decoration:none;font-weight:bold;display:inline-block'>
            تأیید ایمیل
          </a>
        </p>
        <p style='color:#888;font-size:13px'>این لینک تا ۲۴ ساعت معتبر است.</p>
        <p style='color:#888;font-size:13px'>
          یا این آدرس را در مرورگر باز کنید:<br>
          <a href='$link' style='color:#4f46e5'>$link</a>
        </p>
      "
    );

    return self::send($toEmail, $subject, $body);
  }


  /**
   * ارسال کد OTP
   */
  public static function sendOtp(string $toEmail, string $code, string $purpose): bool {
    global $config;

    $purposeLabel = match($purpose) {
      'register' => 'فعال‌سازی حساب',
      'login'    => 'ورود به حساب',
      'reset'    => 'بازیابی رمز عبور',
      default    => 'تأیید هویت',
    };

    $subject = "کد $purposeLabel — " . ($config['page']['title'] ?? 'سرناز');

    $body = self::emailTemplate(
      title:   "کد $purposeLabel",
      content: "
        <p>کد تأیید شما:</p>
        <div style='text-align:center;margin:32px 0'>
          <span style='font-size:42px;font-weight:900;letter-spacing:12px;
                       color:#4f46e5;font-family:monospace'>$code</span>
        </div>
        <p style='color:#888;font-size:13px'>این کد تا " . OtpModel::EXPIRE_MINUTES . " دقیقه معتبر است.</p>
        <p style='color:#888;font-size:13px'>اگر این درخواست را شما ارسال نکرده‌اید، این ایمیل را نادیده بگیرید.</p>
      "
    );

    return self::send($toEmail, $subject, $body);
  }


  /**
   * ارسال لینک بازیابی رمز عبور
   */
  public static function sendPasswordReset(string $toEmail, string $token): bool {
    global $config;
    $base = fullBaseUrl();
    $link = "$base/account/reset-password?token=" . urlencode($token);

    $subject = 'بازیابی رمز عبور — ' . ($config['page']['title'] ?? 'سرناز');

    $body = self::emailTemplate(
      title:   'بازیابی رمز عبور',
      content: "
        <p>درخواست بازیابی رمز عبور برای حساب شما ثبت شده است.</p>
        <p style='text-align:center;margin:32px 0'>
          <a href='$link'
             style='background:#4f46e5;color:#fff;padding:14px 32px;
                    border-radius:8px;text-decoration:none;font-weight:bold;display:inline-block'>
            تغییر رمز عبور
          </a>
        </p>
        <p style='color:#888;font-size:13px'>این لینک تا ۱ ساعت معتبر است.</p>
        <p style='color:#888;font-size:13px'>اگر این درخواست را شما ارسال نکرده‌اید، این ایمیل را نادیده بگیرید.</p>
      "
    );

    return self::send($toEmail, $subject, $body);
  }


  // ── Core Send ────────────────────────────────────
private static function send(string $to, string $subject, string $htmlBody): bool {
  if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    return self::sendWithPhpMail($to, $subject, $htmlBody);
  }

  global $config;
  $mailConfig = $config['mail'] ?? [];

  try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true); // true = exceptions فعال

    // ── Debug — فقط موقت برای پیدا کردن مشکل ──
    /*
      $mail->SMTPDebug  = 2; // همه چیز رو نشون بده
      $mail->Debugoutput = function($str, $level) {
        error_log("PHPMailer [$level]: $str");
        echo "<pre>$str</pre>"; // موقت برای دیدن در مرورگر
      };
    */


    $mail->isSMTP();
    $mail->Host       = $mailConfig['host']     ?? 'localhost';
    $mail->SMTPAuth   = true;
    $mail->Username   = $mailConfig['user']     ?? '';
    $mail->Password   = $mailConfig['pass']     ?? '';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int) ($mailConfig['port'] ?? 587);
    $mail->CharSet    = 'UTF-8';

    // ── اضافه کن ──────────────────────────────
    $mail->SMTPOptions = [
      'ssl' => [
        'verify_peer'       => false,
        'verify_peer_name'  => false,
        'allow_self_signed' => true,
      ],
    ];
    // ──────────────────────────────────────────

    $mail->setFrom(
      $mailConfig['from']      ?? 'no-reply@sornaz.com',
      $mailConfig['from_name'] ?? 'Sornaz Academy'
    );
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $htmlBody;
    $mail->AltBody = strip_tags($htmlBody);

    $mail->send();
    return true;

  } catch (\Exception $e) {
    error_log("[Mailer Error] " . $e->getMessage());
    echo "<b>خطا:</b> " . $e->getMessage(); // موقت
    return false;
  }
}



/*

SERVER -> CLIENT: 220 smtp.gmail.com ESMTP 5b1f17b1804b1-490bc391aaasm377701695e9.1 - gsmtp

CLIENT -> SERVER: EHLO sornaz.local

SERVER -> CLIENT: 250-smtp.gmail.com at your service, [216.147.121.162]
250-SIZE 35882577
250-8BITMIME
250-STARTTLS
250-ENHANCEDSTATUSCODES
250-PIPELINING
250 SMTPUTF8

CLIENT -> SERVER: STARTTLS

SERVER -> CLIENT: 220 2.0.0 Ready to start TLS

SMTP Error: Could not connect to SMTP host. Connection failed. stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages:
error:0A000086:SSL routines::certificate verify failed

CLIENT -> SERVER: QUIT

SERVER -> CLIENT: 

SMTP ERROR: QUIT command failed: 

SMTP Error: Could not connect to SMTP host. Connection failed. stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages:
error:0A000086:SSL routines::certificate verify failed

خطا: SMTP Error: Could not connect to SMTP host. Connection failed. stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages: error:0A000086:SSL routines::certificate verify failedemail not sent

*/
















  private static function sendWithPhpMail(string $to, string $subject, string $body): bool {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@sornaz.com\r\n";
    return mail($to, $subject, $body, $headers);
  }


  // ── Email Template ───────────────────────────────

  private static function emailTemplate(string $title, string $content): string {
    global $config;
    $appName = $config['page']['title'] ?? 'Sornaz Academy';
    $year    = date('Y');

    return <<<HTML
    <!DOCTYPE html>
    <html dir="rtl" lang="fa">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <style>
        body { font-family: Tahoma, Arial, sans-serif; background:#f4f4f8; margin:0; padding:0; direction:rtl }
        .wrapper { max-width:560px; margin:40px auto; background:#fff; border-radius:12px;
                   overflow:hidden; box-shadow:0 2px 16px rgba(0,0,0,.08) }
        .header { background:#4f46e5; padding:32px 40px; text-align:center }
        .header h1 { color:#fff; margin:0; font-size:22px }
        .body { padding:40px; color:#333; line-height:1.9; font-size:15px }
        .body h2 { color:#4f46e5; margin-top:0 }
        .footer { background:#f9f9f9; padding:20px 40px; text-align:center;
                  font-size:12px; color:#999; border-top:1px solid #eee }
      </style>
    </head>
    <body>
      <div class="wrapper">
        <div class="header"><h1>$appName</h1></div>
        <div class="body">
          <h2>$title</h2>
          $content
        </div>
        <div class="footer">
          &copy; $year $appName — تمامی حقوق محفوظ است
        </div>
      </div>
    </body>
    </html>
    HTML;
  }
}
