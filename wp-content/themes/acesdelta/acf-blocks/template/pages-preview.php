<?php

/**
 * Block Name: Page Previews
 *
 * This is the template that displays the Page Previews block.
 */

// get image field (array)
$block_title = get_field('section_title');
$how_to_fetch_data = get_field('how_to_fetch_data');
$pages_to_fetch = get_field('pages_to_fetch');
$custom_content = get_field('custom_content');
// create id attribute for specific styling
$id = 'pages-preview-' . $block['id'];
// create align class ("alignwide") from block setting ("wide")
$align_class = $block['align'] ? 'align' . $block['align'] : '';
?>
<section id="<?php echo $id; ?>" class="pages-preview <?php echo $align_class; ?>">
    <div class="ctn-main">
        <?php if ($block_title != '') {
            ?><h2><?php echo $block_title; ?></h2><?php
        } ?>

        <?php
        foreach (return_list_of_pages_previews() as $row) { ?>
                <div class="pages-preview__item">
                    <?php $image = $row['featured_image'];
                    if (!empty($image)) :
                        echo $image;
                    endif; ?>
                    <div class="pages-preview__content">
                        <h3><?php echo $row['title']; ?></h3>
                        <?php echo $row['excerpt']; ?>
                        <?php if ($row['btn_label']) { ?>
                            <a class="red-link" href="<?php echo $row['link']; ?>"><?php echo $row['btn_label']; ?> <span class="icon-arrow -red"></span></a>
                        <?php } ?>
                    </div>
                </div>
        <?php }
        ?>
    </div>
</section>
