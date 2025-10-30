import { Suspense } from "react"
import { PlayerPageContent } from "./player-page-content"
import { Skeleton } from "@/components/ui/skeleton";
import { getMovieDetails, getSeasonDetails, getTvDetails } from "@/lib/tmdb";
import { notFound } from "next/navigation";
import type { MovieDetails, TVDetails, Episode } from "@/types/tmdb";

export const runtime = 'edge';

interface PlayerPageProps {
  params: { type: 'movie' | 'tv'; id: string };
  searchParams: { [key: string]: string | string[] | undefined };
}

function PlayerPageSkeleton() {
    return (
        <div className="py-8 px-4 md:px-8 animate-fade-in">
            <Skeleton className="aspect-video bg-black rounded-xl mb-8" />
            <div className="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <Skeleton className="h-8 w-1/2 md:w-1/3" />
                <Skeleton className="h-10 w-full md:w-64" />
            </div>
        </div>
    )
}

export default async function PlayerPage({ params, searchParams }: PlayerPageProps) {
    const { type, id } = params;
    const { s, e } = searchParams;
    const seasonNumber = s ? Number(s) : 1;
    const episodeNumber = e ? Number(e) : 1;

    let details: MovieDetails | TVDetails;
    let initialEpisodes: Episode[] = [];

    try {
        if (type === 'movie') {
            details = await getMovieDetails(id);
        } else if (type === 'tv') {
            details = await getTvDetails(id);
            if ((details as TVDetails).seasons.some(season => season.season_number === seasonNumber)) {
                const seasonDetails = await getSeasonDetails(id, seasonNumber);
                initialEpisodes = seasonDetails.episodes;
            }
        } else {
            notFound();
        }
    } catch (error) {
        console.error("Failed to fetch details for player:", error);
        notFound();
    }

    return (
        <Suspense fallback={<PlayerPageSkeleton />}>
            <PlayerPageContent 
                type={type} 
                id={id} 
                initialDetails={details}
                initialSeason={seasonNumber}
                initialEpisode={episodeNumber}
                initialEpisodes={initialEpisodes}
            />
        </Suspense>
    )
}
