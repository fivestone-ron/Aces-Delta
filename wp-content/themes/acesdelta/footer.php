<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package New
 */

?>

</div><!-- #content -->

<footer id="colophon" class="site-footer">
    <div class="ctn-main">
        <?php wp_nav_menu(array( 'theme_location' => 'footer_menu' )); ?>
        <div class="secondary-footer">
            <?php dynamic_sidebar('secondary-footer'); ?>
        </div>
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
