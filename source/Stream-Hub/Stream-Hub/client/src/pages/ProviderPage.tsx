import { useState, useEffect, useRef, useCallback } from "react";
import { useParams, useSearch } from "wouter";
import { Shell } from "@/components/layout/Shell";
import { MovieCard } from "@/components/shared/MovieCard";
import { Loader2, Tv2, Film } from "lucide-react";
import { TMDBItem, getImageUrl } from "@/lib/tmdb";

type MediaTab = 'movie' | 'tv';

export default function ProviderPage() {
  const { id } = useParams<{ id: string }>();
  const search = useSearch();
  const params = new URLSearchParams(search);
  const providerName = params.get('name') || 'Provider';
  const logoPath = params.get('logo') || '';

  const [tab, setTab] = useState<MediaTab>('movie');
  const [items, setItems] = useState<TMDBItem[]>([]);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(false);
  const [initialLoad, setInitialLoad] = useState(true);
  const sentinel = useRef<HTMLDivElement>(null);

  const fetchPage = useCallback(async (pageNum: number, mediaType: MediaTab, reset = false) => {
    setLoading(true);
    try {
      const res = await fetch(`/api/discover/${mediaType}/provider/${id}?page=${pageNum}`);
      const data = await res.json();
      const results: TMDBItem[] = (data.results || []).map((r: any) => ({
        ...r,
        media_type: mediaType,
      }));
      setTotalPages(data.total_pages || 1);
      setItems(prev => reset ? results : [...prev, ...results]);
    } catch {}
    setLoading(false);
    setInitialLoad(false);
  }, [id]);

  useEffect(() => {
    setItems([]);
    setPage(1);
    setTotalPages(1);
    setInitialLoad(true);
    fetchPage(1, tab, true);
  }, [tab, fetchPage]);

  useEffect(() => {
    if (page === 1) return;
    fetchPage(page, tab);
  }, [page, fetchPage, tab]);

  useEffect(() => {
    const obs = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting && !loading && page < totalPages) {
        setPage(p => p + 1);
      }
    }, { rootMargin: '300px' });
    if (sentinel.current) obs.observe(sentinel.current);
    return () => obs.disconnect();
  }, [loading, page, totalPages]);

  return (
    <Shell>
      <div className="pb-20">
        <div className="relative overflow-hidden">
          <div className="px-4 lg:px-12 pt-20 md:pt-24 pb-6">
            <div className="flex items-center gap-3 mb-1">
              {logoPath && (
                <div className="w-12 h-12 rounded-xl overflow-hidden border border-white/20 shrink-0">
                  <img
                    src={`https://image.tmdb.org/t/p/w92${logoPath}`}
                    alt={providerName}
                    className="w-full h-full object-cover"
                  />
                </div>
              )}
              <div>
                <h1 className="text-xl md:text-2xl font-display font-bold text-white" data-testid="text-provider-name">
                  {providerName}
                </h1>
                <p className="text-xs text-muted-foreground">Available in India</p>
              </div>
            </div>
          </div>
        </div>

        <div className="px-4 lg:px-12">
          <div className="flex gap-1 mb-5 border-b border-white/8">
            {(['movie', 'tv'] as MediaTab[]).map(t => (
              <button
                key={t}
                onClick={() => setTab(t)}
                className={`flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px ${
                  tab === t ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-white'
                }`}
                data-testid={`tab-${t}`}
              >
                {t === 'movie' ? <Film className="w-3.5 h-3.5" /> : <Tv2 className="w-3.5 h-3.5" />}
                {t === 'movie' ? 'Movies' : 'TV Shows'}
              </button>
            ))}
          </div>

          {initialLoad ? (
            <div className="flex items-center justify-center py-20">
              <Loader2 className="w-6 h-6 text-primary animate-spin" />
            </div>
          ) : items.length === 0 ? (
            <div className="text-center py-20 text-muted-foreground">
              <p className="text-sm">No {tab === 'movie' ? 'movies' : 'TV shows'} found for this provider in India.</p>
            </div>
          ) : (
            <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-2 md:gap-3">
              {items.map((item, i) => (
                <MovieCard key={`${item.id}-${i}`} item={item} index={i} />
              ))}
            </div>
          )}

          <div ref={sentinel} className="py-4 flex justify-center">
            {loading && !initialLoad && <Loader2 className="w-5 h-5 text-primary animate-spin" />}
          </div>
        </div>
      </div>
    </Shell>
  );
}
