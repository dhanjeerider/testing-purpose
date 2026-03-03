import { ReactNode, useState, useEffect, useRef } from "react";
import { Link, useLocation } from "wouter";
import { Search, Home, Clapperboard, Tv, Bookmark, Menu, X, Shield, User, Film, Sparkles, ChevronRight, CreditCard } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { useQuery } from "@tanstack/react-query";
import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";
import type { Page } from "@shared/schema";

function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

interface PublicSettings {
  telegramLink?: string;
  searchConsoleMeta?: string;
  googleAnalyticsId?: string;
  siteTitle?: string;
  siteDescription?: string;
  razorpayKeyId?: string;
  subscriptionEnabled?: boolean;
  upiId?: string;
  faviconUrl?: string | null;
  logoUrl?: string | null;
  ogImageUrl?: string | null;
  metaKeywords?: string | null;
  footerText?: string | null;
  twitterUrl?: string | null;
  instagramUrl?: string | null;
  youtubeUrl?: string | null;
  facebookUrl?: string | null;
}

export function usePublicSettings() {
  return useQuery<PublicSettings>({ queryKey: ['/api/settings/public'] });
}

export function Shell({ children }: { children: ReactNode }) {
  const [location] = useLocation();
  const [scrolled, setScrolled] = useState(false);
  const [visible, setVisible] = useState(true);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const lastScrollY = useRef(0);

  const { data: footerPages = [] } = useQuery<Page[]>({
    queryKey: ['/api/pages/footer'],
  });

  const { data: publicSettings } = usePublicSettings();

  useEffect(() => {
    const handleScroll = () => {
      const currentY = window.scrollY;
      if (currentY <= 10) {
        setVisible(true);
      } else if (currentY > lastScrollY.current + 6) {
        setVisible(false);
      } else if (currentY < lastScrollY.current - 6) {
        setVisible(true);
      }
      lastScrollY.current = currentY;
      setScrolled(currentY > 20);
    };
    window.addEventListener("scroll", handleScroll, { passive: true });
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  useEffect(() => {
    if (!publicSettings) return;

    if (publicSettings.searchConsoleMeta) {
      let el = document.querySelector('meta[name="google-site-verification"]') as HTMLMetaElement | null;
      if (!el) { el = document.createElement('meta'); el.name = 'google-site-verification'; document.head.appendChild(el); }
      el.content = publicSettings.searchConsoleMeta;
    }

    if (publicSettings.siteTitle) {
      document.title = publicSettings.siteTitle + ' - Stream Movies & TV Shows Free';
      const og = document.querySelector('meta[property="og:title"]');
      if (og) og.setAttribute('content', publicSettings.siteTitle + ' - Stream Movies & TV Shows Free');
      const tw = document.querySelector('meta[name="twitter:title"]');
      if (tw) tw.setAttribute('content', publicSettings.siteTitle + ' - Stream Movies & TV Shows Free');
    }

    if (publicSettings.siteDescription) {
      const desc = document.querySelector('meta[name="description"]');
      if (desc) desc.setAttribute('content', publicSettings.siteDescription);
      const og = document.querySelector('meta[property="og:description"]');
      if (og) og.setAttribute('content', publicSettings.siteDescription);
      const tw = document.querySelector('meta[name="twitter:description"]');
      if (tw) tw.setAttribute('content', publicSettings.siteDescription);
    }

    if (publicSettings.faviconUrl) {
      let link = document.querySelector('link[rel="icon"]') as HTMLLinkElement | null;
      if (!link) { link = document.createElement('link'); link.rel = 'icon'; document.head.appendChild(link); }
      link.href = publicSettings.faviconUrl;
      let apple = document.querySelector('link[rel="apple-touch-icon"]') as HTMLLinkElement | null;
      if (apple) apple.href = publicSettings.faviconUrl;
    }

    if (publicSettings.ogImageUrl) {
      const og = document.querySelector('meta[property="og:image"]');
      if (og) og.setAttribute('content', publicSettings.ogImageUrl);
      const tw = document.querySelector('meta[name="twitter:image"]');
      if (tw) tw.setAttribute('content', publicSettings.ogImageUrl);
    }

    if (publicSettings.metaKeywords) {
      let el = document.querySelector('meta[name="keywords"]') as HTMLMetaElement | null;
      if (!el) { el = document.createElement('meta'); el.name = 'keywords'; document.head.appendChild(el); }
      el.content = publicSettings.metaKeywords;
    }
  }, [publicSettings]);

  useEffect(() => {
    if (!publicSettings?.googleAnalyticsId) return;
    const gaid = publicSettings.googleAnalyticsId;
    if (document.getElementById('ga-script')) return;
    const script1 = document.createElement('script');
    script1.id = 'ga-script';
    script1.async = true;
    script1.src = `https://www.googletagmanager.com/gtag/js?id=${gaid}`;
    document.head.appendChild(script1);
    const script2 = document.createElement('script');
    script2.id = 'ga-config';
    script2.textContent = `window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '${gaid}');`;
    document.head.appendChild(script2);
  }, [publicSettings?.googleAnalyticsId]);

  const navLinks = [
    { href: "/", label: "Home", icon: Home },
    { href: "/search", label: "Search", icon: Search },
    { href: "/watchlist", label: "Watchlist", icon: Bookmark },
    { href: "/profile", label: "Profile", icon: User },
    { href: "/admin", label: "Admin", icon: Shield },
  ];

  const mobileMenuLinks = [
    { href: "/", label: "Home", icon: Home },
    { href: "/search?type=movie", label: "Movies", icon: Film },
    { href: "/search?type=tv", label: "TV Shows", icon: Tv },
    { href: "/search?type=anime", label: "Anime", icon: Sparkles },
    { href: "/watchlist", label: "Watchlist", icon: Bookmark },
    { href: "/pricing", label: "Pricing", icon: CreditCard },
    { href: "/search", label: "Search", icon: Search },
    { href: "/admin", label: "Admin", icon: Shield },
  ];

  const bottomNavLinks = [
    { href: "/", label: "Home", icon: Home },
    { href: "/search", label: "Search", icon: Search },
    { href: "/watchlist", label: "Saved", icon: Bookmark },
  ];

  return (
    <div className="min-h-screen text-foreground flex flex-col" style={{backgroundColor: '#1a1d2e'}}>
      <header
        className={cn(
          "fixed top-0 w-full z-50 px-4 lg:px-12 py-3.5 flex items-center justify-between",
          scrolled ? "shadow-[0_4px_20px_#0c0e1d,_0_-2px_8px_#272c44]" : ""
        )}
        style={{
          background: scrolled
            ? 'linear-gradient(180deg, #1e2237 0%, #1a1d2e 100%)'
            : 'linear-gradient(180deg, rgba(26,29,46,0.98) 0%, rgba(26,29,46,0) 100%)',
          transform: visible ? 'translateY(0)' : 'translateY(-100%)',
          transition: 'transform 320ms ease, box-shadow 300ms ease, background 300ms ease',
        }}
      >
        <div className="flex items-center gap-8">
          <Link href="/" className="flex items-center gap-2 group cursor-pointer">
            {publicSettings?.logoUrl ? (
              <img
                src={publicSettings.logoUrl}
                alt={publicSettings.siteTitle || 'TMovie'}
                className="h-8 max-w-[120px] object-contain transition-all group-hover:scale-105"
                onError={e => { e.currentTarget.style.display = 'none'; }}
              />
            ) : (
              <>
                <div className="w-8 h-8 bg-primary flex items-center justify-center transition-all group-hover:scale-110">
                  <Clapperboard className="text-primary-foreground w-4 h-4" />
                </div>
                <span className="font-display font-bold text-xl tracking-wider text-white">
                  {publicSettings?.siteTitle ? (
                    publicSettings.siteTitle
                  ) : (
                    <>T<span className="text-primary">movie</span></>
                  )}
                </span>
              </>
            )}
          </Link>

          <nav className="hidden md:flex items-center gap-6">
            {navLinks.map((link) => (
              <Link 
                key={link.href} 
                href={link.href}
                className={cn(
                  "text-xs font-medium uppercase tracking-widest transition-colors hover:text-primary",
                  location === link.href ? "text-primary" : "text-muted-foreground"
                )}
                data-testid={`nav-${link.label.toLowerCase()}`}
              >
                {link.label}
              </Link>
            ))}
          </nav>
        </div>

        <div className="flex items-center gap-2">
          <Link href="/search" className="flex items-center justify-center w-9 h-9 neu-flat neu-btn text-white/70 hover:text-white transition-colors cursor-pointer" data-testid="button-header-search">
            <Search className="w-4 h-4" />
          </Link>
          
          <button 
            className="md:hidden text-white p-2"
            onClick={() => setMobileMenuOpen(true)}
            data-testid="button-mobile-menu"
          >
            <Menu className="w-5 h-5" />
          </button>
        </div>
      </header>

      <AnimatePresence>
        {mobileMenuOpen && (
          <>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              transition={{ duration: 0.2 }}
              className="fixed inset-0 z-[100] bg-black/70"
              onClick={() => setMobileMenuOpen(false)}
            />
            <motion.div
              initial={{ x: "100%" }}
              animate={{ x: 0 }}
              exit={{ x: "100%" }}
              transition={{ type: "spring", damping: 25, stiffness: 200 }}
              className="fixed top-0 right-0 bottom-0 z-[101] w-[67%] flex flex-col p-5 overflow-y-auto"
              style={{background:'linear-gradient(135deg,#1e2237 0%,#1a1d2e 100%)',boxShadow:'-8px 0 32px #0c0e1d'}}
            >
              <div className="flex justify-between items-center mb-6">
                <span className="font-display font-bold text-lg text-white uppercase tracking-wider">Menu</span>
                <button onClick={() => setMobileMenuOpen(false)} className="p-2 neu-flat" data-testid="button-close-menu">
                  <X className="w-5 h-5" />
                </button>
              </div>

              <Link href="/profile">
                <div
                  onClick={() => setMobileMenuOpen(false)}
                  className="flex items-center gap-3 p-3.5 neu-flat border border-white/10 rounded-xl mb-5 cursor-pointer"
                  data-testid="mobile-nav-profile-card"
                >
                  <div className="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                    <User className="w-5 h-5 text-primary" />
                  </div>
                  <div className="min-w-0">
                    <p className="text-sm font-bold text-white leading-tight">My Profile</p>
                    <p className="text-xs text-muted-foreground mt-0.5">View profile & settings</p>
                  </div>
                  <ChevronRight className="w-4 h-4 text-muted-foreground ml-auto shrink-0" />
                </div>
              </Link>
              
              <nav className="flex flex-col gap-0.5">
                {mobileMenuLinks.map((link) => (
                  <Link key={link.href} href={link.href}>
                    <div 
                      onClick={() => setMobileMenuOpen(false)}
                      className={cn(
                        "flex items-center gap-3.5 text-sm font-display font-medium p-3.5 transition-all rounded-xl",
                        location === link.href ? "neu-pressed text-primary" : "text-muted-foreground hover:text-white hover:neu-flat"
                      )}
                      data-testid={`mobile-nav-${link.label.toLowerCase()}`}
                    >
                      <link.icon className="w-4.5 h-4.5 w-5 h-5" />
                      {link.label}
                    </div>
                  </Link>
                ))}
              </nav>
            </motion.div>
          </>
        )}
      </AnimatePresence>

      <main className="flex-1 w-full relative pb-16 md:pb-0">
        {children}
      </main>

      <nav className="md:hidden fixed bottom-0 left-0 right-0 z-50 border-t border-white/5" style={{background:'linear-gradient(180deg,#1e2237 0%,#1a1d2e 100%)',boxShadow:'0 -4px 20px #0c0e1d, 0 2px 8px #272c44',transform: visible ? 'translateY(0)' : 'translateY(100%)', transition:'transform 320ms ease'}} data-testid="mobile-bottom-nav">
        <div className="flex items-center justify-around py-1.5">
          {bottomNavLinks.map((link) => {
            const isActive = location === link.href || (link.href !== '/' && location.startsWith(link.href.split('?')[0]));
            return (
              <Link key={link.href} href={link.href}>
                <div className={cn(
                  "flex flex-col items-center gap-0.5 px-3 py-1.5 transition-colors min-w-[48px]",
                  isActive ? "text-primary" : "text-muted-foreground"
                )} data-testid={`bottom-nav-${link.label.toLowerCase()}`}>
                  <link.icon className="w-5 h-5" />
                  <span className="text-[10px] font-medium">{link.label}</span>
                </div>
              </Link>
            );
          })}
        </div>
      </nav>

      <footer className="mt-16 pb-20 md:pb-0 py-8 border-t border-white/5 text-center text-muted-foreground text-xs" style={{background:'linear-gradient(180deg,#1a1d2e 0%,#161928 100%)',boxShadow:'0 -4px 20px #0c0e1d'}} data-testid="footer">
        {footerPages.length > 0 && (
          <div className="flex flex-wrap items-center justify-center gap-4 mb-4" data-testid="footer-page-links">
            {footerPages.map((page) => (
              <Link
                key={page.id}
                href={`/page/${page.slug}`}
                className="text-muted-foreground hover:text-primary transition-colors text-xs uppercase tracking-wider"
                data-testid={`footer-link-${page.slug}`}
              >
                {page.title}
              </Link>
            ))}
          </div>
        )}

        {(publicSettings?.twitterUrl || publicSettings?.instagramUrl || publicSettings?.youtubeUrl || publicSettings?.facebookUrl || publicSettings?.telegramLink) && (
          <div className="flex items-center justify-center gap-4 mb-4" data-testid="footer-social-links">
            {publicSettings.telegramLink && (
              <a href={publicSettings.telegramLink} target="_blank" rel="noopener noreferrer"
                className="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-[#229ED9]/20 hover:text-[#229ED9] transition-all text-muted-foreground"
                title="Telegram" data-testid="footer-telegram">
                <svg viewBox="0 0 24 24" className="w-4 h-4 fill-current"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
              </a>
            )}
            {publicSettings.twitterUrl && (
              <a href={publicSettings.twitterUrl} target="_blank" rel="noopener noreferrer"
                className="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 hover:text-white transition-all text-muted-foreground"
                title="Twitter / X" data-testid="footer-twitter">
                <svg viewBox="0 0 24 24" className="w-4 h-4 fill-current"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.259 5.631 5.905-5.631zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
              </a>
            )}
            {publicSettings.instagramUrl && (
              <a href={publicSettings.instagramUrl} target="_blank" rel="noopener noreferrer"
                className="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-[#E1306C]/20 hover:text-[#E1306C] transition-all text-muted-foreground"
                title="Instagram" data-testid="footer-instagram">
                <svg viewBox="0 0 24 24" className="w-4 h-4 fill-current"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
              </a>
            )}
            {publicSettings.youtubeUrl && (
              <a href={publicSettings.youtubeUrl} target="_blank" rel="noopener noreferrer"
                className="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-[#FF0000]/20 hover:text-[#FF0000] transition-all text-muted-foreground"
                title="YouTube" data-testid="footer-youtube">
                <svg viewBox="0 0 24 24" className="w-4 h-4 fill-current"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
              </a>
            )}
            {publicSettings.facebookUrl && (
              <a href={publicSettings.facebookUrl} target="_blank" rel="noopener noreferrer"
                className="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 hover:bg-[#1877F2]/20 hover:text-[#1877F2] transition-all text-muted-foreground"
                title="Facebook" data-testid="footer-facebook">
                <svg viewBox="0 0 24 24" className="w-4 h-4 fill-current"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
            )}
          </div>
        )}

        <p>{publicSettings?.footerText || `\u00a9 ${new Date().getFullYear()} ${publicSettings?.siteTitle || 'TMovie'}. All rights reserved.`}</p>
        <p className="mt-1 opacity-40">Data provided by TMDB</p>
      </footer>
    </div>
  );
}
