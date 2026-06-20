<?php

date_default_timezone_set('Asia/Tehran');

global $config;
require_once getcwd() . '/lib/phpmailer/Exception.php';
require_once getcwd() . '/lib/phpmailer/PHPMailer.php';
require_once getcwd() . '/lib/phpmailer/SMTP.php';
require_once getcwd() . '/config.php';
require_once getcwd() . '/system/core.php';
require_once getcwd() . '/system/db.php';
require_once getcwd() . '/system/router.php';
require_once getcwd() . '/system/base_controller.php';
require_once getcwd() . '/system/base_model.php';
require_once getcwd() . '/system/access.php';
require_once getcwd() . '/system/common.php';
require_once getcwd() . '/system/view.php';
require_once getcwd() . '/system/notes.php';
require_once getcwd() . '/system/graphic.php';
require_once getcwd() . '/system/services/mailer.php';
require_once getcwd() . '/system/services/sms.php';

session_start();
initializeSettings();
