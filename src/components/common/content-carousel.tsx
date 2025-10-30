'use client'

import { Movie, TV } from '@/types/tmdb';
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel';
import { ContentCard } from './content-card';
import Link from 'next/link';
import { Button } from '../ui/button';
import Autoplay from "embla-carousel-autoplay"
import React from 'react';

interface ContentCarouselProps {
  title: string;
  items: (Movie | TV)[];
  type?: 'movie' | 'tv';
  genreId?: string;
}

export function ContentCarousel({ title, items, type = 'movie', genreId }: ContentCarouselProps) {
  if (!items || items.length === 0) return null;

  const plugin = React.useRef(
    Autoplay({ delay: 3000, stopOnInteraction: true })
  )

  return (
    <section>
      <div className="px-4 md:px-8 flex items-center justify-between">
        <h2 className="text-xl font-headline font-bold mb-4">{title}</h2>
        {genreId && (
          <Button asChild variant="gradient" size="sm" className="-my-2">
            <Link href={`/genre/${genreId}?type=${type}`}>
              View All
            </Link>
          </Button>
        )}
      </div>
      <Carousel
        plugins={[plugin.current]}
        opts={{
          align: 'start',
          dragFree: true,
        }}
        className="w-full max-w-7xl mx-auto"
        onMouseEnter={plugin.current.stop}
        onMouseLeave={plugin.current.reset}
      >
        <CarouselContent className="px-4 md:px-8">
          {items.map((item) => (
            <CarouselItem key={item.id} className="basis-1/2 sm:basis-1/5 md:basis-1/8 pl-2 md:pl-4">
              <ContentCard item={item} type={type} />
            </CarouselItem>
          ))}
        </CarouselContent>
        <CarouselPrevious className="hidden md:flex" />
        <CarouselNext className="hidden md:flex" />
      </Carousel>
    </section>
  );
}
