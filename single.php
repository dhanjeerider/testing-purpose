<?php get_header(); ?>

<div class="container">
    <div class="single-post">
        <?php while (have_posts()) { the_post(); ?>
            <?php if (get_the_category()) { ?>
                <span class="post-cat"><?php echo get_the_category()[0]->name; ?></span>
            <?php } ?>
            <h1 class="post-title-single"><?php the_title(); ?></h1>
            <div class="post-meta-top">
                <span><i class="far fa-user"></i> <?php the_author(); ?></span>
                <span><i class="far fa-calendar"></i> <?php echo get_the_date('F j, Y'); ?></span>
            </div>
            <?php if (has_post_thumbnail()) { ?>
                <div class="featured-img"><?php the_post_thumbnail('post-thumb'); ?></div>
            <?php } ?>
            <div class="post-content"><?php the_content(); ?></div>
        <?php } ?>
    </div>
</div>

<?php get_footer(); ?>
