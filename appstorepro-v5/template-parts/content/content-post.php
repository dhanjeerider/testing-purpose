<?php
// template-parts/content/content-post.php — AppStore Pro V5
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'aspv5-reveal' ); ?>>
	<a href="<?php echo esc_url( get_the_permalink() ); ?>"
	   class="flex gap-4 p-4 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 hover:border-primary/30 dark:hover:border-primary/30 transition-colors group">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0">
				<?php the_post_thumbnail( 'thumbnail', [ 'class' => 'w-full h-full object-cover', 'loading' => 'lazy' ] ); ?>
			</div>
		<?php endif; ?>
		<div class="flex-1 min-w-0">
			<h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 group-hover:text-primary transition-colors line-clamp-2 mb-1">
				<?php the_title(); ?>
			</h3>
			<p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2"><?php the_excerpt(); ?></p>
			<div class="text-[10px] text-gray-400 mt-1.5"><?php echo esc_html( get_the_date() ); ?></div>
		</div>
	</a>
</article>
