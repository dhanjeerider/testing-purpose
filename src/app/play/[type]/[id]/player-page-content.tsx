'use client'

import { useState, useEffect, Suspense, useCallback } from "react"
import { useRouter } from "next/navigation"
import { getMovieRecommendations, getTvRecommendations, getSeasonDetails, getImageUrl } from "@/lib/tmdb"
import { servers, buildStreamUrl } from "@/lib/servers"
import { Movie, MovieDetails, TV, TVDetails, Episode } from "@/types/tmdb"
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select"
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from "@/components/ui/accordion"
import Image from 'next/image'
import { Tv } from 'lucide-react'
import { format } from 'date-fns'
import { Skeleton } from "@/components/ui/skeleton"
import { InfiniteContentGrid } from "@/components/common/infinite-content-grid"
import { EpisodesGrid } from "./episodes-grid"
import { addToHistory } from "@/lib/history"
import { Switch } from "@/components/ui/switch"
import { Label } from "@/components/ui/label"

interface PlayerPageContentProps {
    type: 'movie' | 'tv';
    id: string;
    initialDetails: MovieDetails | TVDetails;
    initialSeason: number;
    initialEpisode: number;
    initialEpisodes: Episode[];
}

export function PlayerPageContent({ type, id, initialDetails, initialSeason, initialEpisode, initialEpisodes }: PlayerPageContentProps) {
  const router = useRouter()

  const [details, setDetails] = useState(initialDetails)
  const [selectedServer, setSelectedServer] = useState(servers[0])
  const [streamUrl, setStreamUrl] = useState('')
  const [isSandboxed, setIsSandboxed] = useState(true);
  
  const [season, setSeason] = useState(initialSeason)
  const [episode, setEpisode] = useState(initialEpisode)
  const [episodesBySeason, setEpisodesBySeason] = useState<Record<number, Episode[]>>(() => {
    if (initialEpisodes.length > 0) {
      return { [initialSeason]: initialEpisodes };
    }
    return {};
  });
  const [activeAccordionItem, setActiveAccordionItem] = useState(`season-${initialSeason}`);

  useEffect(() => {
    setDetails(initialDetails);
    addToHistory(initialDetails, type);
    setSeason(initialSeason);
    setEpisode(initialEpisode);
    setActiveAccordionItem(`season-${initialSeason}`);
    if (initialEpisodes.length > 0) {
        setEpisodesBySeason(prev => ({ ...prev, [initialSeason]: initialEpisodes }));
    }
  }, [initialDetails, type, initialSeason, initialEpisode, initialEpisodes]);

  useEffect(() => {
    if (details) {
      const isTV = type === 'tv'
      const imdbId = (details as MovieDetails).imdb_id || (details as TVDetails).external_ids?.imdb_id;
      
      const url = buildStreamUrl(selectedServer, Number(details.id), imdbId, isTV ? season : undefined, isTV ? episode : undefined, isTV)
      setStreamUrl(url)
      
      if(isTV) {
        const newPath = `/play/${type}/${id}?s=${season}&e=${episode}`
        window.history.replaceState(null, '', newPath)
      }
    }
  }, [details, selectedServer, type, id, season, episode])
  
  const fetchEpisodesForSeason = useCallback(async (seasonNumber: number) => {
    if (!episodesBySeason[seasonNumber]) {
        try {
            const seasonDetails = await getSeasonDetails(id, seasonNumber);
            setEpisodesBySeason(prev => ({ ...prev, [seasonNumber]: seasonDetails.episodes }));
        } catch (error) {
            console.error(`Failed to fetch episodes for season ${seasonNumber}:`, error);
        }
    }
  }, [id, episodesBySeason]);

  const handleServerChange = (serverName: string) => {
    const server = servers.find(s => s.name === serverName)
    if (server) setSelectedServer(server)
  }

  const handleEpisodeSelect = (selectedSeason: number, selectedEpisode: number) => {
    router.push(`/play/${type}/${id}?s=${selectedSeason}&e=${selectedEpisode}`);
  };

  const handleAccordionChange = (value: string) => {
    setActiveAccordionItem(value);
    if(value) {
        const seasonNumber = Number(value.replace('season-', ''));
        fetchEpisodesForSeason(seasonNumber);
    }
  }
  
  const recommendationsFetcher = useCallback(async (page: number): Promise<(Movie|TV)[]> => {
    if (type === 'movie') {
      const data = await getMovieRecommendations(id, page);
      return data;
    } else {
      const data = await getTvRecommendations(id, page);
      return data;
    }
  }, [type, id]);

  if (!details) return <div className="px-4 md:px-8 text-center py-16">Loading player...</div>

  return (
    <div className="py-8 animate-fade-in">
      <div className="aspect-video bg-black rounded-xl overflow-hidden mb-8 mx-4 md:mx-8">
        {streamUrl && (
            <iframe
              key={`${selectedServer.name}-${id}-${season}-${episode}`}
              src={streamUrl}
              allowFullScreen
              className="w-full h-full border-0"
              sandbox={isSandboxed ? "allow-scripts allow-same-origin allow-presentation allow-fullscreen" : undefined}
            ></iframe>
        )}
      </div>

      <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 px-4 md:px-8">
        <h1 className="text-2xl font-bold text-center md:text-left">
          {type === 'movie' ? (details as MovieDetails).title : (details as TVDetails).name}
          {type === 'tv' && ` - S${season} E${episode}`}
        </h1>
        <div className="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            <div className="flex items-center space-x-2">
                <Switch id="sandbox-mode" checked={isSandboxed} onCheckedChange={setIsSandboxed} />
                <Label htmlFor="sandbox-mode">Ad-Free (Sandbox)</Label>
            </div>
          <div className="w-full sm:w-64">
            <Select onValueChange={handleServerChange} defaultValue={selectedServer.name}>
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                {servers.map(server => (
                  <SelectItem key={server.name} value={server.name}>{server.name}</SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>
      </div>
      
      {type === 'tv' && (
        <div className="mt-16 px-4 md:px-8">
          <h2 className="mb-8 text-2xl font-bold">Seasons & Episodes</h2>
          <Accordion type="single" collapsible className="w-full" value={activeAccordionItem} onValueChange={handleAccordionChange}>
            {(details as TVDetails).seasons
              .filter(season => season.season_number > 0)
              .map((sItem) => (
              <AccordionItem key={sItem.id} value={`season-${sItem.season_number}`}>
                <AccordionTrigger>
                  <div className="flex items-center gap-4">
                    <div className="relative h-28 w-20 flex-shrink-0">
                      {sItem.poster_path ? (
                        <Image
                          src={getImageUrl(sItem.poster_path, 'w300')!}
                          alt={sItem.name}
                          width={80}
                          height={112}
                          className="rounded-md object-cover w-full h-full"
                        />
                      ) : (
                         <div className="flex h-full w-full items-center justify-center rounded-md bg-muted">
                           <Tv className="h-8 w-8 text-muted-foreground" />
                         </div>
                      )}
                    </div>
                    <div>
                      <h3 className="text-xl font-semibold">{sItem.name}</h3>
                      <div className="text-sm text-muted-foreground">
                        <span>{sItem.air_date ? format(new Date(sItem.air_date), 'yyyy') : 'TBA'}</span>
                        <span className="mx-2">&bull;</span>
                        <span>{sItem.episode_count} Episodes</span>
                      </div>
                    </div>
                  </div>
                </AccordionTrigger>
                <AccordionContent>
                  <Suspense fallback={<EpisodeGridSkeleton />}>
                    {episodesBySeason[sItem.season_number] ? (
                        <EpisodesGrid 
                            season={sItem} 
                            episodes={episodesBySeason[sItem.season_number]}
                            currentEpisodeNumber={sItem.season_number === season ? episode : -1} 
                            onEpisodeSelect={handleEpisodeSelect}
                        />
                    ) : (
                        <EpisodeGridSkeleton />
                    )}
                  </Suspense>
                </AccordionContent>
              </AccordionItem>
            ))}
          </Accordion>
        </div>
      )}

      {details.recommendations?.results.length > 0 && (
          <div className="mt-16">
            <h2 className="text-2xl font-bold mb-8 px-4 md:px-8">Recommendations</h2>
            <InfiniteContentGrid
              initialItems={details.recommendations.results}
              totalPages={details.recommendations.total_pages}
              fetcher={recommendationsFetcher}
              contentType={type}
              showLoadMoreButton={true}
              className="px-4 md:px-8"
            />
          </div>
        )}
    </div>
  )
}

function EpisodeGridSkeleton() {
  return (
    <div className="content-grid">
      {Array.from({ length: 8 }).map((_, index) => (
        <div key={index} className="space-y-2">
           <Skeleton className="aspect-video w-full rounded-lg" />
           <Skeleton className="h-4 w-3/4" />
           <Skeleton className="h-3 w-1/2" />
         </div>
      ))}
    </div>
  );
}
