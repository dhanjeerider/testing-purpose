<?php get_header(); ?>

<main class="site-main">
    <div class="container py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Archive Title -->
                <header class="page-header mb-4">
                    <h1 class="page-title" style="font-size: 1.75rem; font-weight: 700; color: var(--text-color); margin-bottom: 10px;">
                        <?php
                        if (is_category()) {
                            single_cat_title();
                        } elseif (is_tag()) {
                            single_tag_title();
                        } elseif (is_author()) {
                            echo 'Author: ' . get_the_author();
                        } elseif (is_date()) {
                            if (is_day()) {
                                echo 'Daily Archives: ' . get_the_date();
                            } elseif (is_month()) {
                                echo 'Monthly Archives: ' . get_the_date('F Y');
                            } elseif (is_year()) {
                                echo 'Yearly Archives: ' . get_the_date('Y');
                            }
                        } else {
                            echo 'Archives';
                        }
                        ?>
                    </h1>
                    <?php
                    if (is_category() && category_description()) {
                        echo '<div class="archive-description text-muted" style="font-size: 14px;">' . category_description() . '</div>';
                    }
                    ?>
                </header>

                <div id="posts-container">
                    <div class="posts-grid" id="main-content" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                        <?php if (have_posts()) : ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <?php get_template_part('template-parts/content', 'loop'); ?>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h3>No posts found</h3>
                                    <p>Sorry, no posts were found in this archive.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <?php
                    the_posts_pagination(array(
                        'mid_size' => 2,
                        'prev_text' => __('&laquo; Previous', 'ajax-news-theme'),
                        'next_text' => __('Next &raquo;', 'ajax-news-theme'),
                    ));
                    ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
