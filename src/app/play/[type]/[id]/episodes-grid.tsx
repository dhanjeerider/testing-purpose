'use client'

import { Season, Episode } from "@/types/tmdb"
import { Card, CardContent } from "@/components/ui/card"
import Image from "next/image"
import { Tv } from "lucide-react"
import { format, formatDistanceToNow } from 'date-fns'
import { getImageUrl } from "@/lib/tmdb"

interface EpisodesGridProps {
    season: Season;
    episodes: Episode[];
    currentEpisodeNumber: number;
    onEpisodeSelect: (season: number, episode: number) => void;
}

export function EpisodesGrid({ season, episodes, currentEpisodeNumber, onEpisodeSelect }: EpisodesGridProps) {
  return (
    <div className="content-grid">
      {episodes.map((ep) => (
        <EpisodeCard 
            key={ep.id} 
            episode={ep} 
            onClick={() => onEpisodeSelect(season.season_number, ep.episode_number)}
            isActive={ep.episode_number === currentEpisodeNumber}
        />
      ))}
    </div>
  );
}

function EpisodeCard({ episode, onClick, isActive }: { episode: Episode, onClick: () => void, isActive: boolean }) {
  const stillUrl = getImageUrl(episode.still_path, 'w300');
  const airDate = episode.air_date ? new Date(episode.air_date) : null;
  const hasAired = airDate ? airDate < new Date() : false;

  return (
    <Card className={`overflow-hidden group cursor-pointer ${isActive ? 'border-primary ring-2 ring-primary' : ''}`} onClick={onClick}>
      <CardContent className="p-0">
        <div className="aspect-video relative">
          {stillUrl ? (
            <Image
              src={stillUrl}
              alt={episode.name}
              width={300}
              height={169}
              loading="lazy"
              className="object-cover group-hover:scale-105 transition-transform duration-300 w-full h-full"
            />
          ) : (
             <div className="flex h-full w-full items-center justify-center bg-muted">
               <Tv className="h-8 w-8 text-muted-foreground" />
             </div>
          )}
          <div className="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity" />
        </div>
        <div className="p-3 space-y-1">
          <h4 className="font-semibold truncate text-sm">
            E{episode.episode_number}: {episode.name}
          </h4>
          <div className="text-xs text-muted-foreground flex items-center justify-between">
            <span>{airDate ? format(airDate, 'MMM d, yyyy') : 'TBA'}</span>
            {airDate && !hasAired && (
              <span className="text-amber-400">{formatDistanceToNow(airDate, { addSuffix: true })}</span>
            )}
          </div>
        </div>
      </CardContent>
    </Card>
  );
}
