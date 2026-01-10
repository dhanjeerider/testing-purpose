# WordPress AJAX News Theme - Installation Guide

## 🚀 Quick Start

### Step 1: Upload Theme to WordPress

**Option A: Via WordPress Admin**
1. Zip the `wp-theme-ajax-news` folder
2. Go to: WordPress Admin → Appearance → Themes → Add New
3. Click "Upload Theme"
4. Choose the zip file
5. Click "Install Now"
6. Click "Activate"

**Option B: Via FTP/File Manager**
1. Upload `wp-theme-ajax-news` folder to: `wp-content/themes/`
2. Go to: WordPress Admin → Appearance → Themes
3. Find "AJAX News Theme" and click "Activate"

### Step 2: Initial Setup

**2.1 Create Navigation Menu**
```
1. Go to: Appearance → Menus
2. Create a new menu (e.g., "Main Menu")
3. Add pages/categories to menu
4. Check "Primary Menu" location
5. Save Menu
```

**2.2 Setup Widgets**
```
1. Go to: Appearance → Widgets
2. Drag widgets to "Sidebar" area
3. Recommended widgets:
   - Search
   - Recent Posts
   - Categories
   - Tag Cloud
```

**2.3 Create Demo Content (Optional)**
```
1. Create some posts with featured images
2. Assign categories
3. Add tags
```

### Step 3: Test Features

**3.1 Test AJAX Navigation**
- Click on any internal link
- Page should change without reload
- URL should update
- Browser back button should work

**3.2 Test Dark Mode**
- Click moon icon (top-right)
- Theme should switch to dark
- Refresh page - setting should persist

**3.3 Test Reactions**
- Open any post
- Click Like/Dislike/Save buttons
- Counts should update
- State should persist

## 📝 Using Shortcodes

### In Posts/Pages Editor

**Social Share:**
```
[social_share]
```

**Reactions:**
```
[reactions]
```

**YouTube Video:**
```
[video_player url="https://www.youtube.com/watch?v=VIDEO_ID" type="youtube"]
```

**Vimeo Video:**
```
[video_player url="https://vimeo.com/VIDEO_ID" type="vimeo"]
```

**MP4 Video:**
```
[video_player url="https://example.com/video.mp4" type="mp4" poster="https://example.com/poster.jpg"]
```

**Newsletter:**
```
[newsletter title="Subscribe to Newsletter" placeholder="Enter email"]
```

**Related Posts:**
```
[related_posts count="3"]
```

### In Theme Files

```php
<?php echo do_shortcode('[social_share]'); ?>
<?php echo do_shortcode('[reactions post_id="' . get_the_ID() . '"]'); ?>
```

## 🎨 Customization

### Change Colors

Edit `style.css` (lines 8-18):
```css
:root {
    --primary-color: #007bff;    /* Main color */
    --secondary-color: #6c757d;  /* Secondary */
    --success-color: #28a745;    /* Success */
    --danger-color: #dc3545;     /* Danger */
}
```

### Add Custom CSS

**Method 1: Theme Customizer**
```
1. Go to: Appearance → Customize → Additional CSS
2. Add your CSS
```

**Method 2: Edit custom.css**
```
Edit: assets/css/custom.css
```

### Modify Excerpt Length

Edit `functions.php`:
```php
function ajax_news_excerpt_length($length) {
    return 30; // Change this number
}
```

## 🔧 Advanced Configuration

### Enable/Disable Features

**Disable AJAX Navigation:**
Remove this line from `functions.php`:
```php
wp_enqueue_script('ajax-navigation', get_template_directory_uri() . '/assets/js/ajax-navigation.js', array('jquery'), '1.0.0', true);
```

**Change Video Player:**
Replace Plyr with another player in `functions.php`

### Add Custom Post Meta

Example in `functions.php`:
```php
function add_custom_meta_boxes() {
    add_meta_box('custom_meta', 'Custom Info', 'render_custom_meta', 'post');
}
add_action('add_meta_boxes', 'add_custom_meta_boxes');
```

## 📱 Responsive Testing

Test on:
- Desktop (1920px, 1366px, 1024px)
- Tablet (768px)
- Mobile (375px, 414px)

## ⚡ Performance Optimization

### 1. Use Caching Plugin
- WP Super Cache
- W3 Total Cache
- WP Rocket

### 2. Image Optimization
- Install "Smush" or "ShortPixel"
- Use WebP format
- Lazy load images

### 3. Minify Assets
```php
// Add to functions.php
function enqueue_minified_assets() {
    wp_enqueue_style('style-min', get_template_directory_uri() . '/style.min.css');
}
```

## 🐛 Troubleshooting

### AJAX Navigation Not Working
**Check:**
1. jQuery is loaded
2. No JavaScript errors in console (F12)
3. `ajaxNewsTheme` object is defined
4. WordPress AJAX URL is correct

**Fix:**
```javascript
console.log(ajaxNewsTheme); // Should show object
```

### Reactions Not Saving
**Check:**
1. AJAX nonce is valid
2. Database permissions
3. User is logged in (for user meta)
4. Cookies enabled (for guests)

**Debug:**
```php
// Add to inc/reactions.php
error_log('Reaction: ' . $reaction . ' Post: ' . $post_id);
```

### Dark Mode Not Persisting
**Check:**
1. LocalStorage is enabled
2. No browser extensions blocking
3. Console for errors

**Test:**
```javascript
console.log(localStorage.getItem('darkMode'));
```

### Video Player Not Loading
**Check:**
1. Plyr CDN is accessible
2. Video URL is valid
3. Correct video type specified

## 🔐 Security

### Recommended Plugins
- Wordfence Security
- iThemes Security
- All In One WP Security

### Best Practices
1. Keep WordPress updated
2. Use strong passwords
3. Limit login attempts
4. Regular backups
5. SSL certificate

## 📊 SEO Optimization

### Recommended Plugins
- Yoast SEO
- Rank Math
- All in One SEO

### Theme Features for SEO
- Clean HTML structure
- Semantic markup
- Fast load times
- Mobile responsive
- Schema markup ready

## 🎯 Next Steps

1. **Add Logo:** Appearance → Customize → Site Identity
2. **Set Homepage:** Settings → Reading → Homepage displays
3. **Configure Permalinks:** Settings → Permalinks → Post name
4. **Install Essential Plugins:**
   - Contact Form 7
   - Akismet (spam protection)
   - Google Analytics
5. **Create Important Pages:**
   - About
   - Contact
   - Privacy Policy
   - Terms of Service

## 💡 Tips

- Use featured images for all posts
- Write engaging titles
- Add categories and tags
- Enable comments moderation
- Regular content updates
- Monitor site analytics

## 📞 Support

Need help? Common resources:
- WordPress Codex: https://codex.wordpress.org
- WordPress Forums: https://wordpress.org/support
- Stack Overflow: Tag "wordpress"

---

**Theme Version:** 1.0.0  
**Last Updated:** January 2026  
**Minimum Requirements:** WordPress 5.0+, PHP 7.4+
