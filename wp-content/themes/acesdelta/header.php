<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package New
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
   
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Typekit Fonts starts -->
<!-- <script src="//use.typekit.net/fug6pri.js"></script>
<script>try{Typekit.load();}catch(e){}</script> -->
<!-- Typekit Fonts ends -->
<link rel="apple-touch-icon-precomposed" sizes="300x300" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/favicon-300x300.png">
<link rel="apple-touch-icon-precomposed" sizes="150x150" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/favicon-150x150.png">
<link rel="icon" sizes="16x16 32x32" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicons/favicon.png">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5PD8F3W"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'acesdelta'); ?></a>

    <?php echo page_heading_component(); ?>

    <div id="content" class="">