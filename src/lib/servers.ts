export interface Server {
  name: string
  type: "imdb" | "tmdb"
  url: string
  url_tv: string
}

export const servers: Server[] = [

 {
    name: "Vidify",
    type: "tmdb",
    url: "https://vidify.top/embed/movie/{tmdb_id}",
    url_tv: "https://vidify.top/embed/tv/{tmdb_id}/{season}/{episode}",
  },
  {
    name: "RiveStream",
    type: "tmdb",
    url: "https://rivestream.org/embed?type=movie&id={tmdb_id}",
    url_tv: "https://rivestream.org/embed?type=tv&id={tmdb_id}&season={season}&episode={episode}",
  },
{
    name: "VidJoy",
    type: "tmdb",
    url: "https://vidjoy.pro/embed/movie/{tmdb_id}",
    url_tv: "https://vidjoy.pro/embed/tv/{tmdb_id}/{season}/{episode}",
  },
{  
    name: "1XBET NO POPUP ❤️‍🔥 DATA SAVER ✅ HD+",  
    type: "imdb",  
    url: "https://dezzu370xol.com/play/{imdb_id}",  
    url_tv: "https://dezzu370xol.com/play/{imdb_id}", 
  },   

{  
    name: "Change Server If Not Playing",  
    type: "tmdb",  
    url: "https://autoembed.co/movie/tmdb/{tmdb_id}",  
    url_tv: "https://autoembed.co/tv/tmdb/{tmdb_id}-{season}-{episode}",  
  },  
  {
    name: "All in one 🔥with download + 4k size",
    type: "tmdb",
    url: "https://iframe.pstream.mov/media/tmdb-movie-{tmdb_id}",
    url_tv: "https://iframe.pstream.mov/media/tmdb-tv-{tmdb_id}-{season}-{episode}",
  }, {
    name: "Hexa",
    type: "tmdb",
    url: "https://hexa.watch/watch/movie/{tmdb_id}",
    url_tv: "https://hexa.watch/watch/tv/{tmdb_id}/{season}/{episode}",
  }, 
  {  
    name: "Best no pupup ads",  
    type: "imdb",  
    url: "https://test.autoembed.cc/embed/movie/{tmdb_id}",  
    url_tv: "https://test.autoembed.cc/embed/tv/{tmdb_id}",  
  },  
  {  
    name: "vid rock with download link 🔗",  
    type: "imdb",  
    url: "https://vidrock.net/movie/{imdb_id}",  
    url_tv: "https://vidrock.net/tv/{imdb_id}/{season}/{episode}",  
  },   {
    name: "Vip Playing",
    type: "imdb",
    url: "https://vidsrc.vip/embed/movie/{imdb_id}",
    url_tv: "https://vidsrc.vip/embed/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "MoviesAPI",
    type: "tmdb",
    url: "https://moviesapi.club/movie/{tmdb_id}",
    url_tv: "https://moviesapi.club/tv/{tmdb_id}-{season}-{episode}",
  },
{
    name: "VidSrcVIP",
    type: "tmdb",
    url: "https://vidsrc.vip/embed/movie/{tmdb_id}",
    url_tv: "https://vidsrc.vip/embed/tv/{tmdb_id}/{season}/{episode}",
  },
  {
    name: "2Embed",
    type: "tmdb",
    url: "https://www.2embed.cc/embed/{tmdb_id}",
    url_tv: "https://www.2embed.cc/embedtv/{tmdb_id}&s={season}&e={episode}",
  },
 
  {
    name: "alpha",
    type: "imdb",
    url: "https://vidsrc.to/embed/movie/{imdb_id}",
    url_tv: "https://vidsrc.to/embed/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "beta",
    type: "imdb",
    url: "https://vidsrc.icu/embed/movie/{imdb_id}",
    url_tv: "https://vidsrc.icu/embed/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "gama",
    type: "imdb",
    url: "https://vidsrc.cc/v2/embed/movie/{imdb_id}",
    url_tv: "https://vidsrc.cc/v2/embed/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "penta",
    type: "imdb",
    url: "https://embed.su/embed/movie/{imdb_id}",
    url_tv: "https://embed.su/embed/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "vidme",
    type: "imdb",
    url: "https://vidsrc.me/embed/movie/{imdb_id}",
    url_tv: "https://vidsrc.me/embed/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "autoemded pro",
    type: "imdb",
    url: "https://autoembed.pro/embed/movie/{imdb_id}",
    url_tv: "https://autoembed.pro/embed/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "vifast",
    type: "imdb",
    url: "https://vidfast.pro/movie/{imdb_id}",
    url_tv: "https://vidfast.pro/tv/{imdb_id}/{season}/{episode}",
  },
 
  {
    name: "High HD",
    type: "imdb",
    url: "https://hyhd.org/embed/{imdb_id}",
    url_tv: "https://hyhd.org/embed/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "11 movies",
    type: "imdb",
    url: "https://111movies.com/movie/{imdb_id}",
    url_tv: "https://111movies.com/tv/{imdb_id}/{season}/{episode}",
  },
  {
    name: "MultiEmbed",
    type: "tmdb",
    url: "https://multiembed.mov/?video_id={tmdb_id}&tmdb=1",
    url_tv: "https://multiembed.mov/?video_id={tmdb_id}&tmdb=1&s={season}&e={episode}",
  },

  {
    name: "EmbedSU",
    type: "tmdb",
    url: "https://embed.su/embed/movie/{tmdb_id}",
    url_tv: "https://embed.su/embed/tv/{tmdb_id}/{season}/{episode}",
  },
 
  {
    name: "VidLink",
    type: "tmdb",
    url: "https://vidlink.pro/movie/{tmdb_id}",
    url_tv: "https://vidlink.pro/tv/{tmdb_id}/{season}/{episode}",
  },
  {
    name: "VidSrcXyz",
    type: "tmdb",
    url: "https://vidsrc.xyz/embed/movie/{tmdb_id}",
    url_tv: "https://vidsrc.xyz/embed/tv/{tmdb_id}/{season}/{episode}",
  },
 
  {
    name: "VidSrcSU",
    type: "tmdb",
    url: "https://vidsrc.su/embed/movie/{tmdb_id}",
    url_tv: "https://vidsrc.su/embed/tv/{tmdb_id}/{season}/{episode}",
  },
 
  {
    name: "123Embed",
    type: "tmdb",
    url: "https://play2.123embed.net/movie/{tmdb_id}",
    url_tv: "https://play2.123embed.net/tv/{tmdb_id}/{season}/{episode}",
  },
  {
    name: "SmashyStream",
    type: "tmdb",
    url: "https://player.smashy.stream/movie/{tmdb_id}",
    url_tv: "https://player.smashy.stream/tv/{tmdb_id}?s={season}&e={episode}",
  },
  {
    name: "VidEasy (4K)",
    type: "tmdb",
    url: "https://player.videasy.net/movie/{tmdb_id}?color=8834ec",
    url_tv: "https://player.videasy.net/tv/{tmdb_id}/{season}/{episode}?color=8834ec",
  },
 
  {
    name: "Vidora",
    type: "tmdb",
    url: "https://vidora.su/movie/{tmdb_id}",
    url_tv: "https://vidora.su/tv/{tmdb_id}/{season}/{episode}",
  },
  {
    name: "VidSrcCC",
    type: "tmdb",
    url: "https://vidsrc.cc/v2/embed/movie/{tmdb_id}?autoPlay=false",
    url_tv: "https://vidsrc.cc/v2/embed/tv/{tmdb_id}/{season}/{episode}?autoPlay=false",
  },
  {
    name: "StreamFlix",
    type: "tmdb",
    url: "https://watch.streamflix.one/movie/{tmdb_id}/watch?server=1",
    url_tv: "https://watch.streamflix.one/tv/{tmdb_id}/watch?server=1&season={season}&episode={episode}",
  },
  {
    name: "NebulaFlix",
    type: "tmdb",
    url: "https://nebulaflix.stream/movie?mt={tmdb_id}&server=1",
    url_tv: "https://nebulaflix.stream/show?st={tmdb_id}&season={season}&episode={episode}&server=1",
  },
  
  {
    name: "VidZee",
    type: "tmdb",
    url: "https://player.vidzee.wtf/embed/movie/{tmdb_id}",
    url_tv: "https://player.vidzee.wtf/embed/tv/{tmdb_id}/{season}/{episode}",
  },
  {
    name: "Spenflix",
    type: "tmdb",
    url: "https://spencerdevs.xyz/movie/{tmdb_id}",
    url_tv: "https://spencerdevs.xyz/tv/{tmdb_id}/{season}/{episode}",
  },
  {
    name: "Frembed (FR)",
    type: "tmdb",
    url: "https://frembed.icu/api/film.php?id={tmdb_id}",
    url_tv: "https://frembed.icu/api/serie.php?id={tmdb_id}&sa={season}&epi={episode}",
  },
  {
    name: "UEmbed (premium)",
    type: "tmdb",
    url: "https://uembed.site/?id={tmdb_id}&apikey=thisisforsurenotapremiumkey_right?",
    url_tv:
      "https://uembed.site/?id={tmdb_id}&season={season}&episode={episode}&apikey=thisisforsurenotapremiumkey_right?",
  },
]

export const buildStreamUrl = (
  server: Server,
  tmdbId: number,
  imdbId?: string,
  season?: number,
  episode?: number,
  isTV = false,
): string => {
  const template = isTV ? server.url_tv : server.url

  let url = template

  // Replace TMDB ID
  url = url.replace(/\{tmdb_id\}/g, tmdbId.toString())

  // Replace IMDB ID (ensure it has 'tt' prefix if it's an IMDB server)
  if (server.type === "imdb" && imdbId) {
    const formattedImdbId = imdbId.startsWith("tt") ? imdbId : `tt${imdbId}`
    url = url.replace(/\{imdb_id\}/g, formattedImdbId)
  } else if (server.type === "imdb" && !imdbId) {
    // If IMDB server but no IMDB ID available, use empty string
    url = url.replace(/\{imdb_id\}/g, "")
  }

  // Replace season and episode for TV shows
  if (isTV && season && episode) {
    url = url.replace(/\{season\}/g, season.toString())
    url = url.replace(/\{episode\}/g, episode.toString())
  }

  return url
}
