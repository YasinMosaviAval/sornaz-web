<?php

trait ModelAdminTrait {


    public static function soft_delete_user_poll(int $user_poll_id) {
        return Db::getInstance()->modify("UPDATE user_polls 
            SET deleted_by = :deleted_by, deleted_at = NOW(), updated_by = :updated_by
            WHERE user_poll_id = :user_poll_id",
        array(
            'deleted_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
            'user_poll_id' => $user_poll_id,
        ));
    }



    public static function soft_delete_user_lesson(int $user_lesson_id) {
        return Db::getInstance()->modify("UPDATE user_lessons 
            SET deleted_by = :deleted_by, deleted_at = NOW(), updated_by = :updated_by
            WHERE user_lesson_id = :user_lesson_id",
        array(
            'deleted_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
            'user_lesson_id' => $user_lesson_id,
        ));
    }

    public static function soft_delete_lesson(int $lesson_id) {
        return Db::getInstance()->modify("UPDATE lessons 
            SET deleted_by = :deleted_by, deleted_at = NOW(), updated_by = :updated_by
            WHERE lesson_id = :lesson_id",
        array(
            'deleted_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
            'lesson_id' => $lesson_id,
        ));
    }



    public static function soft_delete_user_instrument(int $user_instrument_id) {
        return Db::getInstance()->modify("UPDATE user_instruments 
            SET deleted_by = :deleted_by, deleted_at = NOW(), updated_by = :updated_by
            WHERE user_instrument_id = :user_instrument_id",
        array(
            'deleted_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
            'user_instrument_id' => $user_instrument_id,
        ));
    }

    public static function soft_delete_instrument(int $instrument_id) {
        return Db::getInstance()->modify("UPDATE instruments 
            SET deleted_by = :deleted_by, deleted_at = NOW(), updated_by = :updated_by
            WHERE instrument_id = :instrument_id",
        array(
            'deleted_by' => session_get('user_id'),
            'updated_by' => session_get('user_id'),
            'instrument_id' => $instrument_id,
        ));
    }













    
    public static function soft_delete_user_certificate(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_certificates 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_education(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_educations 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_event(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_events 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }

    public static function soft_delete_user_experience(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_experiences 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, 
                updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }



    public static function soft_delete_user_award(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_awards 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }


    // ===================== user_poll_options =====================
    public static function soft_delete_user_poll_option(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_poll_options 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE id = :id",
            array(
                'id' => $id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }

    // ===================== user_poll_votes =====================
    public static function soft_delete_user_poll_vote(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE user_poll_votes 
            SET deleted_by = :deleted_by, 
                deleted_at = :deleted_at, 
                updated_by = :updated_by, 
                updated_at = :updated_at 
            WHERE id = :id",
            array(
                'id' => $id,
                'deleted_by' => $deleted_by,
                'deleted_at' => $deleted_at,
                'updated_by' => $deleted_by,
                'updated_at' => $deleted_at
            ));
    }


    public static function soft_delete_media_file(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE media_files 
            SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at 
            WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }



    public static function soft_delete_level(int $id, int $deleted_by, string $deleted_at) {
        return Db::getInstance()->modify("UPDATE levels SET deleted_by = :deleted_by, deleted_at = :deleted_at, updated_by = :updated_by, updated_at = :updated_at WHERE id = :id",
            array('id' => $id, 'deleted_by' => $deleted_by, 'deleted_at' => $deleted_at, 'updated_by' => $deleted_by, 'updated_at' => $deleted_at));
    }


}