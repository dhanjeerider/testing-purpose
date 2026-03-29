<?php
// single.php — Single blog post
get_header();
?>
<main id="main" class="site-main" tabindex="-1">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post-article pas-reveal' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="single-post-thumb">
						<?php the_post_thumbnail( 'large', [ 'alt' => get_the_title() ] ); ?>
					</div>
				<?php endif; ?>
				<header class="single-post-header">
					<div class="post-card-meta">
						<?php appstorepro_posted_on(); ?>
						<?php appstorepro_posted_by(); ?>
					</div>
					<h1 class="single-post-title"><?php the_title(); ?></h1>
					<?php
					$cats = get_the_category();
					$tags = get_the_tags();
					if ( $cats || $tags ) : ?>
						<div class="single-post-terms">
							<?php foreach ( $cats as $cat ) : ?>
								<a href="<?= esc_url( get_category_link( $cat->term_id ) ); ?>" class="term-chip term-chip-cat"><?= esc_html( $cat->name ); ?></a>
							<?php endforeach; ?>
							<?php if ( $tags ) : foreach ( $tags as $tag ) : ?>
								<a href="<?= esc_url( get_tag_link( $tag->term_id ) ); ?>" class="term-chip term-chip-tag"><?= esc_html( $tag->name ); ?></a>
							<?php endforeach; endif; ?>
						</div>
					<?php endif; ?>
				</header>
				<div class="single-post-content entry-content">
					<?php
					the_content(
						sprintf(
							wp_kses(
								__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'appstorepro' ),
								[ 'span' => [ 'class' => [] ] ]
							),
							get_the_title()
						)
					);
					wp_link_pages( [
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'appstorepro' ),
						'after'  => '</div>',
					] );
					?>
				</div>
			</article>

			<?php
			the_post_navigation( [
				'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'appstorepro' ) . '</span> <span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'appstorepro' ) . '</span> <span class="nav-title">%title</span>',
			] );
			?>

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>
		<?php endwhile; ?>
	</div>
</main>
<?php get_footer(); ?>
