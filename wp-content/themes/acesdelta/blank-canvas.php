<?php
/*
Template Name: Blank Canvas
*/

get_header();

if (have_posts()) {
    while (have_posts()) {
        the_post(); 
        
        get_template_part('template-parts/content', 'page'); 
    }
}

get_footer(); ?>
