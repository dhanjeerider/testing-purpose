<?php
/**
 * Template Name: Saved Posts Page
 * Display user's saved/bookmarked posts
 */

get_header(); ?>

<main class="site-main">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="mb-4">My Saved Articles</h1>
                
                <?php if (is_user_logged_in()) : ?>
                    <?php echo do_shortcode('[saved_posts]'); ?>
                <?php else : ?>
                    <div class="alert alert-info">
                        <h4>Login Required</h4>
                        <p>Please login to view your saved posts.</p>
                        <a href="<?php echo wp_login_url(get_permalink()); ?>" class="btn btn-primary">Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
