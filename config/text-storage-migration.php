<?php
return [
    'varchar'=>[
        'f_comments'=>['author'=>255],
        'f_conversation_messages'=>['attachment_path'=>2048],
        'f_media_files'=>['path'=>2048,'thumbnail_path'=>2048],
        'f_tracking_user_consents'=>['user_agent'=>2048],
        'f_tracking_user_sessions'=>['user_agent'=>2048],
        'f_user_certificates'=>['certificate_url'=>2048,'file_path'=>2048],
        'f_user_publications'=>['url'=>2048],
    ],
    // Technical serialized data stays textual; translatable fields use translations.
    'binary'=>[
        'f_legacy_settings'=>['value'=>'LONGTEXT'],
        'f_social_account_settings'=>['settings_json'=>'TEXT'],
        'f_tracking_user_events'=>['event_data'=>'LONGTEXT'],
        'f_tracking_user_page_views'=>['query_params'=>'LONGTEXT'],
        'f_user_points'=>['metadata'=>'LONGTEXT'],
        'f_user_settings'=>['value'=>'TEXT'],
        'p_creator_courses'=>['curriculum'=>'LONGTEXT'],
        'p_creator_course_details'=>['metadata'=>'LONGTEXT'],
        'p_creator_course_lessons'=>['media_json'=>'TEXT'],
        'p_music_sheets'=>['metadata'=>'LONGTEXT','score'=>'LONGTEXT'],
    ],
    'json'=>['f_legacy_settings.value','f_social_account_settings.settings_json','f_tracking_user_events.event_data','f_tracking_user_page_views.query_params','f_user_points.metadata','p_creator_courses.curriculum','p_creator_course_details.metadata','p_creator_course_lessons.media_json','p_music_sheets.metadata','p_music_sheets.score'],
    'unused'=>[],
    'retained_text'=>['f_posts.pinged'],
];
