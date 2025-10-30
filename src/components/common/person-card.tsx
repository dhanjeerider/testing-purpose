import Link from 'next/link';
import Image from 'next/image';
import { Person } from '@/types/tmdb';
import { getImageUrl } from '@/lib/tmdb';
import { Card, CardContent } from '@/components/ui/card';
import { User } from 'lucide-react';
import { cn } from '@/lib/utils';

interface PersonCardProps {
  person: Person;
  className?: string;
}

export function PersonCard({ person, className }: PersonCardProps) {
  const href = `/person/${person.id}`;
  const posterPath = getImageUrl(person.profile_path, 'w500');

  return (
    <Link href={href} className={cn("group block", className)}>
      <Card className="overflow-hidden h-full transition-all duration-300 ease-in-out hover:shadow-lg hover:shadow-accent/20 hover:border-accent/50 hover:-translate-y-1">
        <CardContent className="p-0">
          <div className="aspect-[2/3] relative">
            {posterPath ? (
              <Image
                src={posterPath}
                alt={person.name}
                width={500}
                height={750}
                className="object-cover w-full h-full"
              />
            ) : (
              <div className="w-full h-full bg-muted flex items-center justify-center">
                <User className="w-12 h-12 text-muted-foreground" />
              </div>
            )}
            <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent" />
            <div className="absolute bottom-0 left-0 p-3 text-white">
              <h3 className="font-bold text-base group-hover:text-accent transition-colors truncate">{person.name}</h3>
              <p className="text-xs text-muted-foreground mt-1 truncate">{person.known_for_department}</p>
            </div>
          </div>
        </CardContent>
      </Card>
    </Link>
  );
}
