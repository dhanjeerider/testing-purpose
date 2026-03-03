import { Switch, Route } from "wouter";
import { queryClient } from "./lib/queryClient";
import { QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import { ScrollToTop } from "@/components/shared/ScrollToTop";

import Home from "@/pages/Home";
import Search from "@/pages/Search";
import Watchlist from "@/pages/Watchlist";
import Player from "@/pages/Player";
import Admin from "@/pages/Admin";
import Profile from "@/pages/Profile";
import PageView from "@/pages/PageView";
import UpiPayment from "@/pages/UpiPayment";
import Pricing from "@/pages/Pricing";
import ProviderPage from "@/pages/ProviderPage";
import NotFound from "@/pages/not-found";

function Router() {
  return (
    <>
      <ScrollToTop />
      <Switch>
        <Route path="/" component={Home} />
        <Route path="/search" component={Search} />
        <Route path="/watchlist" component={Watchlist} />
        <Route path="/watch/:type/:id" component={Player} />
        <Route path="/admin" component={Admin} />
        <Route path="/profile" component={Profile} />
        <Route path="/page/:slug" component={PageView} />
        <Route path="/upi-payment" component={UpiPayment} />
        <Route path="/pricing" component={Pricing} />
        <Route path="/provider/:id" component={ProviderPage} />
        <Route component={NotFound} />
      </Switch>
    </>
  );
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <Toaster />
        <Router />
      </TooltipProvider>
    </QueryClientProvider>
  );
}

export default App;
