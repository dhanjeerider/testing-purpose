'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { Home, Search, Film, Tv, Share2 } from 'lucide-react';
import { cn } from '@/lib/utils';

const navItems = [
  { href: '/', label: 'Home', icon: Home },
  { href: '/discover/movie', label: 'Movies', icon: Film },
  { href: '/discover/tv', label: 'TV', icon: Tv },
  { href: '/search', label: 'Search', icon: Search },
];

export function MobileBottomNav() {
  const pathname = usePathname();

  const handleShare = () => {
    if (navigator.share) {
      navigator.share({
        title: document.title,
        url: window.location.href,
      }).catch(console.error);
    } else {
      // Fallback for browsers that do not support the Web Share API
      alert('Share functionality is not supported in this browser.');
    }
  };

  return (
      <div className="fixed bottom-0 left-0 z-50 w-full h-16 bg-background border-t border-border/40 md:hidden">
        <div className="grid h-full max-w-lg grid-cols-5 mx-auto font-medium">
          {navItems.map((item) => {
            const isActive = pathname === item.href;
            return (
              <Link
                key={item.href}
                href={item.href}
                className={cn(
                  'inline-flex flex-col items-center justify-center px-5 hover:bg-muted/50 group',
                  isActive ? 'text-primary' : 'text-muted-foreground'
                )}
              >
                <item.icon className="w-5 h-5 mb-1" />
                <span className="text-xs">{item.label}</span>
              </Link>
            );
          })}
           <button
            onClick={handleShare}
            className="inline-flex flex-col items-center justify-center px-5 text-muted-foreground hover:bg-muted/50 group"
          >
            <Share2 className="w-5 h-5 mb-1" />
            <span className="text-xs">Share</span>
          </button>
        </div>
      </div>
    );
}
