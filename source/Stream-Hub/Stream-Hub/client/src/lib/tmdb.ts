// Helper functions and types for TMDB data returned by our proxy

export const getImageUrl = (path: string | undefined | null, size: string = 'w342') => {
  if (!path) return 'https://via.placeholder.com/500x750?text=No+Image';
  return `https://image.tmdb.org/t/p/${size}${path}`;
};

export interface TMDBItem {
  id: number;
  title?: string; // Movies
  name?: string;  // TV Shows
  overview: string;
  poster_path: string | null;
  backdrop_path: string | null;
  media_type?: 'movie' | 'tv';
  vote_average: number;
  release_date?: string; // Movies
  first_air_date?: string; // TV Shows
  genre_ids?: number[];
  original_language?: string;
}

export interface TMDBDetails extends TMDBItem {
  genres: { id: number; name: string }[];
  runtime?: number;
  episode_run_time?: number[];
  number_of_seasons?: number;
  vote_count?: number;
  popularity?: number;
  production_companies?: { id: number; name: string; logo_path: string | null }[];
  seasons?: {
    air_date: string;
    episode_count: number;
    id: number;
    name: string;
    overview: string;
    poster_path: string;
    season_number: number;
  }[];
  credits?: {
    cast: { id: number; name: string; character: string; profile_path: string | null }[];
    crew: { id: number; name: string; job: string; profile_path: string | null }[];
  };
  videos?: {
    results: { id: string; key: string; name: string; site: string; type: string }[];
  };
  similar?: {
    results: TMDBItem[];
  };
}

export interface TMDBEpisode {
  air_date: string;
  episode_number: number;
  id: number;
  name: string;
  overview: string;
  still_path: string | null;
  vote_average: number;
}
