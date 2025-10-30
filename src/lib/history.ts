// src/lib/history.ts
'use client';

import type { Movie, TV } from '@/types/tmdb';

export interface HistoryItem {
  id: number;
  type: 'movie' | 'tv';
  title: string;
  poster_path: string | null;
  timestamp: number;
}

const HISTORY_KEY = 'vega_watch_history';
const MAX_HISTORY_ITEMS = 50;

export function getHistory(): HistoryItem[] {
  if (typeof window === 'undefined') return [];
  const historyJson = localStorage.getItem(HISTORY_KEY);
  return historyJson ? JSON.parse(historyJson) : [];
}

export function addToHistory(item: Movie | TV, type: 'movie' | 'tv'): void {
  if (typeof window === 'undefined') return;
  const history = getHistory();
  
  const newItem: HistoryItem = {
    id: item.id,
    type: type,
    title: 'title' in item ? item.title : item.name,
    poster_path: item.poster_path,
    timestamp: Date.now(),
  };

  const existingIndex = history.findIndex(h => h.id === newItem.id && h.type === newItem.type);
  if (existingIndex > -1) {
    history.splice(existingIndex, 1);
  }

  const newHistory = [newItem, ...history];
  
  if (newHistory.length > MAX_HISTORY_ITEMS) {
    newHistory.splice(MAX_HISTORY_ITEMS);
  }

  localStorage.setItem(HISTORY_KEY, JSON.stringify(newHistory));
}

export function clearHistory(): void {
  if (typeof window === 'undefined') return;
  localStorage.removeItem(HISTORY_KEY);
}
