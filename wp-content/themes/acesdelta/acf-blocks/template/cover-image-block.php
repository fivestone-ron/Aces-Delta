<?php

/**
 * Block Name: Cover Image with an overlapping icon
 *
 * This is the template that displays a cover image with an overlapping icon
 */

$image = get_field('ci_image');
$icon = get_field('ci_icon');
$alignment = get_field('ci_icon_alignment');

// create id attribute for specific styling
$id = 'cover-image-' . $block['id'];
// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';
?>
<section id="<?php echo $id; ?>" class="<?php echo $align_class; ?>">
    <div class="ctn-main">
        <div class="cover-image">
            <img class="cover-image__img" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">

            <div class="cover-image__icon -<?php echo $alignment; ?>">
                <img src="<?php echo $icon["url"]; ?>" alt="<?php echo $icon["alt"]; ?>">
            </div>
        </div>
    </div> <!-- End .ctn-main -->
</section>