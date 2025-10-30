import Link from 'next/link';
import Image from 'next/image';
import { MovieDetails, TVDetails, Image as TmdbImage } from '@/types/tmdb';
import { getImageUrl } from '@/lib/tmdb';
import { Button } from '@/components/ui/button';
import { Star, PlayCircle, Clock } from 'lucide-react';

interface TrendingHeroCardProps {
  item: MovieDetails | (TVDetails & { reason: string });
}

export function TrendingHeroCard({ item }: TrendingHeroCardProps) {
  const isMovie = 'title' in item;
  const href = `/play/${isMovie ? 'movie' : 'tv'}/${item.id}`;
  const title = isMovie ? item.title : item.name;
  const backdropUrl = getImageUrl(item.backdrop_path, 'w1280');
  const posterUrl = getImageUrl(item.poster_path, 'w500');

  const englishLogo = item.images?.logos.find(
    (logo: TmdbImage) => logo.iso_639_1 === 'en'
  );
  const logoUrl = englishLogo ? getImageUrl(englishLogo.file_path, 'w500') : null;

  return (
    <div className="relative w-full aspect-[16/9] overflow-hidden">
      {backdropUrl && (
        <Image
          src={backdropUrl}
          alt={title}
          fill
          priority
          className="object-cover"
        />
      )}

      <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent" />
      <div className="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent" />


      <div className="absolute inset-0 flex items-end justify-between container px-4 md:px-8 pb-3 md:pb-12">
        {/* Left side text / logo */}
        <div className="relative z-10 flex flex-col items-start md:gap-3 text-white max-w-md">
          {logoUrl ? (
            <div className="relative w-32 sm:w-34 md:w-80 h-20 sm:h-28 md:h-32 mb-1 md:mb-5">
              <Image
                src={logoUrl}
                alt={`${title} logo`}
                fill
                className="object-contain"
                sizes="(max-width: 640px) 152px, (max-width: 768px) 256px, 320px"
              />
            </div>
          ) : (
            <h1 className="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-headline font-bold">
              {title}
            </h1>
          )}

          <div className="flex items-center gap-4 text-xs sm:text-sm md:text-base">
            <div className="flex items-center gap-1">
              <Star className="w-4 h-4 text-yellow-400 fill-yellow-400" />
              <span className="font-bold">{item.vote_average.toFixed(1)}</span>
            </div>
            {(item as MovieDetails).runtime && (
              <div className="flex items-center gap-1">
                <Clock className="w-4 h-4" />
                <span>{(item as MovieDetails).runtime} min</span>
              </div>
            )}
          </div>

          <Button
            asChild
            variant="gradient"
            size="sm"
            className="mt-3 md:size-lg flex items-center gap-2"
          >
            <Link href={href}>
              <PlayCircle className="w-5 h-5" />
              Watch Now
            </Link>
          </Button>
        </div>

        {/* Right side poster with animation */}
        {posterUrl && (
          <div className="relative w-16 sm:w-40 md:w-48 lg:w-56 animate-float self-end mb-2
        ">
            <Image
              src={posterUrl}
              alt={`${title} poster`}
              width={224}
              height={336}
              className="rounded-lg shadow-lg"
            />
          </div>
        )}
      </div>
    </div>
  );
}
