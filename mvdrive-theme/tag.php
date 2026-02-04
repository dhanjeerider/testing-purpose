<?php
/**
 * Template for displaying tag archives.
 *
 * This file simply loads the archive template since the layout is the same
 * for categories and tags.  Having a separate file allows WordPress to
 * distinguish tag archives in the template hierarchy while reusing the
 * common logic.
 */

// Reuse the archive template for tag archives.
get_template_part( 'archive' );