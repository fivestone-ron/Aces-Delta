<?php

// Change dashboard Posts to Resource Center
add_action('init', 'cp_change_post_object');
function cp_change_post_object()
{
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
    $labels->name = 'resource_center';
    $labels->singular_name = 'Resource Center';
    $labels->add_new = 'Add Resource Center';
    $labels->add_new_item = 'Add Resource Center';
    $labels->edit_item = 'Edit Resource Center';
    $labels->new_item = 'Resource Center';
    $labels->view_item = 'View Resource Center';
    $labels->search_items = 'Search Resource Center';
    $labels->not_found = 'No Resource Center found';
    $labels->not_found_in_trash = 'No Resource Center found in Trash';
    $labels->all_items = 'All Resource Center';
    $labels->menu_name = 'Resource Center';
    $labels->name_admin_bar = 'Resource Center';
}
