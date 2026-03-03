import { Link } from "wouter";
import { motion } from "framer-motion";
import { Play, Star } from "lucide-react";
import { TMDBItem, getImageUrl } from "@/lib/tmdb";

interface MovieCardProps {
  item: TMDBItem;
  index?: number;
}

export function MovieCard({ item, index = 0 }: MovieCardProps) {
  const type = item.media_type || (item.name ? 'tv' : 'movie');
  const title = item.title || item.name;

  return (
    <Link href={`/watch/${type}/${item.id}`}>
      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ delay: Math.min(index, 12) * 0.03, duration: 0.3 }}
        className="group relative cursor-pointer overflow-hidden rounded-lg neu-hover-inset"
        style={{
          background: 'linear-gradient(145deg, #1e2237, #171a2a)',
          boxShadow: '5px 5px 12px #0c0e1d, -5px -5px 12px #272c44',
        }}
        data-testid={`card-movie-${item.id}`}
      >
        <div className="relative aspect-[2/3] overflow-hidden rounded-lg">
          <img
            src={getImageUrl(item.poster_path)}
            alt={title}
            className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
          />

          <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />

          <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none" data-testid={`play-button-${item.id}`}>
            <div className="w-12 h-12 rounded-full border-2 border-primary flex items-center justify-center bg-black/50 backdrop-saturate-150 shadow-[0_0_20px_rgba(0,255,255,0.35)]">
              <Play className="w-5 h-5 text-primary fill-primary pl-0.5" />
            </div>
          </div>

          {item.vote_average > 0 && (
            <div className="absolute top-1.5 right-1.5 flex items-center gap-0.5 bg-black/50 rounded px-1.5 py-0.5">
              <Star className="w-2.5 h-2.5 text-accent fill-accent" />
              <span className="text-[10px] font-bold text-white/90">{item.vote_average.toFixed(1)}</span>
            </div>
          )}
        </div>
      </motion.div>
    </Link>
  );
}
