<?php
// template-parts/content/content-post.php
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card pas-reveal' ); ?>>
	<a href="<?= esc_url( get_the_permalink() ); ?>" class="post-card-link">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="post-card-thumb">
				<?php the_post_thumbnail( 'medium_large', [ 'alt' => get_the_title(), 'loading' => 'lazy' ] ); ?>
			</div>
		<?php endif; ?>
		<div class="post-card-body">
			<div class="post-card-meta">
				<?php appstorepro_posted_on(); ?>
				<?php appstorepro_posted_by(); ?>
			</div>
			<h2 class="post-card-title"><?= esc_html( get_the_title() ); ?></h2>
			<?php if ( has_excerpt() || get_the_excerpt() ) : ?>
				<p class="post-card-excerpt"><?= esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
			<?php endif; ?>
			<span class="post-card-read"><?php esc_html_e( 'Read more', 'appstorepro' ); ?> &rarr;</span>
		</div>
	</a>
</article>
