<?php

add_action('acf/init', 'my_acf_block_init');
function my_acf_block_init()
{

    // check function exists
    if (function_exists('acf_register_block')) {
        // register resource center block
        acf_register_block(array(
            'name'              => 'resource_center',
            'title'             => __('Resource Center', 'acesdelta'),
            'description'       => __('A Resource Center block.', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'admin-home',
            'keywords'          => array( 'Center', 'Resource' ),
        ));

        acf_register_block(array(
            'name'              => 'resource_center_grid',
            'title'             => __('Resource Center Grid', 'acesdelta'),
            'description'       => __('A Resource Center Grid block.', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'admin-home',
            'keywords'          => array( 'Grid', 'Center', 'Resource' ),
        ));

         acf_register_block(array(
            'name'              => 'mastering_challenges',
            'title'             => __('Mastering Challenges', 'acesdelta'),
            'description'       => __('A Mastering Challenges block.', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'admin-home',
            'keywords'          => array( 'Mastering', 'Challenges' ),
         ));

        acf_register_block(array(
            'name'              => 'brochure_form',
            'title'             => __('Brochure Form', 'acesdelta'),
            'description'       => __('A Brochure Form block.', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'edit',
            'keywords'          => array( 'Brochure', 'Form' ),
        ));

        acf_register_block(array(
            'name'              => 'pages_preview',
            'title'             => __('Pages Preview', 'acesdelta'),
            'description'       => __('A Pages Preview block.', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'excerpt-view',
            'keywords'          => array( 'Pages', 'Preview' ),
        ));

        acf_register_block(array(
            'name'              => 'grid',
            'title'             => __('Grid', 'acesdelta'),
            'description'       => __('A Grid block.', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'align-left',
            'keywords'          => array( 'Grid', 'Masonry' ),
        ));

        acf_register_block(array(
            'name'              => 'section_overview',
            'title'             => __('Section Overview Block', 'acesdelta'),
            'description'       => __('A custom overview block with anchor links', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'location-alt',
            'keywords'          => array( 'section', 'anchor', 'overview'),
        ));

        acf_register_block(array(
            'name'              => 'filter',
            'title'             => __('Filter Block', 'acesdelta'),
            'description'       => __('A custom filter for the grid', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'filter',
            'keywords'          => array( 'filter', 'grid',),
        ));

        acf_register_block(array(
            'name'              => 'cover_image_block',
            'title'             => __('Cover Image Block with overlapping Icon', 'acesdelta'),
            'description'       => __('A custom Cover Image block with an overlapping icon', 'acesdelta'),
            'render_callback'   => 'my_acf_block_render_callback',
            'category'          => 'layout',
            'icon'              => 'format-image',
            'keywords'          => array( 'cover', 'image', 'icon'),
        ));
    }
}

function my_acf_block_render_callback($block)
{
    // convert name ("acf/testimonial") into path friendly slug ("testimonial")
    $slug = str_replace('acf/', '', $block['name']);
    // include a template part from within the "template-parts/block" folder
    if (file_exists(get_theme_file_path("/acf-blocks/template/{$slug}.php"))) {
        include(get_theme_file_path("/acf-blocks/template/{$slug}.php"));
    }
}
