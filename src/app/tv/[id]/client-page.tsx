'use client'

import { getImageUrl, getTvRecommendations } from '@/lib/tmdb';
import Image from 'next/image';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import Link from 'next/link';
import { Star, Tv, PlayCircle, Calendar, Users, Languages, Heart } from 'lucide-react';
import { format } from 'date-fns';
import { Card, CardContent } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import { useState, useEffect, useCallback } from 'react';
import { TVDetails, Image as TmdbImage, CastMember, Review } from '@/types/tmdb';
import { InfiniteContentGrid } from '@/components/common/infinite-content-grid';
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import { CastMemberDialog } from '@/components/movie/cast-member-dialog';
import { toggleLike, isLiked } from '@/lib/likes';
import { useToast } from '@/hooks/use-toast';
import { User } from 'lucide-react';
import { cn } from '@/lib/utils';

interface ClientPageProps {
  tvShow: TVDetails;
}

export function TVDetailsClient({ tvShow: initialTvShow }: ClientPageProps) {
  const [tvShow, setTvShow] = useState<TVDetails>(initialTvShow);
  const [liked, setLiked] = useState(false);
  const { toast } = useToast();

  useEffect(() => {
    setTvShow(initialTvShow);
    setLiked(isLiked(initialTvShow.id, 'tv'));
  }, [initialTvShow]);

  const handleLike = () => {
    if (!tvShow) return;
    const newLikedState = toggleLike(tvShow, 'tv');
    setLiked(newLikedState);
    toast({
      title: newLikedState ? 'Added to Liked' : 'Removed from Liked',
      description: tvShow.name,
    });
  };

  const recommendationsFetcher = useCallback((page: number) => {
    return getTvRecommendations(String(tvShow.id), page);
  }, [tvShow.id]);
  
  if (!tvShow) {
    return <TVDetailsSkeleton />;
  }

  const backdropUrl = getImageUrl(tvShow.backdrop_path, 'w1280');
  const posterUrl = getImageUrl(tvShow.poster_path, 'w780');
  const englishLogo = tvShow.images?.logos.find((logo: TmdbImage) => logo.iso_639_1 === 'en');
  const logoUrl = englishLogo ? getImageUrl(englishLogo.file_path, 'w500') : null;
  const trailer = tvShow.videos?.results.find(v => v.type === 'Trailer' && v.site === 'YouTube');

  return (
    <div className="flex flex-col">
      <div className="relative w-full">
        <div className="aspect-[16/9] w-full">
          {backdropUrl ? (
            <Image
              src={backdropUrl}
              alt={`${tvShow.name} backdrop`}
              width={1280}
              height={720}
              className="object-cover object-top w-full h-full"
              priority
            />
          ) : (
            <div className="flex h-full w-full items-center justify-center bg-muted">
              <Tv className="h-24 w-24 text-muted-foreground" />
            </div>
          )}
        </div>

        <div className="absolute bottom-0 left-0 w-full px-4 md:px-8">
           <div className="flex items-end justify-between">
              <div className="relative h-40 w-28 md:h-56 md:w-40 flex-shrink-0 -mb-8">
                {posterUrl && (
                  <Image
                    src={posterUrl}
                    alt={`${tvShow.name} poster`}
                    width={160}
                    height={240}
                    className="rounded-xl object-cover w-full h-full"
                  />
                )}
              </div>
             {logoUrl && (
              <div className="relative w-36 h-20 md:w-56 md:h-28 mb-4">
                <Image
                  src={logoUrl}
                  alt={`${tvShow.name} logo`}
                  fill
                  className="object-contain"
                  sizes="(max-width: 768px) 144px, 224px"
                />
              </div>
            )}
           </div>
        </div>
      </div>
      
      <div className="mt-12 pb-16 px-4 md:px-8">
        <div className="flex gap-4 mb-8">
            <Button asChild size="sm" variant="gradient">
              <Link href={`/play/tv/${tvShow.id}`}>
                <PlayCircle className="size-4" />
                Watch Now
              </Link>
            </Button>
            <Button size="sm" variant="outline" onClick={handleLike}>
              <Heart className={cn("size-4", liked ? 'fill-red-500 text-red-500' : '')} />
              {liked ? 'Liked' : 'Like'}
            </Button>
        </div>


        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
          <div className="md:col-span-2 flex flex-col gap-4">
            <h1 className="text-2xl font-bold">{tvShow.name}</h1>
            {tvShow.tagline && <p className="text-sm text-muted-foreground italic">"{tvShow.tagline}"</p>}
            <p className="max-w-3xl text-foreground/80">{tvShow.overview}</p>
          </div>

          <div className="bg-muted/50 p-4 rounded-lg space-y-3 text-sm">
             <div className="flex items-center gap-2">
                <Star className="w-4 h-4 text-yellow-400 fill-yellow-400" />
                <span className="font-bold">{tvShow.vote_average.toFixed(1)}</span>
                <span className="text-muted-foreground">({tvShow.vote_count.toLocaleString()} votes)</span>
              </div>
            <div className="flex flex-wrap gap-2">
              {tvShow.genres.map((genre) => (
                <Link key={genre.id} href={`/genre/${genre.id}?type=tv`}>
                  <Badge
                    variant="default"
                    className="btn-gradient text-white hover:scale-105 transition-transform duration-200 cursor-pointer"
                  >
                    {genre.name}
                  </Badge>
                </Link>
              ))}
            </div>
             <div className="flex items-center gap-2"><Calendar className="size-4" /> <strong>First Air Date:</strong> {tvShow.first_air_date ? format(new Date(tvShow.first_air_date), 'MMMM d, yyyy') : 'N/A'}</div>
             <div className="flex items-center gap-2"><Calendar className="size-4" /> <strong>Last Air Date:</strong> {tvShow.last_air_date ? format(new Date(tvShow.last_air_date), 'MMMM d, yyyy') : 'N/A'}</div>
             <div className="flex items-center gap-2"><Tv className="size-4" /> <strong>Seasons:</strong> {tvShow.number_of_seasons}</div>
             <div className="flex items-center gap-2"><Users className="size-4" /> <strong>Episodes:</strong> {tvShow.number_of_episodes}</div>
             <div className="flex items-center gap-2"><Languages className="size-4" /> <strong>Language:</strong> {tvShow.original_language.toUpperCase()}</div>
          </div>
        </div>

        {trailer && (
          <div className="mt-12">
            <h2 className="text-2xl font-bold mb-4">Trailer</h2>
            <div className="aspect-video">
              <iframe
                src={`https://www.youtube.com/embed/${trailer.key}`}
                title="Trailer"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen
                className="w-full h-full rounded-xl"
              ></iframe>
            </div>
          </div>
        )}

        <Tabs defaultValue="cast" className="mt-16">
          <TabsList>
            <TabsTrigger value="cast">Cast</TabsTrigger>
            <TabsTrigger value="reviews">Reviews</TabsTrigger>
          </TabsList>
          <TabsContent value="cast" className="mt-4">
            {tvShow.credits.cast.length > 0 ? (
               <Carousel opts={{ align: 'start', dragFree: true }} className="w-full">
                <CarouselContent className="-ml-2 md:-ml-4">
                  {tvShow.credits.cast.map((member) => (
                    <CarouselItem key={member.credit_id} className="basis-1/2 sm:basis-1/3 md:basis-1/4 lg:basis-1/4 pl-2 md:pl-4">
                       <CastMemberCard member={member} />
                    </CarouselItem>
                  ))}
                </CarouselContent>
                <CarouselPrevious className="hidden md:flex" />
                <CarouselNext className="hidden md:flex" />
              </Carousel>
            ) : <p>No cast information available.</p>}
          </TabsContent>
          <TabsContent value="reviews" className="mt-4">
             {tvShow.reviews.results.length > 0 ? (
                <div className="space-y-6">
                    {tvShow.reviews.results.map((review) => (
                        <ReviewCard key={review.id} review={review} />
                    ))}
                </div>
            ) : <p>No reviews available for this show.</p>}
          </TabsContent>
        </Tabs>

        {tvShow.recommendations.results.length > 0 && (
          <div className="mt-16">
            <h2 className="text-2xl font-bold mb-8">Recommendations</h2>
            <InfiniteContentGrid
              initialItems={tvShow.recommendations.results}
              totalPages={tvShow.recommendations.total_pages}
              fetcher={recommendationsFetcher}
              contentType="tv"
            />
          </div>
        )}

      </div>
    </div>
  );
}

function TVDetailsSkeleton() {
    return (
        <div className="flex flex-col">
            <Skeleton className="aspect-[16/9] w-full" />
            <div className="mt-12 pb-16 px-4 md:px-8">
                <div className="flex gap-4 mb-8">
                    <Skeleton className="h-9 w-32 rounded-md" />
                    <Skeleton className="h-9 w-32 rounded-md" />
                </div>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                    <div className="md:col-span-2 space-y-4">
                        <Skeleton className="h-8 w-3/4" />
                        <Skeleton className="h-6 w-1/2" />
                        <Skeleton className="h-20 w-full" />
                    </div>
                    <div className="space-y-3">
                        <Skeleton className="h-8 w-1/2" />
                        <div className="flex flex-wrap gap-2">
                            <Skeleton className="h-6 w-16 rounded-full" />
                            <Skeleton className="h-6 w-20 rounded-full" />
                        </div>
                        <Skeleton className="h-5 w-full" />
                        <Skeleton className="h-5 w-full" />
                        <Skeleton className="h-5 w-full" />
                        <Skeleton className="h-5 w-full" />
                    </div>
                </div>
                 <div className="mt-16">
                    <Skeleton className="h-10 w-48 mb-4" />
                     <Skeleton className="h-40 w-full mb-4" />
                     <Skeleton className="h-40 w-full" />
                </div>
            </div>
        </div>
    );
}

function CastMemberCard({ member }: { member: CastMember }) {
    const profileUrl = getImageUrl(member.profile_path, 'w300');
    return (
        <CastMemberDialog member={member}>
            <Card className="overflow-hidden group cursor-pointer">
                <CardContent className="p-0">
                    <div className="aspect-[2/3] relative">
                    {profileUrl ? (
                        <Image
                            src={profileUrl}
                            alt={member.name}
                            width={300}
                            height={450}
                            loading="lazy"
                            className="object-cover group-hover:scale-105 transition-transform duration-300 w-full h-full"
                        />
                    ) : (
                        <div className="flex h-full w-full items-center justify-center bg-muted">
                            <User className="h-8 w-8 text-muted-foreground" />
                        </div>
                    )}
                    </div>
                    <div className="p-2">
                        <h4 className="font-semibold truncate text-sm">{member.name}</h4>
                        <p className="text-xs text-muted-foreground truncate">{member.character}</p>
                    </div>
                </CardContent>
            </Card>
        </CastMemberDialog>
    );
}

function ReviewCard({ review }: { review: Review }) {
    const avatarUrl = review.author_details.avatar_path ? getImageUrl(review.author_details.avatar_path, 'w300') : null;
    return (
      <Card>
        <CardContent className="p-6">
          <div className="flex items-start gap-4">
            <div className="relative h-12 w-12 flex-shrink-0">
              {avatarUrl ? (
                <Image src={avatarUrl} alt={review.author} width={48} height={48} className="rounded-full object-cover" />
              ) : (
                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                  <User className="h-6 w-6 text-muted-foreground" />
                </div>
              )}
            </div>
            <div className="flex-grow">
              <div className="flex items-center justify-between">
                <div>
                  <h4 className="font-semibold">{review.author}</h4>
                  <p className="text-xs text-muted-foreground">@{review.author_details.username}</p>
                </div>
                {review.author_details.rating && (
                  <div className="flex items-center gap-1 text-sm font-bold">
                    <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
                    <span>{review.author_details.rating}/10</span>
                  </div>
                )}
              </div>
              <p className="mt-4 text-sm text-foreground/80 whitespace-pre-line line-clamp-6">{review.content}</p>
              <p className="text-xs text-muted-foreground mt-2">{format(new Date(review.created_at), "MMMM d, yyyy")}</p>
            </div>
          </div>
        </CardContent>
      </Card>
    );
}
