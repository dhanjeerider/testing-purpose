'use client'

import { useCallback } from 'react'
import { usePathname, useRouter, useSearchParams } from 'next/navigation'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

interface DiscoverFiltersProps {
  type: 'movie' | 'tv',
  showGenreFilter?: boolean
}

// Custom lists for filters
const customGenres = [
    { id: '28', name: 'Action' },
    { id: '12', name: 'Adventure' },
    { id: '16', name: 'Animation' },
    { id: '35', name: 'Comedy' },
    { id: '80', name: 'Crime' },
    { id: '99', name: 'Documentary' },
    { id: '18', name: 'Drama' },
    { id: '10751', name: 'Family' },
    { id: '14', name: 'Fantasy' },
    { id: '36', name: 'History' },
    { id: '27', name: 'Horror' },
    { id: '10402', name: 'Music' },
    { id: '9648', name: 'Mystery' },
    { id: '10749', name: 'Romance' },
    { id: '878', name: 'Science Fiction' },
    { id: '53', name: 'Thriller' },
    { id: '10752', name: 'War' },
    { id: '37', name: 'Western' },
];

const customLanguages = [
    { iso_639_1: 'en', english_name: 'English' },
    { iso_639_1: 'es', english_name: 'Spanish' },
    { iso_639_1: 'fr', english_name: 'French' },
    { iso_639_1: 'de', english_name: 'German' },
    { iso_639_1: 'ja', english_name: 'Japanese' },
    { iso_639_1: 'hi', english_name: 'Hindi' },
    { iso_639_1: 'ko', english_name: 'Korean' },
];

const years = Array.from({ length: new Date().getFullYear() - 1949 }, (_, i) => new Date().getFullYear() - i);


export function DiscoverFilters({ type, showGenreFilter = true }: DiscoverFiltersProps) {
  const router = useRouter()
  const pathname = usePathname()
  const searchParams = useSearchParams()
  
  const createQueryString = useCallback(
    (name: string, value: string) => {
      const params = new URLSearchParams(searchParams.toString())
      if (value && value !== 'all') {
        params.set(name, value)
      } else {
        params.delete(name)
      }
      return params.toString()
    },
    [searchParams]
  )

  const handleFilterChange = (name: string, value: string) => {
    router.push(pathname + '?' + createQueryString(name, value))
  }
  
  const getParam = (name: string, defaultValue: string = 'all') => searchParams.get(name) || defaultValue;
  
  const yearParam = type === 'movie' ? 'primary_release_year' : 'first_air_date_year';

  return (
    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
      <Select onValueChange={(v) => handleFilterChange('sort_by', v)} value={getParam('sort_by', 'release_date.desc')}>
        <SelectTrigger><SelectValue /></SelectTrigger>
        <SelectContent>
          <SelectItem value="release_date.desc">Release Date Desc</SelectItem>
          <SelectItem value="release_date.asc">Release Date Asc</SelectItem>
          <SelectItem value="vote_average.desc">Rating Desc</SelectItem>
          <SelectItem value="vote_average.asc">Rating Asc</SelectItem>
          <SelectItem value="popularity.desc">Popularity Desc</SelectItem>
        </SelectContent>
      </Select>
      {showGenreFilter && (
        <Select onValueChange={(v) => handleFilterChange('with_genres', v)} value={getParam('with_genres')}>
            <SelectTrigger><SelectValue /></SelectTrigger>
            <SelectContent>
            <SelectItem value="all">All Genres</SelectItem>
            {customGenres.map(g => <SelectItem key={g.id} value={String(g.id)}>{g.name}</SelectItem>)}
            </SelectContent>
        </Select>
      )}
      <Select onValueChange={(v) => handleFilterChange(yearParam, v)} value={getParam(yearParam)}>
        <SelectTrigger><SelectValue /></SelectTrigger>
        <SelectContent>
          <SelectItem value="all">Any Year</SelectItem>
          {years.map(y => <SelectItem key={y} value={String(y)}>{y}</SelectItem>)}
        </SelectContent>
      </Select>
      <Select onValueChange={(v) => handleFilterChange('with_original_language', v)} value={getParam('with_original_language')}>
        <SelectTrigger><SelectValue /></SelectTrigger>
        <SelectContent>
          <SelectItem value="all">Any Language</SelectItem>
          {customLanguages.map(l => <SelectItem key={l.iso_639_1} value={l.iso_639_1}>{l.english_name}</SelectItem>)}
        </SelectContent>
      </Select>
    </div>
  )
}
