<?php

// if (!function_exists('blogs')) {
//     function blogs() {
//     }
// }


if (!function_exists('blog_categories')) {
    function blog_categories(?string $categories): array {
        if (!$categories) {
            return [];
        }
        return array_values(
            array_filter(
                array_map('trim', explode(',', $categories))
            )
        );
    }

}