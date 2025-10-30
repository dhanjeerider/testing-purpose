'use client';

import { useState, useEffect, Suspense } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useToast } from '@/hooks/use-toast';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { History, Heart, User, Trash2, Edit } from 'lucide-react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { HistoryItem, getHistory, clearHistory } from '@/lib/history';
import { LikedItem, getLikes, clearLikes } from '@/lib/likes';
import { getImageUrl } from '@/lib/tmdb';
import { formatDistanceToNow } from 'date-fns';
import { EditProfileDialog } from '@/components/profile/edit-profile-dialog';
import { UserProfile, getUserProfile, saveUserProfile } from '@/lib/profile';
import { usePathname, useRouter, useSearchParams } from 'next/navigation';

function ProfilePageContent() {
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const [userProfile, setUserProfile] = useState<UserProfile>(getUserProfile());
  const [watchHistory, setWatchHistory] = useState<HistoryItem[]>([]);
  const [likedContent, setLikedContent] = useState<LikedItem[]>([]);
  const { toast } = useToast();
  const tab = searchParams.get('tab') || 'history';

  useEffect(() => {
    setUserProfile(getUserProfile());
    setWatchHistory(getHistory());
    setLikedContent(getLikes());
  }, []);

  const handleTabChange = (value: string) => {
    router.push(`${pathname}?tab=${value}`);
  };

  const handleProfileUpdate = (newProfile: UserProfile) => {
    saveUserProfile(newProfile);
    setUserProfile(newProfile);
    toast({ title: "Profile Updated!" });
  };

  const handleClearHistory = () => {
    clearHistory();
    setWatchHistory([]);
    toast({ title: 'Watch history cleared.' });
  };

  const handleClearLikes = () => {
    clearLikes();
    setLikedContent([]);
    toast({ title: 'Liked content cleared.' });
  };

  const displayName = userProfile.name;
  const displayImage = userProfile.avatarUrl || `https://picsum.photos/seed/vega-user/200/200`;

  return (
    <div className="container py-12">
      <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
        <aside className="md:col-span-1">
          <Card className="shadow-lg">
            <CardContent className="flex flex-col items-center p-6 text-center">
              <EditProfileDialog profile={userProfile} onSave={handleProfileUpdate}>
                <div className="relative mb-4 cursor-pointer">
                  <Avatar className="w-32 h-32 border-4 border-primary">
                    <AvatarImage src={displayImage} alt={displayName} />
                    <AvatarFallback>
                      <User className="w-16 h-16" />
                    </AvatarFallback>
                  </Avatar>
                </div>
              </EditProfileDialog>
              <EditProfileDialog profile={userProfile} onSave={handleProfileUpdate}>
                <h2 className="text-2xl font-bold flex items-center gap-2 cursor-pointer">
                  {displayName}
                  <Edit className="h-5 w-5" />
                </h2>
              </EditProfileDialog>
            </CardContent>
          </Card>
        </aside>

        <main className="md:col-span-3">
          <Tabs defaultValue={tab} value={tab} onValueChange={handleTabChange}>
            <TabsList className="mb-4">
              <TabsTrigger value="history"><History className="mr-2 h-4 w-4" />Watch History</TabsTrigger>
              <TabsTrigger value="liked"><Heart className="mr-2 h-4 w-4" />Liked Content</TabsTrigger>
            </TabsList>
            <TabsContent value="history">
              <Card>
                <CardHeader>
                  <div className="flex justify-between items-center">
                    <div>
                      <CardTitle>Your Watch History</CardTitle>
                      <CardDescription>Content you have recently watched.</CardDescription>
                    </div>
                    {watchHistory.length > 0 && (
                      <Button variant="destructive" size="sm" onClick={handleClearHistory}>
                        <Trash2 className="mr-2 h-4 w-4" /> Clear
                      </Button>
                    )}
                  </div>
                </CardHeader>
                <CardContent>
                  {watchHistory.length > 0 ? (
                    <ul className="space-y-4">
                      {watchHistory.map(item => (
                        <ListItem key={`${item.type}-${item.id}`} item={item} />
                      ))}
                    </ul>
                  ) : (
                    <p className="text-center text-muted-foreground py-8">No watch history yet.</p>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
            <TabsContent value="liked">
              <Card>
                <CardHeader>
                  <div className="flex justify-between items-center">
                    <div>
                      <CardTitle>Your Liked Content</CardTitle>
                      <CardDescription>Movies and TV shows you have liked.</CardDescription>
                    </div>
                    {likedContent.length > 0 && (
                      <Button variant="destructive" size="sm" onClick={handleClearLikes}>
                        <Trash2 className="mr-2 h-4 w-4" /> Clear
                      </Button>
                    )}
                  </div>
                </CardHeader>
                <CardContent>
                  {likedContent.length > 0 ? (
                    <ul className="space-y-4">
                      {likedContent.map(item => (
                        <ListItem key={`${item.type}-${item.id}`} item={item} />
                      ))}
                    </ul>
                  ) : (
                    <p className="text-center text-muted-foreground py-8">You haven't liked any content yet.</p>
                  )}
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </main>
      </div>
    </div>
  );
}

function ListItem({ item }: { item: HistoryItem | LikedItem }) {
  const posterUrl = getImageUrl(item.poster_path, 'w300');
  const isHistory = 'timestamp' in item;

  return (
    <li className="flex items-center gap-4 p-3 bg-muted/50 rounded-lg">
      <Link href={`/${item.type}/${item.id}`} className="flex-shrink-0">
        <div className="relative h-24 w-16">
          {posterUrl ? (
            <Image src={posterUrl} alt={item.title} width={64} height={96} className="rounded-md object-cover w-full h-full" />
          ) : (
            <div className="h-full w-full bg-secondary rounded-md flex items-center justify-center">
              <User />
            </div>
          )}
        </div>
      </Link>
      <div className="flex-grow">
        <Link href={`/${item.type}/${item.id}`} className="font-semibold hover:underline">
          {item.title}
        </Link>
        <p className="text-sm text-muted-foreground capitalize">{item.type}</p>
        {isHistory && (
          <p className="text-xs text-muted-foreground mt-1">
            Watched {formatDistanceToNow(new Date(item.timestamp), { addSuffix: true })}
          </p>
        )}
      </div>
      <Button asChild variant="ghost" size="sm">
        <Link href={`/${item.type}/${item.id}`}>View</Link>
      </Button>
    </li>
  );
}

export default function ProfilePage() {
  return (
    <Suspense fallback={<div>Loading...</div>}>
      <ProfilePageContent />
    </Suspense>
  );
}
