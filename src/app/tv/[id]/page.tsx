import { getTvDetails, getImageUrl } from '@/lib/tmdb';
import { notFound } from 'next/navigation';
import { Metadata, ResolvingMetadata } from 'next';
import { TVDetails } from '@/types/tmdb';
import { TVDetailsClient } from './client-page';

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
  const tvId = params.id;
  let tvShow: TVDetails;

  try {
    tvShow = await getTvDetails(tvId);
  } catch (error) {
    return {
      title: 'TV Show Not Found',
    };
  }

  const backdropUrl = getImageUrl(tvShow.backdrop_path, 'w1280');
  
  return {
    title: tvShow.name,
    description: tvShow.overview,
    openGraph: {
      title: tvShow.name,
      description: tvShow.overview,
      images: backdropUrl ? [backdropUrl] : [],
    },
  };
}

export default async function TVShowDetailsPage({ params }: PageProps) {
  const tvId = params.id;
  let tvShow: TVDetails;

  try {
    tvShow = await getTvDetails(tvId);
  } catch (error) {
    console.error(error);
    notFound();
  }

  return <TVDetailsClient tvShow={tvShow} />;
}
