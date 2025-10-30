'use client';

import { discover, getGenreName } from '@/lib/tmdb';
import { notFound, useSearchParams, useRouter, usePathname } from 'next/navigation';
import { InfiniteContentGrid } from '@/components/common/infinite-content-grid';
import { useEffect, useState, useCallback, useMemo, Suspense, use } from 'react';
import { Movie, TV } from '@/types/tmdb';
import { Skeleton } from '@/components/ui/skeleton';
import { DiscoverFilters } from '@/components/discover/discover-filters';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';

type ContentItem = Movie | TV;

interface GenrePageProps {
  params: { id: string };
}

function GenrePageContent({ params: routeParams }: GenrePageProps) {
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const { id } = routeParams;

  const type = searchParams.get('type') || 'movie';
  const sortBy = searchParams.get('sort_by') || 'popularity.desc';
  const withKeywords = searchParams.get('with_keywords');
  const withOriginalLanguage = searchParams.get('with_original_language');
  const withWatchProviders = searchParams.get('with_watch_providers');
  const primaryReleaseYear = searchParams.get('primary_release_year');
  const firstAirDateYear = searchParams.get('first_air_date_year');


  const [name, setName] = useState<string | null>(null);
  const [isLoadingName, setIsLoadingName] = useState(true);

  const discoverParams = useMemo(() => {
    const params: any = { sort_by: sortBy };
    if (id !== 'bollywood' && id !== 'anime') {
        params.with_genres = id;
    }
    if (withKeywords) params.with_keywords = withKeywords;
    if (withOriginalLanguage && withOriginalLanguage !== 'all') params.with_original_language = withOriginalLanguage;
    if (withWatchProviders && withWatchProviders !== 'all') params.with_watch_providers = withWatchProviders;

    if (type === 'movie' && primaryReleaseYear && primaryReleaseYear !== 'all') {
      params.primary_release_year = Number(primaryReleaseYear);
    }
    if (type === 'tv' && firstAirDateYear && firstAirDateYear !== 'all') {
      params.first_air_date_year = Number(firstAirDateYear);
    }
    
    return params;
  }, [id, sortBy, withKeywords, withOriginalLanguage, withWatchProviders, primaryReleaseYear, firstAirDateYear, type]);

  useEffect(() => {
    async function fetchGenreName() {
      setIsLoadingName(true);
      try {
        let genreName: string | null = null;
        if (id === 'bollywood') {
            genreName = "Bollywood";
        } else if (id === 'anime') {
            genreName = "Anime";
        } else {
            genreName = await getGenreName(id, type as 'movie' | 'tv');
        }

        if (!genreName) {
            notFound();
        } else {
            setName(genreName);
        }
      } catch (error) {
        console.error("Failed to fetch genre name", error);
        notFound();
      } finally {
        setIsLoadingName(false);
      }
    }
    fetchGenreName();
  }, [id, type]);
  
  const handleTypeChange = (newType: string) => {
    const params = new URLSearchParams(searchParams.toString());
    params.set('type', newType);
    router.push(`${pathname}?${params.toString()}`);
  };

  const fetcher = useCallback(async (page: number) => {
    const data = await discover(type as 'movie' | 'tv', { ...discoverParams, page });
    return data.results;
  }, [type, discoverParams]);
  
  const pageTitle = useMemo(() => {
    if (id === 'bollywood') return 'Bollywood';
    return name;
  }, [id, name]);
  
  const key = `${id}-${type}-${sortBy}-${withKeywords}-${withOriginalLanguage}-${withWatchProviders}-${primaryReleaseYear}-${firstAirDateYear}`;

  if (isLoadingName) {
    return <GenrePageSkeleton />;
  }

  return (
    <div className="py-8 animate-fade-in">
      <div className="px-4 md:px-8 mb-8 flex flex-col gap-4">
        <h1 className="text-2xl font-bold">
          {pageTitle}
        </h1>
        <Tabs defaultValue={type} onValueChange={handleTypeChange} className="w-full">
            <TabsList className="grid w-full grid-cols-2 md:w-[200px]">
                <TabsTrigger value="movie">Movies</TabsTrigger>
                <TabsTrigger value="tv">TV Shows</TabsTrigger>
            </TabsList>
        </Tabs>

        <DiscoverFilters type={type as 'movie' | 'tv'} showGenreFilter={false} />
      </div>

      <InfiniteContentGrid
        key={key}
        initialItems={[]}
        totalPages={500}
        fetcher={fetcher}
        contentType={type as 'movie' | 'tv'}
        className="px-4 md:px-8"
      />
    </div>
  );
}

function GenrePageSkeleton() {
  return (
    <div className="py-8 animate-fade-in">
      <div className="px-4 md:px-8 mb-8">
          <Skeleton className="h-8 w-48 mb-4" />
          <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
            <Skeleton className="h-10 w-full" />
            <Skeleton className="h-10 w-full" />
            <Skeleton className="h-10 w-full" />
            <Skeleton className="h-10 w-full" />
            <Skeleton className="h-10 w-full" />
          </div>
      </div>
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
  );
}


// This new wrapper component will handle the promise-like `params`.
function ClientGenrePage(props: { params: Promise<any> }) {
  const resolvedParams = use(props.params);
  return <GenrePageContent params={resolvedParams} />;
}


export const runtime = 'edge';

export default function GenrePage(props: GenrePageProps) {
  return (
    <Suspense fallback={<GenrePageSkeleton />}>
      <ClientGenrePage {...props} />
    </Suspense>
  );
}
