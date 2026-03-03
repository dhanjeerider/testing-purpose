import { useState } from "react";
import { Shell } from "@/components/layout/Shell";
import { useQuery, useMutation } from "@tanstack/react-query";
import { IndianRupee, Send, CheckCircle, Loader2, QrCode } from "lucide-react";
import { apiRequest, queryClient } from "@/lib/queryClient";

export default function UpiPayment() {
  const [form, setForm] = useState({
    userName: "",
    userEmail: "",
    transactionId: "",
    amount: "",
  });
  const [submitted, setSubmitted] = useState(false);

  const { data: publicSettings } = useQuery<{
    subscriptionEnabled: boolean;
    subscriptionAmount: string;
    subscriptionName: string;
    upiId: string | null;
    upiQrUrl: string | null;
  }>({
    queryKey: ["/api/settings/public"],
  });

  const submitMutation = useMutation({
    mutationFn: async (data: typeof form) => {
      await apiRequest("POST", "/api/upi-payments", {
        userName: data.userName,
        userEmail: data.userEmail || null,
        transactionId: data.transactionId,
        amount: data.amount,
        status: "pending",
      });
    },
    onSuccess: () => {
      setSubmitted(true);
    },
  });

  if (submitted) {
    return (
      <Shell>
        <div className="pt-24 px-4 md:px-8 lg:px-16 pb-20 max-w-lg mx-auto">
          <div className="neu-flat p-8 border border-white/5 text-center rounded-xl">
            <CheckCircle className="w-16 h-16 text-green-400 mx-auto mb-4" />
            <h2 className="text-xl font-bold text-white mb-2" data-testid="text-payment-submitted">Payment Submitted</h2>
            <p className="text-sm text-muted-foreground mb-1">
              Your payment details have been submitted for verification.
            </p>
            <p className="text-sm text-muted-foreground">
              Admin will review and approve your payment shortly.
            </p>
            <p className="text-xs text-muted-foreground mt-4">Transaction ID: {form.transactionId}</p>
          </div>
        </div>
      </Shell>
    );
  }

  return (
    <Shell>
      <div className="pt-24 px-4 md:px-8 lg:px-16 pb-20 max-w-lg mx-auto">
        <div className="flex items-center gap-3 mb-6">
          <div className="w-12 h-12 bg-primary flex items-center justify-center rounded-lg">
            <IndianRupee className="w-6 h-6 text-black" />
          </div>
          <div>
            <h1 className="text-2xl font-bold text-white" data-testid="text-upi-title">UPI Payment</h1>
            <p className="text-sm text-muted-foreground">Pay via UPI and submit your transaction details</p>
          </div>
        </div>

        {publicSettings?.upiId && (
          <div className="neu-flat p-6 border border-primary/20 rounded-xl mb-6">
            <h3 className="text-sm font-bold text-muted-foreground uppercase tracking-widest mb-3">Pay To</h3>
            <div className="flex items-center gap-3 mb-3">
              <QrCode className="w-5 h-5 text-primary" />
              <span className="text-white font-mono text-lg" data-testid="text-upi-id">{publicSettings.upiId}</span>
            </div>
            {publicSettings.upiQrUrl && (
              <div className="mt-4 flex justify-center">
                <img
                  src={publicSettings.upiQrUrl}
                  alt="UPI QR Code"
                  className="w-48 h-48 object-contain rounded-lg border border-white/10"
                  data-testid="img-upi-qr"
                />
              </div>
            )}
            {publicSettings.subscriptionAmount && (
              <p className="text-center text-sm text-muted-foreground mt-3">
                Amount: <span className="text-white font-bold">₹{publicSettings.subscriptionAmount}</span>
              </p>
            )}
          </div>
        )}

        <div className="neu-flat p-6 border border-white/5 rounded-xl">
          <h3 className="text-lg font-bold text-white mb-4">Submit Payment Details</h3>
          <div className="space-y-4">
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Your Name *</label>
              <input
                type="text"
                value={form.userName}
                onChange={(e) => setForm({ ...form, userName: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                placeholder="Enter your name"
                data-testid="input-upi-name"
              />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Email (optional)</label>
              <input
                type="email"
                value={form.userEmail}
                onChange={(e) => setForm({ ...form, userEmail: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                placeholder="your@email.com"
                data-testid="input-upi-email"
              />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">UPI Transaction ID *</label>
              <input
                type="text"
                value={form.transactionId}
                onChange={(e) => setForm({ ...form, transactionId: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none font-mono rounded-lg"
                placeholder="Enter UPI transaction/reference ID"
                data-testid="input-upi-txn-id"
              />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Amount (₹) *</label>
              <input
                type="number"
                value={form.amount}
                onChange={(e) => setForm({ ...form, amount: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                placeholder={publicSettings?.subscriptionAmount || "299"}
                data-testid="input-upi-amount"
              />
            </div>

            {submitMutation.isError && (
              <div className="p-3 bg-destructive/20 border border-destructive/30 text-destructive text-sm rounded-lg">
                {(submitMutation.error as Error).message || "Failed to submit payment"}
              </div>
            )}

            <button
              onClick={() => submitMutation.mutate(form)}
              disabled={!form.userName || !form.transactionId || !form.amount || submitMutation.isPending}
              className="w-full py-3 bg-primary text-primary-foreground font-bold hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-2 rounded-lg"
              data-testid="button-submit-upi"
            >
              {submitMutation.isPending ? (
                <Loader2 className="w-4 h-4 animate-spin" />
              ) : (
                <Send className="w-4 h-4" />
              )}
              Submit Payment
            </button>
          </div>
        </div>
      </div>
    </Shell>
  );
}
