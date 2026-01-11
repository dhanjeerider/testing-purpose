<?php get_header(); ?>

<main class="site-main">
    <div class="container py-4">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Search Title -->
                <header class="page-header mb-4">
                    <h1 class="page-title" style="font-size: 1.75rem; font-weight: 700; color: var(--text-color); margin-bottom: 10px;">
                        <?php
                        printf(
                            esc_html__('Search Results for: %s', 'ajax-news-theme'),
                            '<span style="color: var(--primary-color);">' . get_search_query() . '</span>'
                        );
                        ?>
                    </h1>
                    <?php if (have_posts()) : ?>
                        <p class="search-results-count text-muted" style="font-size: 14px;">
                            <?php
                            global $wp_query;
                            echo $wp_query->found_posts . ' result(s) found';
                            ?>
                        </p>
                    <?php endif; ?>
                </header>

                <div id="posts-container">
                    <div class="posts-grid" id="main-content" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                        <?php if (have_posts()) : ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <?php get_template_part('template-parts/content', 'loop'); ?>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="col-12">
                                <div class="no-results" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 40px 20px; text-align: center;">
                                    <i class="fas fa-search" style="font-size: 3rem; color: var(--secondary-color); margin-bottom: 20px;"></i>
                                    <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--text-color); margin-bottom: 10px;">
                                        No results found
                                    </h3>
                                    <p class="text-muted" style="font-size: 14px; margin-bottom: 20px;">
                                        Sorry, no posts matched your search criteria. Try different keywords.
                                    </p>
                                    
                                    <!-- Search Again -->
                                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                                        <div class="input-group" style="max-width: 500px; margin: 0 auto;">
                                            <input type="search" class="form-control" placeholder="Search again..." name="s" value="<?php echo get_search_query(); ?>" style="padding: 10px 15px; font-size: 14px;">
                                            <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 14px;">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
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
