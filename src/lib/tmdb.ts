import type {
  Movie,
  MovieDetails,
  PaginatedResponse,
  TV,
  TVDetails,
  Person,
  TrendingResult,
  SeasonDetails,
  Genre,
  DiscoverParams,
  CombinedCredits,
  WatchProvider
} from "@/types/tmdb";

const API_KEY = process.env.TMDB_API_KEY || "fed86956458f19fb45cdd382b6e6de83";
const BASE_URL = "https://api.themoviedb.org/3";

async function fetchFromTMDB<T>(
  endpoint: string,
  params: Record<string, string | number | string[] | undefined> = {}
): Promise<T> {
  if (!API_KEY) {
    console.error("TMDB_API_KEY is not set.");
    return { results: [], total_pages: 0, total_results: 0, page: 1 } as unknown as T;
  }

  const url = new URL(`${BASE_URL}/${endpoint}`);
  url.searchParams.append("api_key", API_KEY);
  url.searchParams.append("language", "hi-IN"); // Hindi results
  url.searchParams.append("region", "IN"); // Indian region

  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null || value === "") return;
    if (Array.isArray(value)) {
      value.forEach((v) => url.searchParams.append(key, String(v)));
    } else {
      url.searchParams.append(key, String(value));
    }
  });

  try {
    const response = await fetch(url.toString(), {
      ...(typeof window === "undefined" ? { next: { revalidate: 3600 } } : {}),
    });

    if (!response.ok) {
      const text = await response.text();
      let errorMessage = response.statusText;

      try {
        const errJson = JSON.parse(text);
        errorMessage = errJson.status_message || errorMessage;
      } catch {
        // HTML or plain text response
        errorMessage = text.slice(0, 150);
      }

      console.error(`❌ TMDB API Error [${response.status}] on ${endpoint}: ${errorMessage}`);
      return { results: [], total_pages: 0, total_results: 0, page: 1 } as unknown as T;
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error(`⚠️ Network or JSON parse error on TMDB endpoint: ${endpoint}`, error);
    return { results: [], total_pages: 0, total_results: 0, page: 1 } as unknown as T;
  }
}

type ImageSize = "w300" | "w500" | "w780" | "w1280" | "original";

export function getImageUrl(path: string | null | undefined, size: ImageSize = "w300") {
  if (!path) return null;
  return `https://image.tmdb.org/t/p/${size}${path}`;
}

// 🎬 MOVIES
export const getNowPlayingMovies = async () =>
  await fetchFromTMDB<PaginatedResponse<Movie>>("movie/now_playing");

export const getUpcomingMovies = async () =>
  await fetchFromTMDB<PaginatedResponse<Movie>>("movie/upcoming");

export const getMovieDetails = async (id: number | string) =>
  await fetchFromTMDB<MovieDetails>(`movie/${id}`, {
    append_to_response:
      "videos,credits,images,recommendations,similar,watch/providers,reviews",
  });

export const getMovieGenres = async () => {
  const data = await fetchFromTMDB<{ genres: Genre[] }>("genre/movie/list");
  return data?.genres;
};

export const getMovieRecommendations = async (id: string | number, page: number = 1) => {
  const data = await fetchFromTMDB<PaginatedResponse<Movie>>(
    `movie/${id}/recommendations`,
    { page }
  );
  return data.results;
};

export const getMovieWatchProviders = async () =>
  await fetchFromTMDB<{ results: WatchProvider[] }>("watch/providers/movie", {
    watch_region: "US",
  });

// 📺 TV SHOWS
export const getAiringTodayTv = async () =>
  await fetchFromTMDB<PaginatedResponse<TV>>("tv/airing_today");

export const getOnTheAirTv = async () =>
  await fetchFromTMDB<PaginatedResponse<TV>>("tv/on_the_air");

export const getTvDetails = async (id: number | string) =>
  await fetchFromTMDB<TVDetails>(`tv/${id}`, {
    append_to_response:
      "videos,credits,images,recommendations,similar,watch/providers,reviews,content_ratings",
  });

export const getSeasonDetails = async (tvId: number | string, seasonNumber: number) =>
  await fetchFromTMDB<SeasonDetails>(`tv/${tvId}/season/${seasonNumber}`);

export const getTvGenres = async () => {
  const data = await fetchFromTMDB<{ genres: Genre[] }>("genre/tv/list");
  return data?.genres;
};

export const getTvRecommendations = async (id: string | number, page: number = 1) => {
  const data = await fetchFromTMDB<PaginatedResponse<TV>>(`tv/${id}/recommendations`, {
    page,
  });
  return data.results;
};

export const getTvWatchProviders = async () =>
  await fetchFromTMDB<{ results: WatchProvider[] }>("watch/providers/tv", {
    watch_region: "US",
  });

// 🏷️ GENRES
export const getGenreName = async (id: string, type: "movie" | "tv") => {
  const genres = type === "movie" ? await getMovieGenres() : await getTvGenres();
  const genre = genres?.find((g) => g.id.toString() === id);
  return genre ? genre.name : null;
};

// ⭐ POPULAR / TRENDING / SEARCH
export const getPopular = async (type: "movie" | "tv", page: number = 1) =>
  await fetchFromTMDB<PaginatedResponse<Movie | TV>>(`${type}/popular`, { page });

export const getTopRated = async (type: "movie" | "tv") =>
  await fetchFromTMDB<PaginatedResponse<Movie | TV>>(`${type}/top_rated`);

export const getTrending = async (
  mediaType: "all" | "movie" | "tv" | "person",
  timeWindow: "day" | "week" = "week"
) =>
  await fetchFromTMDB<PaginatedResponse<TrendingResult>>(
    `trending/${mediaType}/${timeWindow}`
  );

export const getPersonDetails = async (id: number | string) =>
  await fetchFromTMDB<Person>(`person/${id}`, {
    append_to_response: "combined_credits,images",
  });

export const getPersonCombinedCredits = async (id: number | string) =>
  await fetchFromTMDB<CombinedCredits>(`person/${id}/combined_credits`);

export const searchMulti = async (query: string, page: number = 1) => {
  const data = await fetchFromTMDB<PaginatedResponse<Movie | TV | Person>>("search/multi", {
    query,
    page,
  });
  return data;
};

// 🔍 DISCOVER
export const discover = async (type: "movie" | "tv", params: DiscoverParams) => {
  const data = await fetchFromTMDB<PaginatedResponse<Movie | TV>>(`discover/${type}`, params);
  return data;
};

// 🌐 LANGUAGES
export const getLanguages = async () =>
  await fetchFromTMDB<{ iso_639_1: string; english_name: string; name: string }[]>(
    "configuration/languages"
  );
