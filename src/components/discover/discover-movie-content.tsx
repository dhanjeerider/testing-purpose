'use client'

import { Movie } from '@/types/tmdb';
import { useSearchParams } from 'next/navigation';
import { useCallback, useMemo } from 'react';
import { DiscoverFilters } from '@/components/discover/discover-filters';
import { InfiniteContentGrid } from '@/components/common/infinite-content-grid';
import { discover } from '@/lib/tmdb';

interface DiscoverMovieContentProps {
  initialItems: Movie[];
}

export function DiscoverMovieContent({ initialItems }: DiscoverMovieContentProps) {
  const searchParams = useSearchParams();

  const isFiltered = useMemo(() => {
    return searchParams.has('sort_by') || 
           searchParams.has('with_genres') || 
           searchParams.has('primary_release_year') || 
           searchParams.has('with_original_language');
  }, [searchParams]);

  const discoverParams = useMemo(() => {
    const params: any = {
      sort_by: searchParams.get('sort_by') || 'release_date.desc',
    };
    const genres = searchParams.get('with_genres');
    const year = searchParams.get('primary_release_year');
    const language = searchParams.get('with_original_language');
    
    if (genres && genres !== 'all') params.with_genres = genres;
    if (year && year !== 'all') params.primary_release_year = Number(year);
    if (language && language !== 'all') params.with_original_language = language;
    
    return params;
  }, [searchParams]);

  const fetcher = useCallback(async (page: number) => {
    const movies = await discover('movie', { ...discoverParams, page });
    return movies.results as Movie[];
  }, [discoverParams]);
  
  const key = useMemo(() => {
    return `movie-${discoverParams.sort_by}-${discoverParams.with_genres}-${discoverParams.primary_release_year}-${discoverParams.with_original_language}`;
  }, [discoverParams]);

  return (
    <div className="flex flex-col gap-8 py-8 animate-fade-in">
      <div className="px-4 md:px-8">
        <h1 className="text-2xl font-bold mb-4">
          Discover Movies
        </h1>
        <DiscoverFilters type="movie" />
      </div>

      <InfiniteContentGrid
        key={key}
        initialItems={isFiltered ? [] : initialItems}
        totalPages={500} // TMDB's max is 500
        fetcher={fetcher}
        contentType="movie"
        className="px-4 md:px-8"
      />
    </div>
  );
}
