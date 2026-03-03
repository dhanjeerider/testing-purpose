import type { Express } from "express";
import type { Server } from "http";
import { storage } from "./storage";
import { api } from "@shared/routes";
import { z } from "zod";
import bcrypt from "bcryptjs";
import session from "express-session";
import { DEFAULT_STREAMING_SERVERS, DEFAULT_DOWNLOAD_SERVERS } from "@shared/servers";

const TMDB_BASE_URL = "https://api.themoviedb.org/3";

declare module "express-session" {
  interface SessionData {
    userId?: number;
    role?: string;
  }
}

async function fetchTmdb(endpoint: string, params: Record<string, string> = {}) {
  const TMDB_API_KEY = process.env.TMDB_API_KEY;
  if (!TMDB_API_KEY) {
    throw new Error("TMDB_API_KEY environment variable is missing");
  }
  
  const url = new URL(`${TMDB_BASE_URL}${endpoint}`);
  url.searchParams.append("api_key", TMDB_API_KEY);
  Object.entries(params).forEach(([key, value]) => {
    if (value) url.searchParams.append(key, value);
  });

  const response = await fetch(url.toString());
  if (!response.ok) {
    throw new Error(`TMDB API error: ${response.statusText}`);
  }
  return await response.json();
}

async function ensureDefaultAdmin() {
  const existing = await storage.getUserByUsername('admin');
  if (!existing) {
    const hashed = await bcrypt.hash('admin123', 10);
    await storage.createUser({ username: 'admin', password: hashed, role: 'admin' });
    console.log('Default admin created: admin / admin123');
  }
}

async function seedDefaultServers() {
  const existing = await storage.getCustomServers();
  if (existing.length > 0) return;

  let order = 0;
  for (const srv of DEFAULT_STREAMING_SERVERS) {
    await storage.addCustomServer({
      name: srv.name,
      type: srv.type,
      url: srv.url,
      urlTv: srv.url_tv,
      hasAds: srv.hasAds || false,
      has4K: srv.has4K || false,
      isDownload: false,
      isActive: true,
      isDefault: true,
      icon: srv.icon || null,
      description: srv.description || null,
      sortOrder: order++,
    });
  }
  for (const srv of DEFAULT_DOWNLOAD_SERVERS) {
    await storage.addCustomServer({
      name: srv.name,
      type: srv.type,
      url: srv.url,
      urlTv: srv.url_tv,
      hasAds: srv.hasAds || false,
      has4K: srv.has4K || false,
      isDownload: true,
      isActive: true,
      isDefault: true,
      icon: srv.icon || null,
      description: srv.description || null,
      sortOrder: order++,
    });
  }
  console.log('Default servers seeded');
}

function requireAdmin(req: any, res: any, next: any) {
  if (!req.session?.userId || req.session?.role !== 'admin') {
    return res.status(401).json({ message: 'Admin access required' });
  }
  next();
}

export async function registerRoutes(
  httpServer: Server,
  app: Express
): Promise<Server> {

  const sessionSecret = process.env.SESSION_SECRET || 'tmovie-dev-session-secret';

  app.set('trust proxy', 1);

  app.use(session({
    secret: sessionSecret,
    resave: false,
    saveUninitialized: false,
    name: 'tmovie_session',
    proxy: true,
    cookie: {
      secure: 'auto' as any,
      httpOnly: true,
      maxAge: 7 * 24 * 60 * 60 * 1000,
      sameSite: 'lax',
    },
  }));

  await ensureDefaultAdmin();
  await seedDefaultServers();

  // Auth Routes
  const loginSchema = z.object({
    username: z.string().min(1),
    password: z.string().min(1),
  });

  app.post('/api/auth/login', async (req, res) => {
    try {
      const { username, password } = loginSchema.parse(req.body);
      const user = await storage.getUserByUsername(username);
      if (!user) {
        return res.status(401).json({ message: 'Invalid credentials' });
      }
      const valid = await bcrypt.compare(password, user.password);
      if (!valid) {
        return res.status(401).json({ message: 'Invalid credentials' });
      }
      req.session.userId = user.id;
      req.session.role = user.role;
      res.json({ id: user.id, username: user.username, role: user.role });
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: 'Username and password required' });
      }
      res.status(500).json({ message: 'Internal server error' });
    }
  });

  app.post('/api/auth/logout', (req, res) => {
    req.session.destroy(() => {
      res.json({ message: 'Logged out' });
    });
  });

  app.get('/api/auth/me', async (req, res) => {
    if (!req.session.userId) {
      return res.json(null);
    }
    const user = await storage.getUserById(req.session.userId);
    if (!user) {
      return res.json(null);
    }
    res.json({ id: user.id, username: user.username, role: user.role });
  });

  app.post('/api/auth/forgot-password', async (req, res) => {
    try {
      const schema = z.object({ email: z.string().email() });
      const { email } = schema.parse(req.body);
      const storedEmail = await storage.getSetting('admin_email');
      if (!storedEmail || storedEmail.trim().toLowerCase() !== email.trim().toLowerCase()) {
        return res.status(400).json({ message: 'Email not matched. Please contact hosting support.' });
      }
      const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
      let newPassword = '';
      for (let i = 0; i < 10; i++) newPassword += chars[Math.floor(Math.random() * chars.length)];
      const hashed = await bcrypt.hash(newPassword, 10);
      const allUsers = await storage.getUserById(1);
      if (allUsers) await storage.updateUser(allUsers.id, { password: hashed });
      res.json({ newPassword });
    } catch (err) {
      if (err instanceof z.ZodError) return res.status(400).json({ message: err.errors[0].message });
      res.status(500).json({ message: 'Server error' });
    }
  });

  app.post('/api/auth/change-password', async (req, res) => {
    try {
      if (!req.session.userId) {
        return res.status(401).json({ message: 'Not authenticated' });
      }
      const schema = z.object({
        currentPassword: z.string().min(1),
        newPassword: z.string().min(4),
      });
      const { currentPassword, newPassword } = schema.parse(req.body);
      const user = await storage.getUserById(req.session.userId);
      if (!user) return res.status(404).json({ message: 'User not found' });

      const valid = await bcrypt.compare(currentPassword, user.password);
      if (!valid) return res.status(400).json({ message: 'Current password is incorrect' });

      const hashed = await bcrypt.hash(newPassword, 10);
      await storage.updateUser(user.id, { password: hashed });
      res.json({ message: 'Password changed successfully' });
    } catch (err) {
      if (err instanceof z.ZodError) return res.status(400).json({ message: err.errors[0].message });
      res.status(500).json({ message: 'Internal server error' });
    }
  });

  // Watchlist Routes
  app.get(api.watchlist.list.path, async (req, res) => {
    try {
      const items = await storage.getWatchlist();
      res.json(items);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post(api.watchlist.add.path, async (req, res) => {
    try {
      const input = api.watchlist.add.input.parse(req.body);
      const existing = await storage.getWatchlistItem(input.tmdbId, input.type);
      if (existing) {
        return res.status(200).json(existing);
      }
      const item = await storage.addWatchlist(input);
      res.status(201).json(item);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.delete(api.watchlist.remove.path, async (req, res) => {
    try {
      await storage.removeWatchlist(Number(req.params.id));
      res.status(204).end();
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // TMDB Proxy Routes
  app.get(api.tmdb.trending.path, async (req, res) => {
    try {
      const type = req.query.type as string || 'all';
      const data = await fetchTmdb(`/trending/${type}/week`);
      res.json(data);
    } catch (err: any) {
      res.status(500).json({ message: err.message });
    }
  });

  app.get(api.tmdb.search.path, async (req, res) => {
    try {
      const query = req.query.q as string;
      if (!query) return res.json({ results: [] });
      const data = await fetchTmdb('/search/multi', { query });
      res.json(data);
    } catch (err: any) {
      res.status(500).json({ message: err.message });
    }
  });

  app.get(api.tmdb.details.path, async (req, res) => {
    try {
      const { type, id } = req.params;
      const data = await fetchTmdb(`/${type}/${id}`, { append_to_response: 'videos,credits,similar,reviews' });
      res.json(data);
    } catch (err: any) {
      res.status(500).json({ message: err.message });
    }
  });

  app.get(api.tmdb.season.path, async (req, res) => {
    try {
      const { id, seasonNumber } = req.params;
      const data = await fetchTmdb(`/tv/${id}/season/${seasonNumber}`);
      res.json(data);
    } catch (err: any) {
      res.status(500).json({ message: err.message });
    }
  });

  // OTT providers list (for strip on home page)
  app.get('/api/ott-providers', async (req, res) => {
    try {
      const data = await fetchTmdb(`/watch/providers/movie?watch_region=IN`);
      res.json(data);
    } catch (err: any) {
      res.status(500).json({ message: err.message });
    }
  });

  // Discover movies/tv by watch provider
  app.get('/api/discover/:type/provider/:providerId', async (req, res) => {
    try {
      const { type, providerId } = req.params;
      const page = req.query.page || 1;
      if (type !== 'movie' && type !== 'tv') return res.status(400).json({ message: 'Invalid type' });
      const data = await fetchTmdb(`/discover/${type}?with_watch_providers=${providerId}&watch_region=IN&sort_by=popularity.desc&page=${page}`);
      res.json(data);
    } catch (err: any) {
      res.status(500).json({ message: err.message });
    }
  });

  // TMDB Watch Providers proxy
  app.get('/api/tmdb/:type/:id/providers', async (req, res) => {
    try {
      const { type, id } = req.params;
      if (type !== 'movie' && type !== 'tv') return res.status(400).json({ message: 'Invalid type' });
      const data = await fetchTmdb(`/${type}/${id}/watch/providers`);
      res.json(data);
    } catch (err: any) {
      res.status(500).json({ message: err.message });
    }
  });

  // Public API: Movie details from DB (with TMDB fallback + cache)
  app.get('/api/movies/:tmdbId/details', async (req, res) => {
    try {
      const tmdbId = Number(req.params.tmdbId);
      if (isNaN(tmdbId)) return res.status(400).json({ message: 'Invalid ID' });

      const movie = await storage.getMovieByTmdbId(tmdbId);
      if (movie && movie.genresJson) {
        const details: any = {
          id: movie.tmdbId,
          title: movie.title,
          name: movie.mediaType === 'tv' ? movie.title : undefined,
          original_title: movie.originalTitle,
          overview: movie.overview,
          poster_path: movie.posterPath,
          backdrop_path: movie.backdropPath,
          release_date: movie.mediaType === 'movie' ? movie.releaseDate : undefined,
          first_air_date: movie.mediaType === 'tv' ? movie.releaseDate : undefined,
          vote_average: movie.voteAverage || 0,
          vote_count: movie.voteCount || 0,
          popularity: movie.popularity || 0,
          runtime: movie.runtime,
          original_language: movie.originalLanguage,
          adult: movie.adult,
          media_type: movie.mediaType,
          genres: movie.genresJson ? JSON.parse(movie.genresJson) : [],
          credits: movie.castJson ? { cast: JSON.parse(movie.castJson) } : { cast: [] },
          videos: movie.videosJson ? { results: JSON.parse(movie.videosJson) } : { results: [] },
          seasons: movie.seasonsJson ? JSON.parse(movie.seasonsJson) : undefined,
          production_companies: movie.productionCompaniesJson ? JSON.parse(movie.productionCompaniesJson) : [],
          downloadLinksJson: movie.downloadLinksJson || null,
        };
        return res.json(details);
      }

      const type = movie?.mediaType || req.query.type as string || 'movie';
      const data = await fetchTmdb(`/${type}/${tmdbId}`, { append_to_response: 'videos,credits,similar' });

      if (movie) {
        await storage.updateMovie(movie.id, {
          runtime: data.runtime || null,
          genresJson: data.genres ? JSON.stringify(data.genres) : null,
          castJson: data.credits?.cast ? JSON.stringify(data.credits.cast.slice(0, 15)) : null,
          videosJson: data.videos?.results ? JSON.stringify(data.videos.results.slice(0, 5)) : null,
          seasonsJson: data.seasons ? JSON.stringify(data.seasons) : null,
          productionCompaniesJson: data.production_companies ? JSON.stringify(data.production_companies.slice(0, 5)) : null,
        });
      }

      res.json(data);
    } catch (err: any) {
      res.status(500).json({ message: err.message || 'Failed to fetch details' });
    }
  });

  // Public API: Get active servers
  app.get('/api/servers', async (req, res) => {
    try {
      const servers = await storage.getActiveServers();
      res.json(servers);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Public API: Get active movies by type
  app.get('/api/movies', async (req, res) => {
    try {
      const type = req.query.type as string;
      const featured = req.query.featured as string;
      const q = req.query.q as string;
      const page = req.query.page ? parseInt(req.query.page as string) : null;
      const limit = req.query.limit ? parseInt(req.query.limit as string) : 24;

      if (q) {
        const results = await storage.searchMovies(q);
        return res.json(results);
      }
      if (featured === 'true') {
        const results = await storage.getFeaturedMovies();
        return res.json(results);
      }
      if (page !== null) {
        const result = await storage.getActiveMoviesPaginated(page, limit, type);
        return res.json({ movies: result.movies, total: result.total, totalPages: Math.ceil(result.total / limit), page, limit });
      }
      if (type && type !== 'all') {
        const results = await storage.getMoviesByMediaType(type);
        return res.json(results);
      }
      const results = await storage.getActiveMovies();
      res.json(results);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Public API: Get active widgets
  app.get('/api/widgets', async (req, res) => {
    try {
      const widgetsList = await storage.getActiveWidgets();
      res.json(widgetsList);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Admin - Movie Management CRUD
  const { insertCustomServerSchema, insertMovieSchema, insertWidgetSchema, insertPageSchema, insertUpiPaymentSchema } = await import("@shared/schema");

  app.get('/api/admin/movies', requireAdmin, async (req, res) => {
    try {
      const moviesList = await storage.getMovies();
      res.json(moviesList);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/admin/movies', requireAdmin, async (req, res) => {
    try {
      const input = insertMovieSchema.parse(req.body);
      const existing = await storage.getMovieByTmdbId(input.tmdbId);
      if (existing) {
        return res.status(409).json({ message: "Movie already exists in database" });
      }
      const movie = await storage.addMovie(input);
      res.status(201).json(movie);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message, field: err.errors[0].path.join('.') });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  const importSchema = z.object({
    tmdbId: z.number({ required_error: "tmdbId is required" }).int().positive(),
    mediaType: z.enum(['movie', 'tv']).default('movie'),
  });

  const bulkImportSchema = z.object({
    type: z.enum(['movie', 'tv', 'all']).default('all'),
  });

  app.post('/api/admin/movies/import', requireAdmin, async (req, res) => {
    try {
      const { tmdbId, mediaType } = importSchema.parse(req.body);
      const type = mediaType;

      const existing = await storage.getMovieByTmdbId(tmdbId);
      if (existing) {
        return res.status(409).json({ message: "Already imported", movie: existing });
      }

      const details = await fetchTmdb(`/${type}/${tmdbId}`, { append_to_response: 'videos,credits' });
      const movieData: any = {
        tmdbId: details.id,
        title: details.title || details.name || 'Unknown',
        originalTitle: details.original_title || details.original_name || null,
        overview: details.overview || null,
        posterPath: details.poster_path || null,
        backdropPath: details.backdrop_path || null,
        mediaType: type,
        releaseDate: details.release_date || details.first_air_date || null,
        voteAverage: details.vote_average || 0,
        voteCount: details.vote_count || 0,
        popularity: details.popularity || 0,
        genreIds: details.genres ? details.genres.map((g: any) => g.id).join(',') : null,
        originalLanguage: details.original_language || null,
        adult: details.adult || false,
        isFeatured: false,
        isActive: true,
        runtime: details.runtime || null,
        genresJson: details.genres ? JSON.stringify(details.genres) : null,
        castJson: details.credits?.cast ? JSON.stringify(details.credits.cast.slice(0, 15)) : null,
        videosJson: details.videos?.results ? JSON.stringify(details.videos.results.slice(0, 5)) : null,
        seasonsJson: details.seasons ? JSON.stringify(details.seasons) : null,
        productionCompaniesJson: details.production_companies ? JSON.stringify(details.production_companies.slice(0, 5)) : null,
      };

      const movie = await storage.addMovie(movieData);
      res.status(201).json(movie);
    } catch (err: any) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message });
      }
      res.status(500).json({ message: err.message || "Failed to import" });
    }
  });

  app.post('/api/admin/movies/import-bulk', requireAdmin, async (req, res) => {
    try {
      const { type: mediaType } = bulkImportSchema.parse(req.body);
      const data = await fetchTmdb(`/trending/${mediaType}/week`);
      const results = data.results || [];
      let imported = 0;
      let skipped = 0;

      for (const item of results) {
        const existing = await storage.getMovieByTmdbId(item.id);
        if (existing) { skipped++; continue; }

        await storage.addMovie({
          tmdbId: item.id,
          title: item.title || item.name || 'Unknown',
          originalTitle: item.original_title || item.original_name || null,
          overview: item.overview || null,
          posterPath: item.poster_path || null,
          backdropPath: item.backdrop_path || null,
          mediaType: item.media_type || mediaType,
          releaseDate: item.release_date || item.first_air_date || null,
          voteAverage: item.vote_average || 0,
          voteCount: item.vote_count || 0,
          popularity: item.popularity || 0,
          genreIds: item.genre_ids ? item.genre_ids.join(',') : null,
          originalLanguage: item.original_language || null,
          adult: item.adult || false,
          isFeatured: false,
          isActive: true,
        });
        imported++;
      }

      res.json({ imported, skipped, total: results.length });
    } catch (err: any) {
      res.status(500).json({ message: err.message || "Failed to import" });
    }
  });

  app.put('/api/admin/movies/:id', requireAdmin, async (req, res) => {
    try {
      const input = insertMovieSchema.partial().parse(req.body);
      const movie = await storage.updateMovie(Number(req.params.id), input);
      if (!movie) {
        return res.status(404).json({ message: "Movie not found" });
      }
      res.status(200).json(movie);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message, field: err.errors[0].path.join('.') });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/admin/movies/bulk-delete', requireAdmin, async (req, res) => {
    try {
      const { ids } = z.object({ ids: z.array(z.number()) }).parse(req.body);
      for (const id of ids) {
        await storage.deleteMovie(id);
      }
      res.json({ deleted: ids.length });
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.delete('/api/admin/movies/:id', requireAdmin, async (req, res) => {
    try {
      const id = Number(req.params.id);
      if (isNaN(id)) return res.status(400).json({ message: "Invalid ID" });
      await storage.deleteMovie(id);
      res.status(204).end();
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Admin - Server Management
  app.get('/api/admin/servers', requireAdmin, async (req, res) => {
    try {
      const servers = await storage.getCustomServers();
      res.json(servers);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/admin/servers', requireAdmin, async (req, res) => {
    try {
      const input = insertCustomServerSchema.parse(req.body);
      const server = await storage.addCustomServer(input);
      res.status(201).json(server);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message, field: err.errors[0].path.join('.') });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.put('/api/admin/servers/:id', requireAdmin, async (req, res) => {
    try {
      const input = insertCustomServerSchema.partial().parse(req.body);
      const server = await storage.updateCustomServer(Number(req.params.id), input);
      res.status(200).json(server);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message, field: err.errors[0].path.join('.') });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.delete('/api/admin/servers/:id', requireAdmin, async (req, res) => {
    try {
      await storage.deleteCustomServer(Number(req.params.id));
      res.status(204).end();
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Admin - Widget Management
  app.get('/api/admin/widgets', requireAdmin, async (req, res) => {
    try {
      const widgetsList = await storage.getWidgets();
      res.json(widgetsList);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/admin/widgets', requireAdmin, async (req, res) => {
    try {
      const input = insertWidgetSchema.parse(req.body);
      const widget = await storage.addWidget(input);
      res.status(201).json(widget);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.put('/api/admin/widgets/:id', requireAdmin, async (req, res) => {
    try {
      const input = insertWidgetSchema.partial().parse(req.body);
      const widget = await storage.updateWidget(Number(req.params.id), input);
      res.status(200).json(widget);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.delete('/api/admin/widgets/:id', requireAdmin, async (req, res) => {
    try {
      await storage.deleteWidget(Number(req.params.id));
      res.status(204).end();
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Admin - Stats endpoint
  app.get('/api/admin/stats', requireAdmin, async (req, res) => {
    try {
      const watchlistItems = await storage.getWatchlist();
      const serversList = await storage.getCustomServers();
      const moviesList = await storage.getMovies();
      const widgetsList = await storage.getWidgets();
      const streamingCount = serversList.filter(s => !s.isDownload).length;
      const downloadCount = serversList.filter(s => s.isDownload).length;
      res.json({
        watchlistCount: watchlistItems.length,
        serversCount: serversList.length,
        streamingServers: streamingCount,
        downloadServers: downloadCount,
        moviesCount: moviesList.length,
        widgetsCount: widgetsList.length,
      });
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Admin - Settings (Razorpay keys etc)
  app.get('/api/admin/settings', requireAdmin, async (req, res) => {
    try {
      const settings = await storage.getAllSettings();
      const obj: Record<string, string> = {};
      settings.forEach(s => { obj[s.key] = s.value; });
      res.json(obj);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/admin/settings', requireAdmin, async (req, res) => {
    try {
      const entries = Object.entries(req.body) as [string, string][];
      for (const [key, value] of entries) {
        await storage.setSetting(key, value);
      }
      res.json({ message: 'Settings saved' });
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Razorpay - Create subscription order
  app.post('/api/payment/create-order', async (req, res) => {
    try {
      const razorpayKeyId = await storage.getSetting('razorpay_key_id');
      const razorpayKeySecret = await storage.getSetting('razorpay_key_secret');
      
      if (!razorpayKeyId || !razorpayKeySecret) {
        return res.status(400).json({ message: 'Payment not configured. Contact admin.' });
      }

      const Razorpay = (await import('razorpay')).default;
      const razorpay = new Razorpay({
        key_id: razorpayKeyId,
        key_secret: razorpayKeySecret,
      });

      const amountStr = await storage.getSetting('subscription_amount') || '299';
      const amount = parseInt(amountStr) * 100;

      const order = await razorpay.orders.create({
        amount,
        currency: 'INR',
        receipt: `sub_${Date.now()}`,
      });

      res.json({ order, key_id: razorpayKeyId });
    } catch (err: any) {
      res.status(500).json({ message: err.message || 'Failed to create order' });
    }
  });

  app.post('/api/payment/verify', async (req, res) => {
    try {
      const { razorpay_order_id, razorpay_payment_id, razorpay_signature } = req.body;
      const razorpayKeySecret = await storage.getSetting('razorpay_key_secret');
      
      if (!razorpayKeySecret) {
        return res.status(400).json({ message: 'Payment not configured' });
      }

      const crypto = await import('crypto');
      const expectedSignature = crypto
        .createHmac('sha256', razorpayKeySecret)
        .update(`${razorpay_order_id}|${razorpay_payment_id}`)
        .digest('hex');

      if (expectedSignature === razorpay_signature) {
        res.json({ verified: true, message: 'Payment verified successfully' });
      } else {
        res.status(400).json({ verified: false, message: 'Payment verification failed' });
      }
    } catch (err: any) {
      res.status(500).json({ message: err.message || 'Verification failed' });
    }
  });

  // Admin - Page Management
  app.get('/api/admin/pages', requireAdmin, async (req, res) => {
    try {
      const pagesList = await storage.getPages();
      res.json(pagesList);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/admin/pages', requireAdmin, async (req, res) => {
    try {
      const input = insertPageSchema.parse(req.body);
      const existing = await storage.getPageBySlug(input.slug);
      if (existing) {
        return res.status(409).json({ message: "A page with this slug already exists" });
      }
      const page = await storage.addPage(input);
      res.status(201).json(page);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.put('/api/admin/pages/:id', requireAdmin, async (req, res) => {
    try {
      const input = insertPageSchema.partial().parse(req.body);
      const page = await storage.updatePage(Number(req.params.id), input);
      res.status(200).json(page);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.delete('/api/admin/pages/:id', requireAdmin, async (req, res) => {
    try {
      await storage.deletePage(Number(req.params.id));
      res.status(204).end();
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Public - Pages
  app.get('/api/pages', async (req, res) => {
    try {
      const pagesList = await storage.getActivePages();
      res.json(pagesList);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.get('/api/pages/footer', async (req, res) => {
    try {
      const footerPages = await storage.getFooterPages();
      res.json(footerPages);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.get('/api/pages/:slug', async (req, res) => {
    try {
      const page = await storage.getPageBySlug(req.params.slug);
      if (!page || !page.isActive) {
        return res.status(404).json({ message: "Page not found" });
      }
      res.json(page);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Admin - UPI Payment Management
  app.get('/api/admin/upi-payments', requireAdmin, async (req, res) => {
    try {
      const payments = await storage.getUpiPayments();
      res.json(payments);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.put('/api/admin/upi-payments/:id', requireAdmin, async (req, res) => {
    try {
      const { status, adminNote } = req.body;
      const payment = await storage.updateUpiPayment(Number(req.params.id), { status, adminNote });
      res.json(payment);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.delete('/api/admin/upi-payments/:id', requireAdmin, async (req, res) => {
    try {
      await storage.deleteUpiPayment(Number(req.params.id));
      res.status(204).end();
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Public - Submit UPI Payment
  app.post('/api/upi-payments', async (req, res) => {
    try {
      const input = insertUpiPaymentSchema.parse(req.body);
      const payment = await storage.addUpiPayment(input);
      res.status(201).json(payment);
    } catch (err) {
      if (err instanceof z.ZodError) {
        return res.status(400).json({ message: err.errors[0].message });
      }
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Public settings (non-sensitive)
  app.get('/api/settings/public', async (req, res) => {
    try {
      const razorpayKeyId = await storage.getSetting('razorpay_key_id');
      const subscriptionAmount = await storage.getSetting('subscription_amount') || '299';
      const subscriptionName = await storage.getSetting('subscription_name') || 'Tmovie Premium';
      const subscriptionEnabled = await storage.getSetting('subscription_enabled');
      const upiId = await storage.getSetting('upi_id');
      const upiQrUrl = await storage.getSetting('upi_qr_url');
      const telegramLink = await storage.getSetting('telegram_link');
      const searchConsoleMeta = await storage.getSetting('search_console_meta');
      const googleAnalyticsId = await storage.getSetting('google_analytics_id');
      const siteTitle = await storage.getSetting('site_title');
      const siteDescription = await storage.getSetting('site_description');
      const faviconUrl = await storage.getSetting('favicon_url');
      const logoUrl = await storage.getSetting('logo_url');
      const ogImageUrl = await storage.getSetting('og_image_url');
      const metaKeywords = await storage.getSetting('meta_keywords');
      const footerText = await storage.getSetting('footer_text');
      const twitterUrl = await storage.getSetting('twitter_url');
      const instagramUrl = await storage.getSetting('instagram_url');
      const youtubeUrl = await storage.getSetting('youtube_url');
      const facebookUrl = await storage.getSetting('facebook_url');
      const adEnabled = await storage.getSetting('ad_enabled');
      const adType = await storage.getSetting('ad_type');
      const adUrl = await storage.getSetting('ad_url');
      const adSkipSeconds = await storage.getSetting('ad_skip_seconds');
      const isEnabled = subscriptionEnabled !== 'false';
      res.json({
        razorpayKeyId: razorpayKeyId || null,
        subscriptionAmount,
        subscriptionName,
        paymentEnabled: isEnabled && !!razorpayKeyId,
        subscriptionEnabled: isEnabled,
        upiId: upiId || null,
        upiQrUrl: upiQrUrl || null,
        telegramLink: telegramLink || 'https://t.me/tmovieofficial',
        searchConsoleMeta: searchConsoleMeta || null,
        googleAnalyticsId: googleAnalyticsId || null,
        siteTitle: siteTitle || 'TMovie',
        siteDescription: siteDescription || 'Stream movies and TV shows online',
        faviconUrl: faviconUrl || null,
        logoUrl: logoUrl || null,
        ogImageUrl: ogImageUrl || null,
        metaKeywords: metaKeywords || null,
        footerText: footerText || null,
        twitterUrl: twitterUrl || null,
        instagramUrl: instagramUrl || null,
        youtubeUrl: youtubeUrl || null,
        facebookUrl: facebookUrl || null,
        adEnabled: adEnabled === 'true',
        adType: adType || 'image',
        adUrl: adUrl || null,
        adSkipSeconds: parseInt(adSkipSeconds || '5'),
      });
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Comments
  app.get('/api/comments/:mediaType/:tmdbId', async (req, res) => {
    try {
      const tmdbId = parseInt(req.params.tmdbId);
      const { mediaType } = req.params;
      const result = await storage.getComments(tmdbId, mediaType);
      res.json(result);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/comments/:mediaType/:tmdbId', async (req, res) => {
    try {
      const tmdbId = parseInt(req.params.tmdbId);
      const { mediaType } = req.params;
      const { userName, content } = req.body;
      if (!userName?.trim() || !content?.trim()) return res.status(400).json({ message: "Name and content required" });
      if (content.length > 500) return res.status(400).json({ message: "Comment too long (max 500 chars)" });
      const comment = await storage.addComment({ tmdbId, mediaType, userName: userName.trim(), content: content.trim() });
      res.json(comment);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.delete('/api/comments/:id', requireAdmin, async (req, res) => {
    try {
      await storage.deleteComment(parseInt(req.params.id));
      res.json({ success: true });
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  // Reactions
  app.get('/api/reactions/:mediaType/:tmdbId', async (req, res) => {
    try {
      const tmdbId = parseInt(req.params.tmdbId);
      const { mediaType } = req.params;
      const result = await storage.getReactions(tmdbId, mediaType);
      res.json(result);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  app.post('/api/reactions/:mediaType/:tmdbId', async (req, res) => {
    try {
      const tmdbId = parseInt(req.params.tmdbId);
      const { mediaType } = req.params;
      const { emoji } = req.body;
      const allowed = ['❤️', '😂', '😮', '😢', '😡', '👍'];
      if (!allowed.includes(emoji)) return res.status(400).json({ message: "Invalid emoji" });
      const reaction = await storage.reactEmoji(tmdbId, mediaType, emoji);
      res.json(reaction);
    } catch (err) {
      res.status(500).json({ message: "Internal server error" });
    }
  });

  return httpServer;
}
