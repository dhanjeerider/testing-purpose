// src/lib/likes.ts
'use client';

import type { Movie, TV } from '@/types/tmdb';

export interface LikedItem {
  id: number;
  type: 'movie' | 'tv';
  title: string;
  poster_path: string | null;
  timestamp: number;
}

const LIKES_KEY = 'vega_liked_content';

export function getLikes(): LikedItem[] {
  if (typeof window === 'undefined') return [];
  const likesJson = localStorage.getItem(LIKES_KEY);
  return likesJson ? JSON.parse(likesJson) : [];
}

export function isLiked(id: number, type: 'movie' | 'tv'): boolean {
  return getLikes().some(item => item.id === id && item.type === type);
}

export function toggleLike(item: Movie | TV, type: 'movie' | 'tv'): boolean {
  if (typeof window === 'undefined') return false;
  
  const likes = getLikes();
  const existingIndex = likes.findIndex(l => l.id === item.id && l.type === type);

  if (existingIndex > -1) {
    // Unlike
    likes.splice(existingIndex, 1);
    localStorage.setItem(LIKES_KEY, JSON.stringify(likes));
    return false;
  } else {
    // Like
    const newItem: LikedItem = {
      id: item.id,
      type: type,
      title: 'title' in item ? item.title : item.name,
      poster_path: item.poster_path,
      timestamp: Date.now(),
    };
    const newLikes = [newItem, ...likes];
    localStorage.setItem(LIKES_KEY, JSON.stringify(newLikes));
    return true;
  }
}

export function clearLikes(): void {
  if (typeof window === 'undefined') return;
  localStorage.removeItem(LIKES_KEY);
}
