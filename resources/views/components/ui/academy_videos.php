<?php

pushStyle('assets/css/components/academy-videos.css');
pushScript('assets/js/components/academy-videos.js');

$value = $value ?? [];

?>

<div class="academy-videos">
    <div id="academy-video-list">
        <?php foreach($value as $video): ?>
            <div class="academy-video-item">
                <div class="academy-video-preview">
                    <video controls preload="metadata">
                        <source
                            src="<?=asset($video['path'])?>"
                            type="<?=e($video['mime_type'])?>">
                    </video>
                </div>
                <div class="academy-video-content">
                    <?php
                    component('ui.textarea',[
                        'label'=>'توضیح ویدیو',
                        'name'=>'academy_video_note['.$video['media_file_id'].']',
                        'value'=>$video['note'] ?? ''
                    ]);
                    ?>
                    <input
                        type="hidden"
                        name="academy_video_id[]"
                        value="<?=e($video['media_file_id'])?>">
                    <button
                        type="button"
                        class="academy-video-remove"
                        data-id="<?=e($video['media_file_id'])?>">
                        حذف
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="academy-video-upload">
        <label>
            افزودن ویدیو
        </label>
        <input
            type="file"
            id="academy-video-upload"
            name="academy_video[]"
            accept="video/*"
            multiple>
    </div>
</div>