<?php

/**
 * Block Name: Latest-Articles
 *
 * This is the template that displays the Latest Articles block.
 */

$repeater = get_field('section_overview_repeater');


$args = array(
    'hide_empty' => true,
    'orderby'          => 'name'
);
$categories = get_categories($args);
if (!empty($categories)) :
?>
    <section class="dropdown js-dropdown">
        <div class="ctn-main">
            <div class="dropdown__breadcrumb">
                <span class="breadcrumb__anchor js-breadcrumb__anchor"></span>
            </div>
            <div class="dropdown__wrapper js-dropdown__wrapper">
                <span class="dropdown__selected filter__selected js-dropdown__selected js-dropdown__btn">
                    <span>Filter</span>
                    <button class="dropdown__btn js-dropdown__btn"></button>
                </span>
                <ul class="dropdown__list filter__list js-filter__list">
                    <li><span class="js-filter-item" filter="all">All</span></li>
                    <?php foreach ($categories as $category) {
                    ?><li><span class="js-filter-item" filter="<?php echo $category->slug; ?>"><?php echo $category->name; ?></span></li><?php
                                                                                                                                        } ?>
                </ul>
            </div>
        </div> <!-- End .ctn-main -->
    </section>
<?php
endif;
