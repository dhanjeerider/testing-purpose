import { ReactNode } from "react";
import { ChevronRight } from "lucide-react";
import { Link } from "wouter";

interface ContentRowProps {
  title: string;
  children: ReactNode;
  viewAllLink?: string;
}

export function ContentRow({ title, children, viewAllLink }: ContentRowProps) {
  return (
    <div className="w-full py-4">
      <div className="px-4 lg:px-12 flex items-center justify-between mb-3">
        <h2 className="text-base md:text-xl font-display font-bold text-white uppercase tracking-wider">
          {title}
          <div className="mt-1 w-8 h-0.5 bg-primary" />
        </h2>
        
        {viewAllLink && (
          <Link href={viewAllLink} className="text-xs text-muted-foreground hover:text-primary transition-colors flex items-center gap-1 group uppercase tracking-wider font-medium">
            View All
            <ChevronRight className="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
          </Link>
        )}
      </div>
      
      <div className="px-4 lg:px-12">
        <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-1.5 sm:gap-2 md:gap-3">
          {children}
        </div>
      </div>
    </div>
  );
}
