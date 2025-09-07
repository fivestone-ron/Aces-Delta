<?php

/**
 * theme functions and definitions
 *
 * @package theme
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
    $content_width = 640; /* pixels */
}


/**
 * Enqueue scripts and styles.
 */
function theme_scripts()
{
    wp_enqueue_style(
        'theme-style',
        get_stylesheet_directory_uri() . '/build/style.css',
        array(),
        filemtime(get_stylesheet_directory() . '/build/style.css')
    );

    wp_enqueue_script(
        'theme-easing',
        get_stylesheet_directory_uri() . '/assets/js/libs/jquery.easing.1.3.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/assets/js/libs/jquery.easing.1.3.js'),
        true
    );

    wp_enqueue_script(
        'masonry',
        get_stylesheet_directory_uri() . '/assets/js/libs/masonry.min.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/assets/js/libs/masonry.min.js'),
        true
    );

    wp_enqueue_script(
        'theme-scripts',
        get_stylesheet_directory_uri() . '/build/main.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/build/main.js'),
        true
    );

    wp_enqueue_script(
        'theme-skip-link-focus-fix',
        get_stylesheet_directory_uri() . '/assets/js/libs/skip-link-focus-fix.js',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/js/libs/skip-link-focus-fix.js'),
        true
    );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'theme_scripts');

/**
 * Enqueue admin gutenberg scripts and styles.
 */
function admin_gutenberg_scripts($hook)
{
    if ('post.php' != $hook) {
        return;
    }

    wp_enqueue_style('admin-gutenberg-style', get_template_directory_uri() . '/gutenberg-style.min.css');
}
add_action('admin_enqueue_scripts', 'admin_gutenberg_scripts');


/**
 * Load the INCLUDES files.
 */
$dir = dirname(__FILE__);
$files = preg_grep('/^([^.])*\.php/i', scandir($dir . '/inc/'));
if (!empty($files)) {
    foreach ($files as $file) {
        require get_template_directory() . '/inc/' . $file;
    }
}

$files = preg_grep('/^([^.])*\.php/i', scandir($dir . '/acf-blocks/'));
if (!empty($files)) {
    foreach ($files as $file) {
        require get_template_directory() . '/acf-blocks/' . $file;
    }
}

// $files = preg_grep('/^([^.])*\.php/i', scandir($dir . '/lib/'));
// if (!empty($files)) {
//     foreach ($files as $file) {
//         require get_template_directory() . '/lib/' . $file;
//     }
// }

add_filter('xmlrpc_enabled', '__return_false');

add_action('admin_init', 'additional_admin_color_schemes');
function additional_admin_color_schemes()
{
    wp_admin_css_color(
        'custom_admin_theme',
        __('Custom Theme', 'acesdelta'),
        get_template_directory_uri() . '/custom-admin-colors.min.css',
        array('#425159', '#000000', '#e31f26', '#ffffff')
    );
}

add_action('user_register', 'set_default_admin_color');
function set_default_admin_color($user_id)
{
    $args = array(
        'ID' => $user_id,
        'admin_color' => 'custom_admin_theme'
    );
    wp_update_user($args);
}

// Allow SVG type
function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

function wsp_posts_return_title_modification($data)
{
    // Maybe modify $example in some way.
    $data = preg_replace('/<h2 class="wsp-posts-title">(.*)<\/h2>/', '<h2 class="wsp-posts-title">Resource Center Pages</h2>', $data);
    return $data;
}
add_filter('wsp_posts_return', 'wsp_posts_return_title_modification');

function wsp_pages_return_title_modification($data)
{
    // Maybe modify $example in some way.
    $data = preg_replace('/<h2 class="wsp-pages-title">(.*)<\/h2>/', '<h2 class="wsp-pages-title">Site Pages</h2>', $data);
    return $data;
}
add_filter('wsp_pages_return', 'wsp_pages_return_title_modification');

function mhps_check_blank_canvas_page() 
{
    $str = get_page_template_slug(get_the_ID());
    $substr = 'blank-canvas';
    return (strpos($str, $substr) !== false);
}

function current_year_shortcode() {
    return date('Y');
}
add_shortcode('current_year', 'current_year_shortcode');
