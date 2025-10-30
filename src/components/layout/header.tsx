'use client';

import Link from 'next/link';
import { Logo } from '@/components/layout/logo';
import { MainNav } from '@/components/layout/main-nav';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Menu, User } from 'lucide-react';
import { useState, useEffect } from 'react';
import { ThemeToggle } from './theme-toggle';
import { useRouter } from 'next/navigation';

export function Header() {
  const [isSheetOpen, setSheetOpen] = useState(false);
  const [isClient, setIsClient] = useState(false);
  const router = useRouter();

  useEffect(() => {
    setIsClient(true);
  }, []);

  return (
    <header className="z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
      <div className="flex h-14 items-center justify-between px-4 md:px-8">
        {/* Left side: Logo and Mobile Menu Trigger */}
        <div className="flex items-center">
          {isClient && (
            <div className="md:hidden">
              <Sheet open={isSheetOpen} onOpenChange={setSheetOpen}>
                <SheetTrigger asChild>
                  <Button variant="ghost" size="icon">
                    <Menu className="h-5 w-5" />
                    <span className="sr-only">Toggle Menu</span>
                  </Button>
                </SheetTrigger>
                <SheetContent side="left" className="w-full max-w-xs sm:max-w-sm">
                  <SheetHeader>
                    <SheetTitle className="sr-only">Navigation Menu</SheetTitle>
                  </SheetHeader>
                  <div className="flex flex-col h-full">
                    <div className="p-4">
                      <Link href="/" onClick={() => setSheetOpen(false)}>
                        <Logo />
                      </Link>
                    </div>
                    <nav className="flex flex-col gap-2 px-4 overflow-y-auto">
                      <MainNav onLinkClick={() => setSheetOpen(false)} />
                    </nav>
                    <div className="mt-auto p-4 border-t border-border/40">
                      <Link
                        href="/profile"
                        onClick={() => setSheetOpen(false)}
                        className="flex items-center gap-2 text-foreground/60 transition-colors hover:text-foreground/80"
                      >
                        <User className="h-4 w-4" />
                        Profile
                      </Link>
                    </div>
                  </div>
                </SheetContent>
              </Sheet>
            </div>
          )}
          <div className="hidden md:flex">
            <Link href="/">
              <Logo />
            </Link>
          </div>
        </div>

        {/* Center: Main Navigation (Desktop) and Logo (Mobile) */}
        <div className="flex items-center justify-center">
          <div className="hidden md:flex items-center space-x-2 text-sm font-bold">
            <MainNav />
          </div>
          <div className="md:hidden h-20px">
            <Link href="/">
              <Logo />
            </Link>
          </div>
        </div>

        {/* Right side: Actions */}
        <div className="flex items-center justify-end space-x-2">
          <ThemeToggle />
          <Button asChild variant="ghost" size="icon" className="inline-flex">
            <Link href="/profile">
              <User />
              <span className="sr-only">Profile</span>
            </Link>
          </Button>
        </div>
      </div>
    </header>
  );
}
