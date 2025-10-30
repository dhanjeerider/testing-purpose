import { getMovieDetails, getImageUrl } from '@/lib/tmdb';
import { notFound } from 'next/navigation';
import { Metadata, ResolvingMetadata } from 'next';
import { MovieDetailsClient } from './client-page';
import { MovieDetails } from '@/types/tmdb';

export const runtime = 'edge';

interface PageProps {
  params: {
    id: string;
  };
}

export async function generateMetadata(
  { params }: PageProps,
  parent: ResolvingMetadata
): Promise<Metadata> {
  const movieId = params.id;
  try {
    const movie = await getMovieDetails(movieId);
    const backdropUrl = getImageUrl(movie.backdrop_path, 'w1280');
    return {
      title: movie.title,
      description: movie.overview,
      openGraph: {
        title: movie.title,
        description: movie.overview,
        images: backdropUrl ? [backdropUrl] : [],
      },
    };
  } catch (error) {
    return { title: 'Movie not found' };
  }
}

export default async function MovieDetailsPage({ params }: PageProps) {
  const movieId = params.id;
  let movie: MovieDetails;

  try {
    movie = await getMovieDetails(movieId);
  } catch (error) {
    console.error(error);
    notFound();
  }

  return <MovieDetailsClient movie={movie} />;
}
