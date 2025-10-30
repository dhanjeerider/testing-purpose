import { getPopular, getMovieDetails, getTvDetails } from '@/lib/tmdb';
import { MovieDetails, TVDetails } from '@/types/tmdb';
import { TrendingCarouselClient } from './trending-carousel-client';

export default async function TrendingCarousel() {
  let trendingItemsDetails: (MovieDetails | TVDetails | null)[] = [];
  let trendingItems: { id: string; type: 'movie' | 'tv'; reason: string; }[] = [];

  try {
    const popularMovies = await getPopular('movie');
    trendingItems = popularMovies.results.slice(0, 10).map(movie => ({
      id: String(movie.id),
      type: 'movie' as const,
      reason: 'Popular',
    }));
  } catch (error) {
    console.error("Failed to fetch popular movies for trending carousel. Error:", error);
  }

  if (!Array.isArray(trendingItems)) {
      console.error("Fallback failed, no trending items to display.");
      trendingItems = [];
  }

  try {
    trendingItemsDetails = await Promise.all(
      (trendingItems).slice(0, 10).map(async (item) => {
        try {
          const details = item.type === 'movie'
            ? await getMovieDetails(item.id)
            : await getTvDetails(item.id);
          return { ...details, reason: item.reason };
        } catch (error) {
          console.error(`Failed to fetch details for trending ${item.type} ${item.id}`, error);
          return null;
        }
      })
    );
  } catch(e) {
    console.error("Failed to fetch details for trending content", e);
    return null;
  }
    
  const validItems = trendingItemsDetails.filter(Boolean) as (MovieDetails | TVDetails & { reason: string })[];

  if (validItems.length === 0) {
    return null;
  }

  return <TrendingCarouselClient items={validItems} />;
}
