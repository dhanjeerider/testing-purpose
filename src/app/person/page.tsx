import { getPersonDetails, getImageUrl, getPersonCombinedCredits } from '@/lib/tmdb';
import { notFound } from 'next/navigation';
import Image from 'next/image';
import { User } from 'lucide-react';
import { format } from 'date-fns';
import { InfiniteContentGrid } from '@/components/common/infinite-content-grid';

interface PersonPageProps {
  params: { id: string };
}

export default async function PersonPage({ params }: PersonPageProps) {
  const personId = params.id;
  let person;
  try {
    person = await getPersonDetails(personId);
  } catch (error) {
    notFound();
  }

  const profileUrl = getImageUrl(person.profile_path, 'w500');

  const knownFor = person.combined_credits?.cast
    .sort((a, b) => b.popularity - a.popularity)
    .slice(0, 18);

  const fetcher = async () => {
    'use server';
    const allCredits = await getPersonCombinedCredits(personId);
    return allCredits.cast.sort((a, b) => b.popularity - a.popularity).slice(knownFor?.length || 0);
  };


  return (
    <div className="py-12 animate-fade-in">
      <div className="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 px-4 md:px-8">
        <div className="md:col-span-1">
          <div className="relative aspect-[2/3] w-full max-w-sm mx-auto overflow-hidden rounded-xl shadow-lg">
            {profileUrl ? (
              <Image
                src={profileUrl}
                alt={person.name}
                width={500}
                height={750}
                className="object-cover w-full h-full"
              />
            ) : (
              <div className="bg-muted rounded-xl flex items-center justify-center h-full">
                <User className="w-24 h-24 text-muted-foreground" />
              </div>
            )}
          </div>
        </div>
        <div className="md:col-span-2">
          <h1 className="text-3xl md:text-5xl font-bold">{person.name}</h1>
          {person.known_for_department && (
            <p className="text-lg text-muted-foreground mt-1">{person.known_for_department}</p>
          )}
          
          <div className="mt-6 space-y-2 text-sm text-foreground/80">
            {person.birthday && (
              <p><strong>Born:</strong> {format(new Date(person.birthday), 'MMMM d, yyyy')}</p>
            )}
            {person.place_of_birth && (
              <p><strong>Place of Birth:</strong> {person.place_of_birth}</p>
            )}
          </div>
          
          {person.biography && (
            <div className="mt-8">
              <h2 className="text-xl font-semibold mb-2">Biography</h2>
              <p className="text-foreground/70 leading-relaxed max-w-3xl whitespace-pre-line text-sm">
                {person.biography}
              </p>
            </div>
          )}
        </div>
      </div>

      {knownFor && knownFor.length > 0 && (
        <div className="mt-16">
          <h2 className="text-2xl font-bold mb-8 px-4 md:px-8">Known For</h2>
          <InfiniteContentGrid
            initialItems={knownFor}
            totalPages={1}
            fetcher={fetcher}
            className="px-4 md:px-8"
          />
        </div>
      )}
    </div>
  );
}
