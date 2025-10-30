import { getTrending } from '@/lib/tmdb';
import { ContentCard } from '@/components/common/content-card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

export default async function TrendingPage() {
  const [trendingDay, trendingWeek] = await Promise.all([
    getTrending('all', 'day'),
    getTrending('all', 'week'),
  ]);

  return (
    <div className="py-8 animate-fade-in">
      <div className="px-4 md:px-8">
        <h1 className="text-2xl font-bold mb-8">Trending</h1>
        <Tabs defaultValue="week">
          <TabsList className="grid w-full grid-cols-2 md:w-[400px]">
            <TabsTrigger value="day">Today</TabsTrigger>
            <TabsTrigger value="week">This Week</TabsTrigger>
          </TabsList>
          <TabsContent value="day">
            <div className="content-grid mt-4 px-4 md:px-8">
              {trendingDay.results.map((item) => {
                if (item.media_type === 'person') return null;
                return <ContentCard key={item.id} item={item} type={item.media_type} />;
              })}
            </div>
          </TabsContent>
          <TabsContent value="week">
            <div className="content-grid mt-4 px-4 md:px-8">
              {trendingWeek.results.map((item) => {
                if (item.media_type === 'person') return null;
                return <ContentCard key={item.id} item={item} type={item.media_type} />;
              })}
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
}
