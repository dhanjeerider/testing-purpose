import { useState, useEffect, useRef, useCallback } from "react";
import { Shell } from "@/components/layout/Shell";
import { useSearch } from "@/hooks/use-tmdb";
import { useInfiniteQuery } from "@tanstack/react-query";
import { MovieCard } from "@/components/shared/MovieCard";
import { Search as SearchIcon, Loader2, ArrowUpDown, X } from "lucide-react";
import { motion } from "framer-motion";
import { useLocation } from "wouter";
import type { Movie } from "@shared/schema";

function useDebounce<T>(value: T, delay: number): T {
  const [debouncedValue, setDebouncedValue] = useState<T>(value);
  useEffect(() => {
    const handler = setTimeout(() => setDebouncedValue(value), delay);
    return () => clearTimeout(handler);
  }, [value, delay]);
  return debouncedValue;
}

type SortOption = 'default' | 'rating_high' | 'rating_low' | 'name_az' | 'name_za' | 'date_new' | 'date_old' | 'popularity';
type MediaFilter = 'all' | 'movie' | 'tv' | 'anime';

const PAGE_SIZE = 24;

function toCardItem(m: Movie) {
  return {
    id: m.tmdbId,
    title: m.title,
    name: m.mediaType === 'tv' ? m.title : undefined,
    poster_path: m.posterPath,
    backdrop_path: m.backdropPath,
    vote_average: m.voteAverage || 0,
    media_type: m.mediaType,
    release_date: m.releaseDate,
    first_air_date: m.mediaType === 'tv' ? m.releaseDate : undefined,
    overview: m.overview || '',
    genre_ids: m.genreIds ? m.genreIds.split(',').map(Number) : [],
    original_language: m.originalLanguage,
  };
}

export default function Search() {
  const [location] = useLocation();
  const params = new URLSearchParams(location.split('?')[1] || '');
  const typeParam = params.get('type') as MediaFilter || 'all';

  const [query, setQuery] = useState("");
  const [mediaFilter, setMediaFilter] = useState<MediaFilter>(typeParam);
  const [sortBy, setSortBy] = useState<SortOption>('default');
  const [showFilters, setShowFilters] = useState(false);
  const debouncedQuery = useDebounce(query, 500);
  const sentinelRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    setMediaFilter(typeParam);
  }, [typeParam]);

  const isSearching = !!debouncedQuery || mediaFilter === 'anime';
  const searchQ = mediaFilter === 'anime' && !debouncedQuery ? 'anime' : debouncedQuery;
  const { data: tmdbResults, isLoading: tmdbLoading } = useSearch(isSearching ? searchQ : '');

  const dbType = (mediaFilter === 'movie' || mediaFilter === 'tv') ? mediaFilter : undefined;

  const {
    data: dbPages,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage,
    isLoading: dbLoading,
  } = useInfiniteQuery({
    queryKey: ['/api/movies', 'paginated', dbType],
    queryFn: async ({ pageParam = 1 }: { pageParam?: number }) => {
      const urlParams = new URLSearchParams({ page: String(pageParam), limit: String(PAGE_SIZE) });
      if (dbType) urlParams.set('type', dbType);
      const res = await fetch(`/api/movies?${urlParams}`);
      return res.json() as Promise<{ movies: Movie[]; total: number; totalPages: number; page: number }>;
    },
    getNextPageParam: (last) => last.page < last.totalPages ? last.page + 1 : undefined,
    initialPageParam: 1,
    enabled: !isSearching,
  });

  const dbAllItems = (dbPages?.pages ?? []).flatMap(p => p.movies).map(toCardItem);

  const tmdbFiltered = (tmdbResults || []).filter((r: any) => {
    if ((r.media_type as string) === 'person' || !r.poster_path) return false;
    if (mediaFilter === 'all') return true;
    if (mediaFilter === 'movie') return r.media_type === 'movie';
    if (mediaFilter === 'tv') return r.media_type === 'tv';
    if (mediaFilter === 'anime') return r.original_language === 'ja' || (r.genre_ids && r.genre_ids.includes(16));
    return true;
  });

  const applySort = (items: any[]) => [...items].sort((a: any, b: any) => {
    switch (sortBy) {
      case 'rating_high': return (b.vote_average || 0) - (a.vote_average || 0);
      case 'rating_low': return (a.vote_average || 0) - (b.vote_average || 0);
      case 'name_az': return (a.title || a.name || '').localeCompare(b.title || b.name || '');
      case 'name_za': return (b.title || b.name || '').localeCompare(a.title || a.name || '');
      case 'date_new': return (b.release_date || b.first_air_date || '').localeCompare(a.release_date || a.first_air_date || '');
      case 'date_old': return (a.release_date || a.first_air_date || '').localeCompare(b.release_date || b.first_air_date || '');
      case 'popularity': return (b.vote_average || 0) - (a.vote_average || 0);
      default: return 0;
    }
  });

  const sorted = isSearching ? applySort(tmdbFiltered) : applySort(dbAllItems);
  const totalFromDb = dbPages?.pages?.[0]?.total ?? 0;

  const handleObserver = useCallback((entries: IntersectionObserverEntry[]) => {
    if (entries[0].isIntersecting && hasNextPage && !isFetchingNextPage) {
      fetchNextPage();
    }
  }, [fetchNextPage, hasNextPage, isFetchingNextPage]);

  useEffect(() => {
    const el = sentinelRef.current;
    if (!el) return;
    const observer = new IntersectionObserver(handleObserver, { rootMargin: '300px' });
    observer.observe(el);
    return () => observer.disconnect();
  }, [handleObserver]);

  const filterButtons: { value: MediaFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'movie', label: 'Movies' },
    { value: 'tv', label: 'TV Shows' },
    { value: 'anime', label: 'Anime' },
  ];

  const sortOptions: { value: SortOption; label: string }[] = [
    { value: 'default', label: 'Default' },
    { value: 'rating_high', label: 'Rating (High to Low)' },
    { value: 'rating_low', label: 'Rating (Low to High)' },
    { value: 'name_az', label: 'Name (A-Z)' },
    { value: 'name_za', label: 'Name (Z-A)' },
    { value: 'date_new', label: 'Newest First' },
    { value: 'date_old', label: 'Oldest First' },
    { value: 'popularity', label: 'Most Popular' },
  ];

  const isLoading = isSearching ? tmdbLoading : dbLoading;

  return (
    <Shell>
      <div className="pt-24 md:pt-28 px-4 lg:px-12 max-w-7xl mx-auto min-h-screen">
        <div className="max-w-2xl mx-auto mb-8">
          <h1 className="text-2xl md:text-5xl font-display font-bold mb-4 md:mb-6 text-center">
            Find your next <span className="text-gradient">obsession</span>
          </h1>

          <div className="relative">
            <div className="flex items-center neu-pressed p-1.5 md:p-2 border border-white/5 rounded-xl">
              <SearchIcon className="w-4 h-4 md:w-5 md:h-5 text-muted-foreground ml-2 md:ml-3" />
              <input
                type="text"
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                placeholder="Search movies, tv shows..."
                className="w-full bg-transparent border-none outline-none text-white px-3 py-2 md:py-3 text-sm md:text-base placeholder:text-muted-foreground"
                autoFocus
                data-testid="input-search"
              />
              {query && (
                <button
                  onClick={() => setQuery('')}
                  className="px-3 text-muted-foreground hover:text-white"
                  data-testid="button-clear-search"
                >
                  <X className="w-4 h-4" />
                </button>
              )}
            </div>
          </div>
        </div>

        <div className="flex flex-wrap items-center gap-2 mb-6">
          <div className="flex gap-1 flex-1 overflow-x-auto hide-scrollbar">
            {filterButtons.map(f => (
              <button
                key={f.value}
                onClick={() => setMediaFilter(f.value)}
                className={`px-3 py-1.5 text-xs font-medium transition-all rounded-lg whitespace-nowrap ${
                  mediaFilter === f.value
                    ? 'bg-primary text-primary-foreground'
                    : 'neu-flat text-white/70 hover:text-white border border-white/5'
                }`}
                data-testid={`filter-${f.value}`}
              >
                {f.label}
              </button>
            ))}
          </div>

          <button
            onClick={() => setShowFilters(!showFilters)}
            className={`flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-all ${
              showFilters ? 'bg-primary text-primary-foreground' : 'neu-flat text-white/70 border border-white/5'
            }`}
            data-testid="button-toggle-sort"
          >
            <ArrowUpDown className="w-3.5 h-3.5" />
            Sort
          </button>
        </div>

        {showFilters && (
          <motion.div
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: 'auto' }}
            exit={{ opacity: 0, height: 0 }}
            className="mb-6 neu-flat p-4 border border-white/5 rounded-xl"
          >
            <h4 className="text-xs font-bold text-muted-foreground uppercase tracking-widest mb-3">Sort By</h4>
            <div className="flex flex-wrap gap-2">
              {sortOptions.map(opt => (
                <button
                  key={opt.value}
                  onClick={() => setSortBy(opt.value)}
                  className={`px-3 py-1.5 text-xs font-medium rounded-lg transition-all ${
                    sortBy === opt.value
                      ? 'bg-accent text-white'
                      : 'bg-secondary/50 text-white/60 hover:text-white border border-white/5'
                  }`}
                  data-testid={`sort-${opt.value}`}
                >
                  {opt.label}
                </button>
              ))}
            </div>
          </motion.div>
        )}

        {!isSearching && !isLoading && (
          <div className="mb-4">
            <p className="text-sm text-muted-foreground">
              Showing <span className="text-white font-medium">{sorted.length}</span>
              {totalFromDb > sorted.length && (
                <> of <span className="text-white font-medium">{totalFromDb}</span></>
              )} titles from library
            </p>
          </div>
        )}

        {isLoading && (
          <div className="flex justify-center mt-20">
            <Loader2 className="w-10 h-10 text-primary animate-spin" />
          </div>
        )}

        {!isLoading && sorted.length === 0 && (
          <div className="text-center mt-20 text-muted-foreground">
            <SearchIcon className="w-16 h-16 md:w-20 md:h-20 mx-auto mb-4" />
            <p className="text-xl font-display">
              {isSearching ? 'No results found' : 'No movies or TV shows in library yet'}
            </p>
            {!isSearching && (
              <p className="text-sm mt-2">Import content from Admin panel to see it here</p>
            )}
          </div>
        )}

        {!isLoading && sorted.length > 0 && (
          <motion.div
            layout
            className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-1.5 sm:gap-2 md:gap-3 pb-20"
          >
            {sorted.map((item: any, i: number) => (
              <MovieCard key={`${item.id}-${item.media_type}`} item={item} index={i} />
            ))}
          </motion.div>
        )}

        <div ref={sentinelRef} className="h-4" />

        {isFetchingNextPage && (
          <div className="flex justify-center py-8">
            <Loader2 className="w-8 h-8 text-primary animate-spin" />
          </div>
        )}
      </div>
    </Shell>
  );
}
