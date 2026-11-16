<?php

namespace PHPMailer\PHPMailer {
    #[\AllowDynamicProperties]
    class PHPMailer
    {
        public const ENCRYPTION_STARTTLS = 'tls';
        public static int $constructed = 0;
        public static array $deliveries = [];
        private string $recipient = '';

        public function __construct(bool $exceptions = false) { self::$constructed++; }
        public function isSMTP(): void {}
        public function setFrom(string $address, string $name): void {}
        public function addAddress(string $address): void { $this->recipient = $address; }
        public function isHTML(bool $enabled): void {}
        public function send(): bool
        {
            self::$deliveries[] = ['to' => $this->recipient, 'subject' => $this->Subject, 'body' => $this->Body];
            return true;
        }
    }
}

namespace {
    class OtpModel { public const EXPIRE_MINUTES = 10; }
    function fullBaseUrl(): string { return 'https://example.test'; }
    function check(bool $condition, string $message): void
    {
        if (!$condition) { throw new \RuntimeException($message); }
    }

    $config = ['page' => ['title' => 'Fixture Academy'], 'mail' => []];
    require $argv[1] ?? dirname(__DIR__) . '/system/services/mailer.php';

    foreach (['first@example.test', 'second@example.test', 'first@example.test'] as $recipient) {
        check(\Mailer::sendOtp($recipient, '000000', 'register') === false, 'Disabled delivery must report failure.');
    }
    check(\PHPMailer\PHPMailer\PHPMailer::$constructed === 0, 'Registration must not initialize SMTP.');
    check(\PHPMailer\PHPMailer\PHPMailer::$deliveries === [], 'Repeated registration requests must not send mail.');

    check(\Mailer::sendOtp('login@example.test', '111111', 'login'), 'Login OTP delivery must remain available.');
    check(\Mailer::sendOtp('reset@example.test', '222222', 'reset'), 'Reset OTP delivery must remain available.');
    check(\Mailer::sendPasswordReset('reset-link@example.test', 'fixture-reset'), 'Password reset links must remain available.');
    check(\Mailer::sendVerificationLink('verify-link@example.test', 'fixture-verify', 'Fixture'), 'Verification links must remain available.');
    $deliveries = \PHPMailer\PHPMailer\PHPMailer::$deliveries;
    check(count($deliveries) === 4, 'Only the four permitted messages must reach the transport.');
    check($deliveries[0]['to'] === 'login@example.test' && str_contains($deliveries[0]['body'], '111111'), 'Login recipient and code must be preserved.');
    check($deliveries[1]['to'] === 'reset@example.test' && str_contains($deliveries[1]['body'], '222222'), 'Reset recipient and code must be preserved.');
    check(str_contains($deliveries[2]['body'], '/account/reset-password?token=fixture-reset'), 'Reset link must be preserved.');
    check(str_contains($deliveries[3]['body'], '/account/verify-email?token=fixture-verify'), 'Verification link must be preserved.');
    check(\Mailer::sendOtp('retry@example.test', '333333', 'register') === false, 'Registration must remain disabled after other messages.');
    check(count(\PHPMailer\PHPMailer\PHPMailer::$deliveries) === 4, 'Disabled retry must not reach the transport.');
    echo "PASS: registration and repeated requests blocked before SMTP; login/reset OTPs and reset/verification links preserved with a fake transport.\n";
}
