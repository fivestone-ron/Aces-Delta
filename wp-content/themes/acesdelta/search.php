<?php

/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package New
 */

get_header(); ?>

    <section id="primary" class="content-area">
        <main id="main" class="site-main">

            <?php
            if (have_posts()) : ?>
                <header class="page-header">
                    <div class="ctn-main">
                        <h1 class="page-title"><?php
                            /* translators: %s: search query. */
                            printf(esc_html__('Search Results for: %s', 'acesdelta'), '<span>' . get_search_query() . '</span>');
                        ?></h1>

                        <div class="search">
                            <?php get_search_form() ?>
                        </div>
                    </div>
                </header><!-- .page-header -->

                <div class="ctn-main">
                    <div class="search__article">

                        <?php
                        $counter = 1;
                        /* Start the Loop */
                        echo '<div class="search__article-row">';
                        while (have_posts()) :
                            the_post();

                            /**
                             * Run the loop for the search to output the results.
                             * If you want to overload this in a child theme then include a file
                             * called content-search.php and that will be used instead.
                             */
                            get_template_part('template-parts/content', 'search');

                            if ($counter % 3 == 0) {
                                echo '</div><div class="search__article-row">';
                            }
                            $counter++;
                        endwhile;
                        echo '</div>';?>
                    </div>
                </div>

            <?php else :
                get_template_part('template-parts/content', 'none');
            endif; ?>

        </main><!-- #main -->
    </section><!-- #primary -->

<?php
get_sidebar();
get_footer();
