import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Play, Film, Tv, Sparkles, Send, Check } from "lucide-react";

const SPLASH_KEY = "tmovie_splash_seen";
const LANG_KEY = "tmovie_splash_lang";

const LANGUAGES = [
  { code: "all", label: "All Languages", flag: "🌐" },
  { code: "hi", label: "Hindi", flag: "🇮🇳" },
  { code: "en", label: "English", flag: "🇺🇸" },
  { code: "ja", label: "Japanese", flag: "🇯🇵" },
  { code: "ko", label: "Korean", flag: "🇰🇷" },
  { code: "es", label: "Spanish", flag: "🇪🇸" },
  { code: "fr", label: "French", flag: "🇫🇷" },
  { code: "ta", label: "Tamil", flag: "🇮🇳" },
  { code: "te", label: "Telugu", flag: "🇮🇳" },
];

interface SplashOverlayProps {
  onDismiss?: (lang: string) => void;
}

export function SplashOverlay({ onDismiss }: SplashOverlayProps) {
  const [visible, setVisible] = useState(() => {
    return !sessionStorage.getItem(SPLASH_KEY);
  });
  const [selectedLang, setSelectedLang] = useState("all");

  const dismiss = (lang: string = selectedLang) => {
    sessionStorage.setItem(SPLASH_KEY, "1");
    localStorage.setItem(LANG_KEY, lang);
    setVisible(false);
    onDismiss?.(lang);
  };

  useEffect(() => {
    if (!visible) return;
    const timer = setTimeout(() => dismiss(selectedLang), 10000);
    return () => clearTimeout(timer);
  }, [visible]);

  return (
    <AnimatePresence>
      {visible && (
        <motion.div
          className="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden"
          initial={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          transition={{ duration: 0.6, ease: "easeInOut" }}
          data-testid="splash-overlay"
        >
          <div className="absolute inset-0 bg-black" />

          <motion.div
            className="absolute inset-0"
            initial={{ opacity: 0 }}
            animate={{ opacity: 0.15 }}
            transition={{ duration: 2 }}
          >
            <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,hsl(180,100%,50%)_0%,transparent_60%)]" />
            <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_80%_20%,hsl(320,100%,50%)_0%,transparent_50%)]" />
          </motion.div>

          <div className="absolute inset-0 opacity-5" style={{
            backgroundImage: `repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.03) 2px, rgba(255,255,255,0.03) 4px)`,
          }} />

          <div className="relative z-10 flex flex-col items-center text-center px-6 max-w-lg w-full">
            <motion.div
              initial={{ scale: 0, rotate: -180 }}
              animate={{ scale: 1, rotate: 0 }}
              transition={{ duration: 0.8, type: "spring", bounce: 0.4 }}
              className="mb-6"
            >
              <div className="w-20 h-20 bg-primary flex items-center justify-center">
                <Film className="w-12 h-12 text-white" />
              </div>
            </motion.div>

            <motion.h1
              initial={{ y: 40, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ delay: 0.3, duration: 0.7 }}
              className="text-6xl md:text-7xl font-black tracking-tight"
              style={{ fontFamily: "var(--font-display)" }}
            >
              <span className="bg-gradient-to-r from-primary via-white to-accent bg-clip-text text-transparent">
                TMovie
              </span>
            </motion.h1>

            <motion.p
              initial={{ y: 30, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ delay: 0.55, duration: 0.7 }}
              className="mt-3 text-base text-muted-foreground max-w-sm leading-relaxed"
            >
              Your cinematic gateway to unlimited movies & TV shows.
            </motion.p>

            <motion.div
              initial={{ y: 20, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ delay: 0.75, duration: 0.6 }}
              className="mt-6 w-full"
            >
              <p className="text-xs font-bold text-muted-foreground uppercase tracking-widest mb-3">
                Select your preferred language
              </p>
              <div className="grid grid-cols-3 gap-2">
                {LANGUAGES.map((lang) => (
                  <button
                    key={lang.code}
                    onClick={(e) => {
                      e.stopPropagation();
                      setSelectedLang(lang.code);
                    }}
                    className={`flex items-center gap-2 px-3 py-2.5 rounded-xl text-xs font-medium transition-all border ${
                      selectedLang === lang.code
                        ? "bg-primary/20 border-primary text-white"
                        : "bg-white/5 border-white/10 text-white/70 hover:text-white hover:bg-white/10"
                    }`}
                    data-testid={`splash-lang-${lang.code}`}
                  >
                    <span className="text-base leading-none">{lang.flag}</span>
                    <span className="truncate">{lang.label}</span>
                    {selectedLang === lang.code && (
                      <Check className="w-3 h-3 ml-auto shrink-0 text-primary" />
                    )}
                  </button>
                ))}
              </div>
            </motion.div>

            <motion.div
              initial={{ y: 20, opacity: 0 }}
              animate={{ y: 0, opacity: 1 }}
              transition={{ delay: 1.0, duration: 0.6 }}
              className="mt-6 flex flex-col sm:flex-row items-center gap-3 w-full"
            >
              <button
                onClick={(e) => {
                  e.stopPropagation();
                  dismiss(selectedLang);
                }}
                className="w-full group flex items-center justify-center gap-3 px-6 py-3.5 bg-primary text-primary-foreground font-bold text-base hover:opacity-90 transition-all duration-300 uppercase tracking-wider"
                data-testid="splash-enter-button"
              >
                <Play className="w-4 h-4 group-hover:scale-110 transition-transform" />
                Start Watching
              </button>

              <a
                href="https://t.me/tmovieofficial"
                target="_blank"
                rel="noopener noreferrer"
                onClick={(e) => e.stopPropagation()}
                className="w-full flex items-center justify-center gap-2 px-6 py-3.5 font-bold text-base text-white transition-all duration-300 uppercase tracking-wider border border-white/10 hover:border-[#229ED9]/50"
                style={{ background: "rgba(34,158,217,0.15)" }}
                data-testid="splash-telegram-button"
              >
                <Send className="w-4 h-4" style={{ color: "#229ED9" }} />
                <span>Join Telegram</span>
              </a>
            </motion.div>

            <motion.p
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ delay: 1.5, duration: 0.8 }}
              className="mt-4 text-xs text-muted-foreground/50"
            >
              Click anywhere to skip
            </motion.p>
          </div>

          <motion.div
            className="absolute bottom-0 left-0 right-0 h-px"
            initial={{ scaleX: 0 }}
            animate={{ scaleX: 1 }}
            transition={{ delay: 0.5, duration: 9.5, ease: "linear" }}
            style={{ transformOrigin: "left" }}
            onClick={(e) => e.stopPropagation()}
          >
            <div className="h-full bg-gradient-to-r from-primary via-accent to-primary" />
          </motion.div>

          <div
            className="absolute inset-0"
            onClick={() => dismiss(selectedLang)}
          />
        </motion.div>
      )}
    </AnimatePresence>
  );
}

export const LANG_STORAGE_KEY = LANG_KEY;
