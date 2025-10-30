import type { Metadata } from 'next';
import * as tmdb from '@/lib/tmdb';
import { ContentCarousel } from '@/components/common/content-carousel';
import TrendingCarousel from '@/components/home/trending-carousel';
import { Suspense } from 'react';
import { Skeleton } from '@/components/ui/skeleton';
import { Genre } from '@/types/tmdb';
import Link from 'next/link';
import { Button } from '@/components/ui/button';

export const runtime = "edge";
export const metadata: Metadata = {
  title: 'Vega Movies - A modern movie and TV show explorer',
  description: 'Explore the latest movies and TV shows, with recommendations, trailers and more.',
  openGraph: {
    title: 'Vega Movies',
    description: 'A modern movie and TV show explorer built with Next.js and TMDB.',
    images: ['/og-image.jpg'],
  },
};

async function GenresSection() {
  const [movieGenres, tvGenres] = await Promise.all([
    tmdb.getMovieGenres(),
    tmdb.getTvGenres(),
  ]);

  const allGenres = [...movieGenres, ...tvGenres].reduce((acc, genre) => {
    if (!acc.find(g => g.id === genre.id)) {
      acc.push(genre);
    }
    return acc;
  }, [] as Genre[]).slice(0, 12);


  return (
    <section>
      <div className="flex justify-between items-center mb-6 px-4 md:px-8">
        <h2 className="text-xl font-headline font-bold">Popular Genres</h2>
        <Button asChild variant="gradient" size="sm">
          <Link href="/genres">View All</Link>
        </Button>
      </div>
      <div className="flex flex-wrap justify-center gap-3 px-4 md:px-8">
        {allGenres.map((genre) => (
           <Button key={genre.id} asChild variant="gradient" className="rounded-full">
            <Link href={`/genre/${genre.id}?type=movie`}>
              {genre.name}
            </Link>
          </Button>
        ))}
      </div>
    </section>
  )
}

export default async function Home() {
  const [
    nowPlayingMovies,
    upcomingMovies,
    popularMovies,
    topRatedMovies,
    airingTodayTv,
    onTheAirTv,
    popularTv,
    topRatedTv,
    crimeMovies,
    actionMovies,
    dramaMovies,
    bollywoodMovies,
  ] = await Promise.all([
    tmdb.getNowPlayingMovies(),
    tmdb.getUpcomingMovies(),
    tmdb.getPopular('movie'),
    tmdb.getTopRated('movie'),
    tmdb.getAiringTodayTv(),
    tmdb.getOnTheAirTv(),
    tmdb.getPopular('tv'),
    tmdb.getTopRated('tv'),
    tmdb.discover('movie', { with_genres: '80' }),
    tmdb.discover('movie', { with_genres: '28' }),
    tmdb.discover('movie', { with_genres: '18' }),
    tmdb.discover('movie', { with_original_language: 'hi', sort_by: 'popularity.desc' }),
  ]);

  return (
    <div className="flex flex-col gap-8 md:gap-16 pb-8 animate-fade-in">
      <Suspense fallback={<Skeleton className="aspect-video w-full" />}>
        <TrendingCarousel />
      </Suspense>

      <div className="flex flex-col gap-5 md:gap-16">
        <ContentCarousel title="Now Playing Movies" items={nowPlayingMovies.results} />
        <ContentCarousel title="Upcoming Movies" items={upcomingMovies.results} />
        <ContentCarousel title="Popular Movies" items={popularMovies.results} />
        <ContentCarousel title="Top Rated Movies" items={topRatedMovies.results} />
        <ContentCarousel title="Crime Movies" items={crimeMovies.results} genreId="80" />
        <ContentCarousel title="Action Movies" items={actionMovies.results} genreId="28" />
        <ContentCarousel title="Drama Movies" items={dramaMovies.results} genreId="18" />
        <ContentCarousel title="Bollywood" items={bollywoodMovies.results} genreId="bollywood" />
        <ContentCarousel title="Airing Today" items={airingTodayTv.results} type="tv" />
        <ContentCarousel title="On The Air" items={onTheAirTv.results} type="tv" />
        <ContentCarousel title="Popular TV Shows" items={popularTv.results} type="tv" />
        <ContentCarousel title="Top Rated TV Shows" items={topRatedTv.results} type="tv" />
      </div>

      <GenresSection />
    </div>
  );
}
