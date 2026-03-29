<?php
// inc/template-tags.php — Helper functions

// appstorepro_get_app_meta
function appstorepro_get_app_meta( $post_id, $key ) {
	return get_post_meta( $post_id, $key, true );
}

// appstorepro_get_app_rating_stars
function appstorepro_get_app_rating_stars( $rating ) {
	$rating  = (float) $rating;
	$output  = '';
	$full    = floor( $rating );
	$half    = ( $rating - $full ) >= 0.5 ? 1 : 0;
	$empty   = 5 - $full - $half;
	$output .= str_repeat( '<span class="star star-full">★</span>', $full );
	if ( $half ) {
		$output .= '<span class="star star-half">½</span>';
	}
	$output .= str_repeat( '<span class="star star-empty">☆</span>', max( 0, $empty ) );
	return $output;
}

// appstorepro_format_downloads — compact format (1.2K / 3.4M)
function appstorepro_format_downloads( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}
	// If already contains K/M/B suffix, normalize casing
	if ( preg_match( '/^([0-9,.]+)\s*([kmb])\+?$/i', $raw, $m ) ) {
		$val = (float) str_replace( ',', '', $m[1] );
		$suf = strtoupper( $m[2] );
		return rtrim( rtrim( number_format( $val, 1 ), '0' ), '.' ) . $suf;
	}
	// Extract digits and convert
	$num = (float) preg_replace( '/[^\d.]/', '', $raw );
	if ( $num <= 0 ) {
		return $raw;
	}
	$units = [ 'B' => 1000000000, 'M' => 1000000, 'K' => 1000 ];
	foreach ( $units as $suffix => $divisor ) {
		if ( $num >= $divisor ) {
			$fmt = $num / $divisor;
			return rtrim( rtrim( number_format( $fmt, 1 ), '0' ), '.' ) . $suffix;
		}
	}
	return number_format_i18n( $num );
}

// appstorepro_allowed_svg_kses (now also allows boxicons <i>)
function appstorepro_allowed_svg_kses() {
	return [
		'i'       => [ 'class' => true, 'aria-hidden' => true ],
		'svg'     => [ 'viewbox' => true, 'viewBox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'width' => true, 'height' => true, 'xmlns' => true, 'aria-hidden' => true ],
		'path'    => [ 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true ],
		'circle'  => [ 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ],
		'line'    => [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true ],
		'polyline'=> [ 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true ],
		'polygon' => [ 'points' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ],
		'rect'    => [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ],
	];
}

// appstorepro_get_category_icon_svg
function appstorepro_get_category_icon_svg( $slug ) {
	$icons = [
		'game'          => 'bxs-joystick',
		'games'         => 'bxs-joystick',
		'education'     => 'bxs-book-open',
		'tools'         => 'bxs-wrench',
		'social'        => 'bxs-user-voice',
		'music'         => 'bxs-music',
		'video'         => 'bxs-videos',
		'photo'         => 'bxs-camera',
		'health'        => 'bxs-heart',
		'finance'       => 'bxs-bank',
		'news'          => 'bxs-news',
		'travel'        => 'bxs-plane-alt',
		'shopping'      => 'bxs-basket',
		'sports'        => 'bxs-basketball',
		'books'         => 'bxs-book',
		'app'           => 'bxs-widget',
		'apps'          => 'bxs-grid',
		'mod'           => 'bxs-chip',
		'mods'          => 'bxs-chip',
		'utilities'     => 'bxs-color',
		'productivity'  => 'bxs-briefcase-alt',
		'communication' => 'bxs-message-dots',
		'entertainment' => 'bxs-tv',
		'lifestyle'     => 'bxs-bolt',
		'security'      => 'bxs-shield-alt-2',
	];

	$class = $icons[ $slug ] ?? 'bxs-grid-alt';
	return '<i class="bx ' . esc_attr( $class ) . '" aria-hidden="true"></i>';
}

// appstorepro_get_color_themes
function appstorepro_get_color_themes() {
	return [
		[ 'name' => 'Green',  'primary' => '#3DDC84', 'light' => '#5DE89A', 'bg' => '#F0FFF6' ],
		[ 'name' => 'Blue',   'primary' => '#4A90E2', 'light' => '#6AA8F0', 'bg' => '#EEF5FD' ],
		[ 'name' => 'Orange', 'primary' => '#FF6A00', 'light' => '#FF8C38', 'bg' => '#FFF4EC' ],
		[ 'name' => 'Purple', 'primary' => '#9B59B6', 'light' => '#B07AC6', 'bg' => '#F7F0FC' ],
		[ 'name' => 'Pink',   'primary' => '#E91E8C', 'light' => '#F04EA3', 'bg' => '#FEF0F7' ],
		[ 'name' => 'Yellow', 'primary' => '#F1C40F', 'light' => '#F5D237', 'bg' => '#FEFBE8' ],
	];
}

// appstorepro_posted_on
function appstorepro_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	$time_string = sprintf(
		$time_string,
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
	echo '<span class="posted-on"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a></span>';
}

// appstorepro_posted_by
function appstorepro_posted_by() {
	echo '<span class="byline"><span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span></span>';
}
