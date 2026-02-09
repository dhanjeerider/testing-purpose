<?php
/**
 * The template for displaying all single posts.
 *
 * This template replicates the structure of the original MoviesDrive single
 * page design.  It includes breadcrumbs, meta information, categories,
 * tags, a Telegram call‑to‑action, comments, and a sidebar.  Widgets in
 * the sidebar can be configured via the WordPress Widgets interface.
 */

get_header();

?>
<div class="app-container">
    <nav class="breadcrumb">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Home', 'mvdrive' ); ?></a>
        <?php
        // If the post has categories, include the first one in the breadcrumb.
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            echo '<span class="breadcrumb-separator">/</span>';
            echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
        }
        ?>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current"><?php the_title(); ?></span>
    </nav>

    <div class="post-layout">
        <article class="post-content">
            <header class="post-header">
                <h1 class="post-title"><?php the_title(); ?></h1>
                <div class="post-meta">
                    <span class="post-date">
                        <svg viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"></rect><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="2"></path></svg>
                        <?php echo get_the_date(); ?>
                    </span>
                    <div class="post-categories">
                        <?php
                        // Show only first category
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) {
                            echo '<a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a>';
                        }
                        ?>
                    </div>
                </div>
            </header>

            <main class="page-body">
                <?php
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
                ?>
            </main>

            <div class="post-tags">
                <span class="tags-label"><?php _e( 'Tags:', 'mvdrive' ); ?></span>
                <?php the_tags( '', ', ', '' ); ?>
            </div>

            <?php mvdrive_display_subscribe_button(); ?>

            <?php mvdrive_display_telegram_cta(); ?>

            <section class="comments-section">
                <?php
                if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }
                ?>
            </section>
        </article>

        <aside class="post-sidebar">
            <?php
            if ( is_active_sidebar( 'sidebar-1' ) ) {
                dynamic_sidebar( 'sidebar-1' );
            } else {
                // Default sidebar content: search and recent posts.
                ?>
                <div class="sidebar-widget">
                    <h3 class="widget-title"><?php _e( 'Search', 'mvdrive' ); ?></h3>
                    <?php get_search_form(); ?>
                </div>
                <div class="sidebar-widget">
                    <h3 class="widget-title"><?php _e( 'Recent Posts', 'mvdrive' ); ?></h3>
                    <ul class="recent-posts">
                        <?php
                        $recent_posts = wp_get_recent_posts( array( 'numberposts' => 5 ) );
                        foreach ( $recent_posts as $recent ) {
                            echo '<li class="recent-post-item">';
                            echo '<a href="' . esc_url( get_permalink( $recent['ID'] ) ) . '">' . esc_html( $recent['post_title'] ) . '</a>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="sidebar-widget">
                    <h3 class="widget-title"><?php _e( 'Categories', 'mvdrive' ); ?></h3>
                    <ul class="category-list">
                        <?php
                        wp_list_categories( array(
                            'title_li' => '',
                            'orderby'  => 'name',
                            'show_count' => false,
                        ) );
                        ?>
                    </ul>
                </div>
                <?php
            }
            ?>
        </aside>
    </div>
</div>

<?php
get_footer();