<?php
// inc/apk-extractor.php — APK Extractor Admin Panel

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Register admin menu ──────────────────────────────────────────────────────
function aspv5_apk_extractor_menu() {
	add_submenu_page(
		'edit.php?post_type=app',
		__( 'APK Extractor', 'aspv5' ),
		__( 'APK Extractor', 'aspv5' ),
		'edit_posts',
		'aspv5-apk-extractor',
		'aspv5_apk_extractor_page'
	);
	add_submenu_page(
		'edit.php?post_type=game',
		__( 'APK Extractor', 'aspv5' ),
		__( 'APK Extractor', 'aspv5' ),
		'edit_posts',
		'aspv5-apk-extractor',
		'aspv5_apk_extractor_page'
	);
}
add_action( 'admin_menu', 'aspv5_apk_extractor_menu' );

// ── Enqueue admin assets ─────────────────────────────────────────────────────
function aspv5_apk_extractor_assets( $hook ) {
	if ( 'app_page_aspv5-apk-extractor' !== $hook ) {
		return;
	}
	$ver = wp_get_theme()->get( 'Version' );
	wp_enqueue_style(
		'aspv5-apk-extractor',
		get_template_directory_uri() . '/assets/css/apk-extractor.css',
		[],
		$ver
	);
	wp_enqueue_script(
		'aspv5-apk-extractor',
		get_template_directory_uri() . '/assets/js/apk-extractor.js',
		[ 'jquery' ],
		$ver,
		true
	);
	wp_localize_script( 'aspv5-apk-extractor', 'ApkExtractor', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'aspv5_apk_extractor' ),
		'i18n'    => [
			'scraping'      => __( 'Scraping…', 'aspv5' ),
			'done'          => __( 'Done!', 'aspv5' ),
			'error'         => __( 'Error', 'aspv5' ),
			'creating'      => __( 'Creating app post…', 'aspv5' ),
			'created'       => __( 'App post created!', 'aspv5' ),
			'no_title'      => __( 'Could not determine app title. Please check the URL.', 'aspv5' ),
			'invalid_url'   => __( 'Please enter a valid Play Store URL.', 'aspv5' ),
			'bulk_start'    => __( 'Starting bulk scrape…', 'aspv5' ),
			'bulk_progress' => __( 'Processing', 'aspv5' ),
			'bulk_done'     => __( 'Bulk scrape complete!', 'aspv5' ),
			'view_post'     => __( 'View Post', 'aspv5' ),
			'edit_post'     => __( 'Edit Post', 'aspv5' ),
		],
	] );
}
add_action( 'admin_enqueue_scripts', 'aspv5_apk_extractor_assets' );

// ── Admin page HTML ──────────────────────────────────────────────────────────
function aspv5_apk_extractor_page() {
	?>
	<div class="wrap apk-extractor-wrap">
		<h1 class="apk-extractor-title">
			<span class="apk-icon-title">&#127381;</span>
			<?php esc_html_e( 'APK Extractor', 'aspv5' ); ?>
		</h1>
		<p class="apk-extractor-desc">
			<?php esc_html_e( 'Paste a Google Play Store URL to scrape app details and auto-create an App post. Use Bulk mode to process multiple URLs at once.', 'aspv5' ); ?>
		</p>

		<?php /* Tab Navigation */ ?>
		<nav class="apk-tabs" role="tablist">
			<button class="apk-tab active" data-tab="single" role="tab" aria-selected="true" aria-controls="tab-single">
				<?php esc_html_e( 'Single URL', 'aspv5' ); ?>
			</button>
			<button class="apk-tab" data-tab="bulk" role="tab" aria-selected="false" aria-controls="tab-bulk">
				<?php esc_html_e( 'Bulk URLs', 'aspv5' ); ?>
			</button>
		</nav>

		<?php /* ── Single URL Tab ── */ ?>
		<div id="tab-single" class="apk-tab-panel active" role="tabpanel">
			<div class="apk-card">
				<div class="apk-field-row">
					<label for="apk-single-url"><?php esc_html_e( 'Play Store URL', 'aspv5' ); ?></label>
					<div class="apk-input-group">
						<input type="url" id="apk-single-url" class="regular-text apk-url-input"
							placeholder="https://play.google.com/store/apps/details?id=com.example.app"
							autocomplete="off">
						<button type="button" id="apk-scrape-btn" class="button button-primary apk-btn-primary">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
							<?php esc_html_e( 'Scrape', 'aspv5' ); ?>
						</button>
					</div>
					<p class="apk-hint"><?php esc_html_e( 'e.g. https://play.google.com/store/apps/details?id=com.whatsapp', 'aspv5' ); ?></p>
				</div>
				<div class="apk-field-row">
					<label for="apk-target-type"><?php esc_html_e( 'Create as', 'aspv5' ); ?></label>
					<select id="apk-target-type">
						<option value="app"><?php esc_html_e( 'App', 'aspv5' ); ?></option>
						<option value="game"><?php esc_html_e( 'Game', 'aspv5' ); ?></option>
					</select>
				</div>
			</div>

			<div id="apk-single-status" class="apk-status" style="display:none;"></div>

			<div id="apk-single-result" class="apk-result-panel" style="display:none;">
				<div class="apk-result-header">
					<div class="apk-result-app-icon" id="apk-result-icon-wrap"></div>
					<div class="apk-result-app-meta">
						<h2 id="apk-result-title"></h2>
						<p id="apk-result-developer" class="apk-result-dev"></p>
						<div class="apk-result-pills" id="apk-result-pills"></div>
					</div>
				</div>

				<table class="apk-data-table widefat">
					<tbody id="apk-data-rows"></tbody>
				</table>

				<div class="apk-result-actions">
					<button type="button" id="apk-create-btn" class="button button-primary apk-btn-primary apk-btn-large">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="16" height="16"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
						<?php esc_html_e( 'Create App Post', 'aspv5' ); ?>
					</button>
					<button type="button" id="apk-rescrape-btn" class="button apk-btn-secondary">
						<?php esc_html_e( 'Re-scrape', 'aspv5' ); ?>
					</button>
				</div>
				<div id="apk-create-status" class="apk-status" style="display:none;"></div>
			</div>
		</div>

		<?php /* ── Bulk URLs Tab ── */ ?>
		<div id="tab-bulk" class="apk-tab-panel" role="tabpanel" hidden>
			<div class="apk-card">
				<div class="apk-field-row">
					<label for="apk-bulk-urls"><?php esc_html_e( 'Play Store URLs (one per line)', 'aspv5' ); ?></label>
					<textarea id="apk-bulk-urls" class="large-text apk-bulk-textarea" rows="8"
						placeholder="https://play.google.com/store/apps/details?id=com.whatsapp&#10;https://play.google.com/store/apps/details?id=com.instagram.android&#10;…"></textarea>
				</div>
				<div class="apk-field-row">
					<label>
						<input type="checkbox" id="apk-bulk-skip-existing" checked>
						<?php esc_html_e( 'Skip if app with same package name already exists', 'aspv5' ); ?>
					</label>
				</div>
				<div class="apk-field-row">
					<button type="button" id="apk-bulk-btn" class="button button-primary apk-btn-primary apk-btn-large">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12l4-4 4 4M12 8v8"/></svg>
						<?php esc_html_e( 'Start Bulk Import', 'aspv5' ); ?>
					</button>
				</div>
			</div>

			<div id="apk-bulk-progress-wrap" style="display:none;">
				<div class="apk-bulk-progress-bar-wrap">
					<div class="apk-bulk-progress-bar" id="apk-bulk-progress-bar"></div>
				</div>
				<div class="apk-bulk-progress-label" id="apk-bulk-progress-label"></div>
			</div>

			<table class="apk-bulk-results widefat" id="apk-bulk-results" style="display:none;">
				<thead>
					<tr>
						<th><?php esc_html_e( '#', 'aspv5' ); ?></th>
						<th><?php esc_html_e( 'App', 'aspv5' ); ?></th>
						<th><?php esc_html_e( 'Status', 'aspv5' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'aspv5' ); ?></th>
					</tr>
				</thead>
				<tbody id="apk-bulk-results-body"></tbody>
			</table>
		</div>
	</div>
	<?php
}

// ── AJAX: Scrape single URL ──────────────────────────────────────────────────
/*
 * AJAX callback to scrape app details from supported sources.
 *
 * This function accepts a URL posted from the admin UI and determines
 * which scraper should handle it based on the domain. Historically it
 * only supported Play Store URLs, but now we also support scraping
 * details from selected third‑party sites such as LiteAPKs. If the
 * provided URL does not match any supported source, an error will be
 * returned. The scraped data is passed back to the JavaScript client
 * where it can be previewed or used to create a post.
 */
function appstorepro_ajax_scrape_playstore() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( [ 'message' => __( 'Permission denied.', 'aspv5' ) ] );
	}

	check_ajax_referer( 'aspv5_apk_extractor', 'nonce' );

    // Sanitize the URL using esc_url_raw (sanitize_url is deprecated in newer WP versions).
    $url = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : '';
    // Validate that a URL was provided. Historically this endpoint only accepted
    // Play Store links, but now it supports additional sources. We defer
    // domain checking to the routing logic below. An empty or malformed URL
    // still triggers an error early to avoid unnecessary processing.
    if ( ! $url ) {
        wp_send_json_error( [ 'message' => __( 'Missing or invalid URL.', 'aspv5' ) ] );
    }

    // Route scraping based on domain. Play Store URLs are handled by
    // appstorepro_fetch_playstore_data(), while LiteAPKs URLs are handled
    // by appstorepro_fetch_liteapks_data(). For any other URL, use generic
    // metadata extraction to pull available data from the page.
    if ( preg_match( '#play\.google\.com/store/apps/details#', $url ) ) {
        $data = appstorepro_fetch_playstore_data( $url );
    } elseif ( preg_match( '#liteapks\.com/#', $url ) ) {
        $data = appstorepro_fetch_liteapks_data( $url );
    } else {
        // Generic scraper for any URL
        $data = appstorepro_fetch_generic_data( $url );
    }

	if ( is_wp_error( $data ) ) {
		wp_send_json_error( [ 'message' => $data->get_error_message() ] );
	}

	wp_send_json_success( $data );
}
add_action( 'wp_ajax_appstorepro_scrape_playstore', 'appstorepro_ajax_scrape_playstore' );

// ── AJAX: Create app post from scraped data ──────────────────────────────────
function appstorepro_ajax_create_app_post() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( [ 'message' => __( 'Permission denied.', 'aspv5' ) ] );
	}

	check_ajax_referer( 'aspv5_apk_extractor', 'nonce' );

	$raw = isset( $_POST['app_data'] ) ? wp_unslash( $_POST['app_data'] ) : '';
	$app = json_decode( $raw, true );
	$post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : 'app';
	if ( ! in_array( $post_type, [ 'app', 'game' ], true ) ) {
		$post_type = 'app';
	}

	if ( ! $app || empty( $app['title'] ) ) {
		wp_send_json_error( [ 'message' => __( 'Invalid app data.', 'aspv5' ) ] );
	}

	$skip_existing = ! empty( $_POST['skip_existing'] );
	$package       = sanitize_text_field( $app['package'] ?? '' );

	// Check for existing post with same package
	if ( $skip_existing && $package ) {
		$existing = get_posts( [
			'post_type'      => [ 'app', 'game' ],
			'posts_per_page' => 1,
			'meta_query'     => [
				[
					'key'     => '_app_package',
					'value'   => $package,
					'compare' => '=',
				],
			],
			'post_status'    => 'any',
			'fields'         => 'ids',
		] );
		if ( $existing ) {
			wp_send_json_success( [
				'skipped' => true,
				'post_id' => $existing[0],
				'title'   => sanitize_text_field( $app['title'] ),
				'edit_url'=> get_edit_post_link( $existing[0], 'raw' ),
				'view_url'=> get_permalink( $existing[0] ),
			] );
		}
	}

	$post_id = wp_insert_post( [
		'post_title'   => sanitize_text_field( $app['title'] ),
		'post_content' => wp_kses_post( $app['description'] ?? '' ),
		'post_status'  => 'draft',
		'post_type'    => $post_type,
		'post_author'  => get_current_user_id(),
	], true );

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error( [ 'message' => $post_id->get_error_message() ] );
	}

	// Save meta
    $meta_map = [
		'_app_package'        => $app['package'] ?? '',
		'_app_version'        => $app['version'] ?? '',
		'_app_size'           => $app['size'] ?? '',
		'_app_developer'      => $app['developer'] ?? '',
		'_app_icon_url'       => $app['icon'] ?? '',
		'_app_rating'         => $app['rating'] ?? '',
		'_app_android_version'=> $app['android_version'] ?? '',
		'_app_play_store_url' => $app['play_store_url'] ?? '',
        '_app_hero_image_url' => $app['hero'] ?? '',
        // Store the trailer (YouTube) URL in the existing `_app_youtube_url` meta
        // instead of a custom `_app_trailer_url`. The meta box labeled
        // “YouTube Tutorial URL” maps to `_app_youtube_url`, so using this key
        // ensures the value appears in the post editor. If the scraper didn't
        // find a trailer, this will remain empty.
        '_app_youtube_url'    => $app['trailer'] ?? '',
		'_app_screenshots'    => implode( "\n", array_map( 'esc_url_raw', array_filter( (array) ( $app['screenshots'] ?? [] ) ) ) ),
		'_app_mod_info'       => $app['mod_info'] ?? '',
		'_app_downloads'      => $app['downloads'] ?? '',
	];

    foreach ( $meta_map as $key => $value ) {
        if ( '' === $value ) {
            continue;
        }
        // Preserve line breaks for screenshot lists. sanitize_text_field() flattens
        // whitespace, converting line breaks to spaces, which breaks the list
        // formatting in the post editor. For screenshots use
        // sanitize_textarea_field() instead, which retains newlines.
        if ( '_app_screenshots' === $key ) {
            update_post_meta( $post_id, $key, sanitize_textarea_field( (string) $value ) );
        } else {
            update_post_meta( $post_id, $key, sanitize_text_field( (string) $value ) );
        }
    }

	// Set category
	if ( ! empty( $app['category'] ) ) {
		$cat_slug = sanitize_title( $app['category'] );
		$term     = get_term_by( 'slug', $cat_slug, 'app-category' );
		if ( ! $term ) {
			$result = wp_insert_term( sanitize_text_field( $app['category'] ), 'app-category', [ 'slug' => $cat_slug ] );
			if ( ! is_wp_error( $result ) ) {
				$term_id = $result['term_id'];
			}
		} else {
			$term_id = $term->term_id;
		}
		if ( ! empty( $term_id ) ) {
			wp_set_object_terms( $post_id, (int) $term_id, 'app-category' );
		}
	}

	// Set featured image from icon URL
	if ( ! empty( $app['icon'] ) ) {
		appstorepro_sideload_image( $app['icon'], $post_id );
	}

	wp_send_json_success( [
		'post_id'  => $post_id,
		'title'    => sanitize_text_field( $app['title'] ),
		'edit_url' => get_edit_post_link( $post_id, 'raw' ),
		'view_url' => get_permalink( $post_id ),
		'skipped'  => false,
	] );
}
add_action( 'wp_ajax_appstorepro_create_app_post', 'appstorepro_ajax_create_app_post' );

// ── Scrape Play Store page ───────────────────────────────────────────────────
function appstorepro_fetch_playstore_data( $url ) {
	$url = add_query_arg( 'hl', 'en', $url );

	$response = wp_remote_get( $url, [
		'timeout'    => 20,
		'user-agent' => 'Mozilla/5.0 (Linux; Android 10; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/Mobile Safari/537.36',
		'headers'    => [
			'Accept-Language' => 'en-US,en;q=0.9',
			'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		],
	] );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'fetch_failed', sprintf(
			/* translators: %s: error message */
			__( 'Failed to fetch Play Store page: %s', 'aspv5' ),
			$response->get_error_message()
		) );
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== (int) $code ) {
		return new WP_Error( 'bad_response', sprintf(
			/* translators: %s: HTTP code */
			__( 'Play Store returned HTTP %s. The URL may be invalid or blocked.', 'aspv5' ),
			(int) $code
		) );
	}

	$html = wp_remote_retrieve_body( $response );
	if ( ! $html ) {
		return new WP_Error( 'empty_body', __( 'Empty response from Play Store.', 'aspv5' ) );
	}

	return appstorepro_parse_playstore_html( $html, $url );
}

// ── Parse Play Store HTML ────────────────────────────────────────────────────
function appstorepro_parse_playstore_html( $html, $original_url ) {
    $data = [
		'title'           => '',
		'developer'       => '',
		'rating'          => '',
		'description'     => '',
		'icon'            => '',
		'hero'            => '',
        'trailer'         => '',
		'screenshots'     => [],
		'category'        => '',
		'size'            => '',
		'downloads'       => '',
		'android_version' => '',
		'version'         => '',
		'package'         => '',
		'play_store_url'  => $original_url,
	];

	// Package ID from URL
	if ( preg_match( '#[?&]id=([A-Za-z0-9._]+)#', $original_url, $m ) ) {
		$data['package'] = $m[1];
	}

	// ── Try JSON-LD first ──
	if ( preg_match_all( '#<script type="application/ld\+json"[^>]*>(.*?)</script>#si', $html, $ldm ) ) {
		foreach ( $ldm[1] as $ld_raw ) {
			$ld = json_decode( $ld_raw, true );
			if ( ! $ld ) {
				continue;
			}
			$type = $ld['@type'] ?? '';
			if ( in_array( $type, [ 'SoftwareApplication', 'MobileApplication', 'VideoGame' ], true ) ) {
				$data['title']       = $data['title']       ?: ( $ld['name'] ?? '' );
				$data['developer']   = $data['developer']   ?: ( $ld['author']['name'] ?? $ld['author'] ?? '' );
				$data['rating']      = $data['rating']      ?: ( $ld['aggregateRating']['ratingValue'] ?? '' );
				$data['description'] = $data['description'] ?: ( $ld['description'] ?? '' );
				$data['category']    = $data['category']    ?: ( $ld['applicationCategory'] ?? '' );
				$data['android_version'] = $data['android_version'] ?: ( $ld['operatingSystem'] ?? '' );
				if ( isset( $ld['image'] ) && is_string( $ld['image'] ) ) {
					$data['icon'] = $data['icon'] ?: $ld['image'];
				}
				break;
			}
		}
	}

	// ── Meta tags fallback ──
	if ( ! $data['title'] && preg_match( '#<meta\s+property="og:title"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['title'] = html_entity_decode( $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}
	if ( ! $data['title'] && preg_match( '#<title[^>]*>([^<]+)</title>#i', $html, $m ) ) {
		$raw_title = html_entity_decode( $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$data['title'] = preg_replace( '#\s*[-–|].*$#', '', $raw_title );
	}
	if ( ! $data['description'] && preg_match( '#<meta\s+(?:name|property)="(?:og:)?description"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['description'] = html_entity_decode( $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}
	if ( ! $data['icon'] && preg_match( '#<meta\s+property="og:image"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['icon'] = esc_url_raw( $m[1] );
	}

	// ── Try additional rating extraction if not found yet ──
	if ( ! $data['rating'] ) {
		// Try numeric rating like "4.5" near the word "star" or "rating"
		if ( preg_match( '#\b([1-4]\.\d)\b[^<]*(?:star|rating)#i', $html, $m ) ) {
			$data['rating'] = $m[1];
		} elseif ( preg_match( '#"ratingValue"\s*:\s*"?([0-9.]+)"?#', $html, $m ) ) {
			$data['rating'] = $m[1];
		}
	}

	// ── Screenshots from og/itemprop images ──
	preg_match_all( '#<img[^>]+src="(https://play-lh\.googleusercontent\.com/[^"]{40,})"[^>]*>#i', $html, $img_m );
	$seen_icons = [];
	foreach ( $img_m[1] as $img_url ) {
		// Icons are typically small; screenshots are large
		$clean = preg_replace( '#=\w\d+#', '', $img_url );
		if ( in_array( $clean, $seen_icons, true ) ) {
			continue;
		}
		$seen_icons[] = $clean;
		// First match is usually the icon; rest are screenshots
		if ( ! $data['icon'] ) {
			$data['icon'] = $img_url;
		} else {
			if ( count( $data['screenshots'] ) < 8 ) {
				$data['screenshots'][] = $img_url;
			}
		}
	}

    // ── Attempt to extract hero/banner and trailer video ──
    // Many Play Store pages include a YouTube trailer embed near the top. The
    // embed uses either a <iframe> with a YouTube embed URL or a
    // data-video-id attribute. If found, we construct a thumbnail URL for the
    // hero image and a watch URL for the trailer. If no video is found we
    // default to using the first screenshot as the hero image.
    if ( ! $data['hero'] ) {
        // Search for YouTube embed URL
        if ( preg_match( '#https?://www\.youtube\.com/embed/([A-Za-z0-9_-]{11})#i', $html, $vm ) ) {
            $vid = $vm[1];
            $data['trailer'] = 'https://www.youtube.com/watch?v=' . $vid;
            $data['hero']    = 'https://img.youtube.com/vi/' . $vid . '/maxresdefault.jpg';
        } elseif ( preg_match( '#data-video-id=\"([A-Za-z0-9_-]{11})\"#i', $html, $vm ) ) {
            $vid = $vm[1];
            $data['trailer'] = 'https://www.youtube.com/watch?v=' . $vid;
            $data['hero']    = 'https://img.youtube.com/vi/' . $vid . '/maxresdefault.jpg';
        } elseif ( preg_match( '#"(https?://www\.youtube\.com/watch\?v=[A-Za-z0-9_-]{11})"#i', $html, $vm ) ) {
            $watch = $vm[1];
            $vid   = substr( strrchr( $watch, '=' ), 1 );
            $data['trailer'] = $watch;
            $data['hero']    = 'https://img.youtube.com/vi/' . $vid . '/maxresdefault.jpg';
        }
    }
    // Use the first screenshot as hero if still empty
    if ( ! $data['hero'] && ! empty( $data['screenshots'] ) ) {
        // Shift the first screenshot to hero and remove it from list
        $first_screenshot = array_shift( $data['screenshots'] );
        $data['hero'] = $first_screenshot;
    }

	// ── Rating from itemprop ──
	if ( ! $data['rating'] ) {
		if ( preg_match( '#itemprop="ratingValue"[^>]*content="([^"]+)"#i', $html, $m ) ) {
			$data['rating'] = $m[1];
		} elseif ( preg_match( '#"(\d+\.\d)\s*(?:out of|\/)\s*5"#', $html, $m ) ) {
			$data['rating'] = $m[1];
		}
	}

	// ── Developer ──
	if ( ! $data['developer'] ) {
		if ( preg_match( '#itemprop="author"[^>]*>.*?<span[^>]*>([^<]+)</span>#si', $html, $m ) ) {
			$data['developer'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}
	}

	// ── Category ──
	if ( ! $data['category'] ) {
		if ( preg_match( '#itemprop="applicationCategory"[^>]*content="([^"]+)"#i', $html, $m ) ) {
			$data['category'] = html_entity_decode( $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}
	}

	// ── Downloads / Installs ──
	if ( ! $data['downloads'] ) {
		if ( preg_match( '#Installs</div><span[^>]*><div[^>]*><span[^>]*>([^<]+)</span>#si', $html, $m ) ) {
			$data['downloads'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		} elseif ( preg_match( '#"interactionCount"\s*:\s*"UserDownloads:([^"]+)"#i', $html, $m ) ) {
			$data['downloads'] = trim( $m[1] );
		} elseif ( preg_match( '#itemprop="downloadCount"[^>]*content="([^"]+)"#i', $html, $m ) ) {
			$data['downloads'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		}
	}

	// ── Size fallback ──
	if ( ! $data['size'] && preg_match( '#Size</div><span[^>]*><div[^>]*><span[^>]*>([^<]+)</span>#si', $html, $m ) ) {
		$data['size'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}

	// ── Android version ──
	if ( ! $data['android_version'] ) {
		if ( preg_match( '#Requires Android</span>[^<]*<span[^>]*>([0-9.]+\+?[^<]*)</span>#si', $html, $m ) ) {
			$data['android_version'] = trim( html_entity_decode( $m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
		} elseif ( preg_match( '#"Requires Android(?: version)?"\s*,\s*"([0-9.]+[^"]*)"#', $html, $m ) ) {
			$data['android_version'] = trim( $m[1] );
		}
	}

	// Sanitize all text fields
	foreach ( [ 'title', 'developer', 'rating', 'category', 'android_version', 'version', 'size', 'package', 'downloads' ] as $k ) {
		$data[ $k ] = sanitize_text_field( $data[ $k ] );
	}
	if ( $data['rating'] ) {
		$data['rating'] = number_format( (float) $data['rating'], 1, '.', '' );
	}
	$data['description'] = wp_kses_post( $data['description'] );
	$data['icon']        = esc_url_raw( $data['icon'] );
	$data['hero']        = esc_url_raw( $data['hero'] );
	$data['screenshots'] = array_map( 'esc_url_raw', $data['screenshots'] );
	$data['play_store_url'] = esc_url_raw( $data['play_store_url'] );

	return $data;
}

// ── Sideload app icon as featured image ─────────────────────────────────────
function appstorepro_sideload_image( $icon_url, $post_id ) {
	if ( ! $icon_url || ! $post_id ) {
		return;
	}
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$attach_id = media_sideload_image( esc_url_raw( $icon_url ), $post_id, null, 'id' );
	if ( ! is_wp_error( $attach_id ) && $attach_id ) {
		set_post_thumbnail( $post_id, $attach_id );
	}
}

/**
 * Scrape app details from a LiteAPKs post.
 *
 * LiteAPKs hosts detailed articles about modified APKs. The markup is
 * WordPress based and includes a top information section with version,
 * size, rating and developer as well as a screenshot gallery and
 * description tabs. This helper extracts structured data similar to
 * Play Store data so that App posts can be created seamlessly from
 * LiteAPKs URLs.
 *
 * @param string $url The LiteAPKs post URL.
 * @return array|WP_Error Parsed app data on success, or WP_Error on failure.
 */
function appstorepro_fetch_liteapks_data( $url ) {
    // Fetch the page.
    $response = wp_remote_get( $url, [
        'timeout'    => 20,
        'user-agent' => 'Mozilla/5.0 (Linux; Android 10; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/Mobile Safari/537.36',
        'headers'    => [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ],
    ] );
    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'fetch_failed', sprintf( __( 'Failed to fetch LiteAPKs page: %s', 'aspv5' ), $response->get_error_message() ) );
    }
    $code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== (int) $code ) {
        return new WP_Error( 'bad_response', sprintf( __( 'LiteAPKs returned HTTP %s. The URL may be invalid or blocked.', 'aspv5' ), (int) $code ) );
    }
    $html = wp_remote_retrieve_body( $response );
    if ( ! $html ) {
        return new WP_Error( 'empty_body', __( 'Empty response from LiteAPKs.', 'aspv5' ) );
    }

    $data = [
        'title'           => '',
        'developer'       => '',
        'rating'          => '',
        'description'     => '',
        'icon'            => '',
        'hero'            => '',
        'trailer'         => '',
        'screenshots'     => [],
        'category'        => '',
        'size'            => '',
        'downloads'       => '',
        'android_version' => '',
        'version'         => '',
        'package'         => '',
        'play_store_url'  => $url,
        'mod_info'        => '',
    ];

    // Title. Use the <h1> in the app-info row.
    if ( preg_match( '#<h1[^>]*>([^<]+)</h1>#i', $html, $m ) ) {
        $data['title'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    }

    // Developer. Inside <div class="developer"><a>...</a></div>
    if ( preg_match( '#<div\s+class="developer"[^>]*>.*?<a[^>]*>([^<]+)</a>#si', $html, $m ) ) {
        $data['developer'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    }

    // Extract version and size from info sections. We look for a pattern where
    // a value div is followed by a label div. The markup appears twice (once
    // in the top stats row and once in the sticky info bar). We'll grab the
    // first occurrence for each.
    if ( preg_match( '#<div[^>]*class="value"[^>]*>([^<]+)</div>\s*<div[^>]*class="label"[^>]*>\s*Version#si', $html, $m ) ) {
        $data['version'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    } elseif ( preg_match( '#<div[^>]*class="info-bar-label"[^>]*>\s*VERSION\s*</div>.*?<div[^>]*class="info-bar-value"[^>]*>([^<]+)#si', $html, $m ) ) {
        $data['version'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    }
    if ( preg_match( '#<div[^>]*class="value"[^>]*>([^<]+)</div>\s*<div[^>]*class="label"[^>]*>\s*Size#si', $html, $m ) ) {
        $data['size'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    } elseif ( preg_match( '#<div[^>]*class="info-bar-label"[^>]*>\s*SIZE\s*</div>.*?<div[^>]*class="info-bar-value"[^>]*>([^<]+)#si', $html, $m ) ) {
        $data['size'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    }

    // Rating. Looks like <div class="value">3.7 <div class="info-bar-sub info-bar-stars">★</div></div>
    if ( preg_match( '#<div[^>]*class="value"[^>]*>\s*([0-9]+(?:\.[0-9]+)?)\s*<div[^>]*info-bar-stars#si', $html, $m ) ) {
        $data['rating'] = number_format( (float) $m[1], 1, '.', '' );
    }

    // Downloads/Views. There are two possible labels: "Views" or "Reached".
    if ( preg_match( '#<div[^>]*class="label"[^>]*>\s*(?:Views|Reached)\s*</div>\s*</?div[^>]*>\s*<div[^>]*class="value"[^>]*>([^<]+)#si', $html, $m ) ) {
        $data['downloads'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    }

    // Category. Try JSON-LD: "articleSection":["Entertainment"]
    if ( preg_match( '#"articleSection"\s*:\s*\[\s*"([^"]+)"#i', $html, $m ) ) {
        $data['category'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    }

    // Icon image. Use og:image meta first; fallback to the app-info-icon.
    if ( preg_match( '#<meta\s+property="og:image"\s+content="([^"]+)"#i', $html, $m ) ) {
        $data['icon'] = esc_url_raw( $m[1] );
    } elseif ( preg_match( '#<div\s+class="app-info-icon"[^>]*>\s*<img[^>]+src="([^"]+)"#si', $html, $m ) ) {
        $data['icon'] = esc_url_raw( $m[1] );
    }

    // Package name and Play Store URL from the "Get it on" anchor.
    if ( preg_match( '#https://play\.google\.com/store/apps/details\?id=([A-Za-z0-9._]+)#', $html, $m ) ) {
        $data['package']        = sanitize_text_field( $m[1] );
        $data['play_store_url'] = 'https://play.google.com/store/apps/details?id=' . $data['package'];
    }

    // Screenshots. Grab up to 8 <img src="..."> within the screenshot section.
    if ( preg_match_all( '#<div[^>]*class="screenshot-scroll"[^>]*>\s*([^<]+)#si', $html, $junk ) ) {
        // Not used.
    }
    if ( preg_match_all( '#<img\s+[^>]*src="(https?://[^"]+)"[^>]*data-idx#i', $html, $sm ) ) {
        foreach ( $sm[1] as $src ) {
            $clean = esc_url_raw( $src );
            if ( ! in_array( $clean, $data['screenshots'], true ) ) {
                if ( count( $data['screenshots'] ) < 8 ) {
                    $data['screenshots'][] = $clean;
                }
            }
        }
    }

    // Description: content of the description tab. Keep limited to a reasonable length.
    if ( preg_match( '#<div\s+class="desc-content[^>]*>\s*(.*?)</div>#si', $html, $m ) ) {
        $desc_raw = trim( $m[1] );
        // Remove script/style tags and HTML comments
        $desc_raw = preg_replace( '#<script.*?</script>#si', '', $desc_raw );
        $desc_raw = preg_replace( '#<style.*?</style>#si', '', $desc_raw );
        $desc_raw = preg_replace( '#<!--.*?-->#s', '', $desc_raw );
        $desc_raw = wp_strip_all_tags( $desc_raw );
        // Limit to 1000 chars to avoid huge excerpts
        $data['description'] = sanitize_text_field( mb_substr( $desc_raw, 0, 1000 ) );
    }

    // Mod info: from "MOD:" preface or mod-info tab.
    if ( preg_match( '#MOD:\s*([^<]+)#i', $html, $m ) ) {
        $data['mod_info'] = sanitize_text_field( trim( $m[1] ) );
    } elseif ( preg_match( '#<div[^>]*id="mod-info"[^>]*>\s*<div[^>]*class="entry-content"[^>]*>\s*<ul>(.*?)</ul>#si', $html, $m ) ) {
        $list = strip_tags( $m[1] );
        $list = preg_replace( '/\s+/', ' ', $list );
        $data['mod_info'] = sanitize_text_field( trim( $list ) );
    }

    // Sanitize scalar text fields.
    foreach ( [ 'title', 'developer', 'category', 'version', 'size', 'downloads', 'android_version', 'package' ] as $k ) {
        $data[ $k ] = sanitize_text_field( $data[ $k ] );
    }
    if ( $data['rating'] ) {
        $data['rating'] = number_format( (float) $data['rating'], 1, '.', '' );
    }
    $data['icon']        = esc_url_raw( $data['icon'] );
    $data['hero']        = esc_url_raw( $data['hero'] );
    $data['trailer']     = esc_url_raw( $data['trailer'] );
    $data['screenshots'] = array_map( 'esc_url_raw', $data['screenshots'] );
    $data['play_store_url'] = esc_url_raw( $data['play_store_url'] );

    return $data;
}

/**
 * Generic metadata scraper for any URL.
 *
 * This scraper works with any website by extracting metadata from multiple
 * sources: JSON-LD structured data, Open Graph meta tags, Schema.org microdata,
 * and custom regex patterns. It intelligently maps common field names across
 * different website structures to populate all available meta fields.
 *
 * @param string $url The URL to scrape.
 * @return array|WP_Error Parsed app data on success, or WP_Error on failure.
 */
function appstorepro_fetch_generic_data( $url ) {
	// Fetch the page
	$response = wp_remote_get( $url, [
		'timeout'             => 30,
		'redirection'        => 5,
		'httpversion'        => '1.0',
		'user-agent'         => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
		'sslverify'          => true,
	] );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'http_error', __( 'Failed to fetch the page.', 'aspv5' ) );
	}

	$code = wp_remote_retrieve_response_code( $response );
	if ( 200 !== (int) $code ) {
		return new WP_Error( 'http_error', sprintf( __( 'HTTP Error: %d', 'aspv5' ), $code ) );
	}

	$html = wp_remote_retrieve_body( $response );
	if ( ! $html ) {
		return new WP_Error( 'no_content', __( 'The page returned no content.', 'aspv5' ) );
	}

	return appstorepro_parse_generic_html( $html, $url );
}

/**
 * Parse generic HTML to extract app metadata.
 *
 * Tries multiple extraction methods:
 * 1. JSON-LD structured data
 * 2. Open Graph meta tags
 * 3. Twitter Card meta tags
 * 4. Schema.org microdata
 * 5. Regex patterns for common field names
 *
 * @param string $html The HTML content.
 * @param string $original_url The original URL.
 * @return array Extracted app data.
 */
function appstorepro_parse_generic_html( $html, $original_url ) {
	$data = [
		'title'            => '',
		'developer'        => '',
		'description'      => '',
		'icon'             => '',
		'hero'             => '',
		'rating'           => '',
		'category'         => '',
		'version'          => '',
		'size'             => '',
		'android_version'  => '',
		'downloads'        => '',
		'package'          => '',
		'screenshots'      => [],
		'play_store_url'   => esc_url_raw( $original_url ),
		'download_url'     => '',
		'telegram_url'     => '',
		'telegram_members' => '',
		'youtube_url'      => '',
		'mod_info'         => '',
		'category_icon'    => '',
	];

	// ── Extract from JSON-LD ──
	if ( preg_match_all( '#<script type="application/ld\+json"[^>]*>(.*?)</script>#si', $html, $ldm ) ) {
		foreach ( $ldm[1] as $json_str ) {
			$json = json_decode( $json_str, true );
			if ( is_array( $json ) ) {
				appstorepro_extract_jsonld_fields( $json, $data );
			}
		}
	}

	// ── Extract from Open Graph / Meta tags ──
	if ( preg_match( '#<meta\s+property="og:title"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['title'] = $data['title'] ?: html_entity_decode( $m[1] );
	}
	if ( preg_match( '#<meta\s+name="title"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['title'] = $data['title'] ?: html_entity_decode( $m[1] );
	}
	if ( ! $data['title'] && preg_match( '#<title[^>]*>([^<]+)</title>#i', $html, $m ) ) {
		$data['title'] = html_entity_decode( $m[1] );
	}

	// Description
	if ( preg_match( '#<meta\s+(?:property="og:)?description"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['description'] = $data['description'] ?: html_entity_decode( $m[1] );
	}
	if ( ! $data['description'] && preg_match( '#<meta\s+name="description"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['description'] = html_entity_decode( $m[1] );
	}

	// Icon / Image
	if ( ! $data['icon'] && preg_match( '#<meta\s+property="og:image"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['icon'] = $m[1];
	}
	if ( ! $data['icon'] && preg_match( '#<meta\s+name="twitter:image"\s+content="([^"]*)"#i', $html, $m ) ) {
		$data['icon'] = $m[1];
	}
	if ( ! $data['icon'] && preg_match( '#<link\s+rel="icon"\s+href="([^"]*)"#i', $html, $m ) ) {
		$data['icon'] = $m[1];
	}

	// ── Extract images for hero and screenshots ──
	$images = [];
	
	// Look for og:image (usually feature image)
	if ( preg_match_all( '#<meta\s+property="og:image"\s+content="([^"]*)"#i', $html, $img_m ) ) {
		$images = array_merge( $images, $img_m[1] );
	}
	
	// Look for all img tags
	if ( preg_match_all( '#<img[^>]+src=["\']([^"\']+)["\'][^>]*>#i', $html, $img_m ) ) {
		$images = array_merge( $images, $img_m[1] );
	}

	// Set hero and screenshots
	$images = array_filter( array_unique( $images ) );
	if ( ! $data['hero'] && ! empty( $images ) ) {
		$data['hero'] = reset( $images );
	}
	if ( count( $images ) > 1 ) {
		$data['screenshots'] = array_slice( $images, 1, 5 );
	}

	// ── Extract specific fields with regex ──
	appstorepro_extract_generic_fields( $html, $data );

	// ── Extract URLs from text ──
	if ( ! $data['telegram_url'] && preg_match( '#https?://t\.me/[^\s"\'<>]+#i', $html, $m ) ) {
		$data['telegram_url'] = $m[0];
	}
	if ( ! $data['youtube_url'] && preg_match( '#https?://(?:www\.)?youtube\.com/[^\s"\'<>]+#i', $html, $m ) ) {
		$data['youtube_url'] = $m[0];
	}
	if ( ! $data['download_url'] && preg_match( '#<a[^>]+href=["\']([^"\']*(?:\.apk|download)[^"\']*)["\'][^>]*>#i', $html, $m ) ) {
		$data['download_url'] = $m[1];
	}

	// ── Clean and sanitize ──
	foreach ( [ 'title', 'developer', 'category', 'version', 'size', 'downloads', 'android_version', 'package', 'mod_info', 'telegram_members' ] as $k ) {
		$data[ $k ] = sanitize_text_field( $data[ $k ] );
	}
	if ( $data['rating'] ) {
		$data['rating'] = number_format( (float) $data['rating'], 1, '.', '' );
	}
	$data['description'] = wp_kses_post( $data['description'] );
	$data['icon']        = esc_url_raw( $data['icon'] );
	$data['hero']        = esc_url_raw( $data['hero'] );
	$data['screenshots'] = array_map( 'esc_url_raw', $data['screenshots'] );
	$data['download_url'] = esc_url_raw( $data['download_url'] );
	$data['telegram_url'] = esc_url_raw( $data['telegram_url'] );
	$data['youtube_url']  = esc_url_raw( $data['youtube_url'] );

	return $data;
}

/**
 * Extract fields from JSON-LD data.
 *
 * @param array $json The JSON-LD object.
 * @param array &$data Reference to the data array to populate.
 */
function appstorepro_extract_jsonld_fields( $json, &$data ) {
	if ( ! is_array( $json ) ) {
		return;
	}

	// Map JSON-LD fields to our data structure
	$field_map = [
		'name'        => 'title',
		'author'      => 'developer',
		'description' => 'description',
		'image'       => 'icon',
		'ratingValue' => 'rating',
		'version'     => 'version',
		'applicationCategory' => 'category',
	];

	foreach ( $field_map as $json_key => $data_key ) {
		if ( ! empty( $json[ $json_key ] ) && empty( $data[ $data_key ] ) ) {
			if ( is_array( $json[ $json_key ] ) ) {
				$data[ $data_key ] = isset( $json[ $json_key ]['name'] )
					? $json[ $json_key ]['name']
					: ( isset( $json[ $json_key ][0] ) ? $json[ $json_key ][0] : '' );
			} else {
				$data[ $data_key ] = $json[ $json_key ];
			}
		}
	}

	// Handle nested structures
	if ( ! empty( $json['author'] ) && is_array( $json['author'] ) && ! empty( $json['author']['name'] ) ) {
		$data['developer'] = $data['developer'] ?: $json['author']['name'];
	}
	if ( ! empty( $json['image'] ) && is_array( $json['image'] ) ) {
		if ( ! empty( $json['image']['url'] ) ) {
			$data['icon'] = $data['icon'] ?: $json['image']['url'];
		} elseif ( ! empty( $json['image'][0] ) ) {
			$data['icon'] = $data['icon'] ?: $json['image'][0];
		}
	}

	// Handle aggregate rating
	if ( ! empty( $json['aggregateRating']['ratingValue'] ) ) {
		$data['rating'] = $data['rating'] ?: $json['aggregateRating']['ratingValue'];
	}
}

/**
 * Extract common app metadata fields using regex patterns.
 *
 * @param string $html The HTML content.
 * @param array &$data Reference to the data array.
 */
function appstorepro_extract_generic_fields( $html, &$data ) {
	// Common patterns for different fields
	$patterns = [
		'version'        => [ '#version["\']?\s*[:=]\s*["\']?([0-9.]+)["\']?#i', '#v(\d+\.\d+(?:\.\d+)?)\b#i' ],
		'size'           => [ '#size["\']?\s*[:=]\s*["\']?([0-9.]+ (?:mb|gb|kb))["\']?#i', '#(\d+\.?\d*\s*(?:mb|gb|kb))\b#i' ],
		'rating'         => [ '#rating["\']?\s*[:=]\s*["\']?([0-9.]+)["\']?#i', '#(\d\.\d+|\d+\/5|\d+%)\b#i' ],
		'downloads'      => [ '#download["\']?\s*[:=]\s*["\']?([0-9.]+[kmb]*\+?)["\']?#i', '#(\d+[kmb]*\+?)\s*(?:download|install)#i' ],
		'android_version'=> [ '#android["\']?\s*[:=]\s*["\']?(\d+\.?\d*\.?\d*)["\']?#i', '#requires? android (\d+\.?\d*)["\']?#i' ],
		'package'        => [ '#package["\']?\s*[:=]\s*["\']?([a-z0-9._]+)["\']?#i', '#com\.[a-z0-9._]{5,}#i' ],
	];

	foreach ( $patterns as $field => $pattern_list ) {
		if ( ! empty( $data[ $field ] ) ) {
			continue;
		}

		foreach ( $pattern_list as $pattern ) {
			if ( preg_match( $pattern, $html, $m ) ) {
				$data[ $field ] = $m[1];
				break;
			}
		}
	}

	// Extract MOD info
	if ( preg_match( '#(?:mod\s+info|mod\s+feature|mod\s+apk)[\'":]?\s*([^<\n]+)#i', $html, $m ) ) {
		$data['mod_info'] = trim( strip_tags( $m[1] ) );
	}

	// Extract Telegram members if available
	if ( preg_match( '#telegram.*?(\d+|\d+\.?\d+[km])\s*(?:members?|subscribers?|users?)#i', $html, $m ) ) {
		$data['telegram_members'] = $m[1];
	}
}

