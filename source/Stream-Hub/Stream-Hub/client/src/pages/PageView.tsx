import { useQuery } from "@tanstack/react-query";
import { useRoute } from "wouter";
import { Shell } from "@/components/layout/Shell";
import { Loader2, FileText } from "lucide-react";
import type { Page } from "@shared/schema";

export default function PageView() {
  const [, params] = useRoute("/page/:slug");
  const slug = params?.slug || "";

  const { data: page, isLoading, error } = useQuery<Page>({
    queryKey: ['/api/pages', slug],
    queryFn: async () => {
      const res = await fetch(`/api/pages/${slug}`);
      if (!res.ok) throw new Error("Page not found");
      return res.json();
    },
    enabled: !!slug,
  });

  if (isLoading) {
    return (
      <Shell>
        <div className="h-screen flex items-center justify-center">
          <Loader2 className="w-12 h-12 text-primary animate-spin" />
        </div>
      </Shell>
    );
  }

  if (error || !page) {
    return (
      <Shell>
        <div className="pt-24 px-4 md:px-8 lg:px-16 pb-20 max-w-4xl mx-auto">
          <div className="neu-flat p-8 border border-white/5 text-center rounded-xl">
            <FileText className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
            <h1 className="text-xl font-bold text-white mb-2" data-testid="text-page-not-found">Page Not Found</h1>
            <p className="text-sm text-muted-foreground">The page you're looking for doesn't exist.</p>
          </div>
        </div>
      </Shell>
    );
  }

  return (
    <Shell>
      <div className="pt-24 px-4 md:px-8 lg:px-16 pb-20 max-w-4xl mx-auto">
        <h1 className="text-2xl md:text-3xl font-display font-bold text-white mb-6" data-testid="text-page-title">
          {page.title}
        </h1>
        <div
          className="prose prose-invert max-w-none text-white/80 leading-relaxed whitespace-pre-wrap"
          data-testid="text-page-content"
        >
          {page.content}
        </div>
      </div>
    </Shell>
  );
}
