'use client';

import { Genre, Movie, TV } from '@/types/tmdb';
import { ContentCarousel } from '@/components/common/content-carousel';

interface GenreCarouselProps {
    genre: Genre;
    type: 'movie' | 'tv';
    items: (Movie | TV)[];
}

export function GenreCarousel({ genre, type, items }: GenreCarouselProps) {
    return (
        <ContentCarousel 
            title={genre.name}
            items={items}
            type={type}
            genreId={String(genre.id)}
        />
    )
}
