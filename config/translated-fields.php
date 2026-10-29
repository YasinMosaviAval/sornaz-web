<?php
// Entity discriminators remain logical names; TableNames resolves physical tables.
return [
    'music_practice_nodes' => ['key'=>'id','fields'=>['title'],'store'=>'f_translations'],
    'music_practice_exams' => ['key'=>'id','fields'=>['title'],'store'=>'f_translations'],
    'music_practice_plans' => ['key'=>'id','fields'=>['title'],'store'=>'f_translations'],
    'conversation_messages' => ['key'=>'conversation_message_id','fields'=>['body'],'store'=>'f_translations'],
    'social_comments' => ['key'=>'id','fields'=>['body'],'store'=>'f_translations'],
    'social_posts' => ['key'=>'id','fields'=>['body'],'store'=>'f_translations'],
    'social_profiles' => ['key'=>'user_id','fields'=>['bio'],'store'=>'f_translations'],
    'user_merges' => ['key'=>'user_merge_id','fields'=>['reason','admin_note'],'store'=>'f_translations'],
    'user_point_rules' => ['key'=>'user_point_rule_id','fields'=>['description'],'store'=>'f_translations'],
    'user_publications' => ['key'=>'user_publication_id','fields'=>['content'],'store'=>'f_translations'],
    'creator_courses' => ['key'=>'id','fields'=>['description'],'store'=>'translations'],
    'creator_course_questions' => ['key'=>'id','fields'=>['body'],'store'=>'translations'],
    'creator_course_reports' => ['key'=>'id','fields'=>['body'],'store'=>'translations'],
    'creator_course_reviews' => ['key'=>'id','fields'=>['body'],'store'=>'translations'],
];
