# AppStore Pro - Shortcodes Documentation

## Available Shortcodes

### 1. Browse Page Shortcode
Display a filterable browse page with advanced search, categories, types, and sorting.

**Shortcode:**
```
[appstorepro_browse type="both" per_page="12"]
```

**Attributes:**
- `type` - `both` (default), `app`, or `game` - Which post types to display
- `per_page` - Number of apps to display per page (default: 12)

**Features:**
- ✅ Search bar with real-time filtering
- ✅ Category filter dropdown
- ✅ App Type filter (MOD, Premium, Free, Paid, Original)
- ✅ Sort options (Newest, Oldest, A-Z, Highest Rated)
- ✅ Responsive grid layout
- ✅ AJAX-powered results loading

**Example Usage:**
Create a new page called "Browse" and add this shortcode:
```
[appstorepro_browse type="both" per_page="15"]
```

---

### 2. Categories List Shortcode
Display all categories in a beautiful responsive grid with images.

**Shortcode:**
```
[appstorepro_categories columns="3" show_count="yes"]
```

**Attributes:**
- `columns` - Number of columns (default: 3)
- `show_count` - Show app count (yes/no, default: yes)

**Features:**
- ✅ Grid layout with category images
- ✅ Hover effects
- ✅ App count display
- ✅ Responsive design

**Example Usage:**
```
[appstorepro_categories columns="4" show_count="yes"]
```

---

### 3. Home Page Hero Shortcode
Display featured apps in a beautiful carousel.

**Shortcode:**
```
[appstorepro_home_hero]
```

**Features:**
- ✅ Hero title section
- ✅ Featured apps carousel
- ✅ Horizontal scrolling layout
- ✅ Responsive design
- ✅ Shows 6 latest apps

**Example Usage:**
```
[appstorepro_home_hero]
```

---

### 4. Home Page Collections Shortcode
Display app collections/categories as featured sections.

**Shortcode:**
```
[appstorepro_home_collections]
```

**Features:**
- ✅ Category showcase grid
- ✅ Beautiful gradient overlays
- ✅ App count per category
- ✅ Responsive cards

**Example Usage:**
```
[appstorepro_home_collections]
```

---

## Category Image Upload

### How to Upload Category Images

1. Go to **Dashboard → Apps → Categories** (or navigate to **edit-app-category**)
2. Click on a category to edit it
3. Scroll down to find the "Category Image" field
4. Click "Upload Image" button
5. Select an image from your media library or upload a new one
6. The image will be used in category listings and collections
7. Click "Update Category" to save

### Image Recommendations
- **Optimal Size:** 800x600px (flexible, will adjust automatically)
- **Format:** JPG, PNG (transparent PNG recommended)
- **Use:** High-quality app category icons or themed photography

---

## Page Setup Examples

### Example 1: Complete Home Page

Create a page called "Home" and add:

```
<!-- Hero Section -->
[appstorepro_home_hero]

<!-- Collections/Categories Section -->
<h2>Popular Collections</h2>
[appstorepro_home_collections]
```

---

### Example 2: Browse/Apps Page

Create a page called "Browse Apps" and add:

```
<!-- Full Browse Interface -->
[appstorepro_browse type="both" per_page="12"]
```

---

### Example 3: Categories Showcase Page

Create a page called "App Categories" and add:

```
<h1>Explore Categories</h1>
[appstorepro_categories columns="3" show_count="yes"]
```

---

## Filtering & Search Features

### Browse Page Filters

The browse page includes:

1. **Search Box** - Real-time search across app titles
2. **Category Dropdown** - Filter by app categories
3. **App Type Filter** - Filter by:
   - MOD (has MOD info meta)
   - Premium
   - Free
   - Paid
   - Original

4. **Sort Options** - Order results by:
   - Newest First (default)
   - Oldest First
   - A to Z
   - Highest Rated

### Filter Combination

Filters work together - you can combine multiple filters:
- Search for "Instagram" + Filter by "Social" category
- Show only "Premium" MOD apps
- Sort by "Highest Rated"

---

## Customization

### CSS Classes for Styling

**Browse Page:**
- `.appstorepro-browse-wrapper` - Main wrapper
- `.browse-hero` - Hero section
- `.game-card` - Individual app card
- `.game-title` - App title
- `.game-download-btn` - Download button

**Categories:**
- `.appstorepro-categories-wrapper` - Main wrapper
- `.categories-grid` - Grid container
- `.category-card` - Individual category
- `.category-image-wrap` - Image wrapper

**Collections:**
- `.collection-card` - Individual collection card
- `.category-info` - Category info section

### Adding Custom CSS

Add custom CSS to your theme's `style.css` or child theme:

```css
/* Custom browse page styling */
.game-download-btn {
  background: #your-color !important;
}

/* Custom category cards */
.category-card h3 {
  font-size: 20px !important;
}
```

---

## Meta Fields Extracted for Filtering

The system uses these meta fields from the App Details meta box:

- **_app_mod_info** - MOD Information
- **_app_rating** - Rating (for sorting)
- **_app_version** - Version
- **_app_size** - File size
- **_app_category_icon** - Category icon
- **_app_is_mod** - Is MOD checkbox

---

## API/Developer Functions

### Get Category Image

```php
// Get category image URL
$image_url = appstorepro_get_category_image( $term_id, 'medium' );

// Usage
$category_image = appstorepro_get_category_image( 21, 'large' );
echo '<img src="' . esc_url( $category_image ) . '" />';
```

### Render Browse Results Programmatically

```php
// Get browse results with custom args
$results = appstorepro_render_browse_results( [
    'category' => 5,
    'type'     => 'mod',
    'sort'     => 'rating-desc',
    'per_page' => 20,
] );
```

---

## Troubleshooting

### Shortcodes not showing?
1. Go to **Settings → Permalinks** and click "Save" to flush rewrite rules
2. Clear any caching plugins
3. Check that `inc/shortcodes.php` is being loaded in `functions.php`

### Category images not showing?
1. Ensure you have uploaded images to categories
2. Check image file format (JPG, PNG)
3. Verify media library has the images

### AJAX filtering not working?
1. Check browser console for errors
2. Ensure `security` nonce is properly passed
3. Verify admin-ajax.php is accessible

### Styling issues?
1. Check CSS cascade - use `!important` if needed
2. Clear browser cache
3. Verify main.css is being loaded

---

## Performance Tips

1. **Use lazy loading** - Images are already lazy-loaded by default
2. **Limit per_page** - Use `per_page="12"` or `per_page="15"` for better performance
3. **Optimize images** - Compress category images before uploading
4. **Cache shortcodes** - Consider using a caching plugin for frequently accessed pages

---

## Updates & Support

For updates, bug reports, or feature requests, please contact theme support.

**Version:** 1.0.0
**Last Updated:** March 2026
