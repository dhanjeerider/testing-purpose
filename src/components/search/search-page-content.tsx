'use client';

import { useState, useEffect, useCallback } from 'react';
import { searchMulti } from '@/lib/tmdb';
import { useSearchParams } from 'next/navigation';
import { InfiniteContentGrid } from '@/components/common/infinite-content-grid';
import { SearchBar } from '@/components/layout/search-bar';

export function SearchPageContent() {
  const searchParams = useSearchParams();
  const initialQuery = searchParams.get('q') || '';
  const [query, setQuery] = useState(initialQuery);
  const [results, setResults] = useState<any[]>([]);
  const [totalPages, setTotalPages] = useState(1);
  const [isLoading, setIsLoading] = useState(false);
  const [page, setPage] = useState(1);

  const performSearch = useCallback(async (searchQuery: string, pageNum: number) => {
    if (!searchQuery) {
      setResults([]);
      setTotalPages(1);
      return;
    }
    setIsLoading(true);
    const data = await searchMulti(searchQuery, pageNum);
    if (pageNum === 1) {
      setResults(data.results);
    } else {
      setResults((prev) => [...prev, ...data.results]);
    }
    setTotalPages(data.total_pages);
    setPage(pageNum);
    setIsLoading(false);
  }, []);

  useEffect(() => {
    setQuery(initialQuery);
    setPage(1);
    performSearch(initialQuery, 1);
  }, [initialQuery, performSearch]);

  const handleSearchSubmit = (searchQuery: string) => {
    setQuery(searchQuery);
    setPage(1);
    const params = new URLSearchParams();
    params.set('q', searchQuery);
    window.history.pushState(null, '', `?${params.toString()}`);
    performSearch(searchQuery, 1);
  };
  
  const fetcher = async (pageNum: number) => {
    const data = await searchMulti(query, pageNum);
    return data.results;
  };

  return (
    <div className="py-8">
      <div className="mb-8 px-4 md:px-8">
        <SearchBar initialQuery={query} onSearch={handleSearchSubmit} />
      </div>

      {query && (
        <h1 className="text-2xl font-bold mb-8 px-4 md:px-8">
          Search results for: <span className="text-primary">{query}</span>
        </h1>
      )}
      
      {isLoading && page === 1 ? (
        <p className="px-4 md:px-8">Loading...</p>
      ) : results.length === 0 && query ? (
        <p className="px-4 md:px-8">No results found.</p>
      ) : (
        <InfiniteContentGrid
          key={query} // Add key to re-mount component on new search
          initialItems={results}
          totalPages={totalPages}
          fetcher={fetcher}
          className="px-4 md:px-8"
        />
      )}
    </div>
  );
}
