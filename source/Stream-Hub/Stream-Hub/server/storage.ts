import { db } from "./db";
import { watchlist, customServers, movies, users, siteSettings, widgets, pages, upiPayments, comments, reactions, type InsertWatchlistItem, type WatchlistItem, type CustomServer, type InsertCustomServer, type Movie, type InsertMovie, type User, type InsertUser, type SiteSetting, type Widget, type InsertWidget, type Page, type InsertPage, type UpiPayment, type InsertUpiPayment, type Comment, type InsertComment, type Reaction } from "@shared/schema";
import { eq, and, desc, ilike, asc, sql, count } from "drizzle-orm";

export interface IStorage {
  getWatchlist(): Promise<WatchlistItem[]>;
  addWatchlist(item: InsertWatchlistItem): Promise<WatchlistItem>;
  removeWatchlist(id: number): Promise<void>;
  getWatchlistItem(tmdbId: number, type: string): Promise<WatchlistItem | undefined>;
  getCustomServers(): Promise<CustomServer[]>;
  getActiveServers(): Promise<CustomServer[]>;
  addCustomServer(server: InsertCustomServer): Promise<CustomServer>;
  updateCustomServer(id: number, updates: Partial<InsertCustomServer>): Promise<CustomServer>;
  deleteCustomServer(id: number): Promise<void>;
  getMovies(): Promise<Movie[]>;
  getActiveMovies(): Promise<Movie[]>;
  getActiveMoviesPaginated(page: number, limit: number, type?: string): Promise<{ movies: Movie[]; total: number }>;
  getMovieByTmdbId(tmdbId: number): Promise<Movie | undefined>;
  getMovieById(id: number): Promise<Movie | undefined>;
  addMovie(movie: InsertMovie): Promise<Movie>;
  updateMovie(id: number, updates: Partial<InsertMovie>): Promise<Movie>;
  deleteMovie(id: number): Promise<void>;
  searchMovies(query: string): Promise<Movie[]>;
  getMoviesByMediaType(mediaType: string): Promise<Movie[]>;
  getFeaturedMovies(): Promise<Movie[]>;
  getUserByUsername(username: string): Promise<User | undefined>;
  getUserById(id: number): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  updateUser(id: number, updates: Partial<InsertUser>): Promise<User>;
  getSetting(key: string): Promise<string | undefined>;
  setSetting(key: string, value: string): Promise<void>;
  getAllSettings(): Promise<SiteSetting[]>;
  getWidgets(): Promise<Widget[]>;
  getActiveWidgets(): Promise<Widget[]>;
  addWidget(widget: InsertWidget): Promise<Widget>;
  updateWidget(id: number, updates: Partial<InsertWidget>): Promise<Widget>;
  deleteWidget(id: number): Promise<void>;
  getPages(): Promise<Page[]>;
  getActivePages(): Promise<Page[]>;
  getFooterPages(): Promise<Page[]>;
  getPageBySlug(slug: string): Promise<Page | undefined>;
  addPage(page: InsertPage): Promise<Page>;
  updatePage(id: number, updates: Partial<InsertPage>): Promise<Page>;
  deletePage(id: number): Promise<void>;
  getUpiPayments(): Promise<UpiPayment[]>;
  addUpiPayment(payment: InsertUpiPayment): Promise<UpiPayment>;
  updateUpiPayment(id: number, updates: Partial<InsertUpiPayment>): Promise<UpiPayment>;
  deleteUpiPayment(id: number): Promise<void>;
  getComments(tmdbId: number, mediaType: string): Promise<Comment[]>;
  addComment(comment: InsertComment): Promise<Comment>;
  deleteComment(id: number): Promise<void>;
  getReactions(tmdbId: number, mediaType: string): Promise<Reaction[]>;
  reactEmoji(tmdbId: number, mediaType: string, emoji: string): Promise<Reaction>;
}

export class DatabaseStorage implements IStorage {
  async getWatchlist(): Promise<WatchlistItem[]> {
    return await db.select().from(watchlist);
  }

  async addWatchlist(item: InsertWatchlistItem): Promise<WatchlistItem> {
    const [newItem] = await db.insert(watchlist).values(item).returning();
    return newItem;
  }

  async removeWatchlist(id: number): Promise<void> {
    await db.delete(watchlist).where(eq(watchlist.id, id));
  }
  
  async getWatchlistItem(tmdbId: number, type: string): Promise<WatchlistItem | undefined> {
    const [item] = await db.select().from(watchlist).where(and(eq(watchlist.tmdbId, tmdbId), eq(watchlist.type, type)));
    return item;
  }

  async getCustomServers(): Promise<CustomServer[]> {
    return await db.select().from(customServers).orderBy(asc(customServers.sortOrder));
  }

  async getActiveServers(): Promise<CustomServer[]> {
    return await db.select().from(customServers).where(eq(customServers.isActive, true)).orderBy(asc(customServers.sortOrder));
  }

  async addCustomServer(server: InsertCustomServer): Promise<CustomServer> {
    const [newServer] = await db.insert(customServers).values(server).returning();
    return newServer;
  }

  async updateCustomServer(id: number, updates: Partial<InsertCustomServer>): Promise<CustomServer> {
    const [updated] = await db.update(customServers).set(updates).where(eq(customServers.id, id)).returning();
    return updated;
  }

  async deleteCustomServer(id: number): Promise<void> {
    await db.delete(customServers).where(eq(customServers.id, id));
  }

  async getMovies(): Promise<Movie[]> {
    return await db.select().from(movies).orderBy(desc(movies.createdAt));
  }

  async getActiveMovies(): Promise<Movie[]> {
    return await db.select().from(movies).where(eq(movies.isActive, true)).orderBy(desc(movies.createdAt));
  }

  async getActiveMoviesPaginated(page: number, limit: number, type?: string): Promise<{ movies: Movie[]; total: number }> {
    const offset = (page - 1) * limit;
    const condition = type && type !== 'all'
      ? and(eq(movies.isActive, true), eq(movies.mediaType, type))
      : eq(movies.isActive, true);
    const [{ value: total }] = await db.select({ value: count() }).from(movies).where(condition);
    const moviesList = await db.select().from(movies).where(condition).orderBy(desc(movies.createdAt)).limit(limit).offset(offset);
    return { movies: moviesList, total: Number(total) };
  }

  async getMovieByTmdbId(tmdbId: number): Promise<Movie | undefined> {
    const [movie] = await db.select().from(movies).where(eq(movies.tmdbId, tmdbId));
    return movie;
  }

  async getMovieById(id: number): Promise<Movie | undefined> {
    const [movie] = await db.select().from(movies).where(eq(movies.id, id));
    return movie;
  }

  async addMovie(movie: InsertMovie): Promise<Movie> {
    const [newMovie] = await db.insert(movies).values(movie).returning();
    return newMovie;
  }

  async updateMovie(id: number, updates: Partial<InsertMovie>): Promise<Movie> {
    const [updated] = await db.update(movies).set(updates).where(eq(movies.id, id)).returning();
    return updated;
  }

  async deleteMovie(id: number): Promise<void> {
    await db.delete(movies).where(eq(movies.id, id));
  }

  async searchMovies(query: string): Promise<Movie[]> {
    return await db.select().from(movies).where(and(ilike(movies.title, `%${query}%`), eq(movies.isActive, true)));
  }

  async getMoviesByMediaType(mediaType: string): Promise<Movie[]> {
    return await db.select().from(movies).where(and(eq(movies.mediaType, mediaType), eq(movies.isActive, true))).orderBy(desc(movies.createdAt));
  }

  async getFeaturedMovies(): Promise<Movie[]> {
    return await db.select().from(movies).where(and(eq(movies.isFeatured, true), eq(movies.isActive, true))).orderBy(desc(movies.createdAt));
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    const [user] = await db.select().from(users).where(eq(users.username, username));
    return user;
  }

  async getUserById(id: number): Promise<User | undefined> {
    const [user] = await db.select().from(users).where(eq(users.id, id));
    return user;
  }

  async createUser(user: InsertUser): Promise<User> {
    const [newUser] = await db.insert(users).values(user).returning();
    return newUser;
  }

  async updateUser(id: number, updates: Partial<InsertUser>): Promise<User> {
    const [updated] = await db.update(users).set(updates).where(eq(users.id, id)).returning();
    return updated;
  }

  async getSetting(key: string): Promise<string | undefined> {
    const [setting] = await db.select().from(siteSettings).where(eq(siteSettings.key, key));
    return setting?.value;
  }

  async setSetting(key: string, value: string): Promise<void> {
    const existing = await this.getSetting(key);
    if (existing !== undefined) {
      await db.update(siteSettings).set({ value }).where(eq(siteSettings.key, key));
    } else {
      await db.insert(siteSettings).values({ key, value });
    }
  }

  async getAllSettings(): Promise<SiteSetting[]> {
    return await db.select().from(siteSettings);
  }

  async getWidgets(): Promise<Widget[]> {
    return await db.select().from(widgets).orderBy(asc(widgets.sortOrder));
  }

  async getActiveWidgets(): Promise<Widget[]> {
    return await db.select().from(widgets).where(eq(widgets.isActive, true)).orderBy(asc(widgets.sortOrder));
  }

  async addWidget(widget: InsertWidget): Promise<Widget> {
    const [newWidget] = await db.insert(widgets).values(widget).returning();
    return newWidget;
  }

  async updateWidget(id: number, updates: Partial<InsertWidget>): Promise<Widget> {
    const [updated] = await db.update(widgets).set(updates).where(eq(widgets.id, id)).returning();
    return updated;
  }

  async deleteWidget(id: number): Promise<void> {
    await db.delete(widgets).where(eq(widgets.id, id));
  }

  async getPages(): Promise<Page[]> {
    return await db.select().from(pages).orderBy(asc(pages.sortOrder));
  }

  async getActivePages(): Promise<Page[]> {
    return await db.select().from(pages).where(eq(pages.isActive, true)).orderBy(asc(pages.sortOrder));
  }

  async getFooterPages(): Promise<Page[]> {
    return await db.select().from(pages).where(and(eq(pages.isActive, true), eq(pages.showInFooter, true))).orderBy(asc(pages.sortOrder));
  }

  async getPageBySlug(slug: string): Promise<Page | undefined> {
    const [page] = await db.select().from(pages).where(eq(pages.slug, slug));
    return page;
  }

  async addPage(page: InsertPage): Promise<Page> {
    const [newPage] = await db.insert(pages).values(page).returning();
    return newPage;
  }

  async updatePage(id: number, updates: Partial<InsertPage>): Promise<Page> {
    const [updated] = await db.update(pages).set(updates).where(eq(pages.id, id)).returning();
    return updated;
  }

  async deletePage(id: number): Promise<void> {
    await db.delete(pages).where(eq(pages.id, id));
  }

  async getUpiPayments(): Promise<UpiPayment[]> {
    return await db.select().from(upiPayments).orderBy(desc(upiPayments.createdAt));
  }

  async addUpiPayment(payment: InsertUpiPayment): Promise<UpiPayment> {
    const [newPayment] = await db.insert(upiPayments).values(payment).returning();
    return newPayment;
  }

  async updateUpiPayment(id: number, updates: Partial<InsertUpiPayment>): Promise<UpiPayment> {
    const [updated] = await db.update(upiPayments).set(updates).where(eq(upiPayments.id, id)).returning();
    return updated;
  }

  async deleteUpiPayment(id: number): Promise<void> {
    await db.delete(upiPayments).where(eq(upiPayments.id, id));
  }

  async getComments(tmdbId: number, mediaType: string): Promise<Comment[]> {
    return await db.select().from(comments)
      .where(and(eq(comments.tmdbId, tmdbId), eq(comments.mediaType, mediaType)))
      .orderBy(desc(comments.createdAt));
  }

  async addComment(comment: InsertComment): Promise<Comment> {
    const [newComment] = await db.insert(comments).values(comment).returning();
    return newComment;
  }

  async deleteComment(id: number): Promise<void> {
    await db.delete(comments).where(eq(comments.id, id));
  }

  async getReactions(tmdbId: number, mediaType: string): Promise<Reaction[]> {
    return await db.select().from(reactions)
      .where(and(eq(reactions.tmdbId, tmdbId), eq(reactions.mediaType, mediaType)));
  }

  async reactEmoji(tmdbId: number, mediaType: string, emoji: string): Promise<Reaction> {
    const existing = await db.select().from(reactions)
      .where(and(eq(reactions.tmdbId, tmdbId), eq(reactions.mediaType, mediaType), eq(reactions.emoji, emoji)));
    if (existing.length > 0) {
      const [updated] = await db.update(reactions)
        .set({ count: existing[0].count + 1 })
        .where(eq(reactions.id, existing[0].id))
        .returning();
      return updated;
    }
    const [newReaction] = await db.insert(reactions).values({ tmdbId, mediaType, emoji, count: 1 }).returning();
    return newReaction;
  }
}

export const storage = new DatabaseStorage();
