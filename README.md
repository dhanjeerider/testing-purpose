8# TMovie PHP (Beginner Friendly)

A PHP + SQLite clone based on your requested streaming-site features, with admin panel, TMDB import, watchlist, player servers, comments, reactions, pages, widgets, UPI flow, subscription gate, PWA basics, and SEO settings.

## 1) Requirements

- PHP 8.1+
- `pdo_sqlite` extension enabled
- Internet connection (for TMDB API and poster/backdrop URLs)

## 2) Quick Start (3 commands)

```bash
cd /workspaces/testing-purpose
php scripts/init_db.php
php -S 0.0.0.0:8000
```

Open: `http://localhost:8000`

## 3) Default Admin Login

- URL: `http://localhost:8000/login.php`
- Username: `admin`
- Password: `admin123`

You can reset admin password from **Forgot Password** using the admin email set in Settings.

## 4) Add TMDB API Key

Set env before running server:

```bash
export TMDB_API_KEY="your_tmdb_key_here"
php -S 0.0.0.0:8000
```

Without TMDB key, app still works with manually added posts in Admin.

## 5) Feature Map

- **Home**: hero + Telegram CTA + widgets + library rows
- **Search**: query + media type + sort
- **Player**: server switcher, TV season/episode, subscription lock, pre-roll ad, quality downloads, comments, reactions, related posts
- **Watchlist**: save/remove
- **Profile**: stats + subscription card
- **Pricing**: monthly/yearly + UPI submission form
- **Admin Tabs**:
  - Movies & TV (TMDB import + delete + comment moderation)
  - Add Post (manual add with download links JSON)
  - Widgets
  - Servers
  - Pages
  - UPI Payments (approve/reject/pending)
  - Settings (site, SEO, analytics, payments, subscription, video ad, admin email)
- **PWA Basics**: `manifest.json` + `sw.js`

## 6) Database

- Schema file: `database/schema.sql`
- DB file: `database/app.db` (auto created)
- Init script seeds:
  - default admin user
  - default streaming/download servers
  - default site settings

## 7) Beginner Notes

- Add one movie manually from **Admin → Add Post** if TMDB key is empty.
- Use server templates with placeholders:
  - Movie: `{id}`
  - TV: `{id}`, `{season}`, `{episode}`
- For quality downloads JSON, sample:

```json
[
  {"quality": "720p", "url": "https://example.com/720"},
  {"quality": "1080p", "url": "https://example.com/1080"}
]
```
