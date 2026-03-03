import { useState, useEffect } from "react";
import { useParams } from "wouter";
import { Link } from "wouter";
import { Shell } from "@/components/layout/Shell";
import { useDetails, useSeason } from "@/hooks/use-tmdb";
import { useWatchlist, useAddToWatchlist, useRemoveFromWatchlist } from "@/hooks/use-watchlist";
import { useQuery } from "@tanstack/react-query";
import { buildServerUrl } from "@shared/servers";
import type { CustomServer, Movie } from "@shared/schema";
import { getImageUrl } from "@/lib/tmdb";
import {
  Loader2, Play, Download, BookmarkPlus, BookmarkCheck, Share2,
  Star, Clock, Calendar, Check, ChevronDown, Film, Monitor, Globe, ExternalLink, Lock, Crown
} from "lucide-react";
import { ContentRow } from "@/components/shared/ContentRow";
import { MovieCard } from "@/components/shared/MovieCard";
import { ReactionsAndComments } from "@/components/shared/ReactionsAndComments";

export default function Player() {
  const { type, id } = useParams<{ type: 'movie' | 'tv', id: string }>();
  const numericId = parseInt(id || "0");

  const { data: dbDetails } = useQuery({
    queryKey: ['/api/movies', numericId, 'details'],
    queryFn: async () => {
      const res = await fetch(`/api/movies/${numericId}/details?type=${type}`);
      if (!res.ok) return null;
      return res.json();
    },
    enabled: !!numericId,
  });

  const { data: tmdbDetails, isLoading: tmdbLoading } = useDetails(type, numericId);
  const details = dbDetails || tmdbDetails;
  const isLoading = !details && tmdbLoading;

  const { data: publicSettings } = useQuery<{ subscriptionEnabled: boolean; subscriptionName: string; subscriptionAmount: string; paymentEnabled: boolean; adEnabled: boolean; adType: string; adUrl: string | null; adSkipSeconds: number }>({
    queryKey: ['/api/settings/public'],
  });

  const { data: servers = [] } = useQuery<CustomServer[]>({
    queryKey: ['/api/servers'],
  });

  const { data: watchProvidersData } = useQuery({
    queryKey: ['/api/tmdb', type, numericId, 'providers'],
    queryFn: async () => {
      const res = await fetch(`/api/tmdb/${type}/${numericId}/providers`);
      if (!res.ok) return null;
      return res.json();
    },
    enabled: !!numericId,
  });
  const inProviders = watchProvidersData?.results?.IN;
  const allProviders: any[] = [
    ...(inProviders?.flatrate || []),
    ...(inProviders?.free || []),
  ].filter((p, i, arr) => arr.findIndex(x => x.provider_id === p.provider_id) === i);

  const streamingServers = servers.filter(s => !s.isDownload && s.isActive);
  const downloadServers = servers.filter(s => s.isDownload && s.isActive);

  const { data: watchlist } = useWatchlist();
  const addWatchlist = useAddToWatchlist();
  const removeWatchlist = useRemoveFromWatchlist();

  const [activeServerId, setActiveServerId] = useState<number | null>(null);
  const [selectedSeason, setSelectedSeason] = useState(1);
  const [selectedEpisode, setSelectedEpisode] = useState(1);
  const [copied, setCopied] = useState(false);
  const [showTrailer, setShowTrailer] = useState(false);
  const [serverDropdownOpen, setServerDropdownOpen] = useState(false);
  const [adVisible, setAdVisible] = useState(false);
  const [adCountdown, setAdCountdown] = useState(0);

  useEffect(() => {
    if (streamingServers.length > 0 && activeServerId === null) {
      setActiveServerId(streamingServers[0].id);
    }
  }, [streamingServers, activeServerId]);

  useEffect(() => {
    if (publicSettings?.adEnabled && publicSettings?.adUrl && !subscriptionRequired) {
      const skip = publicSettings.adSkipSeconds ?? 5;
      setAdCountdown(skip);
      setAdVisible(true);
    }
  }, [publicSettings?.adEnabled, publicSettings?.adUrl, subscriptionRequired]);

  useEffect(() => {
    if (!adVisible || adCountdown <= 0) return;
    const t = setTimeout(() => setAdCountdown(c => c - 1), 1000);
    return () => clearTimeout(t);
  }, [adVisible, adCountdown]);

  const activeServer = streamingServers.find(s => s.id === activeServerId) || streamingServers[0];

  const { data: episodes, isLoading: loadingEpisodes } = useSeason(
    type === 'tv' ? numericId : 0, 
    selectedSeason
  );

  const watchlistItem = watchlist?.find(w => w.tmdbId === numericId && w.type === type);
  const isSaved = !!watchlistItem;

  const handleWatchlist = () => {
    if (isSaved && watchlistItem) {
      removeWatchlist.mutate(watchlistItem.id);
    } else if (details) {
      addWatchlist.mutate({
        tmdbId: numericId,
        type: type,
        title: details.title || details.name || "Unknown",
        posterPath: details.poster_path
      });
    }
  };

  const handleShare = async () => {
    const url = window.location.href;
    if (navigator.share) {
      try {
        await navigator.share({ title: details?.title || details?.name, url });
      } catch (err) { /* user cancelled */ }
    } else {
      navigator.clipboard.writeText(url);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  if (isLoading || !details) {
    return (
      <Shell>
        <div className="h-screen flex items-center justify-center">
          <Loader2 className="w-12 h-12 text-primary animate-spin" />
        </div>
      </Shell>
    );
  }

  const title = details.title || details.name;
  const year = (details.release_date || details.first_air_date || "").substring(0, 4);
  const runtime = details.runtime || (details.episode_run_time?.[0]) || 0;
  const trailerVideo = details.videos?.results?.find(
    (v: any) => v.type === 'Trailer' && v.site === 'YouTube'
  );

  const subscriptionRequired = false;

  const customDownloadLinks: { quality: string; url: string }[] = (() => {
    try {
      if (dbDetails?.downloadLinksJson) return JSON.parse(dbDetails.downloadLinksJson);
    } catch {}
    return [];
  })();

  const iframeSrc = showTrailer && trailerVideo
    ? `https://www.youtube.com/embed/${trailerVideo.key}?autoplay=1`
    : activeServer ? buildServerUrl(
        { url: activeServer.url, url_tv: activeServer.urlTv, type: activeServer.type },
        numericId, undefined, selectedSeason, selectedEpisode, type === 'tv', title
      ) : '';

  return (
    <Shell>
      <div className="pt-16 lg:pt-20 pb-20 max-w-screen-2xl mx-auto">
        
        <div className="px-0 lg:px-8 grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6">
          
          <div className="flex flex-col gap-4">
            
            <div className="relative w-full aspect-video bg-black overflow-hidden rounded-xl neu-raised border border-white/5">
              <div 
                className="absolute inset-0 bg-cover bg-center opacity-20 blur-md"
                style={{ backgroundImage: `url(${getImageUrl(details.backdrop_path, 'w1280')})`}}
              />
              {adVisible && !subscriptionRequired && (
                <div className="absolute inset-0 z-20 flex flex-col bg-black" data-testid="ad-overlay">
                  {publicSettings?.adType === 'vast' ? (
                    <iframe src={publicSettings.adUrl || ''} className="w-full h-full border-0" title="Ad" allowFullScreen />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center">
                      <img src={publicSettings?.adUrl || ''} alt="Advertisement" className="max-w-full max-h-full object-contain" />
                    </div>
                  )}
                  <div className="absolute bottom-4 right-4 flex items-center gap-2">
                    <span className="text-xs text-white/60 bg-black/60 px-2 py-1 rounded">Advertisement</span>
                    {adCountdown > 0 ? (
                      <span className="text-sm font-bold text-white bg-black/70 px-3 py-1.5 rounded-lg" data-testid="ad-countdown">
                        Skip in {adCountdown}s
                      </span>
                    ) : (
                      <button
                        onClick={() => setAdVisible(false)}
                        className="text-sm font-bold text-black bg-white px-4 py-1.5 rounded-lg hover:bg-white/90 transition-colors"
                        data-testid="button-skip-ad"
                      >
                        Skip Ad ›
                      </button>
                    )}
                  </div>
                </div>
              )}

              {subscriptionRequired ? (
                <div className="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/80" data-testid="subscription-gate">
                  <div className="neu-flat p-8 border border-white/10 rounded-xl max-w-md text-center mx-4">
                    <Lock className="w-12 h-12 text-primary mx-auto mb-4" />
                    <h2 className="text-xl font-display font-bold text-white mb-2">Subscription Required</h2>
                    <p className="text-sm text-muted-foreground mb-5">Subscribe to {publicSettings?.subscriptionName || 'Premium'} to watch this content.</p>
                    <div className="flex items-end justify-center gap-1 mb-5">
                      <span className="text-3xl font-bold text-white">₹{publicSettings?.subscriptionAmount || '299'}</span>
                      <span className="text-muted-foreground text-sm mb-1">/month</span>
                    </div>
                    <Link href="/profile">
                      <button className="px-6 py-3 bg-primary text-primary-foreground font-bold text-sm hover:opacity-90 transition-opacity rounded-lg flex items-center gap-2 mx-auto" data-testid="button-subscribe-gate">
                        <Crown className="w-4 h-4" />
                        Subscribe Now
                      </button>
                    </Link>
                  </div>
                </div>
              ) : (
                <iframe
                  src={iframeSrc}
                  allowFullScreen
                  className="absolute inset-0 w-full h-full z-10"
                  title={`${title} Player`}
                />
              )}
            </div>

            <div className="px-4 lg:px-0 flex flex-wrap items-center gap-2">
              <div className="relative flex-1 min-w-[200px]" data-testid="server-selector">
                <button 
                  onClick={() => setServerDropdownOpen(!serverDropdownOpen)}
                  className="w-full flex items-center justify-between gap-2 px-4 py-2.5 neu-flat border border-white/10 text-sm text-white hover:border-primary/30 transition-colors rounded-xl"
                  data-testid="button-server-dropdown"
                >
                  <div className="flex items-center gap-2">
                    <Monitor className="w-4 h-4 text-primary" />
                    <span className="font-medium">{activeServer?.name || 'Select Server'}</span>
                    {activeServer?.hasAds && <span className="text-[10px] bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded-md">Ads</span>}
                  </div>
                  <ChevronDown className={`w-4 h-4 text-muted-foreground transition-transform ${serverDropdownOpen ? 'rotate-180' : ''}`} />
                </button>
                
                {serverDropdownOpen && (
                  <div className="absolute top-full left-0 right-0 mt-1 z-30 neu-raised border border-white/10 max-h-64 overflow-y-auto rounded-xl" data-testid="server-dropdown-list">
                    <div className="p-2 border-b border-white/5">
                      <span className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest px-2">Streaming Servers</span>
                    </div>
                    {streamingServers.map(srv => (
                      <button
                        key={srv.id}
                        onClick={() => { setActiveServerId(srv.id); setServerDropdownOpen(false); setShowTrailer(false); }}
                        className={`w-full flex items-center gap-3 px-4 py-2.5 text-sm text-left transition-colors ${
                          activeServerId === srv.id ? 'bg-primary/10 text-primary' : 'text-white/80 hover:bg-secondary'
                        }`}
                        data-testid={`server-option-${srv.name}`}
                      >
                        <Monitor className="w-3.5 h-3.5 shrink-0" />
                        <span className="flex-1">{srv.icon || ''} {srv.name}</span>
                        {srv.hasAds && <span className="text-[9px] bg-red-500/20 text-red-400 px-1 py-0.5 rounded-md">Ads</span>}
                        {srv.has4K && <span className="text-[9px] bg-primary/20 text-primary px-1 py-0.5 rounded-md">4K</span>}
                        {activeServerId === srv.id && <Check className="w-3.5 h-3.5 text-primary" />}
                      </button>
                    ))}
                  </div>
                )}
              </div>

              {trailerVideo && (
                <button
                  onClick={() => setShowTrailer(!showTrailer)}
                  className={`flex items-center gap-2 px-4 py-2.5 text-sm font-medium transition-all rounded-xl ${
                    showTrailer ? 'bg-accent text-white' : 'neu-flat border border-white/10 text-white hover:border-accent/30'
                  }`}
                  data-testid="button-trailer"
                >
                  <Play className="w-3.5 h-3.5 fill-current" />
                  Trailer
                </button>
              )}

              <button onClick={handleWatchlist} disabled={addWatchlist.isPending || removeWatchlist.isPending}
                className={`flex items-center gap-2 px-4 py-2.5 text-sm font-medium transition-all rounded-xl ${
                  isSaved ? 'neu-pressed text-primary' : 'neu-flat border border-white/10 text-white hover:border-primary/30'
                }`}
                data-testid="button-watchlist"
              >
                {isSaved ? <BookmarkCheck className="w-4 h-4" /> : <BookmarkPlus className="w-4 h-4" />}
                <span className="hidden sm:inline">{isSaved ? 'Saved' : 'Watchlist'}</span>
              </button>

              <button onClick={handleShare}
                className="flex items-center gap-2 px-3 py-2.5 neu-flat border border-white/10 text-white text-sm hover:border-white/20 transition-colors rounded-xl"
                data-testid="button-share"
              >
                {copied ? <Check className="w-4 h-4 text-green-400" /> : <Share2 className="w-4 h-4" />}
              </button>
            </div>

            {type === 'tv' && details.seasons && (
              <div className="px-4 lg:px-0 neu-flat p-5 border border-white/5 rounded-xl">
                <div className="flex items-center justify-between gap-4 mb-4">
                  <h3 className="text-lg font-display font-bold text-white">Episodes</h3>
                  <select 
                    value={selectedSeason}
                    onChange={(e) => setSelectedSeason(Number(e.target.value))}
                    className="bg-background border border-white/10 px-4 py-2 text-white text-sm outline-none focus:border-primary rounded-lg"
                    data-testid="select-season"
                  >
                    {details.seasons.filter((s: any) => s.season_number > 0).map((s: any) => (
                      <option key={s.id} value={s.season_number}>Season {s.season_number}</option>
                    ))}
                  </select>
                </div>

                <div className="flex flex-col gap-2 max-h-[350px] overflow-y-auto pr-1">
                  {loadingEpisodes ? (
                    <Loader2 className="w-8 h-8 text-primary animate-spin mx-auto my-10" />
                  ) : (
                    episodes?.map((ep: any) => (
                      <button
                        key={ep.id}
                        onClick={() => { setSelectedEpisode(ep.episode_number); setShowTrailer(false); }}
                        className={`flex gap-3 p-2.5 transition-all text-left rounded-lg ${
                          selectedEpisode === ep.episode_number 
                            ? "bg-primary/10 border-primary/30 border" 
                            : "bg-background/30 hover:bg-secondary border border-transparent"
                        }`}
                        data-testid={`episode-${ep.episode_number}`}
                      >
                        <div className="relative w-28 aspect-video overflow-hidden flex-shrink-0 bg-black rounded-md">
                          {ep.still_path ? (
                            <img src={getImageUrl(ep.still_path)} alt={ep.name} className="w-full h-full object-cover opacity-80" />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center text-xs text-muted-foreground">No Img</div>
                          )}
                          {selectedEpisode === ep.episode_number && (
                            <div className="absolute inset-0 bg-primary/20 flex items-center justify-center">
                              <Play className="w-5 h-5 text-white fill-current drop-shadow-lg" />
                            </div>
                          )}
                        </div>
                        <div className="flex flex-col justify-center min-w-0">
                          <span className="text-xs text-primary font-bold">E{ep.episode_number}</span>
                          <span className="text-white text-sm font-medium line-clamp-1">{ep.name}</span>
                          <span className="text-muted-foreground text-xs line-clamp-1 mt-0.5">{ep.overview || "No description"}</span>
                        </div>
                      </button>
                    ))
                  )}
                </div>
              </div>
            )}

            {customDownloadLinks.length > 0 && (
              <div className="px-4 lg:px-0">
                <h3 className="text-sm font-bold text-muted-foreground uppercase tracking-widest mb-3">Quality Downloads</h3>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                  {customDownloadLinks.map((link, idx) => (
                    <a key={idx} href={link.url} target="_blank" rel="noopener noreferrer"
                      className="flex items-center justify-between p-3 neu-flat border border-white/5 hover:border-primary/30 transition-all group rounded-xl"
                      data-testid={`quality-download-${idx}`}
                    >
                      <div className="flex items-center gap-2.5">
                        <Download className="w-4 h-4 text-primary" />
                        <span className="font-medium text-sm text-white group-hover:text-primary transition-colors">{link.quality}</span>
                      </div>
                      <ExternalLink className="w-3.5 h-3.5 text-muted-foreground group-hover:text-white shrink-0" />
                    </a>
                  ))}
                </div>
              </div>
            )}

            {downloadServers.length > 0 && (
              <div className="px-4 lg:px-0">
                <h3 className="text-sm font-bold text-muted-foreground uppercase tracking-widest mb-3">Downloads</h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  {downloadServers.map(srv => {
                    const dlUrl = buildServerUrl(
                      { url: srv.url, url_tv: srv.urlTv, type: srv.type },
                      numericId, undefined, selectedSeason, selectedEpisode, type === 'tv', title
                    );
                    return (
                      <a key={srv.id} href={dlUrl} target="_blank" rel="noopener noreferrer"
                        className="flex items-center justify-between p-3 neu-flat border border-white/5 hover:border-accent/30 transition-all group rounded-xl"
                        data-testid={`download-${srv.name}`}
                      >
                        <div className="flex items-center gap-2.5">
                          <span className="text-lg">{srv.icon || "📥"}</span>
                          <div className="flex flex-col">
                            <span className="font-medium text-sm text-white group-hover:text-accent transition-colors">{srv.name}</span>
                            {srv.description && <span className="text-[10px] text-muted-foreground">{srv.description}</span>}
                          </div>
                        </div>
                        <ExternalLink className="w-4 h-4 text-muted-foreground group-hover:text-white shrink-0" />
                      </a>
                    );
                  })}
                </div>
              </div>
            )}
          </div>

          <div className="px-4 lg:px-0 flex flex-col gap-4">
            
            <div className="neu-flat p-5 border border-white/5 rounded-xl relative overflow-hidden">
              {details.backdrop_path && (
                <div className="absolute inset-0 opacity-[0.06]"
                  style={{ backgroundImage: `url(${getImageUrl(details.backdrop_path, 'w780')})`, backgroundSize: 'cover', backgroundPosition: 'center' }}
                />
              )}
              <div className="relative z-10">
                <h1 className="text-2xl md:text-3xl font-display font-bold text-white mb-2 leading-tight" data-testid="text-movie-title">
                  {title}
                </h1>
                
                <div className="flex flex-wrap items-center gap-3 text-sm text-muted-foreground mb-4">
                  {year && <span className="flex items-center gap-1"><Calendar className="w-3.5 h-3.5" />{year}</span>}
                  {runtime > 0 && <span className="flex items-center gap-1"><Clock className="w-3.5 h-3.5" />{runtime}m</span>}
                  <span className="flex items-center gap-1 text-accent"><Star className="w-3.5 h-3.5 fill-current" />{details.vote_average?.toFixed(1) || '0.0'}</span>
                  <span className="px-2 py-0.5 border border-white/20 text-[10px] text-white uppercase rounded-md">{type}</span>
                </div>

                <p className="text-white/70 text-sm leading-relaxed mb-4" data-testid="text-overview">
                  {details.overview || "No description available."}
                </p>

                {details.genres && (
                  <div className="flex flex-wrap gap-1.5 mb-4">
                    {details.genres.map((g: any) => (
                      <span key={g.id} className="px-2 py-0.5 text-[11px] font-medium text-white/50 border border-white/10 rounded">
                        {g.name}
                      </span>
                    ))}
                  </div>
                )}

              </div>
            </div>

            <div className="grid grid-cols-3 gap-2">
              <div className="neu-flat p-3 text-center border border-white/5 rounded-xl">
                <Star className="w-4 h-4 text-accent mx-auto mb-1" />
                <p className="text-lg font-bold text-white">{details.vote_average?.toFixed(1) || '0.0'}</p>
                <p className="text-[10px] text-muted-foreground uppercase">Rating</p>
              </div>
              <div className="neu-flat p-3 text-center border border-white/5 rounded-xl">
                <Film className="w-4 h-4 text-primary mx-auto mb-1" />
                <p className="text-lg font-bold text-white">{details.vote_count || 0}</p>
                <p className="text-[10px] text-muted-foreground uppercase">Votes</p>
              </div>
              <div className="neu-flat p-3 text-center border border-white/5 rounded-xl">
                <Globe className="w-4 h-4 text-green-400 mx-auto mb-1" />
                <p className="text-lg font-bold text-white">{details.popularity?.toFixed(0) || 0}</p>
                <p className="text-[10px] text-muted-foreground uppercase">Popularity</p>
              </div>
            </div>

            {details.credits?.cast && details.credits.cast.length > 0 && (
              <div className="neu-flat p-5 border border-white/5 rounded-xl">
                <h3 className="text-sm font-bold text-muted-foreground uppercase tracking-widest mb-3">Top Cast</h3>
                <div className="space-y-2.5">
                  {details.credits.cast.slice(0, 8).map((person: any) => (
                    <div key={person.id} className="flex items-center gap-3">
                      <div className="w-9 h-9 overflow-hidden bg-secondary shrink-0 rounded-full border border-white/10">
                        {person.profile_path ? (
                          <img src={getImageUrl(person.profile_path, 'w185')} alt={person.name} className="w-full h-full object-cover" />
                        ) : (
                          <div className="w-full h-full flex items-center justify-center text-[10px] text-muted-foreground">?</div>
                        )}
                      </div>
                      <div className="min-w-0">
                        <p className="text-sm font-medium text-white truncate">{person.name}</p>
                        <p className="text-[11px] text-muted-foreground truncate">{person.character}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {allProviders.length > 0 && (
              <div className="neu-flat p-5 border border-white/5 rounded-xl">
                <h3 className="text-sm font-bold text-muted-foreground uppercase tracking-widest mb-3">Watch On</h3>
                <div className="flex flex-wrap gap-2.5">
                  {allProviders.map((p: any) => (
                    <div key={p.provider_id} className="flex flex-col items-center gap-1" title={p.provider_name}>
                      <div className="w-10 h-10 rounded-xl overflow-hidden border border-white/10 shrink-0">
                        <img
                          src={`https://image.tmdb.org/t/p/w92${p.logo_path}`}
                          alt={p.provider_name}
                          className="w-full h-full object-cover"
                        />
                      </div>
                      <span className="text-[9px] text-muted-foreground text-center w-10 truncate">{p.provider_name}</span>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        </div>

        <RelatedMovies
          currentId={numericId}
          type={type}
          tmdbSimilar={tmdbDetails?.similar?.results}
          genreIds={details.genre_ids || (dbDetails?.genreIds ? dbDetails.genreIds.split(',').map(Number) : [])}
          genresJson={dbDetails?.genresJson}
        />

        <ReactionsAndComments tmdbId={numericId} mediaType={type} />
      </div>
    </Shell>
  );
}

function RelatedMovies({
  currentId, type, tmdbSimilar, genreIds, genresJson,
}: {
  currentId: number;
  type: string;
  tmdbSimilar?: any[];
  genreIds: number[];
  genresJson?: string | null;
}) {
  const { data: allDbMovies = [] } = useQuery<Movie[]>({
    queryKey: ['/api/movies'],
  });

  const genreNames: string[] = (() => {
    try {
      if (genresJson) return JSON.parse(genresJson).map((g: any) => g.name);
    } catch {}
    return [];
  })();

  const dbRelated = allDbMovies
    .filter(m => {
      if (m.tmdbId === currentId) return false;
      if (!m.genreIds) return false;
      const mGenres = m.genreIds.split(',').map(Number);
      return mGenres.some(g => genreIds.includes(g));
    })
    .sort((a, b) => (b.voteAverage || 0) - (a.voteAverage || 0))
    .slice(0, 20)
    .map(m => ({
      id: m.tmdbId,
      title: m.title,
      name: m.mediaType === 'tv' ? m.title : undefined,
      poster_path: m.posterPath,
      backdrop_path: m.backdropPath,
      vote_average: m.voteAverage || 0,
      media_type: m.mediaType,
      release_date: m.releaseDate,
      overview: m.overview || '',
    }));

  const hasTmdbSimilar = tmdbSimilar && tmdbSimilar.length > 0;
  const hasDbRelated = dbRelated.length > 0;

  if (!hasTmdbSimilar && !hasDbRelated) return null;

  return (
    <div className="mt-10 space-y-2">
      {hasTmdbSimilar && (
        <ContentRow title="More Like This">
          {tmdbSimilar!.map((item: any, i: number) => (
            <MovieCard key={item.id} item={{ ...item, media_type: type }} index={i} />
          ))}
        </ContentRow>
      )}
      {hasDbRelated && (
        <ContentRow title={genreNames.length > 0 ? `More ${genreNames[0]}` : 'Related from Library'} viewAllLink="/search">
          {dbRelated.map((item: any, i: number) => (
            <MovieCard key={`db-${item.id}`} item={item} index={i} />
          ))}
        </ContentRow>
      )}
    </div>
  );
}
