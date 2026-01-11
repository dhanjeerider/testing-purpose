<?php get_header(); ?>

<div class="container my-5">
    
    <!-- Search Title Section -->
    <div class="search-title-section mb-5">
        <div class="search-title-wrapper">
            <h1 class="search-main-title">Search Results</h1>
            <p class="search-query-text">
                Showing results for: <strong>"<?php echo get_search_query(); ?>"</strong>
            </p>
            <?php if (have_posts()) : ?>
                <div class="search-meta">
                    <span class="post-count">
                        <i class="fas fa-search"></i> <?php echo $wp_query->found_posts; ?> Result(s) Found
                    </span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Search Form -->
        <div class="search-form-wrapper mt-4">
            <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
                <div class="input-group input-group-lg">
                    <input type="search" class="form-control" placeholder="Search again..." value="<?php echo get_search_query(); ?>" name="s" required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="posts-container">
                <div class="row">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <div class="col-md-6 mb-4">
                                <article id="post-<?php the_ID(); ?>" <?php post_class('material-card h-100'); ?>>
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="post-thumbnail">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('news-medium'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-content p-4">
                                        <h2 class="post-title h5">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h2>
                                        
                                        <div class="post-meta mb-3">
                                            <span class="meta-item"><i class="far fa-calendar"></i> <?php echo get_the_date(); ?></span>
                                        </div>
                                        
                                        <div class="post-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                        </div>
                                        
                                        <div class="post-footer mt-3">
                                            <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                                Read More <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endwhile; ?>
                        
                        <div class="col-12">
                            <nav class="pagination-wrapper mt-4">
                                <?php
                                the_posts_pagination(array(
                                    'mid_size' => 2,
                                    'prev_text' => '<i class="fas fa-chevron-left"></i> Previous',
                                    'next_text' => 'Next <i class="fas fa-chevron-right"></i>',
                                ));
                                ?>
                            </nav>
                        </div>
                    <?php else : ?>
                        <div class="col-12">
                            <div class="no-results text-center py-5">
                                <i class="fas fa-search fa-4x mb-3 text-muted"></i>
                                <h3>No Results Found</h3>
                                <p class="text-muted">Try different keywords</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <aside class="main-sidebar">
                <?php if (is_active_sidebar('main-sidebar')) : ?>
                    <?php dynamic_sidebar('main-sidebar'); ?>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</div>

<?php get_footer(); ?>
