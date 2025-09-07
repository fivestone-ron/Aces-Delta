<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package theme
 */

function my_login_logo_one()
{
    ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/Mitsubishi-Power-logo-black.svg');
            background-size: 70%;
            width: 100%;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'my_login_logo_one');

function custom_loginlogo_url($url)
{
    return home_url('/');
}
add_filter('login_headerurl', 'custom_loginlogo_url');