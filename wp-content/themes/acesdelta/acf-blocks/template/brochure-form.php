<?php

/**
 * Block Name: Brochure Form
 *
 * This is the template that displays the Brochure Form block.
 */

// get image field (array)
$background_color = get_field('background_color');
$form_image = get_field('form_image');
$form_title = get_field('form_title');
$form_description = get_field('form_description');
$form_button_label = get_field('form_button_label');

// create id attribute for specific styling
$id = 'brochure-form-' . $block['id'];
// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';
?>
<section id="<?php echo $id; ?>" class="brochure-form <?php echo $align_class; ?>" style="background-color: <?php echo $background_color; ?>;">
    <div class="ctn-main">
    <?php
    if (!empty($form_image)) : ?>
            <div class="img-ctn">
                <img src="<?php echo esc_url($form_image['url']); ?>" alt="<?php echo esc_attr($form_image['alt']); ?>" />
            </div>
    <?php endif; ?>

        <div class="form-block">
            <div class="form-step-1">
                <div class="top-section">
                    <h3><?php echo $form_title; ?></h3>
                    <p><?php echo $form_description; ?></p>
                </div>
                <div class="bottom-section">
                    <input aria-label="Brochure Email address" type="email" value="" placeholder="Email address">
                    <div class="wp-block-buttons">
                        <div class="wp-block-button">
                            <a class="wp-block-button__link has-text-color has-background no-border-radius"><?php echo $form_button_label ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-step-2 -hidden">
                <?php echo do_shortcode('[gravityform id="1" ajax="true"]'); ?>
                <p>This site is protected by reCAPTCHA and the Google Privacy Policy and Terms of Service apply.</p>
            </div>
        </div>
    </div>
</section>

<?php
