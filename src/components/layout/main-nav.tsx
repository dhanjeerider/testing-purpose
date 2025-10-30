'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { cn } from '@/lib/utils';
import { Button } from '../ui/button';

const navLinks = [
  { href: '/', label: 'Home' },
  { href: '/discover/movie', label: 'Movies' },
  { href: '/discover/tv', label: 'TV Shows' },
  { href: '/genres', label: 'Genres' },
  { href: '/trending', label: 'Trending' },
  { href: '/search', label: 'Search' },
  { href: '/profile', query: 'tab=liked', label: 'Liked' },
  { href: '/profile', query: 'tab=history', label: 'History' },
];

interface MainNavProps {
  onLinkClick?: () => void;
}

export function MainNav({ onLinkClick }: MainNavProps) {
  const pathname = usePathname();

  return (
    <>
      {navLinks.map(({ href, label, query }) => {
        const fullHref = query ? `${href}?${query}` : href;
        const isActive = (
            (pathname === "/" && href === "/") ||
            (href !== "/" && pathname?.startsWith(href))
        ) && (!query || (typeof window !== 'undefined' && window.location.search.includes(query)));

        return (
            <Button key={fullHref} asChild variant="ghost" className="justify-start">
            <Link
                href={fullHref}
                onClick={onLinkClick}
                className={cn(
                'transition-colors hover:text-foreground/80 text-lg md:text-sm',
                isActive ? 'text-foreground' : 'text-foreground/60'
                )}
            >
                {label}
            </Link>
            </Button>
        )
      })}
    </>
  );
}
