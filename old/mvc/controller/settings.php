<?php


class SettingsController extends BaseController {


    public static function get_settings() {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return Db::getInstance()->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
        ));
    }



    public static function get_header_data() {
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


    
    public static function get_footer_data() {
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


    

}
