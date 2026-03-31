<?php
// archive.php — AppStore Pro V5
get_header();
?>
<main id="main" class="max-w-6xl mx-auto px-4 py-8 min-h-screen" tabindex="-1">

	<header class="mb-6 aspv5-reveal">
		<h1 class="text-2xl font-bold text-gray-900 dark:text-white">
			<?php
			if ( is_category() )      single_cat_title();
			elseif ( is_tag() )       single_tag_title();
			elseif ( is_author() )    the_author();
			elseif ( is_year() )      get_the_date( _x( 'Y', 'yearly archives date format', 'aspv5' ) );
			elseif ( is_month() )     get_the_date( _x( 'F Y', 'monthly archives date format', 'aspv5' ) );
			elseif ( is_day() )       get_the_date( _x( 'F j, Y', 'daily archives date format', 'aspv5' ) );
			else                      esc_html_e( 'Archives', 'aspv5' );
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="flex flex-col gap-4">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content/content', 'post' ); ?>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( [ 'class' => 'aspv5-pagination flex justify-center gap-1 mt-10', 'prev_text' => '&larr;', 'next_text' => '&rarr;' ] ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
	<?php endif; ?>

</main>
<?php get_footer(); ?>
