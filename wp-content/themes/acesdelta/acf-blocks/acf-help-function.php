<?php

function return_list_of_resource_center_posts()
{
    $type_of_display = get_field('type_of_display');

    if ($type_of_display === 'latest') {
        $include_cat = [];
        $exclude_cat = [];
        $categories = get_categories(array('hide_empty' => false));
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $include_cat[] = $category->term_id;
            }
        }

        if (!empty(get_field('categories_to_exclude'))) {
            foreach (get_field('categories_to_exclude') as $category) {
                $exclude_cat[] = $category->term_id;
            }
        }
        $final_cats = array_diff($include_cat, $exclude_cat);
        $defaults = array(
            'numberposts'      => 4,
            'category'         => $final_cats,
            'orderby'          => 'date',
            'order'            => 'DESC',
            'include'          => array(),
            'exclude'          => array(),
            'post_type'        => 'post',
            'post_status'      => 'publish'
        );
        $posts = get_posts($defaults);
    } elseif ($type_of_display === 'custom') {
        $custom_content = get_field('custom_content');
        if ($custom_content) {
            foreach ($custom_content as $key => $row) {
                $posts[$key]['image'] = $row['image'];
                $posts[$key]['title'] = $row['title'];
                $posts[$key]['link'] = $row['link'];
            }
        }
    } else {
        $posts = get_field('posts_to_display');
    }

    return $posts;
}

function return_list_of_resource_center_posts_for_grid()
{
    $arg = array(
        'posts_per_page'   => -1,
        'post_type'        => 'post',
        'post_status'      => 'publish',
        'orderby'          => 'date',
        'order'            => 'DESC',
    );

    $posts = get_posts($arg);
    return $posts;
}

function return_list_of_pages_previews()
{
    $type_of_display = get_field('how_to_fetch_data');
    $arr = [];

    if ($type_of_display === 'page') {
        $featured_posts = get_field('pages_to_fetch');
        if ($featured_posts) {
            foreach ($featured_posts as $key => $post) {
                $arr[$key]['featured_image'] = get_the_post_thumbnail($post->ID, 'full');
                $arr[$key]['title'] = get_the_title($post->ID);
                $arr[$key]['excerpt'] = get_the_excerpt($post->ID);
                $arr[$key]['link'] = get_the_permalink($post->ID);
                $arr[$key]['btn_label'] = 'Read more';
            }
        }
    } else {
        $custom_content = get_field('custom_content');
        if (!empty($custom_content)) {
            foreach ($custom_content as $key => $post) {
                $arr[$key]['featured_image'] = "<img src=" . esc_url($post['featured_image']['url']) . " alt=" . esc_attr($post['featured_image']['alt']) . " />";
                $arr[$key]['title'] = $post['title'];
                $arr[$key]['excerpt'] = $post['excerpt'];
                $arr[$key]['link'] = $post['link'];
                $arr[$key]['btn_label'] = $post['button_label'];
            }
        }
    }

    return $arr;
}
