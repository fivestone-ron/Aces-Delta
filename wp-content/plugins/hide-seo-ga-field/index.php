<?php
/*
Plugin Name: Hide SEO GA Field
Plugin URI:
Description: Plugin that is used to hide the GA field and Make sure they don't accidentally activate the GA though all in one SEO
Version: 1.0
Author: Pierre Boislard
Author URI:
License:
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}

function hide_ga_field_of_all_in_one_seo_script()
{
    wp_enqueue_style( 'hide-ga-field', plugin_dir_url(dirname(__FILE__)). 'hide-seo-ga-field/hide.css' );
}
add_action( 'admin_enqueue_scripts', 'hide_ga_field_of_all_in_one_seo_script' );