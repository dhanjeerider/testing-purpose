'use client';

import { useState, useEffect, useCallback } from 'react';
import { Movie, TV, Person } from '@/types/tmdb';
import { useInView } from 'react-intersection-observer';
import { ContentCard } from './content-card';
import { PersonCard } from './person-card';
import { Skeleton } from '../ui/skeleton';
import { Button } from '../ui/button';
import { cn } from '@/lib/utils';

type ContentItem = Movie | TV | Person;

interface InfiniteContentGridProps {
  initialItems: ContentItem[];
  totalPages: number;
  fetcher: (page: number) => Promise<ContentItem[]>;
  contentType?: 'movie' | 'tv';
  showLoadMoreButton?: boolean;
  className?: string;
}

export function InfiniteContentGrid({ initialItems, totalPages, fetcher, contentType, showLoadMoreButton = false, className }: InfiniteContentGridProps) {
  const [items, setItems] = useState(initialItems);
  const [page, setPage] = useState(1);
  const [isLoading, setIsLoading] = useState(false);
  const { ref, inView } = useInView({ threshold: 0.5 });

  const loadMore = useCallback(async () => {
    if (isLoading || page >= totalPages) return;
    setIsLoading(true);
    const nextPage = page + 1;
    try {
      const newItems = await fetcher(nextPage);
      if (Array.isArray(newItems)) {
        setItems((prev) => [...prev, ...newItems]);
      }
      setPage(nextPage);
    } catch (error) {
      console.error("Failed to fetch more items", error);
    } finally {
      setIsLoading(false);
    }
  }, [isLoading, page, totalPages, fetcher]);

  useEffect(() => {
    if (inView && !showLoadMoreButton) {
      loadMore();
    }
  }, [inView, loadMore, showLoadMoreButton]);
  
  useEffect(() => {
    setItems(initialItems);
    setPage(1);
  }, [initialItems]);


  return (
    <>
      <div className={cn("content-grid", className)}>
        {items.map((item) => {
          if (item.media_type === 'person' || 'known_for_department' in item) {
            return <PersonCard key={`person-${item.id}`} person={item as Person} />;
          }
          const type = contentType || item.media_type;
          if (!type || (type !== 'movie' && type !== 'tv')) return null;
          return <ContentCard key={`${type}-${item.id}`} item={item as Movie | TV} type={type} />;
        })}
      </div>

      {isLoading && (
        <div className={cn("content-grid w-full mt-4", className)}>
          {Array.from({ length: 6 }).map((_, index) => (
              <div key={index} className="space-y-2">
                  <Skeleton className="aspect-[2/3] w-full rounded-xl" />
                  <Skeleton className="h-4 w-3/4" />
                  <Skeleton className="h-4 w-1/2" />
              </div>
          ))}
        </div>
      )}

      {page < totalPages && (
        <div ref={ref} className="flex justify-center items-center py-8">
          {showLoadMoreButton ? (
            <Button onClick={loadMore} disabled={isLoading}>
              {isLoading ? 'Loading...' : 'Load More'}
            </Button>
          ) : (
            // The intersection observer target is still here, but invisible.
            // Skeletons are shown above when isLoading is true.
            <div />
          )}
        </div>
      )}
    </>
  );
}
