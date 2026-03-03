import { useState } from "react";
import { Shell } from "@/components/layout/Shell";
import { useQuery } from "@tanstack/react-query";
import { Link } from "wouter";
import { Check, Crown, Zap, Star } from "lucide-react";
import { motion } from "framer-motion";

export default function Pricing() {
  const [billing, setBilling] = useState<"monthly" | "yearly">("monthly");

  const { data: publicSettings } = useQuery<{
    subscriptionEnabled: boolean;
    subscriptionName: string;
    subscriptionAmount: string;
    paymentEnabled: boolean;
  }>({
    queryKey: ["/api/settings/public"],
  });

  const planName = publicSettings?.subscriptionName || "Premium";
  const baseAmount = parseFloat(publicSettings?.subscriptionAmount || "199");
  const yearlyAmount = Math.round(baseAmount * 12 * 0.75);

  const features = [
    "Unlimited HD & 4K streaming",
    "No advertisements",
    "Download for offline viewing",
    "Access to exclusive content",
    "Multi-device support",
    "Priority customer support",
    "New releases on day one",
    "Cancel anytime",
  ];

  const price = billing === "monthly" ? baseAmount : yearlyAmount;
  const perMonth =
    billing === "yearly" ? (yearlyAmount / 12).toFixed(0) : baseAmount.toFixed(0);

  return (
    <Shell>
      <div className="pt-24 md:pt-32 pb-20 px-4 lg:px-12 max-w-4xl mx-auto min-h-screen">
        <div className="text-center mb-10">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/10 border border-primary/20 rounded-full mb-4">
            <Crown className="w-4 h-4 text-primary" />
            <span className="text-xs font-medium text-primary uppercase tracking-widest">Subscription Plans</span>
          </div>
          <h1 className="text-3xl md:text-5xl font-display font-bold text-white mb-3">
            Simple, <span className="text-gradient">Transparent</span> Pricing
          </h1>
          <p className="text-muted-foreground text-sm md:text-base max-w-md mx-auto">
            One plan, everything included. Stream without limits.
          </p>
        </div>

        <div className="flex items-center justify-center mb-10">
          <div className="flex items-center p-1 neu-pressed border border-white/5 rounded-xl gap-1">
            <button
              onClick={() => setBilling("monthly")}
              className={`px-5 py-2 text-sm font-medium rounded-lg transition-all ${
                billing === "monthly"
                  ? "bg-primary text-primary-foreground"
                  : "text-muted-foreground hover:text-white"
              }`}
              data-testid="billing-monthly"
            >
              Monthly
            </button>
            <button
              onClick={() => setBilling("yearly")}
              className={`flex items-center gap-2 px-5 py-2 text-sm font-medium rounded-lg transition-all ${
                billing === "yearly"
                  ? "bg-primary text-primary-foreground"
                  : "text-muted-foreground hover:text-white"
              }`}
              data-testid="billing-yearly"
            >
              Yearly
              <span className="text-[10px] font-bold bg-accent/20 text-accent px-1.5 py-0.5 rounded-md">
                25% OFF
              </span>
            </button>
          </div>
        </div>

        <motion.div
          key={billing}
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.3 }}
          className="max-w-md mx-auto"
        >
          <div className="neu-raised border border-primary/20 rounded-2xl overflow-hidden relative">
            <div className="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-primary via-accent to-primary" />

            <div className="p-8">
              <div className="flex items-center gap-3 mb-6">
                <div className="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
                  <Crown className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <h2 className="text-xl font-display font-bold text-white">{planName}</h2>
                  <p className="text-xs text-muted-foreground">Full access, no limits</p>
                </div>
                <div className="ml-auto flex items-center gap-1.5 px-2.5 py-1 bg-accent/10 border border-accent/20 rounded-full">
                  <Star className="w-3 h-3 text-accent fill-accent" />
                  <span className="text-[10px] font-bold text-accent uppercase tracking-wider">Best Value</span>
                </div>
              </div>

              <div className="mb-6">
                <div className="flex items-end gap-2">
                  <span className="text-4xl md:text-5xl font-display font-bold text-white">
                    ₹{price}
                  </span>
                  <div className="mb-1.5">
                    <span className="text-muted-foreground text-sm">
                      /{billing === "monthly" ? "month" : "year"}
                    </span>
                    {billing === "yearly" && (
                      <p className="text-xs text-primary mt-0.5 flex items-center gap-1">
                        <Zap className="w-3 h-3" />
                        ₹{perMonth}/mo — Save 25%
                      </p>
                    )}
                  </div>
                </div>
                {billing === "monthly" && (
                  <p className="text-xs text-muted-foreground mt-1">
                    Or ₹{yearlyAmount}/yr — save 25% with yearly
                  </p>
                )}
              </div>

              <div className="space-y-3 mb-8">
                {features.map((feature, i) => (
                  <div key={i} className="flex items-center gap-3">
                    <div className="w-5 h-5 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                      <Check className="w-3 h-3 text-primary" />
                    </div>
                    <span className="text-sm text-white/80">{feature}</span>
                  </div>
                ))}
              </div>

              {publicSettings?.paymentEnabled ? (
                <Link href="/profile">
                  <button
                    className="w-full py-3.5 bg-primary text-primary-foreground font-bold rounded-xl hover:opacity-90 transition-all text-sm tracking-wide uppercase flex items-center justify-center gap-2"
                    data-testid="button-subscribe-now"
                  >
                    <Crown className="w-4 h-4" />
                    Subscribe Now
                  </button>
                </Link>
              ) : (
                <button
                  disabled
                  className="w-full py-3.5 bg-secondary text-muted-foreground font-bold rounded-xl text-sm tracking-wide uppercase flex items-center justify-center gap-2 cursor-not-allowed"
                  data-testid="button-subscribe-disabled"
                >
                  <Crown className="w-4 h-4" />
                  Coming Soon
                </button>
              )}
            </div>
          </div>
        </motion.div>

        <div className="mt-8 text-center">
          <p className="text-xs text-muted-foreground">
            Secure payment. Cancel anytime. No hidden charges.
          </p>
          <div className="flex items-center justify-center gap-6 mt-4">
            {["Razorpay", "UPI", "Net Banking", "Cards"].map((method) => (
              <span key={method} className="text-xs font-medium text-white/30 uppercase tracking-wider">
                {method}
              </span>
            ))}
          </div>
        </div>
      </div>
    </Shell>
  );
}
