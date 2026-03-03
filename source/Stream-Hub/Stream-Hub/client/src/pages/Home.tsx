import { useState, useEffect, useRef, useCallback } from "react";
import { useTrending } from "@/hooks/use-tmdb";
import { useQuery } from "@tanstack/react-query";
import { Shell, usePublicSettings } from "@/components/layout/Shell";
import { HeroSlider } from "@/components/home/HeroSlider";
import { OttProviderStrip } from "@/components/home/OttProviderStrip";
import { ContentRow } from "@/components/shared/ContentRow";
import { MovieCard } from "@/components/shared/MovieCard";
import { SplashOverlay, LANG_STORAGE_KEY } from "@/components/home/SplashOverlay";
import { Loader2, Sparkles, ArrowRight, ChevronDown, Send } from "lucide-react";
import { Link } from "wouter";
import type { Widget, Movie } from "@shared/schema";

function safeJsonParse(str: string, fallback: any = {}) {
  try { return JSON.parse(str); } catch { return fallback; }
}

const GENRES: { id: number; label: string }[] = [
  { id: 28, label: "Action" },
  { id: 35, label: "Comedy" },
  { id: 18, label: "Drama" },
  { id: 27, label: "Horror" },
  { id: 878, label: "Sci-Fi" },
  { id: 16, label: "Animation" },
  { id: 10749, label: "Romance" },
  { id: 53, label: "Thriller" },
  { id: 10765, label: "Fantasy" },
  { id: 80, label: "Crime" },
];

const LANGUAGES: { code: string; label: string }[] = [
  { code: "all", label: "All Languages" },
  { code: "en", label: "English" },
  { code: "hi", label: "Hindi" },
  { code: "ja", label: "Japanese" },
  { code: "ko", label: "Korean" },
  { code: "es", label: "Spanish" },
  { code: "fr", label: "French" },
];

function WidgetContentRow({ widget }: { widget: Widget }) {
  const config = safeJsonParse(widget.config || "{}");
  const filter = config.filter || "all";
  const limit = config.limit || 20;

  const { data: movies = [] } = useQuery<Movie[]>({
    queryKey: ["/api/movies", filter],
    queryFn: async () => {
      const params = new URLSearchParams();
      if (filter === "featured") params.set("featured", "true");
      else if (filter !== "all") params.set("type", filter);
      const res = await fetch(`/api/movies?${params}`);
      return res.json();
    },
  });

  const items = movies.slice(0, limit).map((m) => ({
    id: m.tmdbId,
    title: m.title,
    name: m.mediaType === "tv" ? m.title : undefined,
    poster_path: m.posterPath,
    backdrop_path: m.backdropPath,
    vote_average: m.voteAverage || 0,
    media_type: m.mediaType,
    release_date: m.releaseDate,
    first_air_date: m.mediaType === "tv" ? m.releaseDate : undefined,
    overview: m.overview || "",
  }));

  if (items.length === 0) return null;

  const viewAllLink =
    filter === "featured"
      ? "/search"
      : filter !== "all"
      ? `/search?type=${filter}`
      : "/search";

  return (
    <ContentRow title={widget.title} viewAllLink={viewAllLink}>
      {items.map((item: any, i: number) => (
        <MovieCard key={`w${widget.id}-${item.id}`} item={item} index={i} />
      ))}
    </ContentRow>
  );
}

function WidgetCtaBanner({ widget }: { widget: Widget }) {
  const config = safeJsonParse(widget.config || "{}");
  const text = config.text || "Subscribe to Premium";
  const buttonText = config.buttonText || "Subscribe Now";
  const link = config.link || "/profile";

  const { data: publicSettings } = useQuery<{ subscriptionEnabled: boolean }>({
    queryKey: ["/api/settings/public"],
  });

  if (publicSettings && !publicSettings.subscriptionEnabled) return null;

  return (
    <div className="px-4 lg:px-12 py-4">
      <div className="neu-raised p-6 md:p-8 border border-white/5 rounded-xl relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-r from-primary/10 via-transparent to-accent/10" />
        <div className="relative z-10 flex flex-col md:flex-row items-center justify-between gap-4">
          <div className="flex items-center gap-3">
            <Sparkles className="w-6 h-6 text-primary" />
            <div>
              <h3 className="text-lg md:text-xl font-display font-bold text-white">{widget.title}</h3>
              <p className="text-sm text-muted-foreground">{text}</p>
            </div>
          </div>
          <Link href={link}>
            <button
              className="px-6 py-3 bg-primary text-primary-foreground font-bold rounded-xl hover:opacity-90 transition-opacity flex items-center gap-2"
              data-testid="cta-subscribe"
            >
              {buttonText}
              <ArrowRight className="w-4 h-4" />
            </button>
          </Link>
        </div>
      </div>
    </div>
  );
}

function WidgetMenuLinks({ widget }: { widget: Widget }) {
  const config = safeJsonParse(widget.config || "{}");
  const links = config.links || [];

  if (links.length === 0) return null;

  return (
    <div className="px-4 lg:px-12 py-4">
      <h2 className="text-base md:text-xl font-display font-bold text-white uppercase tracking-wider mb-3">
        {widget.title}
        <div className="mt-1 w-8 h-0.5 bg-primary" />
      </h2>
      <div className="flex flex-wrap gap-2">
        {links.map((link: { label: string; href: string }, i: number) => (
          <Link key={i} href={link.href}>
            <button
              className="px-4 py-2.5 neu-flat border border-white/5 text-sm text-white font-medium hover:border-primary/30 hover:text-primary transition-all rounded-xl"
              data-testid={`menu-link-${i}`}
            >
              {link.label}
            </button>
          </Link>
        ))}
      </div>
    </div>
  );
}

function RenderWidget({ widget }: { widget: Widget }) {
  switch (widget.type) {
    case "content_row": return <WidgetContentRow widget={widget} />;
    case "cta_banner": return <WidgetCtaBanner widget={widget} />;
    case "menu_links": return <WidgetMenuLinks widget={widget} />;
    default: return null;
  }
}

function TelegramCTA() {
  const { data: ps } = usePublicSettings();
  const link = ps?.telegramLink || 'https://t.me/tmovieofficial';
  return (
    <div className="px-4 lg:px-12 py-4">
      <a
        href={link}
        target="_blank"
        rel="noopener noreferrer"
        className="block"
        data-testid="cta-telegram"
      >
        <div className="neu-flat border border-white/5 rounded-xl p-4 flex items-center justify-between gap-4 group hover:border-primary/20 transition-all">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style={{ background: "#229ED9" }}>
              <Send className="w-5 h-5 text-white" />
            </div>
            <div>
              <p className="text-sm font-bold text-white">Join our Telegram Channel</p>
              <p className="text-xs text-muted-foreground mt-0.5">Get updates, new releases & exclusive content</p>
            </div>
          </div>
          <div className="px-4 py-2 rounded-lg text-xs font-bold text-white uppercase tracking-wider shrink-0 transition-all group-hover:opacity-90" style={{ background: "#229ED9" }}>
            Join Now
          </div>
        </div>
      </a>
    </div>
  );
}

const LIB_PAGE = 24;

function LibraryBrowse({ allMovies, initialLang = "all" }: { allMovies: Movie[]; initialLang?: string }) {
  const [selectedGenre, setSelectedGenre] = useState<number | null>(null);
  const [selectedLang, setSelectedLang] = useState(initialLang);
  const [genreOpen, setGenreOpen] = useState(false);
  const [langOpen, setLangOpen] = useState(false);
  const [visibleCount, setVisibleCount] = useState(LIB_PAGE);
  const sentinelRef = useRef<HTMLDivElement>(null);

  useEffect(() => { setVisibleCount(LIB_PAGE); }, [selectedGenre, selectedLang]);

  useEffect(() => { setSelectedLang(initialLang); }, [initialLang]);

  const allFiltered = allMovies.filter((m) => {
    const genreIds = m.genreIds ? m.genreIds.split(",").map(Number) : [];
    const genreMatch = !selectedGenre || genreIds.includes(selectedGenre);
    const langMatch = selectedLang === "all" || m.originalLanguage === selectedLang;
    return genreMatch && langMatch;
  });

  const hasMore = visibleCount < allFiltered.length;

  const handleSentinel = useCallback((entries: IntersectionObserverEntry[]) => {
    if (entries[0].isIntersecting && hasMore) {
      setVisibleCount(c => c + LIB_PAGE);
    }
  }, [hasMore]);

  useEffect(() => {
    const el = sentinelRef.current;
    if (!el) return;
    const observer = new IntersectionObserver(handleSentinel, { rootMargin: '300px' });
    observer.observe(el);
    return () => observer.disconnect();
  }, [handleSentinel]);

  if (allMovies.length === 0) return null;

  const selectedGenreLabel = selectedGenre
    ? GENRES.find((g) => g.id === selectedGenre)?.label || "Genre"
    : "Genre";
  const selectedLangLabel =
    LANGUAGES.find((l) => l.code === selectedLang)?.label || "All Languages";

  const filtersActive = selectedGenre !== null || selectedLang !== "all";

  const items = allFiltered.slice(0, visibleCount).map((m) => ({
    id: m.tmdbId,
    title: m.title,
    name: m.mediaType === "tv" ? m.title : undefined,
    poster_path: m.posterPath,
    backdrop_path: m.backdropPath,
    vote_average: m.voteAverage || 0,
    media_type: m.mediaType,
    release_date: m.releaseDate,
    first_air_date: m.mediaType === "tv" ? m.releaseDate : undefined,
    overview: m.overview || "",
    original_language: m.originalLanguage,
    genre_ids: m.genreIds ? m.genreIds.split(",").map(Number) : [],
  }));

  return (
    <div className="px-4 lg:px-12 py-4">
      <div className="flex items-center justify-between mb-3 gap-3 flex-wrap">
        <h2 className="text-base md:text-xl font-display font-bold text-white uppercase tracking-wider">
          Browse Library
          <div className="mt-1 w-8 h-0.5 bg-primary" />
        </h2>

        <div className="flex items-center gap-2 flex-wrap">
          <div className="relative">
            <button
              onClick={() => { setGenreOpen(!genreOpen); setLangOpen(false); }}
              className={`flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all border ${
                selectedGenre
                  ? "bg-primary/10 border-primary/30 text-primary"
                  : "neu-flat border-white/5 text-white/70 hover:text-white"
              }`}
              data-testid="dropdown-genre"
            >
              {selectedGenreLabel}
              <ChevronDown className="w-3.5 h-3.5" />
            </button>
            {genreOpen && (
              <div className="absolute top-full mt-1 left-0 z-50 bg-card border border-white/10 rounded-xl shadow-2xl min-w-[140px] py-1">
                <button
                  onClick={() => { setSelectedGenre(null); setGenreOpen(false); }}
                  className="w-full text-left px-3.5 py-2 text-xs text-muted-foreground hover:text-white hover:bg-white/5 transition-colors"
                  data-testid="genre-all"
                >
                  All Genres
                </button>
                {GENRES.map((g) => (
                  <button
                    key={g.id}
                    onClick={() => { setSelectedGenre(g.id); setGenreOpen(false); }}
                    className={`w-full text-left px-3.5 py-2 text-xs transition-colors hover:bg-white/5 ${
                      selectedGenre === g.id ? "text-primary" : "text-white/70 hover:text-white"
                    }`}
                    data-testid={`genre-${g.id}`}
                  >
                    {g.label}
                  </button>
                ))}
              </div>
            )}
          </div>

          <div className="relative">
            <button
              onClick={() => { setLangOpen(!langOpen); setGenreOpen(false); }}
              className={`flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all border ${
                selectedLang !== "all"
                  ? "bg-primary/10 border-primary/30 text-primary"
                  : "neu-flat border-white/5 text-white/70 hover:text-white"
              }`}
              data-testid="dropdown-language"
            >
              {selectedLangLabel}
              <ChevronDown className="w-3.5 h-3.5" />
            </button>
            {langOpen && (
              <div className="absolute top-full mt-1 left-0 z-50 bg-card border border-white/10 rounded-xl shadow-2xl min-w-[150px] py-1">
                {LANGUAGES.map((l) => (
                  <button
                    key={l.code}
                    onClick={() => { setSelectedLang(l.code); setLangOpen(false); }}
                    className={`w-full text-left px-3.5 py-2 text-xs transition-colors hover:bg-white/5 ${
                      selectedLang === l.code ? "text-primary" : "text-white/70 hover:text-white"
                    }`}
                    data-testid={`lang-${l.code}`}
                  >
                    {l.label}
                  </button>
                ))}
              </div>
            )}
          </div>

          {filtersActive && (
            <button
              onClick={() => { setSelectedGenre(null); setSelectedLang("all"); }}
              className="px-3 py-1.5 text-xs font-medium text-accent border border-accent/20 bg-accent/10 rounded-lg hover:bg-accent/20 transition-all"
              data-testid="clear-filters"
            >
              Clear
            </button>
          )}
        </div>
      </div>

      <p className="text-xs text-muted-foreground mb-3">
        {items.length} of {allFiltered.length} titles
      </p>

      {items.length === 0 ? (
        <p className="text-muted-foreground text-sm py-4">
          No titles found for selected filters.
        </p>
      ) : (
        <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-1.5 sm:gap-2 md:gap-3">
          {items.map((item: any, i: number) => (
            <MovieCard key={`lib-${item.id}`} item={item} index={i} />
          ))}
        </div>
      )}

      <div ref={sentinelRef} className="h-4 mt-2" />
      {hasMore && (
        <div className="flex justify-center py-4">
          <Loader2 className="w-6 h-6 text-primary animate-spin" />
        </div>
      )}
    </div>
  );
}

function WatchlistRow() {
  const { data: watchlistItems = [] } = useQuery<any[]>({
    queryKey: ["/api/watchlist"],
  });

  if (!watchlistItems || watchlistItems.length === 0) return null;

  const items = watchlistItems.map((w: any) => ({
    id: w.tmdbId,
    title: w.title,
    name: w.type === "tv" ? w.title : undefined,
    poster_path: w.posterPath,
    backdrop_path: null,
    vote_average: 0,
    media_type: w.type,
    release_date: undefined,
    overview: "",
  }));

  return (
    <ContentRow title="My Watchlist" viewAllLink="/watchlist">
      {items.map((item: any, i: number) => (
        <MovieCard key={`wl-${item.id}`} item={item} index={i} />
      ))}
    </ContentRow>
  );
}

export default function Home() {
  const [splashLang, setSplashLang] = useState<string>(
    () => localStorage.getItem(LANG_STORAGE_KEY) || "all"
  );

  const { data: trendingAll, isLoading: loadingAll } = useTrending("all");
  const { data: trendingMovies } = useTrending("movie");
  const { data: trendingTv } = useTrending("tv");

  const { data: widgetsList = [] } = useQuery<Widget[]>({
    queryKey: ["/api/widgets"],
  });

  const { data: allMovies = [] } = useQuery<Movie[]>({
    queryKey: ["/api/movies"],
  });

  if (loadingAll) {
    return (
      <Shell>
        <div className="h-screen flex items-center justify-center">
          <Loader2 className="w-12 h-12 text-primary animate-spin" />
        </div>
      </Shell>
    );
  }

  const heroItems = trendingAll?.slice(0, 5) || [];
  const hasWidgets = widgetsList.length > 0;

  return (
    <Shell>
      <SplashOverlay onDismiss={(lang) => setSplashLang(lang)} />
      <div className="pb-20">
        <HeroSlider items={heroItems} />

        <OttProviderStrip />

        <div className="mt-4 flex flex-col gap-2 relative z-20">
          <TelegramCTA />

          <WatchlistRow />

          {hasWidgets ? (
            widgetsList.map((widget) => (
              <RenderWidget key={widget.id} widget={widget} />
            ))
          ) : (
            <>
              <ContentRow title="Trending Movies" viewAllLink="/search?type=movie">
                {trendingMovies?.map((item, i) => (
                  <MovieCard key={`m-${item.id}`} item={item} index={i} />
                ))}
              </ContentRow>

              <ContentRow title="Popular TV Shows" viewAllLink="/search?type=tv">
                {trendingTv?.map((item, i) => (
                  <MovieCard key={`tv-${item.id}`} item={item} index={i} />
                ))}
              </ContentRow>

              <ContentRow title="Top Picks For You">
                {trendingAll?.slice(5, 20).map((item, i) => (
                  <MovieCard key={`all-${item.id}`} item={item} index={i} />
                ))}
              </ContentRow>
            </>
          )}

          <LibraryBrowse allMovies={allMovies} initialLang={splashLang} />
        </div>
      </div>
    </Shell>
  );
}
