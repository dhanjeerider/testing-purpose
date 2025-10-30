# 🎬 CineScope

> A modern, feature-rich movie and TV show discovery platform powered by TMDB API

[![Next.js](https://img.shields.io/badge/Next.js-15.5.2-black?style=flat-square&logo=next.js)](https://nextjs.org)
[![React](https://img.shields.io/badge/React-18.3-blue?style=flat-square&logo=react)](https://react.dev)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-blue?style=flat-square&logo=typescript)](https://www.typescriptlang.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.4-38B2AC?style=flat-square&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)

---

## ✨ Features

- **🎯 Trending Content Carousel** - AI-powered suggestions based on your viewing history and ratings
- **🎬 Comprehensive Movie Database** - Browse movies by popularity, ratings, and release dates
- **📺 TV Show Discovery** - Explore TV series with detailed information and episode guides
- **🎭 Genre-Based Browsing** - Filter content by genres with multiple sorting options
- **🔍 Global Search** - Search movies, TV shows, and people with instant results
- **👥 Cast & Crew Information** - Detailed profiles with filmography and career highlights
- **📊 Provider Filtering** - Find where to watch content based on streaming providers
- **🌙 Dark Theme** - Beautiful dark interface optimized for cinematic viewing
- **📱 Fully Responsive** - Seamless experience across mobile, tablet, and desktop
- **⚡ Lightning Fast** - Built with Next.js 15 and optimized for performance

---

## 🎨 Design System

| Element | Color | Usage |
|---------|-------|-------|
| **Primary** | Deep Indigo (#4B0082) | Main brand color, key interactions |
| **Background** | Dark Gray (#222222) | Page backgrounds, cards |
| **Accent** | Electric Purple (#BF00FF) | Highlights, CTAs, hover states |
| **Text** | Light Gray/White | Body text, headings |

**Typography**: Inter (sans-serif) for both headlines and body text

---

## 🚀 Quick Start

### Prerequisites
- Node.js 18.x or higher
- npm or pnpm package manager
- TMDB API key (get one at [themoviedb.org](https://www.themoviedb.org/settings/api))

### Installation

\`\`\`bash
# Clone the repository
git clone <repository-url>
cd cinescope

# Install dependencies
npm install
# or
pnpm install

# Create environment variables
cp .env.example .env.local

# Add your TMDB API key
echo "TMDB_API_KEY=your_api_key_here" >> .env.local
\`\`\`

### Development

\`\`\`bash
# Start development server
npm run dev

# Open http://localhost:9002 in your browser
\`\`\`

### Build & Production

\`\`\`bash
# Build for production
npm run build

# Start production server
npm start

# Type checking
npm run typecheck

# Linting
npm run lint
\`\`\`

---

## 📁 Project Structure

\`\`\`
cinescope/
├── src/
│   ├── app/                    # Next.js app router pages
│   │   ├── page.tsx           # Homepage
│   │   ├── movie/[id]/        # Movie detail pages
│   │   ├── tv/[id]/           # TV show detail pages
│   │   ├── person/[id]/       # Person profile pages
│   │   ├── genres/            # Genre browsing
│   │   ├── discover/          # Discovery pages
│   │   ├── search/            # Search results
│   │   └── play/[type]/[id]/  # Video player
│   ├── components/
│   │   ├── common/            # Reusable components
│   │   ├── layout/            # Layout components
│   │   ├── home/              # Homepage components
│   │   ├── movie/             # Movie-specific components
│   │   └── ui/                # shadcn/ui components
│   ├── lib/
│   │   ├── tmdb.ts           # TMDB API client
│   │   ├── history.ts        # User history management
│   │   ├── likes.ts          # Favorites/likes system
│   │   └── utils.ts          # Utility functions
│   ├── types/
│   │   └── tmdb.ts           # TypeScript types
│   └── hooks/                 # Custom React hooks
├── public/                    # Static assets
├── docs/                      # Documentation
│   └── cloudflare-deployment.md
├── styles/                    # Global styles
├── tailwind.config.ts         # Tailwind configuration
├── next.config.ts            # Next.js configuration
└── wrangler.toml             # Cloudflare Workers config
\`\`\`

---

## 🔧 Core Technologies

| Technology | Purpose |
|-----------|---------|
| **Next.js 15** | React framework with App Router |
| **React 18** | UI library |
| **TypeScript** | Type safety |
| **Tailwind CSS** | Utility-first styling |
| **shadcn/ui** | High-quality UI components |
| **Radix UI** | Accessible component primitives |
| **Lucide React** | Icon library |
| **React Hook Form** | Form state management |
| **Zod** | Schema validation |
| **Embla Carousel** | Carousel component |

---

## 🌐 API Integration

### TMDB API

This project uses the [The Movie Database (TMDB) API](https://www.themoviedb.org/settings/api) for all content data.

**Required Environment Variable:**
\`\`\`
TMDB_API_KEY=your_api_key_here
\`\`\`

**Key Endpoints Used:**
- `/movie/now_playing` - Currently playing movies
- `/movie/upcoming` - Upcoming movies
- `/movie/popular` - Popular movies
- `/movie/top_rated` - Top-rated movies
- `/tv/airing_today` - TV shows airing today
- `/tv/on_the_air` - Currently airing TV shows
- `/tv/popular` - Popular TV shows
- `/tv/top_rated` - Top-rated TV shows
- `/trending/all/week` - Trending content
- `/genre/movie/list` - Movie genres
- `/genre/tv/list` - TV genres
- `/search/multi` - Multi-search (movies, TV, people)

---

## 📦 Deployment

### Cloudflare Pages

CineScope is optimized for deployment on Cloudflare Pages. See [Cloudflare Deployment Guide](./docs/cloudflare-deployment.md) for detailed instructions.

**Quick Deploy:**
\`\`\`bash
npm run pages:build
wrangler pages deploy .vercel/output/static
\`\`\`

### Vercel

\`\`\`bash
# Deploy to Vercel
vercel deploy
\`\`\`

### Docker

\`\`\`bash
# Build Docker image
docker build -t cinescope .

# Run container
docker run -p 3000:3000 -e TMDB_API_KEY=your_key cinescope
\`\`\`

---

## 🎯 Key Features Explained

### Trending Content Carousel
The homepage features an intelligent carousel that displays trending content. The system analyzes your viewing history and ratings to suggest appealing, timely content.

### Genre Discovery
Browse content by genre with multiple sorting options:
- **Popularity** - Most viewed content
- **Top Rated** - Highest-rated content
- **Release Date** - Newest releases first

### Provider Filtering
Find where to watch content based on your preferred streaming services. Filter by available providers in your region.

### Global Search
Search across movies, TV shows, and people with instant results. Each result links to a detailed profile page.

### User Profiles
Create and manage your profile with:
- Viewing history
- Favorite content
- Personal ratings
- Watchlist

---

## 🛠️ Development

### Code Quality

\`\`\`bash
# Run TypeScript type checking
npm run typecheck

# Run ESLint
npm run lint

# Format code (if configured)
npm run format
\`\`\`

### Adding New Features

1. Create components in `src/components/`
2. Add pages in `src/app/`
3. Use existing utilities from `src/lib/`
4. Follow the established TypeScript patterns
5. Test responsiveness across devices

### Component Guidelines

- Use shadcn/ui components for consistency
- Implement proper TypeScript types
- Add proper accessibility attributes (ARIA)
- Ensure mobile-first responsive design
- Use Tailwind CSS for styling

---

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

---

## 🔐 Security

- Environment variables are never exposed to the client
- TMDB API key is kept secure on the server
- No sensitive data is stored in localStorage
- All external links open in new tabs with `rel="noopener noreferrer"`

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📞 Support

For issues, questions, or suggestions, please open an issue on GitHub or contact the development team.

---

## 🙏 Acknowledgments

- [The Movie Database (TMDB)](https://www.themoviedb.org/) for the comprehensive movie and TV data
- [shadcn/ui](https://ui.shadcn.com/) for beautiful, accessible components
- [Tailwind CSS](https://tailwindcss.com/) for utility-first styling
- [Next.js](https://nextjs.org/) for the amazing React framework

---

<div align="center">

**Made with ❤️ by the CineScope Team**

[⬆ back to top](#-cinescope)

</div>
