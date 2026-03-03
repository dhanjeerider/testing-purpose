import { useQuery } from "@tanstack/react-query";
import { api, buildUrl } from "@shared/routes";
import { TMDBItem, TMDBDetails, TMDBEpisode } from "@/lib/tmdb";

// Fetches trending movies/tv from proxy
export function useTrending(type: 'all' | 'movie' | 'tv' = 'all') {
  return useQuery({
    queryKey: [api.tmdb.trending.path, type],
    queryFn: async () => {
      const res = await fetch(`${api.tmdb.trending.path}?type=${type}`);
      if (!res.ok) throw new Error("Failed to fetch trending");
      const data = await res.json();
      // Ensure media_type is set for 'movie' or 'tv' specific requests
      const results = data.results?.map((item: TMDBItem) => ({
        ...item,
        media_type: item.media_type || (type === 'all' ? 'movie' : type)
      })) || [];
      return results as TMDBItem[];
    }
  });
}

// Fetches search results from proxy
export function useSearch(query: string) {
  return useQuery({
    queryKey: [api.tmdb.search.path, query],
    queryFn: async () => {
      if (!query.trim()) return [];
      const res = await fetch(`${api.tmdb.search.path}?q=${encodeURIComponent(query)}`);
      if (!res.ok) throw new Error("Failed to search");
      const data = await res.json();
      return (data.results || []) as TMDBItem[];
    },
    enabled: query.length > 1
  });
}

// Fetches full details for a movie or tv show
export function useDetails(type: 'movie' | 'tv', id: number) {
  return useQuery({
    queryKey: [api.tmdb.details.path, type, id],
    queryFn: async () => {
      const url = buildUrl(api.tmdb.details.path, { type, id });
      const res = await fetch(url);
      if (!res.ok) throw new Error("Failed to fetch details");
      const data = await res.json();
      return data as TMDBDetails;
    },
    enabled: !!id && !!type
  });
}

// Fetches season details (episodes) for a TV show
export function useSeason(tvId: number, seasonNumber: number) {
  return useQuery({
    queryKey: [api.tmdb.season.path, tvId, seasonNumber],
    queryFn: async () => {
      const url = buildUrl(api.tmdb.season.path, { id: tvId, seasonNumber });
      const res = await fetch(url);
      if (!res.ok) throw new Error("Failed to fetch season details");
      const data = await res.json();
      return (data.episodes || []) as TMDBEpisode[];
    },
    enabled: !!tvId && seasonNumber >= 0
  });
}
