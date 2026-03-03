# Replit Agent Guide — TMovie

## Overview

TMovie is a movie and TV show streaming aggregator built as a full-stack TypeScript application. It fetches metadata from The Movie Database (TMDB) API and provides users with multiple third-party streaming/download server options for playback. Key features include:

- **Browse & Discover**: Trending movies and TV shows with a cinematic hero slider
- **Search**: Enhanced debounced search with media type filters (All, Movies, TV Shows, Anime) and 8 sort options
- **Watch/Player**: Embeds third-party streaming servers via iframe, with server selection for both streaming and downloading. When subscription is enabled, player shows a subscription gate instead of the video
- **Subscription Gate**: When subscription is enabled in admin settings, the player shows a lock overlay prompting users to subscribe before watching
- **Custom Download Links**: Admin can add quality-specific download URLs (e.g. 720p, 1080p, 4K) per movie/show in the post editor and edit form, displayed as "Quality Downloads" on the player page
- **Watchlist**: Persistent save-for-later functionality stored in PostgreSQL
- **Mobile Navigation**: Bottom navigation bar with 3 icons (Home, Search, Saved) on mobile screens
- **Authentication**: Admin login system with bcrypt password hashing, express-session (default: admin/admin123)
- **Admin Panel**: Tabbed admin dashboard with seven tabs:
  - **Movies & TV Tab**: Import movies from TMDB (search or bulk trending), edit details, bulk select/delete, filter/sort
  - **Add Post Tab**: Manual content addition (without TMDB) plus TMDB import search
  - **Widgets Tab**: Manage homepage widgets (content rows, CTA banners, menu links) with JSON config
  - **Servers Tab**: Manage ALL streaming/download servers (default + custom) stored in database, edit/delete/toggle any server
  - **Pages Tab**: Create/edit/delete custom pages with auto-generated slugs, show in footer toggle
  - **UPI Payments Tab**: Configure UPI ID/QR, view/approve/reject user payment submissions
  - **Settings Tab**: Razorpay payment key configuration, UPI settings, subscription settings, Site/SEO settings (title, description, Telegram link, Google Analytics ID, Search Console meta), admin password change
  - **Site & SEO Settings sub-sections**: Basic Identity (site name, tagline, favicon URL, logo URL, footer copyright text), SEO/OG (meta keywords, OG image URL for social sharing), Analytics & Verification (GA4 ID, Search Console meta), Social & Community (Telegram, Twitter/X, Instagram, YouTube, Facebook)
- **Comments & Reactions**: Player page shows emoji reactions (❤️ 😂 😮 😢 😡 👍) and a comments section. Reactions stored per (tmdbId, mediaType, emoji) with count increments. Comments stored with userName and content. User's own reactions tracked in localStorage. Admin can delete comments.
- **PWA Support**: Manifest at `/manifest.json`, service worker at `/sw.js` for offline caching. PWA icons at `/icons/icon-*.png` (72–512px). index.html includes manifest link, Apple touch icons, and SW registration script.
- **Dynamic SEO & Analytics**: Shell fetches public settings on mount. Injects Google Analytics GA4 script when `google_analytics_id` configured. Injects Google Search Console meta tag when `search_console_meta` set. Updates OG/description meta tags from `site_title`/`site_description`. All configured from admin Settings tab.
- **Razorpay Integration**: Payment order creation and verification via admin-configured API keys stored in site_settings table
- **UPI Payment System**: Manual UPI payment flow - admin sets UPI ID/QR, users submit transaction details, admin approves/rejects
- **Custom Pages**: Admin can create pages (e.g. About, Terms, Privacy) that auto-appear in footer
- **Scroll to Top**: All page navigations scroll to top automatically
- **Mobile Search**: Search icon in mobile header links to /search page
- **Profile Page**: User info, watchlist stats, subscription card (when Razorpay configured), login/logout
- **Pricing Page**: Monthly/yearly toggle with a premium plan card at /pricing
- **Telegram CTA**: Join Telegram banner shown on home page above content rows
- **Home Genre/Language Filters**: "Browse Library" section with genre dropdown and language filter for DB content
- **Watchlist on Home**: Watchlist row displayed on home page when user has saved items
- **Related Posts on Player**: "More Like This" section at bottom of player page from TMDB similar content
- **Mobile Menu Panel**: 67% width slide-in panel from right with backdrop overlay, profile card at top
- **No Backdrop Blur**: Removed all backdrop-blur classes to prevent phone performance issues
- **Auto-hide Header + Bottom Nav**: Header slides up (out of viewport) on scroll-down, slides back in on scroll-up. Mobile bottom nav mirrors this — slides down on scroll-down, up on scroll-up. Both use 320ms ease transition for a smooth feel
- **Optimized Image Quality**: TMDB poster images use `w342` (medium quality) by default instead of `w500`. Backdrops use `w1280` instead of `original`. Faster load times on mobile
- **Pre-roll Ad System**: Admin Settings > Video Ad Settings — configure enable/disable, type (image/VAST URL), ad URL, skip seconds. Ad overlay appears over video player with countdown timer and Skip Ad button
- **Admin Email + Forgot Password**: Admin email stored in settings. Login page shows "Forgot Password?" — enters email, if matches stored email, generates new password shown on screen to copy
- **Server Edit Modal**: Server add/edit form now opens as proper centered modal overlay (fixed z-50) instead of inline section. Backdrop click closes it
- **Settings New Sections**: Admin Email card + Video Ad Settings card added to Settings tab
- **MovieCard Neumorphic Overhaul**: Cards use `neu-raised` base shadow (no border), transition to inset shadow on hover via `neu-hover-inset` class; play button is centered and large (w-12 h-12) visible only on hover
- **neu-hover-inset CSS utility**: New class in index.css — transitions from raised outer shadow to inset shadow on hover, with 250ms ease + slight scale(0.985) squish
- **Player Watch Providers**: Replaced production companies section with "Watch On" block showing streaming provider logos from TMDB (flatrate + free, IN region)
- **ProviderPage top padding**: Added `pt-20 md:pt-24` to clear the fixed header
- **Neumorphic Dark UI**: Full neumorphic design system — base color `#1a1d2e` (dark navy-slate), dual shadows (`#0f1120` dark / `#272c44` light) on `.neu-raised`, `.neu-flat`, `.neu-pressed`, `.neu-inset`. Header/bottom-nav/footer all use neumorphic gradient backgrounds. Primary stays neon cyan, accent stays neon pink
- **Widget System**: Homepage content is customizable via widgets (content_row, cta_banner, menu_links types). Falls back to TMDB trending when no widgets configured
- **DB-First Architecture**: Movie details cached in database with JSON columns for genres, cast, videos, seasons. Player page serves from DB first, falls back to TMDB API
- **Server Infrastructure**: Default servers seeded into custom_servers table on first startup with isDefault flag. All servers editable from admin panel

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend (React SPA)
- **Location**: `client/src/`
- **Framework**: React 18 with TypeScript, bundled by Vite
- **Routing**: `wouter` (lightweight alternative to React Router)
- **State/Data Fetching**: `@tanstack/react-query` for server state management
- **Styling**: Tailwind CSS with CSS custom properties for theming (cinematic dark theme with neon cyan primary and neon pink accent)
- **UI Components**: shadcn/ui (new-york style) in `client/src/components/ui/`, built on Radix UI primitives
- **Animations**: `framer-motion` for page transitions, hero slider, and card animations
- **Icons**: `lucide-react`
- **Path aliases**: `@/` maps to `client/src/`, `@shared/` maps to `shared/`

### Pages
- `Home` — Hero slider + content rows (trending movies, TV, top picks)
- `Search` — Debounced search with grid results
- `Player` — Details view + embedded iframe player with server/season/episode selection
- `Watchlist` — Saved items grid with remove functionality
- `Admin` — Server management dashboard with stats, default server listing, and custom server CRUD
- `Profile` — User info, watchlist stats, subscription card, login/logout

### Backend (Express API)
- **Location**: `server/`
- **Framework**: Express 5 on Node.js, running via `tsx`
- **API Pattern**: All API routes under `/api/` prefix, defined in `server/routes.ts`
- **Route Contracts**: Shared route definitions with Zod schemas in `shared/routes.ts` for type-safe API contracts
- **TMDB Proxy**: Server proxies all TMDB API calls to keep the API key secret. Endpoints include trending, search, details, and season/episode info
- **Dev Server**: Vite dev server middleware integrated via `server/vite.ts` with HMR
- **Production**: Static files served from `dist/public/` with SPA fallback

### Database (PostgreSQL + Drizzle ORM)
- **ORM**: Drizzle ORM with `drizzle-zod` for schema-to-validation integration
- **Schema Location**: `shared/schema.ts`
- **Tables**:
  - `movies` — Imported movie/TV content from TMDB (tmdbId, title, overview, posterPath, backdropPath, mediaType, releaseDate, voteAverage, genreIds, isFeatured, isActive, runtime, genresJson, castJson, videosJson, seasonsJson, productionCompaniesJson, downloadLinksJson)
  - `watchlist` — Stores saved movies/shows (tmdbId, type, title, posterPath) with a unique index on (tmdbId, type)
  - `custom_servers` — All streaming/download servers (name, type, url, urlTv, hasAds, has4K, isDownload, isActive, isDefault, sortOrder, icon, description). Default servers seeded on first startup
  - `widgets` — Homepage widgets (type: content_row/cta_banner/menu_links, title, config JSON, sortOrder, isActive)
  - `pages` — Custom pages (title, slug, content, isActive, showInFooter, sortOrder). Links auto-appear in footer
  - `upi_payments` — UPI payment submissions (userName, userEmail, transactionId, amount, status: pending/approved/rejected, adminNote)
  - `users` — Admin users with bcrypt-hashed passwords (username, password, role). Default admin auto-created on first run
  - `site_settings` — Key-value settings store for Razorpay keys, subscription config, Telegram link, Google Analytics ID, Search Console meta, site title/description
  - `comments` — User comments per (tmdbId, mediaType) with userName, content, createdAt
  - `reactions` — Emoji reaction counts per (tmdbId, mediaType, emoji) with unique index; count increments on each POST
- **Migrations**: Drizzle Kit with `drizzle-kit push` command (no migration files checked in by default)
- **Connection**: `pg` Pool via `DATABASE_URL` environment variable
- **Storage Layer**: `server/storage.ts` implements `IStorage` interface with `DatabaseStorage` class

### Shared Code (`shared/`)
- `schema.ts` — Database schema + Zod insert schemas + TypeScript types
- `routes.ts` — API route contracts (paths, methods, input/output schemas)
- `servers.ts` — Hardcoded streaming and download server definitions with URL builder function

### Build System
- **Dev**: `tsx server/index.ts` with Vite middleware for HMR
- **Build**: Custom `script/build.ts` — Vite builds client to `dist/public/`, esbuild bundles server to `dist/index.cjs`
- **Production**: `node dist/index.cjs`

## External Dependencies

### Required Environment Variables
- `DATABASE_URL` — PostgreSQL connection string (required, app crashes without it)
- `TMDB_API_KEY` — The Movie Database API key (required for all content fetching)

### Third-Party Services
- **TMDB API** (`api.themoviedb.org/3`) — Movie/TV metadata, images, search, trending. All requests proxied through the Express server
- **TMDB Image CDN** (`image.tmdb.org/t/p/`) — Poster and backdrop images, accessed directly from the client
- **Third-party streaming servers** — Multiple embed URLs defined in `shared/servers.ts` (VidSrcVIP, Vidify, VidJoy, AutoEmbed, MoviesAPI, EmbedSU, VidLink, etc.) loaded in iframes on the player page
- **Third-party download servers** — VidSrc Download, BunnyDDL, and others for download functionality

### Key NPM Dependencies
- `express` v5 — HTTP server
- `drizzle-orm` + `drizzle-kit` — Database ORM and migration tool
- `pg` — PostgreSQL client
- `connect-pg-simple` — Session store (available but auth not yet implemented)
- `zod` + `drizzle-zod` — Schema validation
- `@tanstack/react-query` — Client-side data fetching
- `framer-motion` — Animations
- `wouter` — Client-side routing
- `vite` + `@vitejs/plugin-react` — Frontend bundler
- `esbuild` — Server bundler for production
- Full shadcn/ui component library (Radix UI primitives)