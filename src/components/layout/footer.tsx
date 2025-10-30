import Link from 'next/link';
import { Logo } from './logo';

export function Footer() {
  return (
    <footer className="border-t py-4">
      <div className="container flex flex-col items-center justify-between gap-4 md:h-24 md:flex-row">
        <div className="flex flex-col items-center gap-4 px-8 md:flex-row md:gap-2 md:px-0">
          <Logo />
          <p className="text-center text-sm leading-loose text-muted-foreground md:text-left">
            Explore trending movies & TV shows, search, and enjoy light & dark modes.
          </p>
          <a style={{padding:'5px 12px'}} className='btn-gradient button' href='https://t.me/dktechnozone'>join our telegram</a>
        </div>
        <p className="text-center text-sm text-muted-foreground">
          This website uses the third party API we didn't stored anything on our server
        </p>
      </div>
    </footer>
  );
}
