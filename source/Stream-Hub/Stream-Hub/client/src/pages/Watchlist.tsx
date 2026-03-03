import { Shell } from "@/components/layout/Shell";
import { useWatchlist, useRemoveFromWatchlist } from "@/hooks/use-watchlist";
import { MovieCard } from "@/components/shared/MovieCard";
import { TMDBItem } from "@/lib/tmdb";
import { Loader2, Trash2, Bookmark } from "lucide-react";

export default function Watchlist() {
  const { data: watchlist, isLoading } = useWatchlist();
  const removeMutation = useRemoveFromWatchlist();

  if (isLoading) {
    return (
      <Shell>
        <div className="h-screen flex items-center justify-center">
          <Loader2 className="w-12 h-12 text-primary animate-spin" />
        </div>
      </Shell>
    );
  }

  return (
    <Shell>
      <div className="pt-28 px-6 lg:px-12 max-w-7xl mx-auto min-h-screen pb-20">
        <div className="flex items-center gap-4 mb-10 border-b border-white/10 pb-6">
          <div className="w-12 h-12 bg-primary/20 flex items-center justify-center text-primary">
            <Bookmark className="w-6 h-6 fill-current" />
          </div>
          <div>
            <h1 className="text-3xl md:text-4xl font-display font-bold">My Watchlist</h1>
            <p className="text-muted-foreground mt-1">{watchlist?.length || 0} items saved</p>
          </div>
        </div>

        {watchlist?.length === 0 ? (
          <div className="text-center mt-20 opacity-50 neu-flat p-12 border border-white/5">
            <Bookmark className="w-20 h-20 mx-auto mb-4 text-muted-foreground" />
            <p className="text-xl font-display">Your watchlist is empty.</p>
            <p className="mt-2 text-sm">Save movies and shows here to watch later.</p>
          </div>
        ) : (
          <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-1.5 sm:gap-2 md:gap-3">
            {watchlist?.map((item, i) => {
              // Convert WatchlistItem back to TMDBItem shape for the card
              const tmdbMock: TMDBItem = {
                id: item.tmdbId,
                title: item.type === 'movie' ? item.title : undefined,
                name: item.type === 'tv' ? item.title : undefined,
                media_type: item.type as 'movie' | 'tv',
                poster_path: item.posterPath,
                overview: "",
                backdrop_path: null,
                vote_average: 0
              };

              return (
                <div key={item.id} className="relative group">
                  <MovieCard item={tmdbMock} index={i} />
                  <button
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      removeMutation.mutate(item.id);
                    }}
                    disabled={removeMutation.isPending}
                    className="absolute top-2 right-2 p-2 bg-black/80 text-destructive hover:bg-destructive hover:text-white transition-colors z-10 opacity-0 group-hover:opacity-100"
                    title="Remove from watchlist"
                  >
                    <Trash2 className="w-4 h-4" />
                  </button>
                </div>
              );
            })}
          </div>
        )}
      </div>
    </Shell>
  );
}
