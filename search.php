<?php get_header(); ?>

<div class="container my-5">
    <div class="search-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="search-title">Search Results</h1>
                <p class="search-query">Showing results for: <strong>"<?php echo get_search_query(); ?>"</strong></p>
                <?php if (have_posts()) : ?>
                    <p class="results-count text-muted">Found <?php echo $wp_query->found_posts; ?> result(s)</p>
                <?php endif; ?>
            </div>
            
            <!-- Layout Switcher -->
            <div class="layout-switcher">
                <button class="layout-btn active" data-layout="grid" title="Grid View">
                    <i class="fas fa-th"></i>
                </button>
                <button class="layout-btn" data-layout="list" title="List View">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
        
        <!-- Search Form -->
        <div class="search-form-wrapper mt-4">
            <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
                <div class="input-group">
                    <input type="search" class="form-control" placeholder="Search again..." value="<?php echo get_search_query(); ?>" name="s" required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="posts-container grid-layout">
        <div id="main-content" class="row">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <div class="col-md-6 mb-4">
                        <article id="post-<?php the_ID(); ?>" <?php post_class('material-card h-100'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium_large', array('class' => 'img-fluid')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-content p-4">
                                <div class="post-type-badge mb-2">
                                    <span class="badge bg-secondary"><?php echo get_post_type(); ?></span>
                                </div>
                                
                                <h2 class="post-title h4">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <div class="post-meta mb-3">
                                    <span class="meta-item"><i class="fas fa-calendar"></i> <?php echo get_the_date(); ?></span>
                                    <span class="meta-item"><i class="fas fa-user"></i> <?php the_author(); ?></span>
                                </div>
                                
                                <div class="post-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                
                                <div class="post-footer mt-3">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">Read More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endwhile; ?>
                
                <!-- Pagination -->
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
                        <p class="text-muted">Sorry, nothing matched your search criteria. Please try again with different keywords.</p>
                        
                        <div class="search-suggestions mt-4">
                            <h5>Search Suggestions:</h5>
                            <ul class="list-unstyled">
                                <li>Check your spelling</li>
                                <li>Try more general keywords</li>
                                <li>Try different keywords</li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
