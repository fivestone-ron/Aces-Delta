<?php

/**
 * Block Name: Mastering Challenges
 *
 * This is the template that displays the Mastering Challenges block.
 */

// get image field (array)
$title = get_field('title');
$summary = get_field('summary');
$layout = get_field('layout');
$icon_text_block = get_field('icon_text_block');
$icon_text_block_with_link = get_field('icon_text_block_with_link');

// create id attribute for specific styling
$id = 'mastering-challenges-' . $block['id'];
// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';
?>
<section id="<?php echo $id; ?>" class="mastering-challenges <?php echo $align_class; ?>">

    <?php if ($title) { ?>
        <div class="ctn-main">
            <div class="mastering-challenges__heading">
                <h2><?php echo $title; ?></h2>
                <?php echo $summary; ?>
            </div>
        </div> <!-- End .ctn-main -->
    <?php } ?>

    <?php
    if ($layout == "column") {
        if (have_rows('icon_text_block')) :
            $count = count(get_field('icon_text_block')); ?>
            <div class="ctn-main">
                <div class="icons-block js-icons-block -has-<?php echo $count;?>-rows">
                    <?php while (have_rows('icon_text_block')) :
                        the_row();

                        $icon = get_sub_field('itb_icon');
                        $icon_title = get_sub_field('itb_icon_title');
                        $icon_summary = get_sub_field('itbn_icon_summary');
                        $link = get_sub_field('itbn_link'); ?>

                        <div class="block js-block">
                            <a class="<?php echo $link == null ? 'no-link' : ''; ?>" href="<?php echo $link; ?>">
                                <img src="<?php echo $icon['url'] ?>" alt="<?php echo $icon['alt'] ?>">
                                <h3><?php echo $icon_title; ?></h3>
                                <p><?php echo $icon_summary; ?></p>
                            </a>
                        </div>

                    <?php endwhile; ?>
                </div> <!-- End .icons-block -->
            </div> <!-- End .ctn-main -->
        <?php endif;
    } else {
        if (have_rows('icon_text_block_with_link')) : ?>
            <div class="icons-block-with-link">
                <?php while (have_rows('icon_text_block_with_link')) :
                    the_row();

                    $icon = get_sub_field('itbwl_icon');
                    $icon_title = get_sub_field('itbwl_icon_title');
                    $icon_text = get_sub_field('itbwl_icon_text');
                    $link = get_sub_field('itbwl_link'); ?>

                    <div class="block">
                        <div class="ctn-main">
                            <div class="block__image">
                                <img src="<?php echo $icon['url'] ?>" alt="<?php echo $icon['alt'] ?>">
                            </div>
                            <div class="block__content">
                                <h3><?php echo $icon_title; ?></h3>
                                <?php echo $icon_text; ?>
                                <?php if ($link) { ?>
                                    <a class="red-link" href="<?php echo $link; ?>">Learn more <span class="icon-arrow -red"></span></a>
                                <?php } ?>
                            </div>
                        </div> <!-- End .ctn-main -->
                    </div>

                <?php endwhile; ?>
            </div> <!-- End .icons-block-with-link -->
        <?php endif;
    }
    ?>
</section> <!-- End .mastering-challenges -->
<?php
