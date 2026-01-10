# AJAX News WordPress Theme

Complete WordPress theme with AJAX navigation, dark mode, social sharing, reactions, and video player support.

## Features

### 1. **AJAX Page Navigation** ✅
- Pages switch without reload
- Smooth transitions
- Browser back/forward button support
- URL updates with `history.pushState()`

### 2. **Dark Mode** 🌙
- Toggle button in top-right corner
- Persistent setting (localStorage)
- Smooth color transitions
- Custom dark theme colors

### 3. **Bootstrap 5 Integration** 📱
- Fully responsive design
- Modern card layouts
- Grid system
- Mobile-first approach

### 4. **Social Sharing** 🔗
**Shortcode:** `[social_share]`

Platforms:
- Facebook
- Twitter
- WhatsApp
- LinkedIn
- Telegram

### 5. **Reaction System** 👍👎💾
**Shortcode:** `[reactions post_id="123"]`

Features:
- Like button
- Dislike button
- Save/Bookmark button
- Real-time count updates
- Meta value storage in database
- Works for logged-in and guest users (cookies)

### 6. **Video Player** 🎥
**Shortcode:** `[video_player url="..." type="youtube"]`

Supports:
- YouTube videos
- Vimeo videos
- HTML5 MP4 videos
- Plyr.io player with custom controls
- Responsive 16:9 aspect ratio

### 7. **Additional Shortcodes**

#### Newsletter
```php
[newsletter title="Subscribe" placeholder="Your email"]
```

#### Related Posts
```php
[related_posts count="3" post_id="123"]
```

#### Saved Posts (User's bookmarked posts)
```php
[saved_posts]
```

## Installation

1. **Upload Theme**
   ```bash
   # Copy wp-theme-ajax-news folder to:
   wp-content/themes/wp-theme-ajax-news
   ```

2. **Activate Theme**
   - Go to WordPress Admin → Appearance → Themes
   - Activate "AJAX News Theme"

3. **Setup Menus**
   - Go to Appearance → Menus
   - Create menu and assign to "Primary Menu" location

4. **Configure Widgets**
   - Go to Appearance → Widgets
   - Add widgets to "Sidebar" and "Footer 1" areas

## Usage Examples

### 1. Social Share in Post
```php
<?php echo do_shortcode('[social_share]'); ?>
```

### 2. Reactions with Custom Post ID
```php
<?php echo do_shortcode('[reactions post_id="' . get_the_ID() . '"]'); ?>
```

### 3. YouTube Video
```php
[video_player url="https://www.youtube.com/watch?v=dQw4w9WgXcQ" type="youtube"]
```

### 4. Vimeo Video
```php
[video_player url="https://vimeo.com/123456789" type="vimeo"]
```

### 5. HTML5 Video with Poster
```php
[video_player url="https://example.com/video.mp4" type="mp4" poster="https://example.com/poster.jpg"]
```

### 6. Newsletter Box
```php
[newsletter title="Get Latest Updates" placeholder="Enter your email address"]
```

### 7. Related Articles
```php
[related_posts count="4"]
```

## File Structure

```
wp-theme-ajax-news/
├── assets/
│   ├── css/
│   │   └── custom.css
│   └── js/
│       ├── ajax-navigation.js
│       ├── dark-mode.js
│       └── reactions.js
├── inc/
│   ├── bootstrap-navwalker.php
│   ├── reactions.php
│   └── shortcodes.php
├── footer.php
├── functions.php
├── header.php
├── index.php
├── sidebar.php
├── single.php
├── style.css
└── README.md
```

## Customization

### Change Primary Color
Edit `style.css`:
```css
:root {
    --primary-color: #007bff; /* Change this */
}
```

### Add Custom Shortcode
Edit `inc/shortcodes.php`:
```php
function my_custom_shortcode($atts) {
    // Your code
    return $output;
}
add_shortcode('my_shortcode', 'my_custom_shortcode');
```

### Modify Dark Mode Colors
Edit `style.css`:
```css
body.dark-mode {
    --bg-color: #1a1a1a;
    --text-color: #e0e0e0;
    --card-bg: #2d2d2d;
    --border-color: #404040;
}
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Dependencies

- WordPress 5.0+
- PHP 7.4+
- Bootstrap 5.3.0 (CDN)
- Font Awesome 6.4.0 (CDN)
- Plyr.io 3.7.8 (CDN)

## Credits

- Bootstrap: https://getbootstrap.com
- Font Awesome: https://fontawesome.com
- Plyr: https://plyr.io

## Support

For issues and questions, contact: your-email@example.com

## License

GPL v2 or later
