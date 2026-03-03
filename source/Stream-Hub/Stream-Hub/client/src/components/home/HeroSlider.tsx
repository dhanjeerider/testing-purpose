import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Play } from "lucide-react";
import { Link } from "wouter";
import { TMDBItem, getImageUrl } from "@/lib/tmdb";

export function HeroSlider({ items }: { items: TMDBItem[] }) {
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    if (!items.length) return;
    const timer = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % items.length);
    }, 8000);
    return () => clearInterval(timer);
  }, [items.length]);

  if (!items.length) return <div className="w-full h-[50vh] md:h-[70vh] bg-background animate-pulse" />;

  const item = items[currentIndex];
  const type = item.media_type || (item.name ? 'tv' : 'movie');
  const title = item.title || item.name;

  return (
    <div className="relative w-full h-[50vh] md:h-[70vh] overflow-hidden bg-black">
      <AnimatePresence mode="wait">
        <motion.div
          key={item.id}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.8, ease: "easeInOut" }}
          className="absolute inset-0"
        >
          <div className="absolute inset-0">
            <img
              src={getImageUrl(item.backdrop_path || item.poster_path, 'original')}
              alt={title}
              className="w-full h-full object-cover object-top opacity-80"
            />
          </div>

          <div className="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent" />
          <div className="absolute inset-0 bg-gradient-to-r from-background/80 via-transparent to-transparent w-3/4" />
        </motion.div>
      </AnimatePresence>

      <div className="absolute inset-0 px-4 lg:px-12 flex flex-col justify-end pb-12 md:pb-24 z-10 max-w-6xl">
        <AnimatePresence mode="wait">
          <motion.div
            key={`content-${item.id}`}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            transition={{ duration: 0.5, delay: 0.2 }}
            className="max-w-xl"
          >
            <div className="flex items-center gap-3 mb-2 md:mb-3">
              <span className="text-[10px] font-bold uppercase tracking-widest text-primary border border-primary/40 px-2 py-0.5">
                {type === 'movie' ? 'Movie' : 'TV'}
              </span>
              <span className="text-xs font-bold text-accent">&#9733; {item.vote_average?.toFixed(1)}</span>
            </div>

            <h1 className="text-2xl md:text-5xl lg:text-6xl font-display font-bold text-white leading-tight mb-2 md:mb-3 hero-text-shadow">
              {title}
            </h1>

            <p className="hidden md:block text-sm md:text-base text-white/60 line-clamp-2 mb-4 max-w-lg hero-text-shadow">
              {item.overview}
            </p>

            <div className="flex items-center gap-2 md:gap-3">
              <Link href={`/watch/${type}/${item.id}`}>
                <button className="group relative px-6 py-2.5 md:px-8 md:py-3.5 bg-primary text-primary-foreground font-bold flex items-center gap-2 md:gap-2.5 hover:shadow-[0_0_20px_rgba(0,229,255,0.4)] transition-all duration-300 text-xs md:text-sm uppercase tracking-widest rounded-sm" data-testid="hero-watch-button">
                  <Play className="w-4 h-4 md:w-5 md:h-5 fill-current" />
                  Play Now
                </button>
              </Link>
            </div>
          </motion.div>
        </AnimatePresence>

        <div className="absolute bottom-4 md:bottom-6 right-4 lg:right-12 flex gap-1.5">
          {items.slice(0, 5).map((_, idx) => (
            <button
              key={idx}
              onClick={() => setCurrentIndex(idx)}
              className={`h-1 transition-all duration-300 ${
                idx === currentIndex ? "w-6 bg-primary" : "w-2 bg-white/20 hover:bg-white/40"
              }`}
              data-testid={`hero-indicator-${idx}`}
            />
          ))}
        </div>
      </div>
    </div>
  );
}
