export interface PaginatedResponse<T> {
  page: number;
  results: T[];
  total_pages: number;
  total_results: number;
}

interface BaseContent {
  id: number;
  adult: boolean;
  backdrop_path: string | null;
  poster_path: string | null;
  overview: string;
  popularity: number;
  vote_average: number;
  vote_count: number;
  genres?: Genre[]; // Optional because it might not be in all list responses
}

export interface Movie extends BaseContent {
  title: string;
  original_title: string;
  release_date: string;
  video: boolean;
  media_type?: 'movie';
  runtime?: number;
  imdb_id?: string;
}

export interface TV extends BaseContent {
  name: string;
  original_name: string;
  first_air_date: string;
  origin_country: string[];
  media_type?: 'tv';
}

export type KnownFor = (Movie & { media_type: 'movie' }) | (TV & { media_type: 'tv' });

export interface Person {
  id: number;
  name: string;
  profile_path: string | null;
  adult: boolean;
  popularity: number;
  known_for_department: string;
  biography?: string;
  birthday?: string;
  place_of_birth?: string;
  combined_credits?: CombinedCredits;
  media_type?: 'person';
  images?: {
    profiles: Image[];
  };
}

export interface CombinedCredits {
    cast: KnownFor[];
    crew: (KnownFor & { job: string; department: string })[];
}

export type TrendingResult = Movie | TV | Person;

export interface Genre {
  id: number;
  name: string;
}

export interface Video {
  id: string;
  iso_639_1: string;
  iso_3166_1: string;
  key: string;
  name: string;
  site: string;
  size: number;
  type: string;
}

export interface CastMember {
  id: number;
  name: string;
  profile_path: string | null;
  character: string;
  credit_id: string;
}

export interface CrewMember {
  id: number;
  name: string;
  profile_path: string | null;
  job: string;
  department: string;
  credit_id: string;
}

export interface Review {
  author: string;
  author_details: {
    name: string;
    username: string;
    avatar_path: string | null;
    rating: number | null;
  };
  content: string;
  created_at: string;
  id: string;
  updated_at: string;
  url: string;
}

interface ProviderInfo {
  logo_path: string;
  provider_id: number;
  provider_name: string;
  display_priority: number;
}

export interface WatchProvider extends ProviderInfo {}

interface WatchProviders {
  link: string;
  flatrate?: ProviderInfo[];
  rent?: ProviderInfo[];
  buy?: ProviderInfo[];
}

export interface Image {
  aspect_ratio: number;
  height: number;
  iso_639_1: string | null;
  file_path: string;
  vote_average: number;
  vote_count: number;
  width: number;
}

interface BaseDetails extends BaseContent {
  tagline: string;
  homepage: string;
  status: string;
  genres: Genre[];
  videos: { results: Video[] };
  credits: { cast: CastMember[]; crew: CrewMember[] };
  images: { backdrops: Image[]; posters: Image[]; logos: Image[] };
  recommendations: PaginatedResponse<Movie | TV>;
  similar: PaginatedResponse<Movie | TV>;
  reviews: PaginatedResponse<Review>;
  "watch/providers": {
    results: {
      [countryCode: string]: WatchProviders;
    };
  };
}

export interface MovieDetails extends BaseDetails, Movie {
  runtime: number;
  release_date: string;
  imdb_id: string | null;
}

export interface Season {
  air_date: string;
  episode_count: number;
  id: number;
  name: string;
  overview: string;
  poster_path: string;
  season_number: number;
}

export interface Episode {
  air_date: string;
  episode_number: number;
  id: number;
  name: string;
  overview: string;
  production_code: string;
  season_number: number;
  still_path: string | null;
  vote_average: number;
  vote_count: number;
  runtime?: number;
}

export interface SeasonDetails extends Season {
  episodes: Episode[];
}


export interface TVDetails extends BaseDetails, TV {
  number_of_episodes: number;
  number_of_seasons: number;
  episode_run_time: number[];
  seasons: Season[];
  last_air_date: string;
  content_ratings: {
    results: { iso_3166_1: string; rating: string }[];
  };
  original_language: string;
  external_ids?: {
    imdb_id: string | null;
  };
}


export interface DiscoverParams {
  with_genres?: string;
  page?: string | number;
  sort_by?: string;
  with_keywords?: string;
  with_original_language?: string;
  with_watch_providers?: string;
  primary_release_year?: number;
  first_air_date_year?: number;
}
