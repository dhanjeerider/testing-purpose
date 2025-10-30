// /app/discover/tv/page.tsx
import { Suspense } from "react";
import { DiscoverTvContent } from "@/components/discover/discover-tv-content";
import { Skeleton } from "@/components/ui/skeleton";
import { getPopular } from "@/lib/tmdb";
import { TV } from "@/types/tmdb";

function DiscoverSkeleton() {
  return (
    <div className="flex flex-col gap-8 py-8 animate-fade-in">
      <div className="px-4 md:px-8">
        <h1 className="text-2xl font-bold mb-4">Discover TV Shows</h1>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {Array.from({ length: 4 }).map((_, i) => (
            <Skeleton key={i} className="h-10 w-full" />
          ))}
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

export default async function TvShowsPage() {
  const popularTv = await getPopular("tv");

  return (
    <Suspense fallback={<DiscoverSkeleton />}>
      <DiscoverTvContent initialItems={popularTv.results as TV[]} />
    </Suspense>
  );
}
