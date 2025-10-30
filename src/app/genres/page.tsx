'use client';

import { getMovieGenres, getTvGenres, discover } from '@/lib/tmdb';
import { GenreCarousel } from '@/components/genres/genre-carousel';
import { useEffect, useState } from 'react';
import { Skeleton } from '@/components/ui/skeleton';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import type { Genre, Movie, TV } from '@/types/tmdb';

interface GenreWithContent extends Genre {
  content: (Movie | TV)[];
}

function GenreCarouselSkeleton() {
  return (
    <div className="space-y-4">
      <Skeleton className="h-7 w-48" />
      <div className="flex gap-4 overflow-x-auto pb-4">
        {Array.from({ length: 6 }).map((_, i) => (
          <div key={i} className="w-48 flex-shrink-0 space-y-2">
            <Skeleton className="aspect-[2/3] w-full rounded-xl" />
            <Skeleton className="h-4 w-3/4" />
            <Skeleton className="h-4 w-1/2" />
          </div>
        ))}
      </div>
    </div>
  );
}

export default function GenresPage() {
  const [movieGenres, setMovieGenres] = useState<GenreWithContent[]>([]);
  const [tvGenres, setTvGenres] = useState<GenreWithContent[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchGenresAndContent() {
      setLoading(true);
      const [movieGenreList, tvGenreList] = await Promise.all([
        getMovieGenres(),
        getTvGenres(),
      ]);

      const fetchContentForGenres = async (genres: Genre[], type: 'movie' | 'tv'): Promise<GenreWithContent[]> => {
        return Promise.all(
          genres.map(async (genre) => {
            const data = await discover(type, { with_genres: String(genre.id) });
            return {
              ...genre,
              content: data.results.slice(0, 10), // Limit to 10 items for the carousel
            };
          })
        );
      };

      const [movieGenresWithContent, tvGenresWithContent] = await Promise.all([
        fetchContentForGenres(movieGenreList, 'movie'),
        fetchContentForGenres(tvGenreList, 'tv'),
      ]);

      setMovieGenres(movieGenresWithContent);
      setTvGenres(tvGenresWithContent);
      setLoading(false);
    }
    fetchGenresAndContent();
  }, []);

  return (
    <div className="container mx-auto py-8 md:py-16 flex flex-col gap-8 md:gap-16 animate-fade-in">
      <Tabs defaultValue="movie" className="w-full">
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8 px-4 md:px-8">
            <h1 className="text-2xl font-bold">Genres</h1>
            <TabsList className="grid w-full grid-cols-2 md:w-[200px]">
                <TabsTrigger value="movie">Movies</TabsTrigger>
                <TabsTrigger value="tv">TV Shows</TabsTrigger>
            </TabsList>
        </div>

        <TabsContent value="movie">
          <div className="flex flex-col gap-8 md:gap-16">
            {loading ? (
                Array.from({ length: 5 }).map((_, i) => <GenreCarouselSkeleton key={i} />)
            ) : (
                movieGenres.map((genre) => (
                    <GenreCarousel key={`movie-${genre.id}`} genre={genre} type="movie" items={genre.content} />
                ))
            )}
          </div>
        </TabsContent>
        <TabsContent value="tv">
          <div className="flex flex-col gap-8 md:gap-16">
            {loading ? (
                Array.from({ length: 5 }).map((_, i) => <GenreCarouselSkeleton key={i} />)
            ) : (
                tvGenres.map((genre) => (
                    <GenreCarousel key={`tv-${genre.id}`} genre={genre} type="tv" items={genre.content} />
                ))
            )}
          </div>
        </TabsContent>
      </Tabs>
    </div>
  );
}
