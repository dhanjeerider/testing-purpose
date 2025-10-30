import Link from 'next/link';
import Image from 'next/image';
import { Movie, TV } from '@/types/tmdb';
import { getImageUrl } from '@/lib/tmdb';
import { Card, CardContent } from '@/components/ui/card';
import { Film, Tv, Star, PlayCircle } from 'lucide-react';
import { cn } from '@/lib/utils';

interface ContentCardProps {
  item: Movie | TV;
  type?: 'movie' | 'tv';
  className?: string;
}

export function ContentCard({ item, type, className }: ContentCardProps) {
  const mediaType = type || ('title' in item ? 'movie' : 'tv');
  const href = `/${mediaType}/${item.id}`;

  const isMovie = 'title' in item;
  const title = isMovie ? item.title : item.name;
  const releaseDate = isMovie ? item.release_date : item.first_air_date;
  const posterPath = getImageUrl(item.poster_path, 'w300');

  const releaseYear = releaseDate ? new Date(releaseDate).getFullYear().toString() : 'N/A';
  
  return (
    <Link href={href} className={cn('group block pt-1', className)}>
      <Card className="overflow-hidden h-full bg-card border-border/60 hover:border-primary/60 hover:shadow-lg hover:shadow-primary/20 transition-all duration-300 ease-in-out transform hover:-translate-y-1">
        <CardContent className="p-0">
          <div className="aspect-[2/3] relative">
            {posterPath ? (
              <Image
                src={posterPath}
                alt={title}
                width={300}
                height={450}
                loading="lazy"
                className="object-cover transition-transform duration-300 w-full h-full"
              />
            ) : (
              <div className="w-full h-full bg-muted flex items-center justify-center">
                {mediaType === 'movie' ? <Film className="w-12 h-12 text-muted-foreground" /> : <Tv className="w-12 h-12 text-muted-foreground" />}
              </div>
            )}
            <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
              <PlayCircle className="w-12 h-12 text-white" />
            </div>
          </div>
          <div className="p-3 space-y-1 text-center">
            <h3 className="font-semibold text-sm text-foreground truncate">{title}</h3>
            <div className="flex items-center justify-center text-xs text-muted-foreground gap-2">
              <span>{releaseYear}</span>
              <span className='flex items-center gap-1'>
                <Star className="w-3 h-3 text-yellow-400 fill-yellow-400" />
                <span>{item.vote_average.toFixed(1)}</span>
              </span>
              <span className="capitalize border border-muted-foreground/30 rounded px-1.5 py-0.5 text-xs">
                {mediaType}
              </span>
            </div>
          </div>
        </CardContent>
      </Card>
    </Link>
  );
}
