<?php

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function theme_widgets_init()
{
    register_sidebar(array(
        'name'          => __('Secondary Footer', 'acesdelta'),
        'id'            => 'secondary-footer',
        'description'   => '',
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '',
        'after_title'   => '',
    ));
}
add_action('widgets_init', 'theme_widgets_init');
