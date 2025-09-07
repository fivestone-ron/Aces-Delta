<?php

/**
 * Block Name: Latest-Articles
 *
 * This is the template that displays the Latest Articles block.
 */

$repeater = get_field('section_overview_repeater');
if (!empty($repeater)) :
?>
    <section class="dropdown js-dropdown">
        <div class="ctn-main">
            <div class="dropdown__breadcrumb -no-nav">
                <span><?php the_title(); ?></span> <span class="breadcrumb__anchor js-breadcrumb__anchor"></span>
            </div>
            <!--
        <div class="dropdown__wrapper js-dropdown__wrapper">
            <span class="dropdown__selected js-dropdown__selected">
                <a href="#">Overview</a>
                <button class="dropdown__btn js-dropdown__btn"></button>
            </span>
            <ul class="dropdown__list js-section-overview__list">
                <li><a class="js-section-overview__list-item" href="#">Overview</a></li>
                <?php foreach ($repeater as $row) : ?>
                    <li>
                        <a class="js-section-overview__list-item" href="#<?php echo $row['overview_anchor'] ?>"><?php echo $row['overview_label'] ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        -->
        </div> <!-- End .ctn-main -->
    </section>
<?php
endif;
