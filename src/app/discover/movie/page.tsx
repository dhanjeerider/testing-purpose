import { Suspense } from 'react';
import { DiscoverMovieContent } from '@/components/discover/discover-movie-content';
import { Skeleton } from '@/components/ui/skeleton';
import { getPopular } from '@/lib/tmdb';
import { Movie } from '@/types/tmdb';

function DiscoverSkeleton() {
  return (
    <div className="flex flex-col gap-8 py-8 animate-fade-in">
      <div className="px-4 md:px-8">
        <h1 className="text-2xl font-bold mb-4">
          Discover Movies
        </h1>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Skeleton className="h-10 w-full" />
          <Skeleton className="h-10 w-full" />
          <Skeleton className="h-10 w-full" />
          <Skeleton className="h-10 w-full" />
        </div>
      </div>
      <div className="content-grid px-4 md:px-8">
        {Array.from({ length: 18 }).map((_, index) => (
          <div key={index} className="space-y-2">
            <Skeleton className="aspect-[2/3] w-full rounded-xl" />
            <Skeleton className="h-4 w-3/4" />
            <Skeleton className="h-4 w-1/2" />
          </div>
        ))}
      </div>
    </div>
  );
}

export default async function MoviesPage() {
  const popularMovies = await getPopular('movie');

  return (
    <Suspense fallback={<DiscoverSkeleton />}>
      <DiscoverMovieContent initialItems={popularMovies.results as Movie[]} />
    </Suspense>
  );
}
