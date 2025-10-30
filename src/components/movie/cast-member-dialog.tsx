'use client';

import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import { getImageUrl } from '@/lib/tmdb';
import { CastMember } from '@/types/tmdb';
import Image from 'next/image';
import { User } from 'lucide-react';
import Link from 'next/link';
import { Button } from '../ui/button';

interface CastMemberDialogProps {
  member: CastMember;
  children: React.ReactNode;
}

export function CastMemberDialog({ member, children }: CastMemberDialogProps) {
  const profileUrl = getImageUrl(member.profile_path, 'w500');

  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent className="max-w-md">
        <DialogHeader>
          <DialogTitle>{member.name}</DialogTitle>
        </DialogHeader>
        <div className="flex flex-col items-center gap-4">
          <div className="relative h-64 w-44">
            {profileUrl ? (
              <Image
                src={profileUrl}
                alt={member.name}
                width={176}
                height={256}
                className="rounded-lg object-cover w-full h-full"
              />
            ) : (
              <div className="flex h-full w-full items-center justify-center rounded-lg bg-muted">
                <User className="h-16 w-16 text-muted-foreground" />
              </div>
            )}
          </div>
          <div className="text-center">
            <p className="text-lg font-semibold">as</p>
            <p className="text-xl text-primary">{member.character}</p>
          </div>
          <Button asChild>
            <Link href={`/person/${member.id}`}>View Full Profile</Link>
          </Button>
        </div>
      </DialogContent>
    </Dialog>
  );
}
