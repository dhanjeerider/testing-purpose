import { useState } from "react";
import { Shell } from "@/components/layout/Shell";
import { useWatchlist } from "@/hooks/use-watchlist";
import { useAuth, useLogin, useLogout } from "@/hooks/use-auth";
import { useQuery } from "@tanstack/react-query";
import { User, Film, Bookmark, TrendingUp, Eye, LogIn, LogOut, Crown, Loader2 } from "lucide-react";
import { MovieCard } from "@/components/shared/MovieCard";
import { TMDBItem } from "@/lib/tmdb";

declare global {
  interface Window {
    Razorpay: any;
  }
}

export default function Profile() {
  const { data: watchlist = [] } = useWatchlist();
  const { user, isLoggedIn, isLoading: authLoading } = useAuth();
  const logout = useLogout();
  const login = useLogin();
  const [loginUsername, setLoginUsername] = useState('');
  const [loginPassword, setLoginPassword] = useState('');
  const [showLogin, setShowLogin] = useState(false);

  const { data: publicSettings } = useQuery<{ razorpayKeyId: string | null; subscriptionAmount: string; subscriptionName: string; paymentEnabled: boolean; subscriptionEnabled: boolean }>({
    queryKey: ['/api/settings/public'],
  });

  const { data: trending = [] } = useQuery<TMDBItem[]>({
    queryKey: ['/api/tmdb/trending', 'movie'],
    queryFn: async () => {
      const res = await fetch('/api/tmdb/trending?type=movie');
      return res.json();
    },
    select: (data: any) => data.results?.slice(0, 6) || [],
  });

  const totalWatched = watchlist.length;

  const handleSubscribe = async () => {
    if (!publicSettings?.paymentEnabled || !publicSettings.razorpayKeyId) return;

    try {
      const res = await fetch('/api/payment/create-order', { method: 'POST', headers: { 'Content-Type': 'application/json' } });
      const data = await res.json();
      if (!data.order) return;

      if (!window.Razorpay) {
        const script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        document.body.appendChild(script);
        await new Promise(resolve => script.onload = resolve);
      }

      const options = {
        key: data.key_id,
        amount: data.order.amount,
        currency: data.order.currency,
        name: publicSettings.subscriptionName,
        description: 'Monthly Subscription',
        order_id: data.order.id,
        handler: async function (response: any) {
          await fetch('/api/payment/verify', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(response),
          });
        },
        theme: { color: '#00e5ff' },
      };

      const rzp = new window.Razorpay(options);
      rzp.open();
    } catch (err) {
      console.error('Payment error:', err);
    }
  };

  return (
    <Shell>
      <div className="pt-24 px-4 md:px-8 lg:px-16 pb-20 max-w-4xl mx-auto">

        {/* Profile Header */}
        <div className="neu-flat p-6 md:p-8 border border-white/5 rounded-md mb-6 relative overflow-hidden">
          <div className="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2" />
          <div className="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-5">
            <div className="w-20 h-20 md:w-24 md:h-24 bg-gradient-to-br from-primary/30 to-accent/30 flex items-center justify-center rounded-full border-2 border-primary/20 shrink-0">
              <User className="w-10 h-10 md:w-12 md:h-12 text-white/60" />
            </div>
            <div className="text-center md:text-left flex-1">
              <h1 className="text-2xl md:text-3xl font-display font-bold text-white mb-1" data-testid="text-profile-title">
                {isLoggedIn ? user?.username : 'Guest Viewer'}
              </h1>
              <p className="text-sm text-muted-foreground mb-3">Welcome to Tmovie. Browse, watch, and save your favorites.</p>
              <div className="flex flex-wrap justify-center md:justify-start gap-3">
                <div className="flex items-center gap-1.5 text-sm text-muted-foreground">
                  <Bookmark className="w-3.5 h-3.5 text-primary" />
                  <span>{totalWatched} saved</span>
                </div>
                <div className="flex items-center gap-1.5 text-sm text-muted-foreground">
                  <Eye className="w-3.5 h-3.5 text-accent" />
                  <span>{isLoggedIn ? user?.role : 'Visitor'}</span>
                </div>
              </div>
            </div>

            <div className="shrink-0">
              {isLoggedIn ? (
                <button onClick={() => logout.mutate()}
                  className="flex items-center gap-2 px-4 py-2 neu-flat text-muted-foreground hover:text-white border border-white/5 text-sm transition-colors rounded-md"
                  data-testid="button-profile-logout">
                  <LogOut className="w-4 h-4" /> Logout
                </button>
              ) : (
                <button onClick={() => setShowLogin(!showLogin)}
                  className="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground font-medium text-sm rounded-md hover:opacity-90 transition-opacity"
                  data-testid="button-profile-login">
                  <LogIn className="w-4 h-4" /> Login
                </button>
              )}
            </div>
          </div>

          {showLogin && !isLoggedIn && (
            <div className="mt-6 pt-6 border-t border-white/10 max-w-sm">
              <div className="space-y-3">
                <input type="text" value={loginUsername} onChange={e => setLoginUsername(e.target.value)}
                  className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-md text-sm"
                  placeholder="Username" data-testid="input-profile-username" />
                <input type="password" value={loginPassword} onChange={e => setLoginPassword(e.target.value)}
                  className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-md text-sm"
                  placeholder="Password" data-testid="input-profile-password"
                  onKeyDown={e => e.key === 'Enter' && login.mutate({ username: loginUsername, password: loginPassword })} />
                {login.isError && (
                  <p className="text-destructive text-xs">Invalid credentials</p>
                )}
                <button onClick={() => login.mutate({ username: loginUsername, password: loginPassword })}
                  disabled={login.isPending}
                  className="w-full py-2.5 bg-primary text-primary-foreground font-medium text-sm rounded-md hover:opacity-90 disabled:opacity-50 flex items-center justify-center gap-2"
                  data-testid="button-profile-submit-login">
                  {login.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <LogIn className="w-4 h-4" />}
                  Sign In
                </button>
              </div>
            </div>
          )}
        </div>

        {/* Stats Row */}
        <div className="grid grid-cols-3 gap-3 mb-6">
          <div className="neu-flat p-4 border border-white/5 rounded-md text-center" data-testid="stat-watchlist">
            <Bookmark className="w-5 h-5 text-primary mx-auto mb-2" />
            <p className="text-2xl font-bold text-white">{totalWatched}</p>
            <p className="text-[11px] text-muted-foreground uppercase tracking-wider">Watchlist</p>
          </div>
          <div className="neu-flat p-4 border border-white/5 rounded-md text-center" data-testid="stat-movies">
            <Film className="w-5 h-5 text-accent mx-auto mb-2" />
            <p className="text-2xl font-bold text-white">{watchlist.filter(w => w.type === 'movie').length}</p>
            <p className="text-[11px] text-muted-foreground uppercase tracking-wider">Movies</p>
          </div>
          <div className="neu-flat p-4 border border-white/5 rounded-md text-center" data-testid="stat-tvshows">
            <TrendingUp className="w-5 h-5 text-green-400 mx-auto mb-2" />
            <p className="text-2xl font-bold text-white">{watchlist.filter(w => w.type === 'tv').length}</p>
            <p className="text-[11px] text-muted-foreground uppercase tracking-wider">TV Shows</p>
          </div>
        </div>

        {/* Subscription Card */}
        {publicSettings?.paymentEnabled && (
          <div className="neu-flat p-6 border border-primary/20 rounded-md mb-6 relative overflow-hidden">
            <div className="absolute top-0 right-0 w-40 h-40 bg-primary/10 rounded-full blur-3xl -translate-y-1/4 translate-x-1/4" />
            <div className="relative z-10">
              <div className="flex items-center gap-2 mb-3">
                <Crown className="w-5 h-5 text-primary" />
                <h3 className="text-lg font-bold text-white">{publicSettings.subscriptionName}</h3>
              </div>
              <p className="text-sm text-muted-foreground mb-4">Get ad-free streaming, 4K quality, and exclusive content.</p>
              <div className="flex items-end gap-2 mb-4">
                <span className="text-3xl font-bold text-white">₹{publicSettings.subscriptionAmount}</span>
                <span className="text-muted-foreground text-sm mb-1">/month</span>
              </div>
              <button onClick={handleSubscribe}
                className="px-6 py-3 bg-primary text-primary-foreground font-bold text-sm hover:opacity-90 transition-opacity rounded-md flex items-center gap-2"
                data-testid="button-subscribe">
                <Crown className="w-4 h-4" />
                Subscribe Now
              </button>
            </div>
          </div>
        )}

        {/* About */}
        <div className="neu-flat p-5 border border-white/5 rounded-md mb-6">
          <h3 className="text-sm font-bold text-muted-foreground uppercase tracking-widest mb-3">About</h3>
          <div className="space-y-3">
            <div className="flex items-center justify-between py-2 border-b border-white/5">
              <span className="text-sm text-white/70">Account Type</span>
              <span className="text-sm text-primary font-medium">{isLoggedIn ? user?.role : 'Guest'}</span>
            </div>
            <div className="flex items-center justify-between py-2 border-b border-white/5">
              <span className="text-sm text-white/70">Streaming Quality</span>
              <span className="text-sm text-white font-medium">Auto</span>
            </div>
            <div className="flex items-center justify-between py-2 border-b border-white/5">
              <span className="text-sm text-white/70">Saved Items</span>
              <span className="text-sm text-white font-medium">{totalWatched} titles</span>
            </div>
            <div className="flex items-center justify-between py-2">
              <span className="text-sm text-white/70">Data Source</span>
              <span className="text-sm text-white/50 font-medium">TMDB</span>
            </div>
          </div>
        </div>

        {/* Recommended */}
        {trending.length > 0 && (
          <div>
            <h3 className="text-sm font-bold text-muted-foreground uppercase tracking-widest mb-3">Recommended For You</h3>
            <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
              {trending.map((item, i) => (
                <MovieCard key={item.id} item={{ ...item, media_type: 'movie' }} index={i} />
              ))}
            </div>
          </div>
        )}
      </div>
    </Shell>
  );
}
