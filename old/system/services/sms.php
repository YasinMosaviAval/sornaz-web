<?php

/**
 * system/services/sms.php
 *
 * سرویس ارسال SMS
 * پشتیبانی از: کاوه‌نگار و ملی‌پیامک
 *
 * تنظیمات در .env:
 *   SMS_PROVIDER=kavenegar     (یا melipayamak)
 *   SMS_API_KEY=xxx
 *   SMS_FROM=3000xxxxx         (شماره ارسال — برای ملی‌پیامک)
 *   SMS_KAVENEGAR_SENDER=xxx   (برای کاوه‌نگار)
 */
class Sms {

    /**
     * ارسال کد OTP به شماره موبایل
     */
    public static function sendOtp(string $phone, string $code, string $purpose): bool {
        global $config;

        $purposeLabel = match($purpose) {
            'register' => 'فعال‌سازی',
            'login'    => 'ورود',
            'reset'    => 'بازیابی رمز',
            default    => 'تأیید',
        };

        $message = "کد $purposeLabel شما: $code\nاین کد تا " . OtpModel::EXPIRE_MINUTES . " دقیقه معتبر است.";

        $provider = $config['sms']['provider'] ?? 'kavenegar';
        // $provider = 'kavenegar';

        return match($provider) {
            'kavenegar'   => self::sendKavenegar($phone, $message, $code),
            'melipayamak' => self::sendMeliPayamak($phone, $message),
            default       => self::log($phone, $message),
        };
    }



/*
URL: https://api.kavenegar.com/v1/6E3241395848516168334A674E46453766466264423033694C3461446B7369684F78594A455054376958733D/sms/send.json
HTTP Code: 0
Response: 
cURL Error: Resolving timed out after 10009 milliseconds

SMS ارسال نشد





*/



  // ── کاوه‌نگار ─────────────────────────────────────

  private static function sendKavenegar(string $phone, string $message, string $code = null): bool {
    global $config;
    $apiKey  = $config['sms']['api_key'] ?? '';
    $sender  = $config['sms']['sender']  ?? '10004346';

    // اگه template name تعریف شده → از Lookup API استفاده کن
    $template = $config['sms']['kavenegar_template'] ?? null;

    if ($template && $code) {
      $url = "https://api.kavenegar.com/v1/$apiKey/verify/lookup.json";
      $params = http_build_query([
        'receptor' => $phone,
        'template' => $template,
        'token'    => $code,
      ]);
    } else {
      $params = array_filter([
        'receptor' => $phone,
        'sender'   => $sender ?: null,
        'message'  => $message,
      ]);

      $url    = "https://api.kavenegar.com/v1/$apiKey/sms/send.json";
      $params = http_build_query(array_filter($params));
    }

    return self::httpPost($url, $params);
  }


  // ── ملی‌پیامک ─────────────────────────────────────

  private static function sendMeliPayamak(string $phone, string $message): bool {
    global $config;
    $username = $config['sms']['username'] ?? '';
    $password = $config['sms']['password'] ?? '';
    $from     = $config['sms']['from']     ?? '98200089';

    $url    = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';
    $params = http_build_query([
      'username' => $username,
      'password' => $password,
      'to'       => $phone,
      'from'     => $from,
      'text'     => $message,
      'isFlash'  => 'false',
    ]);

    return self::httpPost($url, $params);
  }


  // ── HTTP ─────────────────────────────────────────
private static function httpPost(string $url, string $params): bool {
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $params,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_SSL_VERIFYPEER => false, // ← مثل Mailer اضافه کن
  ]);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $error    = curl_error($ch);
  curl_close($ch);

  // ── debug موقت ──────────────────────────
  error_log("[SMS Debug] URL: $url");
  error_log("[SMS Debug] Params: $params");
  error_log("[SMS Debug] HTTP Code: $httpCode");
  error_log("[SMS Debug] Response: $response");
  error_log("[SMS Debug] cURL Error: $error");
  echo "<pre>";
  echo "URL: $url\n";
  echo "HTTP Code: $httpCode\n";
  echo "Response: $response\n";
  if ($error) echo "cURL Error: $error\n";
  echo "</pre>";
  // ────────────────────────────────────────

  if ($error) {
    error_log("[SMS Error] cURL: $error");
    return false;
  }

  if ($httpCode < 200 || $httpCode >= 300) {
    error_log("[SMS Error] HTTP $httpCode — $response");
    return false;
  }

  return true;
}


  // ── Local Log (برای محیط dev) ─────────────────────

  private static function log(string $phone, string $message): bool {
    error_log("[SMS-DEV] To: $phone | Message: $message");
    return true;
  }


}