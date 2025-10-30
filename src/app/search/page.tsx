'use client';

import { Suspense } from 'react';
import { SearchPageContent } from '@/components/search/search-page-content';
import { Skeleton } from '@/components/ui/skeleton';

function SearchPageSkeleton() {
  return (
    <div className="py-8 animate-fade-in">
      <div className="mb-8 px-4 md:px-8">
        <Skeleton className="h-10 w-full" />
      </div>

      <Skeleton className="h-8 w-64 mb-8 px-4 md:px-8" />
      
      <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 px-4 md:px-8">
        {Array.from({ length: 18 }).map((_, index) => (
          <div key={index} className="space-y-2">
            <Skeleton className="aspect-[2/3] w-full rounded-xl" />
            <Skeleton className="h-4 w-3/4" />
            <Skeleton className="h-4 w-1/2" />
          </div>
        ))}
      </div>
    </div>
  )
}

export default function SearchPage() {
  return (
    <Suspense fallback={<SearchPageSkeleton />}>
      <SearchPageContent />
    </Suspense>
  );
}
