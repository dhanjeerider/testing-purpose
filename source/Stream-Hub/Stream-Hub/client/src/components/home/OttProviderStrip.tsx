import { useRef } from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { useQuery } from "@tanstack/react-query";
import { useLocation } from "wouter";
import { Loader2 } from "lucide-react";

interface TmdbProvider {
  provider_id: number;
  provider_name: string;
  logo_path: string;
  display_priority: number;
}

export function OttProviderStrip() {
  const scrollRef = useRef<HTMLDivElement>(null);
  const [, navigate] = useLocation();

  const { data, isLoading } = useQuery<{ results: TmdbProvider[] }>({
    queryKey: ['/api/ott-providers'],
    staleTime: 1000 * 60 * 60 * 6,
  });

  const scroll = (dir: 'left' | 'right') => {
    if (scrollRef.current) {
      scrollRef.current.scrollBy({ left: dir === 'left' ? -200 : 200, behavior: 'smooth' });
    }
  };

  const providers = data?.results
    ?.filter(p => p.logo_path)
    ?.sort((a, b) => a.display_priority - b.display_priority)
    ?.slice(0, 30) ?? [];

  if (isLoading) {
    return (
      <div className="px-4 lg:px-12 py-3 flex items-center gap-2" data-testid="ott-provider-strip-loading">
        <Loader2 className="w-3.5 h-3.5 animate-spin text-muted-foreground" />
        <span className="text-[10px] text-muted-foreground">Loading platforms...</span>
      </div>
    );
  }

  if (!providers.length) return null;

  return (
    <div className="px-4 lg:px-12 py-3" data-testid="ott-provider-strip">
      <div className="flex items-center gap-1.5 mb-2">
        <span className="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">Watch On</span>
        <div className="flex-1 h-px bg-white/5" />
        <button onClick={() => scroll('left')} className="p-1 text-muted-foreground hover:text-white transition-colors" data-testid="ott-scroll-left">
          <ChevronLeft className="w-3.5 h-3.5" />
        </button>
        <button onClick={() => scroll('right')} className="p-1 text-muted-foreground hover:text-white transition-colors" data-testid="ott-scroll-right">
          <ChevronRight className="w-3.5 h-3.5" />
        </button>
      </div>

      <div
        ref={scrollRef}
        className="flex gap-2 overflow-x-auto pb-1"
        style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
      >
        {providers.map((p) => (
          <button
            key={p.provider_id}
            onClick={() => navigate(`/provider/${p.provider_id}?name=${encodeURIComponent(p.provider_name)}&logo=${encodeURIComponent(p.logo_path)}`)}
            className="flex flex-col items-center gap-1 shrink-0 group"
            data-testid={`ott-provider-${p.provider_id}`}
            title={p.provider_name}
          >
            <div className="w-10 h-10 rounded-lg overflow-hidden border border-white/10 group-hover:border-primary/50 group-hover:scale-105 transition-all duration-200">
              <img
                src={`https://image.tmdb.org/t/p/w45${p.logo_path}`}
                alt={p.provider_name}
                className="w-full h-full object-cover"
              />
            </div>
            <span className="text-[9px] text-muted-foreground/60 group-hover:text-white/70 transition-colors text-center leading-tight max-w-[42px] truncate">
              {p.provider_name.split(' ')[0]}
            </span>
          </button>
        ))}
      </div>
    </div>
  );
}
