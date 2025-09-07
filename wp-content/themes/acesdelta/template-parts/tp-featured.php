
<?php if (have_rows('featured_items')) : ?>
<div class="featured">
    <?php while (have_rows('featured_items')) :
        the_row();
        $featured_itemID = get_sub_field('featured_item');
        if ($featured_itemID) :
            $post = $featured_itemID;
            setup_postdata($post);
            ?>
      <div class="featured-item" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>);">
        <div class="featured-content">
          <h4><?php the_title(); ?></h4>
          <div class="featured-copy">
            <?php
              $text = strip_shortcodes($post->post_content);
              $text = apply_filters('the_content', $text);
              $text = str_replace(']]>', ']]&gt;', $text);
              $excerpt_length = apply_filters('excerpt_length', 10);
              $excerpt_more = apply_filters('excerpt_more', ' ' . '&hellip;');
              $text = wp_trim_words($text, $excerpt_length, $excerpt_more);
            ?>
            <p><?php echo $text; ?></p>
          </div>
          <a href="<?php the_permalink(); ?>">Read more</a>
        </div>

      </div>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
    <?php endwhile; ?>
</div>
<?php endif; ?>