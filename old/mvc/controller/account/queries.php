<?php

trait AccountQueriesTrait {


    private function get_home_data() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND page=:page
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
            'page'       => 'home'
        ));
    }


    private static function get_header_data() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND page=:page
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
            'page'       => 'header'
        ));
    }



    private static function get_footer_data() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND page=:page
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
            'page'       => 'footer'
        ));
    }


    // ========================================================================================================
    // ========================================================================================================
    // ========================================================================================================
    // ========================================================================================================
    // ========================================================================================================



    public function sendOtpToPhone() {
        
        $sent = Sms::sendOtp(
            phone:   '09168004588',
            code:    '483920',
            purpose: 'register' // 'register' | 'login' | 'reset'
        );
        echo $sent ? 'SMS ارسال شد' : 'SMS ارسال نشد';
        exit();
    }


    public function sendApprovedEmail() {
        
        // ارسال لینک تأیید ایمیل (ثبت‌نام)
        // در AccountController/register trait:
        // $sent = Mailer::sendVerificationLink(
        // toEmail:  'sornaz.ac@gmail.com',
        // token:    'abc123...',
        // fullname: 'علی رضایی'
        // );

        // if (!$sent) {
        //     error_log('Email failed for: user@example.com');
        //     echo 'email not sent';
        // } else {
        //     echo 'email sent';
        // }

        // ارسال لینک بازیابی رمز
        // $sent = Mailer::sendPasswordReset(
        //     toEmail: 'sornaz.ac@gmail.com',
        //     token:   'xyz789...'
        // );
        
        exit();
    }

}