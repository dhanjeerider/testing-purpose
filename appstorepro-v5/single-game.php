<?php
// single-game.php — AppStore Pro V5
get_header();
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/single/app-single' );
endwhile;
get_footer();
