import { useState, useEffect } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { Shell } from "@/components/layout/Shell";
import { queryClient, apiRequest } from "@/lib/queryClient";
import type { CustomServer, Movie, Widget, Page, UpiPayment } from "@shared/schema";
import { useAuth, useLogin, useLogout, useChangePassword } from "@/hooks/use-auth";
import {
  Server, Plus, Trash2, Edit, Power, PowerOff,
  Monitor, Download, Loader2,
  Shield, Save, X, Globe,
  Film, Search, Import, Star, Eye, EyeOff, Bookmark, BarChart3,
  LogIn, LogOut, Key, Settings, CreditCard, ArrowUpDown, Check,
  PlusCircle, LayoutGrid, Megaphone, Link2, GripVertical, ToggleLeft, ToggleRight,
  FileText, IndianRupee, Clock, CheckCircle, XCircle, Mail, Copy, Tv
} from "lucide-react";

const IMG_BASE = "https://image.tmdb.org/t/p/";

function LoginForm() {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showForgot, setShowForgot] = useState(false);
  const [forgotEmail, setForgotEmail] = useState('');
  const [forgotMsg, setForgotMsg] = useState('');
  const [forgotNewPw, setForgotNewPw] = useState('');
  const [copied, setCopied] = useState(false);
  const login = useLogin();

  const handleForgot = async () => {
    setForgotMsg(''); setForgotNewPw('');
    try {
      const res = await fetch('/api/auth/forgot-password', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: forgotEmail }),
      });
      const data = await res.json();
      if (!res.ok) { setForgotMsg(data.message || 'Failed'); return; }
      setForgotNewPw(data.newPassword);
    } catch { setForgotMsg('Network error'); }
  };

  return (
    <Shell>
      <div className="pt-32 px-4 max-w-md mx-auto min-h-screen">
        <div className="neu-flat p-8 border border-white/5 rounded-md">
          <div className="flex items-center gap-3 mb-8">
            <div className="w-12 h-12 bg-primary flex items-center justify-center rounded-md">
              <Shield className="w-6 h-6 text-black" />
            </div>
            <div>
              <h1 className="text-2xl font-bold text-white" data-testid="text-login-title">Admin Login</h1>
              <p className="text-sm text-muted-foreground">Enter your credentials to continue</p>
            </div>
          </div>

          {!showForgot ? (
            <div className="space-y-4">
              <div>
                <label className="text-sm text-muted-foreground mb-1.5 block">Username</label>
                <input type="text" value={username} onChange={e => setUsername(e.target.value)}
                  className="w-full bg-secondary/50 px-4 py-3 text-white border border-white/10 focus:border-primary focus:outline-none rounded-md"
                  placeholder="admin" data-testid="input-login-username"
                  onKeyDown={e => e.key === 'Enter' && login.mutate({ username, password })} />
              </div>
              <div>
                <label className="text-sm text-muted-foreground mb-1.5 block">Password</label>
                <input type="password" value={password} onChange={e => setPassword(e.target.value)}
                  className="w-full bg-secondary/50 px-4 py-3 text-white border border-white/10 focus:border-primary focus:outline-none rounded-md"
                  placeholder="••••••••" data-testid="input-login-password"
                  onKeyDown={e => e.key === 'Enter' && login.mutate({ username, password })} />
              </div>

              {login.isError && (
                <div className="p-3 bg-destructive/20 border border-destructive/30 text-destructive text-sm rounded-md" data-testid="login-error">
                  {(login.error as Error).message || 'Login failed'}
                </div>
              )}

              <button onClick={() => login.mutate({ username, password })} disabled={login.isPending || !username || !password}
                className="w-full py-3 bg-primary text-primary-foreground font-bold hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-2 rounded-md"
                data-testid="button-login">
                {login.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <LogIn className="w-4 h-4" />}
                Sign In
              </button>

              <button onClick={() => setShowForgot(true)} className="w-full text-center text-sm text-muted-foreground hover:text-primary transition-colors" data-testid="link-forgot-password">
                Forgot Password?
              </button>
            </div>
          ) : (
            <div className="space-y-4">
              <button onClick={() => { setShowForgot(false); setForgotMsg(''); setForgotNewPw(''); }}
                className="flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors mb-2">
                ← Back to Login
              </button>
              <p className="text-sm text-muted-foreground">Enter the admin email set in Settings to reset your password.</p>
              <div>
                <label className="text-sm text-muted-foreground mb-1.5 block">Admin Email</label>
                <input type="email" value={forgotEmail} onChange={e => setForgotEmail(e.target.value)}
                  className="w-full bg-secondary/50 px-4 py-3 text-white border border-white/10 focus:border-primary focus:outline-none rounded-md"
                  placeholder="admin@example.com" data-testid="input-forgot-email" />
              </div>
              {forgotMsg && <p className="text-sm text-destructive" data-testid="forgot-error">{forgotMsg}</p>}
              {forgotNewPw && (
                <div className="p-4 bg-green-500/10 border border-green-500/30 rounded-md">
                  <p className="text-xs text-green-400 mb-2 font-medium">New password generated! Save it now:</p>
                  <div className="flex items-center gap-2">
                    <code className="flex-1 text-lg font-bold text-white font-mono bg-black/30 px-3 py-2 rounded">{forgotNewPw}</code>
                    <button onClick={() => { navigator.clipboard.writeText(forgotNewPw); setCopied(true); setTimeout(() => setCopied(false), 2000); }}
                      className="p-2 hover:bg-white/10 rounded-lg transition-colors" data-testid="button-copy-password">
                      {copied ? <Check className="w-4 h-4 text-green-400" /> : <Copy className="w-4 h-4 text-white" />}
                    </button>
                  </div>
                </div>
              )}
              <button onClick={handleForgot} disabled={!forgotEmail}
                className="w-full py-3 bg-primary text-primary-foreground font-bold hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-2 rounded-md"
                data-testid="button-reset-password">
                <Mail className="w-4 h-4" /> Reset Password
              </button>
            </div>
          )}

          <p className="text-xs text-muted-foreground mt-6 text-center">Default: admin / admin123</p>
        </div>
      </div>
    </Shell>
  );
}

function StatCard({ title, value, icon: Icon, color }: { title: string; value: number | string; icon: any; color: string }) {
  return (
    <div className="neu-flat p-5 flex items-center gap-4 border border-white/5 rounded-lg" data-testid={`stat-${title.toLowerCase().replace(/\s/g, '-')}`}>
      <div className={`w-12 h-12 flex items-center justify-center rounded-lg ${color}`}>
        <Icon className="w-6 h-6 text-white" />
      </div>
      <div>
        <p className="text-2xl font-bold text-white">{value}</p>
        <p className="text-sm text-muted-foreground">{title}</p>
      </div>
    </div>
  );
}

function ServerCard({ server, onToggle, onDelete, onEdit }: {
  server: CustomServer;
  onToggle?: () => void;
  onDelete?: () => void;
  onEdit?: () => void;
}) {
  return (
    <div className="neu-flat p-4 flex items-center justify-between gap-3 border border-white/5 rounded-lg" data-testid={`server-card-${server.name}`}>
      <div className="flex items-center gap-3 min-w-0">
        <div className={`w-10 h-10 flex items-center justify-center shrink-0 rounded-lg ${server.isDownload ? 'bg-accent/20 text-accent' : 'bg-primary/20 text-primary'}`}>
          {server.isDownload ? <Download className="w-5 h-5" /> : <Monitor className="w-5 h-5" />}
        </div>
        <div className="min-w-0">
          <p className="text-white font-medium truncate">{server.icon || ''} {server.name}</p>
          <div className="flex flex-wrap items-center gap-2 mt-0.5">
            <span className="text-xs text-muted-foreground uppercase">{server.type}</span>
            {server.hasAds && <span className="text-xs bg-destructive/20 text-destructive px-1.5 py-0.5 rounded-sm">ADS</span>}
            {server.has4K && <span className="text-xs bg-primary/20 text-primary px-1.5 py-0.5 rounded-sm">4K</span>}
            {server.isDefault && <span className="text-xs bg-secondary text-muted-foreground px-1.5 py-0.5 rounded-sm">DEFAULT</span>}
            {server.isActive !== false && <span className="text-xs bg-green-500/20 text-green-400 px-1.5 py-0.5 rounded-sm">ACTIVE</span>}
            {server.isActive === false && <span className="text-xs bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded-sm">INACTIVE</span>}
          </div>
        </div>
      </div>

      <div className="flex items-center gap-1 shrink-0">
        {onToggle && (
          <button onClick={onToggle} className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid={`toggle-server-${server.id}`}>
            {server.isActive !== false ? <Power className="w-4 h-4 text-green-400" /> : <PowerOff className="w-4 h-4 text-red-400" />}
          </button>
        )}
        {onEdit && (
          <button onClick={onEdit} className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid={`edit-server-${server.id}`}>
            <Edit className="w-4 h-4 text-muted-foreground" />
          </button>
        )}
        {onDelete && (
          <button onClick={onDelete} className="p-2 hover:bg-destructive/20 transition-colors rounded-lg" data-testid={`delete-server-${server.id}`}>
            <Trash2 className="w-4 h-4 text-destructive" />
          </button>
        )}
      </div>
    </div>
  );
}

function AddServerForm({ onClose, editServer }: { onClose: () => void; editServer?: CustomServer | null }) {
  const [form, setForm] = useState({
    name: editServer?.name || '',
    type: editServer?.type || 'tmdb',
    url: editServer?.url || '',
    urlTv: editServer?.urlTv || '',
    hasAds: editServer?.hasAds || false,
    has4K: editServer?.has4K || false,
    isDownload: editServer?.isDownload || false,
    isActive: editServer?.isActive !== false,
    icon: editServer?.icon || '',
    description: editServer?.description || '',
  });

  const [formError, setFormError] = useState('');

  const createMutation = useMutation({
    mutationFn: async (data: typeof form) => {
      setFormError('');
      if (editServer) {
        await apiRequest('PUT', `/api/admin/servers/${editServer.id}`, data);
      } else {
        await apiRequest('POST', '/api/admin/servers', data);
      }
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/servers'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
      onClose();
    },
    onError: (err: Error) => {
      setFormError(err.message || 'Failed to save server');
    },
  });

  return (
    <div className="neu-flat p-6 border border-white/5 rounded-xl" data-testid="add-server-form">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-lg font-bold text-white">{editServer ? 'Edit Server' : 'Add New Server'}</h3>
        <button onClick={onClose} className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid="close-form">
          <X className="w-5 h-5" />
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Server Name</label>
          <input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="My Server" data-testid="input-server-name" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Type</label>
          <select value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            data-testid="select-server-type">
            <option value="tmdb">TMDB</option>
            <option value="imdb">IMDB</option>
          </select>
        </div>
        <div className="md:col-span-2">
          <label className="text-sm text-muted-foreground mb-1 block">Movie URL Template</label>
          <input type="text" value={form.url} onChange={(e) => setForm({ ...form, url: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none font-mono text-sm rounded-lg"
            placeholder="https://example.com/movie/{tmdb_id}" data-testid="input-server-url" />
        </div>
        <div className="md:col-span-2">
          <label className="text-sm text-muted-foreground mb-1 block">TV URL Template</label>
          <input type="text" value={form.urlTv} onChange={(e) => setForm({ ...form, urlTv: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none font-mono text-sm rounded-lg"
            placeholder="https://example.com/tv/{tmdb_id}/{season}/{episode}" data-testid="input-server-url-tv" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Icon (optional)</label>
          <input type="text" value={form.icon} onChange={(e) => setForm({ ...form, icon: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="e.g. a short label" data-testid="input-server-icon" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Description (optional)</label>
          <input type="text" value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="Short description" data-testid="input-server-desc" />
        </div>
      </div>

      <div className="flex flex-wrap gap-4 mt-4">
        {[
          { key: 'hasAds', label: 'Has Ads' },
          { key: 'has4K', label: '4K Support' },
          { key: 'isDownload', label: 'Download Server' },
          { key: 'isActive', label: 'Active' },
        ].map(({ key, label }) => (
          <label key={key} className="flex items-center gap-2 cursor-pointer text-sm text-muted-foreground">
            <input type="checkbox" checked={(form as any)[key]}
              onChange={(e) => setForm({ ...form, [key]: e.target.checked })}
              className="w-4 h-4 accent-primary" data-testid={`checkbox-${key}`} />
            {label}
          </label>
        ))}
      </div>

      {formError && (
        <div className="mt-4 p-3 bg-destructive/20 border border-destructive/30 text-destructive text-sm rounded-lg" data-testid="form-error">
          {formError}
        </div>
      )}

      <div className="flex justify-end gap-3 mt-6">
        <button onClick={onClose}
          className="px-4 py-2 bg-secondary text-muted-foreground hover:bg-secondary/80 transition-colors rounded-lg"
          data-testid="button-cancel">Cancel</button>
        <button onClick={() => createMutation.mutate(form)}
          disabled={!form.name || !form.url || !form.urlTv || createMutation.isPending}
          className="px-4 py-2 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg"
          data-testid="button-save-server">
          {createMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
          {editServer ? 'Update' : 'Save'} Server
        </button>
      </div>
    </div>
  );
}

function MovieEditorPanel({ movie, onClose }: { movie: Movie; onClose: () => void }) {
  const [form, setForm] = useState({
    title: movie.title,
    overview: movie.overview || '',
    releaseDate: movie.releaseDate || '',
    isFeatured: movie.isFeatured || false,
    isActive: movie.isActive !== false,
  });
  const [downloadLinks, setDownloadLinks] = useState<{ quality: string; url: string }[]>(() => {
    try {
      if (movie.downloadLinksJson) return JSON.parse(movie.downloadLinksJson);
    } catch {}
    return [];
  });
  const [saved, setSaved] = useState(false);

  const addDownloadLink = () => setDownloadLinks([...downloadLinks, { quality: '', url: '' }]);
  const removeDownloadLink = (idx: number) => setDownloadLinks(downloadLinks.filter((_, i) => i !== idx));
  const updateDownloadLink = (idx: number, field: 'quality' | 'url', value: string) => {
    const updated = [...downloadLinks];
    updated[idx][field] = value;
    setDownloadLinks(updated);
  };

  const updateMutation = useMutation({
    mutationFn: async (data: typeof form) => {
      const validLinks = downloadLinks.filter(l => l.quality && l.url);
      await apiRequest('PUT', `/api/admin/movies/${movie.id}`, {
        ...data,
        downloadLinksJson: validLinks.length > 0 ? JSON.stringify(validLinks) : null,
      });
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/movies'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
      setSaved(true);
      setTimeout(() => setSaved(false), 2000);
    },
  });

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80" data-testid="movie-editor-overlay">
      <div className="neu-raised w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto border border-white/10 rounded-xl" data-testid="edit-movie-form">
        <div className="sticky top-0 z-10 bg-card border-b border-white/10 px-6 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 bg-primary/20 flex items-center justify-center rounded-lg">
              <Edit className="w-4 h-4 text-primary" />
            </div>
            <div>
              <h3 className="text-lg font-bold text-white">Edit Content</h3>
              <p className="text-xs text-muted-foreground">TMDB ID: {movie.tmdbId} · {movie.mediaType?.toUpperCase()}</p>
            </div>
          </div>
          <button onClick={onClose} className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid="close-edit-movie">
            <X className="w-5 h-5" />
          </button>
        </div>

        <div className="p-6">
          <div className="flex gap-5 mb-6">
            {movie.posterPath ? (
              <img src={`${IMG_BASE}w185${movie.posterPath}`} alt={movie.title} className="w-28 h-40 object-cover shrink-0 border border-white/10 rounded-lg" />
            ) : (
              <div className="w-28 h-40 bg-secondary flex items-center justify-center shrink-0 border border-white/10 rounded-lg">
                <Film className="w-8 h-8 text-muted-foreground" />
              </div>
            )}
            <div className="flex-1 space-y-4">
              <div>
                <label className="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1.5 block">Title</label>
                <input type="text" value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })}
                  className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                  data-testid="input-movie-title" />
              </div>
              <div>
                <label className="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1.5 block">Release Date</label>
                <input type="text" value={form.releaseDate} onChange={(e) => setForm({ ...form, releaseDate: e.target.value })}
                  className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                  placeholder="2024-01-15" data-testid="input-movie-date" />
              </div>
            </div>
          </div>

          <div className="mb-5">
            <label className="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-1.5 block">Overview</label>
            <textarea value={form.overview} onChange={(e) => setForm({ ...form, overview: e.target.value })} rows={4}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none resize-none rounded-lg"
              data-testid="input-movie-overview" />
          </div>

          <div className="flex flex-wrap gap-5 mb-5 p-4 bg-secondary/20 border border-white/5 rounded-lg">
            <label className="flex items-center gap-2 cursor-pointer text-sm text-muted-foreground">
              <input type="checkbox" checked={form.isFeatured} onChange={(e) => setForm({ ...form, isFeatured: e.target.checked })}
                className="w-4 h-4 accent-primary" data-testid="checkbox-featured" />
              <Star className="w-4 h-4 text-yellow-400" />
              Featured
            </label>
            <label className="flex items-center gap-2 cursor-pointer text-sm text-muted-foreground">
              <input type="checkbox" checked={form.isActive} onChange={(e) => setForm({ ...form, isActive: e.target.checked })}
                className="w-4 h-4 accent-primary" data-testid="checkbox-movie-active" />
              {form.isActive ? <Eye className="w-4 h-4 text-green-400" /> : <EyeOff className="w-4 h-4 text-red-400" />}
              {form.isActive ? 'Visible' : 'Hidden'}
            </label>
          </div>

          <div className="mb-5 p-4 bg-secondary/20 border border-white/5 rounded-lg">
            <div className="flex items-center justify-between mb-3">
              <label className="text-xs font-medium text-muted-foreground uppercase tracking-wider flex items-center gap-2">
                <Download className="w-4 h-4" />
                Custom Download Links
              </label>
              <button onClick={addDownloadLink} className="text-xs text-primary hover:text-primary/80 flex items-center gap-1" data-testid="button-edit-add-dl">
                <Plus className="w-3.5 h-3.5" /> Add Link
              </button>
            </div>
            {downloadLinks.length === 0 && (
              <p className="text-xs text-muted-foreground">No custom download links. Click "Add Link" to add quality-specific URLs.</p>
            )}
            {downloadLinks.map((link, idx) => (
              <div key={idx} className="flex items-center gap-2 mb-2" data-testid={`edit-dl-row-${idx}`}>
                <input type="text" value={link.quality} onChange={e => updateDownloadLink(idx, 'quality', e.target.value)}
                  className="w-28 bg-secondary/50 px-3 py-2 text-white text-sm border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                  placeholder="e.g. 1080p" data-testid={`edit-dl-quality-${idx}`} />
                <input type="text" value={link.url} onChange={e => updateDownloadLink(idx, 'url', e.target.value)}
                  className="flex-1 bg-secondary/50 px-3 py-2 text-white text-sm border border-white/10 focus:border-primary focus:outline-none font-mono rounded-lg"
                  placeholder="https://..." data-testid={`edit-dl-url-${idx}`} />
                <button onClick={() => removeDownloadLink(idx)} className="p-2 text-red-400 hover:bg-red-500/10 transition-colors rounded-lg" data-testid={`edit-dl-remove-${idx}`}>
                  <Trash2 className="w-4 h-4" />
                </button>
              </div>
            ))}
          </div>

          <div className="flex items-center justify-between pt-4 border-t border-white/10">
            {saved && <span className="text-sm text-green-400 flex items-center gap-1"><Check className="w-3.5 h-3.5" />Saved!</span>}
            {!saved && <span />}
            <div className="flex gap-3">
              <button onClick={onClose} className="px-4 py-2.5 bg-secondary text-muted-foreground hover:bg-secondary/80 transition-colors rounded-lg">Cancel</button>
              <button onClick={() => updateMutation.mutate(form)} disabled={updateMutation.isPending}
                className="px-5 py-2.5 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg"
                data-testid="button-update-movie">
                {updateMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
                Save Changes
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

function MovieImportSearch({ onClose }: { onClose: () => void }) {
  const [searchQuery, setSearchQuery] = useState('');
  const [searchResults, setSearchResults] = useState<any[]>([]);
  const [searching, setSearching] = useState(false);
  const [selected, setSelected] = useState<Set<number>>(new Set());
  const [bulkDone, setBulkDone] = useState<Set<number>>(new Set());
  const [mediaTypeFilter, setMediaTypeFilter] = useState<'all' | 'movie' | 'tv'>('all');

  const { data: existingMovies = [] } = useQuery<Movie[]>({
    queryKey: ['/api/movies'],
  });

  const existingIds = new Set(existingMovies.map(m => m.tmdbId));

  const { data: trendingData } = useQuery<{ results: any[] }>({
    queryKey: ['/api/tmdb/trending'],
    queryFn: async () => {
      const res = await fetch('/api/tmdb/trending?type=all');
      return res.json();
    },
    staleTime: 5 * 60 * 1000,
  });

  const trendingRaw = (trendingData?.results || [])
    .filter((r: any) => r.media_type === 'movie' || r.media_type === 'tv')
    .sort((a: any, b: any) => (b.popularity || 0) - (a.popularity || 0));

  const isSearchMode = !!searchQuery.trim();

  const displayItems = isSearchMode
    ? searchResults.filter((r: any) => mediaTypeFilter === 'all' || r.media_type === mediaTypeFilter)
    : trendingRaw.filter((r: any) => mediaTypeFilter === 'all' || r.media_type === mediaTypeFilter);

  const doSearch = async () => {
    if (!searchQuery.trim()) return;
    setSearching(true);
    setSelected(new Set());
    try {
      const res = await fetch(`/api/tmdb/search?q=${encodeURIComponent(searchQuery)}`);
      const data = await res.json();
      const filtered = (data.results || [])
        .filter((r: any) => r.media_type === 'movie' || r.media_type === 'tv')
        .sort((a: any, b: any) => (b.popularity || 0) - (a.popularity || 0));
      setSearchResults(filtered);
    } catch {
      setSearchResults([]);
    }
    setSearching(false);
  };

  const toggleSelect = (id: number) => {
    setSelected(prev => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id); else next.add(id);
      return next;
    });
  };

  const selectAll = () => {
    const importable = displayItems.filter((r: any) => !existingIds.has(r.id));
    if (selected.size === importable.length) {
      setSelected(new Set());
    } else {
      setSelected(new Set(importable.map((r: any) => r.id)));
    }
  };

  const importMutation = useMutation({
    mutationFn: async ({ tmdbId, mediaType }: { tmdbId: number; mediaType: string }) => {
      await apiRequest('POST', '/api/admin/movies/import', { tmdbId, mediaType });
    },
    onSuccess: (_data, vars) => {
      setBulkDone(prev => new Set([...prev, vars.tmdbId]));
      queryClient.invalidateQueries({ queryKey: ['/api/admin/movies'] });
      queryClient.invalidateQueries({ queryKey: ['/api/movies'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
    },
  });

  const [bulkPending, setBulkPending] = useState(false);

  const handleBulkImport = async () => {
    const toImport = displayItems.filter((r: any) => selected.has(r.id) && !existingIds.has(r.id));
    if (!toImport.length) return;
    setBulkPending(true);
    for (const item of toImport) {
      try {
        await importMutation.mutateAsync({ tmdbId: item.id, mediaType: item.media_type });
      } catch {
        setBulkDone(prev => new Set([...prev, item.id]));
      }
    }
    setSelected(new Set());
    setBulkPending(false);
    queryClient.invalidateQueries({ queryKey: ['/api/movies'] });
  };

  const importableSelected = [...selected].filter(id => !existingIds.has(id)).length;

  return (
    <div className="neu-flat border border-white/5 rounded-xl overflow-hidden" data-testid="import-search-panel">
      <div className="p-4 border-b border-white/5">
        <div className="flex items-center justify-between mb-3">
          <h3 className="text-base font-bold text-white">Import from TMDB</h3>
          {selected.size > 0 && (
            <button
              onClick={handleBulkImport}
              disabled={bulkPending || importableSelected === 0}
              className="flex items-center gap-1.5 px-3 py-1.5 bg-primary text-primary-foreground text-xs font-bold rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50"
              data-testid="button-bulk-import"
            >
              {bulkPending ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <Import className="w-3.5 h-3.5" />}
              Import {importableSelected} Selected
            </button>
          )}
        </div>

        <div className="flex gap-2 mb-3">
          <input type="text" value={searchQuery}
            onChange={(e) => { setSearchQuery(e.target.value); if (!e.target.value.trim()) setSearchResults([]); }}
            onKeyDown={(e) => e.key === 'Enter' && doSearch()}
            className="flex-1 bg-secondary/50 px-3 py-2 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg text-sm"
            placeholder="Search TMDB or browse popular below..." data-testid="input-import-search" />
          <button onClick={doSearch} disabled={searching}
            className="px-3 py-2 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-1.5 rounded-lg text-sm"
            data-testid="button-search-tmdb">
            {searching ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <Search className="w-3.5 h-3.5" />}
            Search
          </button>
        </div>

        <div className="flex items-center justify-between gap-2 flex-wrap">
          <div className="flex gap-1">
            {(['all', 'movie', 'tv'] as const).map(f => (
              <button key={f} onClick={() => setMediaTypeFilter(f)}
                className={`px-2.5 py-1 text-xs font-medium rounded-md transition-all capitalize ${mediaTypeFilter === f ? 'bg-primary text-primary-foreground' : 'bg-secondary/50 text-white/60 hover:text-white border border-white/5'}`}
                data-testid={`import-filter-${f}`}>
                {f === 'all' ? 'All' : f === 'movie' ? 'Movies' : 'TV Shows'}
              </button>
            ))}
          </div>
          <div className="flex items-center gap-2">
            <button onClick={selectAll} className="text-xs text-primary hover:text-primary/80 transition-colors" data-testid="button-select-all">
              {selected.size > 0 ? 'Deselect All' : 'Select All'}
            </button>
            <span className="text-xs text-muted-foreground">
              {isSearchMode ? `${displayItems.length} results` : `${trendingRaw.length} trending`}
            </span>
          </div>
        </div>
      </div>

      <div className="max-h-[500px] overflow-y-auto">
        {!isSearchMode && !trendingData && (
          <div className="flex justify-center py-8">
            <Loader2 className="w-6 h-6 text-primary animate-spin" />
          </div>
        )}
        {isSearchMode && searching && (
          <div className="flex justify-center py-8">
            <Loader2 className="w-6 h-6 text-primary animate-spin" />
          </div>
        )}
        {isSearchMode && !searching && searchResults.length === 0 && (
          <p className="text-center text-muted-foreground py-8 text-sm">No results found for "{searchQuery}"</p>
        )}

        <div className="divide-y divide-white/5">
          {displayItems.map((item: any) => {
            const alreadyImported = existingIds.has(item.id);
            const justDone = bulkDone.has(item.id);
            const isSelected = selected.has(item.id);
            return (
              <div
                key={item.id}
                onClick={() => !alreadyImported && toggleSelect(item.id)}
                className={`flex items-center gap-3 p-3 transition-colors cursor-pointer ${
                  isSelected ? 'bg-primary/10' : alreadyImported ? 'opacity-50' : 'hover:bg-secondary/30'
                }`}
                data-testid={`import-item-${item.id}`}
              >
                <div className={`w-4 h-4 shrink-0 rounded border flex items-center justify-center transition-all ${
                  alreadyImported ? 'border-green-500/50 bg-green-500/20' :
                  isSelected ? 'bg-primary border-primary' : 'border-white/20'
                }`}>
                  {(alreadyImported || justDone) && <Check className="w-2.5 h-2.5 text-green-400" />}
                  {isSelected && !alreadyImported && <Check className="w-2.5 h-2.5 text-primary-foreground" />}
                </div>

                {item.poster_path ? (
                  <img src={`${IMG_BASE}w92${item.poster_path}`} alt={item.title || item.name} className="w-10 h-14 object-cover shrink-0 rounded" />
                ) : (
                  <div className="w-10 h-14 bg-secondary flex items-center justify-center shrink-0 rounded">
                    <Film className="w-4 h-4 text-muted-foreground" />
                  </div>
                )}

                <div className="flex-1 min-w-0">
                  <p className="text-white text-sm font-medium truncate">{item.title || item.name}</p>
                  <div className="flex items-center flex-wrap gap-x-2 gap-y-0.5 text-xs text-muted-foreground mt-0.5">
                    <span className="uppercase font-medium text-[10px] px-1 py-0.5 bg-white/5 rounded">{item.media_type}</span>
                    <span>{(item.release_date || item.first_air_date || '').slice(0, 4)}</span>
                    {item.vote_average > 0 && (
                      <span className="flex items-center gap-0.5">
                        <Star className="w-2.5 h-2.5 text-yellow-400" />{item.vote_average.toFixed(1)}
                      </span>
                    )}
                    {item.popularity > 0 && (
                      <span className="text-[10px] text-muted-foreground/60">Pop: {item.popularity.toFixed(0)}</span>
                    )}
                  </div>
                  {alreadyImported && (
                    <span className="text-[10px] text-green-400 font-medium">✓ Already in library</span>
                  )}
                </div>

                <button
                  onClick={(e) => {
                    e.stopPropagation();
                    if (!alreadyImported) importMutation.mutate({ tmdbId: item.id, mediaType: item.media_type });
                  }}
                  disabled={alreadyImported || importMutation.isPending}
                  className={`px-2.5 py-1.5 text-xs font-medium rounded-lg transition-colors shrink-0 ${
                    alreadyImported
                      ? 'bg-green-500/10 text-green-400 cursor-default'
                      : 'bg-primary/20 text-primary hover:bg-primary hover:text-primary-foreground'
                  }`}
                  data-testid={`import-single-${item.id}`}
                >
                  {alreadyImported ? <Check className="w-3.5 h-3.5" /> : <Import className="w-3.5 h-3.5" />}
                </button>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
}

function ManualAddForm() {
  const [form, setForm] = useState({
    title: '',
    tmdbId: '',
    mediaType: 'movie',
    overview: '',
    posterPath: '',
    backdropPath: '',
    releaseDate: '',
    voteAverage: '',
    genreIds: '',
    runtime: '',
  });
  const [downloadLinks, setDownloadLinks] = useState<{ quality: string; url: string }[]>([]);
  const [formError, setFormError] = useState('');
  const [saved, setSaved] = useState(false);

  const addDownloadLink = () => setDownloadLinks([...downloadLinks, { quality: '', url: '' }]);
  const removeDownloadLink = (idx: number) => setDownloadLinks(downloadLinks.filter((_, i) => i !== idx));
  const updateDownloadLink = (idx: number, field: 'quality' | 'url', value: string) => {
    const updated = [...downloadLinks];
    updated[idx][field] = value;
    setDownloadLinks(updated);
  };

  const createMutation = useMutation({
    mutationFn: async (data: typeof form) => {
      setFormError('');
      const payload: any = {
        title: data.title,
        tmdbId: parseInt(data.tmdbId),
        mediaType: data.mediaType,
      };
      if (data.overview) payload.overview = data.overview;
      if (data.posterPath) payload.posterPath = data.posterPath;
      if (data.backdropPath) payload.backdropPath = data.backdropPath;
      if (data.releaseDate) payload.releaseDate = data.releaseDate;
      if (data.voteAverage) payload.voteAverage = parseFloat(data.voteAverage);
      if (data.genreIds) payload.genreIds = data.genreIds;
      if (data.runtime) payload.runtime = parseInt(data.runtime);
      const validLinks = downloadLinks.filter(l => l.quality && l.url);
      if (validLinks.length > 0) payload.downloadLinksJson = JSON.stringify(validLinks);
      await apiRequest('POST', '/api/admin/movies', payload);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/movies'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
      setSaved(true);
      setTimeout(() => setSaved(false), 2000);
      setForm({ title: '', tmdbId: '', mediaType: 'movie', overview: '', posterPath: '', backdropPath: '', releaseDate: '', voteAverage: '', genreIds: '', runtime: '' });
      setDownloadLinks([]);
    },
    onError: (err: Error) => {
      setFormError(err.message || 'Failed to add movie');
    },
  });

  return (
    <div className="neu-flat p-6 border border-white/5 rounded-xl" data-testid="manual-add-form">
      <div className="flex items-center gap-3 mb-5">
        <PlusCircle className="w-5 h-5 text-primary" />
        <h3 className="text-lg font-bold text-white">Manual Add</h3>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Title *</label>
          <input type="text" value={form.title} onChange={e => setForm({ ...form, title: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="Movie title" data-testid="input-manual-title" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">TMDB ID *</label>
          <input type="number" value={form.tmdbId} onChange={e => setForm({ ...form, tmdbId: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="12345" data-testid="input-manual-tmdb-id" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Media Type</label>
          <select value={form.mediaType} onChange={e => setForm({ ...form, mediaType: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            data-testid="select-manual-media-type">
            <option value="movie">Movie</option>
            <option value="tv">TV Show</option>
          </select>
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Release Date</label>
          <input type="text" value={form.releaseDate} onChange={e => setForm({ ...form, releaseDate: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="2024-01-15" data-testid="input-manual-release-date" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Vote Average</label>
          <input type="number" step="0.1" value={form.voteAverage} onChange={e => setForm({ ...form, voteAverage: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="7.5" data-testid="input-manual-vote" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Runtime (min)</label>
          <input type="number" value={form.runtime} onChange={e => setForm({ ...form, runtime: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="120" data-testid="input-manual-runtime" />
        </div>
        <div className="md:col-span-2">
          <label className="text-sm text-muted-foreground mb-1 block">Overview</label>
          <textarea value={form.overview} onChange={e => setForm({ ...form, overview: e.target.value })} rows={3}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none resize-none rounded-lg"
            placeholder="Movie description..." data-testid="input-manual-overview" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Poster Path URL</label>
          <input type="text" value={form.posterPath} onChange={e => setForm({ ...form, posterPath: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none font-mono text-sm rounded-lg"
            placeholder="/path/to/poster.jpg" data-testid="input-manual-poster" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Backdrop Path URL</label>
          <input type="text" value={form.backdropPath} onChange={e => setForm({ ...form, backdropPath: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none font-mono text-sm rounded-lg"
            placeholder="/path/to/backdrop.jpg" data-testid="input-manual-backdrop" />
        </div>
        <div className="md:col-span-2">
          <label className="text-sm text-muted-foreground mb-1 block">Genres</label>
          <input type="text" value={form.genreIds} onChange={e => setForm({ ...form, genreIds: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="Action, Drama, Thriller" data-testid="input-manual-genres" />
        </div>
      </div>

      <div className="mt-5 p-4 bg-secondary/20 border border-white/5 rounded-lg">
        <div className="flex items-center justify-between mb-3">
          <label className="text-sm font-medium text-muted-foreground flex items-center gap-2">
            <Download className="w-4 h-4" />
            Custom Download Links
          </label>
          <button onClick={addDownloadLink} className="text-xs text-primary hover:text-primary/80 flex items-center gap-1" data-testid="button-add-download-link">
            <Plus className="w-3.5 h-3.5" /> Add Link
          </button>
        </div>
        {downloadLinks.length === 0 && (
          <p className="text-xs text-muted-foreground">No custom download links added. Click "Add Link" to add quality-specific download URLs.</p>
        )}
        {downloadLinks.map((link, idx) => (
          <div key={idx} className="flex items-center gap-2 mb-2" data-testid={`download-link-row-${idx}`}>
            <input type="text" value={link.quality} onChange={e => updateDownloadLink(idx, 'quality', e.target.value)}
              className="w-32 bg-secondary/50 px-3 py-2 text-white text-sm border border-white/10 focus:border-primary focus:outline-none rounded-lg"
              placeholder="e.g. 720p" data-testid={`input-dl-quality-${idx}`} />
            <input type="text" value={link.url} onChange={e => updateDownloadLink(idx, 'url', e.target.value)}
              className="flex-1 bg-secondary/50 px-3 py-2 text-white text-sm border border-white/10 focus:border-primary focus:outline-none font-mono rounded-lg"
              placeholder="https://download-url.com/file" data-testid={`input-dl-url-${idx}`} />
            <button onClick={() => removeDownloadLink(idx)} className="p-2 text-red-400 hover:bg-red-500/10 transition-colors rounded-lg" data-testid={`button-remove-dl-${idx}`}>
              <Trash2 className="w-4 h-4" />
            </button>
          </div>
        ))}
      </div>

      {formError && (
        <div className="mt-4 p-3 bg-destructive/20 border border-destructive/30 text-destructive text-sm rounded-lg" data-testid="manual-add-error">
          {formError}
        </div>
      )}

      <div className="flex items-center gap-3 mt-5">
        <button onClick={() => createMutation.mutate(form)}
          disabled={!form.title || !form.tmdbId || createMutation.isPending}
          className="px-4 py-2.5 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg"
          data-testid="button-manual-add">
          {createMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Plus className="w-4 h-4" />}
          Add Post
        </button>
        {saved && <span className="text-sm text-green-400 flex items-center gap-1"><Check className="w-3.5 h-3.5" />Added!</span>}
      </div>
    </div>
  );
}

function WidgetForm({ widget, onClose }: { widget?: Widget | null; onClose: () => void }) {
  const [form, setForm] = useState({
    type: widget?.type || 'content_row',
    title: widget?.title || '',
    config: widget?.config || '{}',
    sortOrder: widget?.sortOrder?.toString() || '0',
    isActive: widget?.isActive !== false,
  });
  const [formError, setFormError] = useState('');

  const getDefaultConfig = (type: string) => {
    switch (type) {
      case 'content_row': return JSON.stringify({ filter: 'all', limit: 10 }, null, 2);
      case 'cta_banner': return JSON.stringify({ text: 'Subscribe now', buttonText: 'Subscribe', link: '/profile' }, null, 2);
      case 'menu_links': return JSON.stringify({ links: [{ label: 'Movies', href: '/search?type=movie' }] }, null, 2);
      default: return '{}';
    }
  };

  const saveMutation = useMutation({
    mutationFn: async (data: typeof form) => {
      setFormError('');
      const payload = {
        type: data.type,
        title: data.title,
        config: data.config,
        sortOrder: parseInt(data.sortOrder) || 0,
        isActive: data.isActive,
      };
      if (widget) {
        await apiRequest('PUT', `/api/admin/widgets/${widget.id}`, payload);
      } else {
        await apiRequest('POST', '/api/admin/widgets', payload);
      }
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/widgets'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
      onClose();
    },
    onError: (err: Error) => {
      setFormError(err.message || 'Failed to save widget');
    },
  });

  return (
    <div className="neu-flat p-6 border border-white/5 rounded-xl" data-testid="widget-form">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-lg font-bold text-white">{widget ? 'Edit Widget' : 'Add Widget'}</h3>
        <button onClick={onClose} className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid="close-widget-form">
          <X className="w-5 h-5" />
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Widget Type</label>
          <select value={form.type} onChange={e => {
            const newType = e.target.value;
            setForm({ ...form, type: newType, config: getDefaultConfig(newType) });
          }}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            data-testid="select-widget-type">
            <option value="content_row">Content Row</option>
            <option value="cta_banner">CTA Banner</option>
            <option value="menu_links">Menu Links</option>
          </select>
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Title</label>
          <input type="text" value={form.title} onChange={e => setForm({ ...form, title: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="Widget title" data-testid="input-widget-title" />
        </div>
        <div>
          <label className="text-sm text-muted-foreground mb-1 block">Sort Order</label>
          <input type="number" value={form.sortOrder} onChange={e => setForm({ ...form, sortOrder: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="0" data-testid="input-widget-sort" />
        </div>
        <div className="flex items-center">
          <label className="flex items-center gap-2 cursor-pointer text-sm text-muted-foreground mt-6">
            <input type="checkbox" checked={form.isActive} onChange={e => setForm({ ...form, isActive: e.target.checked })}
              className="w-4 h-4 accent-primary" data-testid="checkbox-widget-active" />
            Active
          </label>
        </div>
        <div className="md:col-span-2">
          <label className="text-sm text-muted-foreground mb-1 block">Config JSON</label>
          <textarea value={form.config} onChange={e => setForm({ ...form, config: e.target.value })} rows={5}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none font-mono text-sm resize-none rounded-lg"
            placeholder='{"filter":"all","limit":10}' data-testid="input-widget-config" />
          <p className="text-xs text-muted-foreground mt-1">
            {form.type === 'content_row' && 'Config: { "filter": "all"|"movie"|"tv"|"featured", "limit": number }'}
            {form.type === 'cta_banner' && 'Config: { "text": string, "buttonText": string, "link": string }'}
            {form.type === 'menu_links' && 'Config: { "links": [{"label": string, "href": string}] }'}
          </p>
        </div>
      </div>

      {formError && (
        <div className="mt-4 p-3 bg-destructive/20 border border-destructive/30 text-destructive text-sm rounded-lg" data-testid="widget-form-error">
          {formError}
        </div>
      )}

      <div className="flex justify-end gap-3 mt-6">
        <button onClick={onClose}
          className="px-4 py-2 bg-secondary text-muted-foreground hover:bg-secondary/80 transition-colors rounded-lg"
          data-testid="button-cancel-widget">Cancel</button>
        <button onClick={() => saveMutation.mutate(form)}
          disabled={!form.title || saveMutation.isPending}
          className="px-4 py-2 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg"
          data-testid="button-save-widget">
          {saveMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
          {widget ? 'Update' : 'Save'} Widget
        </button>
      </div>
    </div>
  );
}

function WidgetsTab() {
  const { data: widgetsList = [], isLoading } = useQuery<Widget[]>({ queryKey: ['/api/admin/widgets'] });
  const [showForm, setShowForm] = useState(false);
  const [editWidget, setEditWidget] = useState<Widget | null>(null);

  const deleteWidgetMutation = useMutation({
    mutationFn: async (id: number) => { await apiRequest('DELETE', `/api/admin/widgets/${id}`); },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/widgets'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
    },
  });

  const toggleWidgetMutation = useMutation({
    mutationFn: async ({ id, isActive }: { id: number; isActive: boolean }) => {
      await apiRequest('PUT', `/api/admin/widgets/${id}`, { isActive });
    },
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['/api/admin/widgets'] }); },
  });

  const widgetTypeLabel = (type: string) => {
    switch (type) {
      case 'content_row': return 'Content Row';
      case 'cta_banner': return 'CTA Banner';
      case 'menu_links': return 'Menu Links';
      default: return type;
    }
  };

  const widgetTypeIcon = (type: string) => {
    switch (type) {
      case 'content_row': return <LayoutGrid className="w-5 h-5" />;
      case 'cta_banner': return <Megaphone className="w-5 h-5" />;
      case 'menu_links': return <Link2 className="w-5 h-5" />;
      default: return <LayoutGrid className="w-5 h-5" />;
    }
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h2 className="text-xl font-bold text-white">Widget Management</h2>
        <button onClick={() => { setEditWidget(null); setShowForm(true); }}
          className="flex items-center gap-2 px-4 py-2.5 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity rounded-lg"
          data-testid="button-add-widget">
          <Plus className="w-4 h-4" />Add Widget
        </button>
      </div>

      {(showForm || editWidget) && (
        <div className="mb-6">
          <WidgetForm widget={editWidget} onClose={() => { setShowForm(false); setEditWidget(null); }} />
        </div>
      )}

      {isLoading ? (
        <div className="flex items-center justify-center py-12">
          <Loader2 className="w-8 h-8 animate-spin text-primary" />
        </div>
      ) : widgetsList.length === 0 ? (
        <div className="neu-flat p-8 border border-white/5 text-center rounded-xl">
          <LayoutGrid className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
          <p className="text-white font-medium mb-1">No widgets yet</p>
          <p className="text-sm text-muted-foreground">Add widgets to customize your homepage layout</p>
        </div>
      ) : (
        <div className="grid gap-2" data-testid="widgets-list">
          {[...widgetsList].sort((a, b) => (a.sortOrder || 0) - (b.sortOrder || 0)).map((widget) => (
            <div key={widget.id} className="neu-flat p-4 flex items-center justify-between gap-3 border border-white/5 rounded-lg" data-testid={`widget-card-${widget.id}`}>
              <div className="flex items-center gap-3 min-w-0">
                <div className={`w-10 h-10 flex items-center justify-center shrink-0 rounded-lg ${
                  widget.type === 'content_row' ? 'bg-primary/20 text-primary' :
                  widget.type === 'cta_banner' ? 'bg-accent/20 text-accent' :
                  'bg-blue-500/20 text-blue-400'
                }`}>
                  {widgetTypeIcon(widget.type)}
                </div>
                <div className="min-w-0">
                  <p className="text-white font-medium truncate">{widget.title}</p>
                  <div className="flex flex-wrap items-center gap-2 mt-0.5">
                    <span className="text-xs text-muted-foreground">{widgetTypeLabel(widget.type)}</span>
                    <span className="text-xs text-muted-foreground">Order: {widget.sortOrder}</span>
                    {widget.isActive ? (
                      <span className="text-xs bg-green-500/20 text-green-400 px-1.5 py-0.5 rounded-sm">ACTIVE</span>
                    ) : (
                      <span className="text-xs bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded-sm">INACTIVE</span>
                    )}
                  </div>
                </div>
              </div>
              <div className="flex items-center gap-1 shrink-0">
                <button onClick={() => toggleWidgetMutation.mutate({ id: widget.id, isActive: !widget.isActive })}
                  className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid={`toggle-widget-${widget.id}`}>
                  {widget.isActive ? <ToggleRight className="w-4 h-4 text-green-400" /> : <ToggleLeft className="w-4 h-4 text-red-400" />}
                </button>
                <button onClick={() => { setEditWidget(widget); setShowForm(false); }}
                  className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid={`edit-widget-${widget.id}`}>
                  <Edit className="w-4 h-4 text-muted-foreground" />
                </button>
                <button onClick={() => deleteWidgetMutation.mutate(widget.id)}
                  className="p-2 hover:bg-destructive/20 transition-colors rounded-lg" data-testid={`delete-widget-${widget.id}`}>
                  <Trash2 className="w-4 h-4 text-destructive" />
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function PagesTab() {
  const { data: pagesList = [], isLoading } = useQuery<Page[]>({ queryKey: ['/api/admin/pages'] });
  const [showForm, setShowForm] = useState(false);
  const [editPage, setEditPage] = useState<Page | null>(null);
  const [form, setForm] = useState({
    title: '',
    slug: '',
    content: '',
    isActive: true,
    showInFooter: true,
    sortOrder: '0',
  });
  const [formError, setFormError] = useState('');

  const generateSlug = (title: string) => {
    return title.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
  };

  const openEditForm = (page: Page) => {
    setEditPage(page);
    setForm({
      title: page.title,
      slug: page.slug,
      content: page.content || '',
      isActive: page.isActive !== false,
      showInFooter: page.showInFooter !== false,
      sortOrder: (page.sortOrder || 0).toString(),
    });
    setShowForm(true);
    setFormError('');
  };

  const openAddForm = () => {
    setEditPage(null);
    setForm({ title: '', slug: '', content: '', isActive: true, showInFooter: true, sortOrder: '0' });
    setShowForm(true);
    setFormError('');
  };

  const saveMutation = useMutation({
    mutationFn: async (data: typeof form) => {
      setFormError('');
      const payload = {
        title: data.title,
        slug: data.slug,
        content: data.content,
        isActive: data.isActive,
        showInFooter: data.showInFooter,
        sortOrder: parseInt(data.sortOrder) || 0,
      };
      if (editPage) {
        await apiRequest('PUT', `/api/admin/pages/${editPage.id}`, payload);
      } else {
        await apiRequest('POST', '/api/admin/pages', payload);
      }
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/pages'] });
      setShowForm(false);
      setEditPage(null);
    },
    onError: (err: Error) => {
      setFormError(err.message || 'Failed to save page');
    },
  });

  const deletePageMutation = useMutation({
    mutationFn: async (id: number) => { await apiRequest('DELETE', `/api/admin/pages/${id}`); },
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['/api/admin/pages'] }); },
  });

  const togglePageMutation = useMutation({
    mutationFn: async ({ id, isActive }: { id: number; isActive: boolean }) => {
      await apiRequest('PUT', `/api/admin/pages/${id}`, { isActive });
    },
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['/api/admin/pages'] }); },
  });

  return (
    <div>
      <div className="flex items-center justify-between mb-6">
        <h2 className="text-xl font-bold text-white">Page Management</h2>
        <button onClick={openAddForm}
          className="flex items-center gap-2 px-4 py-2.5 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity rounded-lg"
          data-testid="button-add-page">
          <Plus className="w-4 h-4" />Add Page
        </button>
      </div>

      {showForm && (
        <div className="neu-flat p-6 border border-white/5 rounded-xl mb-6" data-testid="page-form">
          <div className="flex items-center justify-between mb-6">
            <h3 className="text-lg font-bold text-white">{editPage ? 'Edit Page' : 'Add New Page'}</h3>
            <button onClick={() => { setShowForm(false); setEditPage(null); }} className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid="close-page-form">
              <X className="w-5 h-5" />
            </button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Title</label>
              <input type="text" value={form.title}
                onChange={e => {
                  const title = e.target.value;
                  setForm({ ...form, title, slug: generateSlug(title) });
                }}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                placeholder="Page title" data-testid="input-page-title" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Slug</label>
              <input type="text" value={form.slug}
                onChange={e => setForm({ ...form, slug: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="page-slug" data-testid="input-page-slug" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Sort Order</label>
              <input type="number" value={form.sortOrder}
                onChange={e => setForm({ ...form, sortOrder: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                placeholder="0" data-testid="input-page-sort" />
            </div>
            <div className="flex items-center gap-4">
              <label className="flex items-center gap-2 cursor-pointer text-sm text-muted-foreground mt-6">
                <input type="checkbox" checked={form.isActive} onChange={e => setForm({ ...form, isActive: e.target.checked })}
                  className="w-4 h-4 accent-primary" data-testid="checkbox-page-active" />
                Active
              </label>
              <label className="flex items-center gap-2 cursor-pointer text-sm text-muted-foreground mt-6">
                <input type="checkbox" checked={form.showInFooter} onChange={e => setForm({ ...form, showInFooter: e.target.checked })}
                  className="w-4 h-4 accent-primary" data-testid="checkbox-page-footer" />
                Show in Footer
              </label>
            </div>
            <div className="md:col-span-2">
              <label className="text-sm text-muted-foreground mb-1 block">Content</label>
              <textarea value={form.content} onChange={e => setForm({ ...form, content: e.target.value })} rows={8}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none resize-none rounded-lg"
                placeholder="Page content..." data-testid="input-page-content" />
            </div>
          </div>

          {formError && (
            <div className="mt-4 p-3 bg-destructive/20 border border-destructive/30 text-destructive text-sm rounded-lg" data-testid="page-form-error">
              {formError}
            </div>
          )}

          <div className="flex justify-end gap-3 mt-6">
            <button onClick={() => { setShowForm(false); setEditPage(null); }}
              className="px-4 py-2 bg-secondary text-muted-foreground hover:bg-secondary/80 transition-colors rounded-lg"
              data-testid="button-cancel-page">Cancel</button>
            <button onClick={() => saveMutation.mutate(form)}
              disabled={!form.title || !form.slug || saveMutation.isPending}
              className="px-4 py-2 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg"
              data-testid="button-save-page">
              {saveMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
              {editPage ? 'Update' : 'Save'} Page
            </button>
          </div>
        </div>
      )}

      {isLoading ? (
        <div className="flex items-center justify-center py-12">
          <Loader2 className="w-8 h-8 animate-spin text-primary" />
        </div>
      ) : pagesList.length === 0 ? (
        <div className="neu-flat p-8 border border-white/5 text-center rounded-xl">
          <FileText className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
          <p className="text-white font-medium mb-1">No pages yet</p>
          <p className="text-sm text-muted-foreground">Add pages for your site footer and content</p>
        </div>
      ) : (
        <div className="grid gap-2" data-testid="pages-list">
          {[...pagesList].sort((a, b) => (a.sortOrder || 0) - (b.sortOrder || 0)).map((page) => (
            <div key={page.id} className="neu-flat p-4 flex items-center justify-between gap-3 border border-white/5 rounded-lg" data-testid={`page-card-${page.id}`}>
              <div className="flex items-center gap-3 min-w-0">
                <div className="w-10 h-10 flex items-center justify-center shrink-0 rounded-lg bg-primary/20 text-primary">
                  <FileText className="w-5 h-5" />
                </div>
                <div className="min-w-0">
                  <p className="text-white font-medium truncate">{page.title}</p>
                  <div className="flex flex-wrap items-center gap-2 mt-0.5">
                    <span className="text-xs text-muted-foreground font-mono">/{page.slug}</span>
                    <span className="text-xs text-muted-foreground">Order: {page.sortOrder}</span>
                    {page.showInFooter && <span className="text-xs bg-blue-500/20 text-blue-400 px-1.5 py-0.5 rounded-sm">FOOTER</span>}
                    {page.isActive ? (
                      <span className="text-xs bg-green-500/20 text-green-400 px-1.5 py-0.5 rounded-sm">ACTIVE</span>
                    ) : (
                      <span className="text-xs bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded-sm">INACTIVE</span>
                    )}
                  </div>
                </div>
              </div>
              <div className="flex items-center gap-1 shrink-0">
                <button onClick={() => togglePageMutation.mutate({ id: page.id, isActive: !page.isActive })}
                  className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid={`toggle-page-${page.id}`}>
                  {page.isActive ? <ToggleRight className="w-4 h-4 text-green-400" /> : <ToggleLeft className="w-4 h-4 text-red-400" />}
                </button>
                <button onClick={() => openEditForm(page)}
                  className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid={`edit-page-${page.id}`}>
                  <Edit className="w-4 h-4 text-muted-foreground" />
                </button>
                <button onClick={() => deletePageMutation.mutate(page.id)}
                  className="p-2 hover:bg-destructive/20 transition-colors rounded-lg" data-testid={`delete-page-${page.id}`}>
                  <Trash2 className="w-4 h-4 text-destructive" />
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

function UpiPaymentsTab() {
  const { data: settings = {} } = useQuery<Record<string, string>>({ queryKey: ['/api/admin/settings'] });
  const { data: paymentsList = [], isLoading } = useQuery<UpiPayment[]>({ queryKey: ['/api/admin/upi-payments'] });
  const [upiForm, setUpiForm] = useState({ upi_id: '', upi_qr_url: '' });
  const [upiSaved, setUpiSaved] = useState(false);

  useEffect(() => {
    if (settings && Object.keys(settings).length) {
      setUpiForm({
        upi_id: settings.upi_id || '',
        upi_qr_url: settings.upi_qr_url || '',
      });
    }
  }, [settings]);

  const saveUpiSettingsMutation = useMutation({
    mutationFn: async (data: typeof upiForm) => {
      await apiRequest('POST', '/api/admin/settings', data);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/settings'] });
      setUpiSaved(true);
      setTimeout(() => setUpiSaved(false), 2000);
    },
  });

  const updatePaymentMutation = useMutation({
    mutationFn: async ({ id, status }: { id: number; status: string }) => {
      await apiRequest('PUT', `/api/admin/upi-payments/${id}`, { status });
    },
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['/api/admin/upi-payments'] }); },
  });

  const deletePaymentMutation = useMutation({
    mutationFn: async (id: number) => { await apiRequest('DELETE', `/api/admin/upi-payments/${id}`); },
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['/api/admin/upi-payments'] }); },
  });

  const statusColor = (status: string) => {
    switch (status) {
      case 'approved': return 'bg-green-500/20 text-green-400';
      case 'rejected': return 'bg-red-500/20 text-red-400';
      default: return 'bg-yellow-500/20 text-yellow-400';
    }
  };

  const statusIcon = (status: string) => {
    switch (status) {
      case 'approved': return <CheckCircle className="w-3.5 h-3.5" />;
      case 'rejected': return <XCircle className="w-3.5 h-3.5" />;
      default: return <Clock className="w-3.5 h-3.5" />;
    }
  };

  return (
    <div className="space-y-6">
      <div className="neu-flat p-6 border border-white/5 rounded-xl">
        <div className="flex items-center gap-3 mb-5">
          <IndianRupee className="w-5 h-5 text-primary" />
          <h3 className="text-lg font-bold text-white">UPI Settings</h3>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">UPI ID</label>
            <input type="text" value={upiForm.upi_id}
              onChange={e => setUpiForm({ ...upiForm, upi_id: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
              placeholder="yourname@upi" data-testid="input-upi-id" />
          </div>
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">UPI QR Image URL</label>
            <input type="text" value={upiForm.upi_qr_url}
              onChange={e => setUpiForm({ ...upiForm, upi_qr_url: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
              placeholder="https://example.com/qr.png" data-testid="input-upi-qr-url" />
          </div>
        </div>

        <div className="flex items-center gap-3 mt-5">
          <button onClick={() => saveUpiSettingsMutation.mutate(upiForm)} disabled={saveUpiSettingsMutation.isPending}
            className="px-4 py-2.5 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg"
            data-testid="button-save-upi-settings">
            {saveUpiSettingsMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            Save UPI Settings
          </button>
          {upiSaved && <span className="text-sm text-green-400 flex items-center gap-1"><Check className="w-3.5 h-3.5" />Saved!</span>}
        </div>
      </div>

      <div>
        <h2 className="text-xl font-bold text-white mb-4">Payment Submissions</h2>

        {isLoading ? (
          <div className="flex items-center justify-center py-12">
            <Loader2 className="w-8 h-8 animate-spin text-primary" />
          </div>
        ) : paymentsList.length === 0 ? (
          <div className="neu-flat p-8 border border-white/5 text-center rounded-xl">
            <IndianRupee className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
            <p className="text-white font-medium mb-1">No payments yet</p>
            <p className="text-sm text-muted-foreground">UPI payment submissions will appear here</p>
          </div>
        ) : (
          <div className="grid gap-2" data-testid="upi-payments-list">
            {paymentsList.map((payment) => (
              <div key={payment.id} className="neu-flat p-4 flex items-center justify-between gap-3 border border-white/5 rounded-lg" data-testid={`payment-card-${payment.id}`}>
                <div className="flex items-center gap-3 min-w-0">
                  <div className="w-10 h-10 flex items-center justify-center shrink-0 rounded-lg bg-primary/20 text-primary">
                    <IndianRupee className="w-5 h-5" />
                  </div>
                  <div className="min-w-0">
                    <p className="text-white font-medium truncate">{payment.userName}</p>
                    <div className="flex flex-wrap items-center gap-2 mt-0.5">
                      <span className="text-xs text-muted-foreground font-mono">{payment.transactionId}</span>
                      <span className="text-xs text-primary font-medium">INR {payment.amount}</span>
                      <span className={`text-xs px-1.5 py-0.5 rounded-sm flex items-center gap-1 ${statusColor(payment.status)}`}>
                        {statusIcon(payment.status)}
                        {payment.status.toUpperCase()}
                      </span>
                      {payment.createdAt && (
                        <span className="text-xs text-muted-foreground flex items-center gap-1">
                          <Clock className="w-3 h-3" />
                          {new Date(payment.createdAt).toLocaleDateString()}
                        </span>
                      )}
                    </div>
                  </div>
                </div>
                <div className="flex items-center gap-1 shrink-0">
                  {payment.status !== 'approved' && (
                    <button onClick={() => updatePaymentMutation.mutate({ id: payment.id, status: 'approved' })}
                      className="p-2 hover:bg-green-500/20 transition-colors rounded-lg" data-testid={`approve-payment-${payment.id}`}>
                      <CheckCircle className="w-4 h-4 text-green-400" />
                    </button>
                  )}
                  {payment.status !== 'rejected' && (
                    <button onClick={() => updatePaymentMutation.mutate({ id: payment.id, status: 'rejected' })}
                      className="p-2 hover:bg-red-500/20 transition-colors rounded-lg" data-testid={`reject-payment-${payment.id}`}>
                      <XCircle className="w-4 h-4 text-red-400" />
                    </button>
                  )}
                  <button onClick={() => deletePaymentMutation.mutate(payment.id)}
                    className="p-2 hover:bg-destructive/20 transition-colors rounded-lg" data-testid={`delete-payment-${payment.id}`}>
                    <Trash2 className="w-4 h-4 text-destructive" />
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

function SettingsTab() {
  const { data: settings = {} } = useQuery<Record<string, string>>({ queryKey: ['/api/admin/settings'] });
  const [form, setForm] = useState({
    razorpay_key_id: '',
    razorpay_key_secret: '',
    subscription_amount: '299',
    subscription_name: 'Tmovie Premium',
    subscription_enabled: 'true',
    upi_id: '',
    upi_qr_url: '',
    telegram_link: '',
    search_console_meta: '',
    google_analytics_id: '',
    site_title: '',
    site_description: '',
    favicon_url: '',
    logo_url: '',
    og_image_url: '',
    meta_keywords: '',
    footer_text: '',
    twitter_url: '',
    instagram_url: '',
    youtube_url: '',
    facebook_url: '',
    admin_email: '',
    ad_enabled: 'false',
    ad_type: 'image',
    ad_url: '',
    ad_skip_seconds: '5',
  });
  const [saved, setSaved] = useState(false);
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [pwMsg, setPwMsg] = useState('');

  const changePassword = useChangePassword();

  useEffect(() => {
    if (settings && Object.keys(settings).length) {
      setForm({
        razorpay_key_id: settings.razorpay_key_id || '',
        razorpay_key_secret: settings.razorpay_key_secret || '',
        subscription_amount: settings.subscription_amount || '299',
        subscription_name: settings.subscription_name || 'Tmovie Premium',
        subscription_enabled: settings.subscription_enabled ?? 'true',
        upi_id: settings.upi_id || '',
        upi_qr_url: settings.upi_qr_url || '',
        telegram_link: settings.telegram_link || '',
        search_console_meta: settings.search_console_meta || '',
        google_analytics_id: settings.google_analytics_id || '',
        site_title: settings.site_title || '',
        site_description: settings.site_description || '',
        favicon_url: settings.favicon_url || '',
        logo_url: settings.logo_url || '',
        og_image_url: settings.og_image_url || '',
        meta_keywords: settings.meta_keywords || '',
        footer_text: settings.footer_text || '',
        twitter_url: settings.twitter_url || '',
        instagram_url: settings.instagram_url || '',
        youtube_url: settings.youtube_url || '',
        facebook_url: settings.facebook_url || '',
        admin_email: settings.admin_email || '',
        ad_enabled: settings.ad_enabled || 'false',
        ad_type: settings.ad_type || 'image',
        ad_url: settings.ad_url || '',
        ad_skip_seconds: settings.ad_skip_seconds || '5',
      });
    }
  }, [settings]);

  const saveMutation = useMutation({
    mutationFn: async (data: typeof form) => {
      await apiRequest('POST', '/api/admin/settings', data);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/settings'] });
      setSaved(true);
      setTimeout(() => setSaved(false), 2000);
    },
  });

  const handleChangePassword = () => {
    setPwMsg('');
    changePassword.mutate({ currentPassword, newPassword }, {
      onSuccess: () => {
        setPwMsg('Password changed successfully');
        setCurrentPassword('');
        setNewPassword('');
      },
      onError: (err: Error) => {
        setPwMsg(err.message || 'Failed to change password');
      },
    });
  };

  return (
    <div className="space-y-6">
      <div className="neu-flat p-6 border border-white/5 rounded-xl">
        <div className="flex items-center gap-3 mb-5">
          <CreditCard className="w-5 h-5 text-primary" />
          <h3 className="text-lg font-bold text-white">Razorpay Payment Settings</h3>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">Razorpay Key ID</label>
            <input type="text" value={form.razorpay_key_id}
              onChange={e => setForm({ ...form, razorpay_key_id: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
              placeholder="rzp_live_..." data-testid="input-razorpay-key-id" />
          </div>
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">Razorpay Key Secret</label>
            <input type="password" value={form.razorpay_key_secret}
              onChange={e => setForm({ ...form, razorpay_key_secret: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
              placeholder="••••••••" data-testid="input-razorpay-key-secret" />
          </div>
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">Subscription Name</label>
            <input type="text" value={form.subscription_name}
              onChange={e => setForm({ ...form, subscription_name: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
              placeholder="Tmovie Premium" data-testid="input-subscription-name" />
          </div>
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">Amount (INR)</label>
            <input type="number" value={form.subscription_amount}
              onChange={e => setForm({ ...form, subscription_amount: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
              placeholder="299" data-testid="input-subscription-amount" />
          </div>
        </div>

        <div className="flex items-center justify-between mt-5 p-4 bg-secondary/30 border border-white/5 rounded-lg">
          <div>
            <p className="text-sm font-medium text-white">Subscription Feature</p>
            <p className="text-xs text-muted-foreground">Show subscription card on Profile page and CTA banners</p>
          </div>
          <button
            onClick={() => setForm({ ...form, subscription_enabled: form.subscription_enabled === 'true' ? 'false' : 'true' })}
            className="flex items-center gap-2"
            data-testid="button-toggle-subscription"
          >
            {form.subscription_enabled === 'true' ? (
              <ToggleRight className="w-10 h-10 text-primary" />
            ) : (
              <ToggleLeft className="w-10 h-10 text-muted-foreground" />
            )}
            <span className={`text-sm font-medium ${form.subscription_enabled === 'true' ? 'text-primary' : 'text-muted-foreground'}`}>
              {form.subscription_enabled === 'true' ? 'ON' : 'OFF'}
            </span>
          </button>
        </div>

        <div className="mt-5 pt-5 border-t border-white/10">
          <div className="flex items-center gap-3 mb-4">
            <IndianRupee className="w-5 h-5 text-primary" />
            <h3 className="text-lg font-bold text-white">UPI Payment Settings</h3>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">UPI ID</label>
              <input type="text" value={form.upi_id}
                onChange={e => setForm({ ...form, upi_id: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="yourname@upi" data-testid="input-settings-upi-id" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">UPI QR Image URL</label>
              <input type="text" value={form.upi_qr_url}
                onChange={e => setForm({ ...form, upi_qr_url: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="https://example.com/qr.png" data-testid="input-settings-upi-qr-url" />
            </div>
          </div>
        </div>

        <div className="mt-5 pt-5 border-t border-white/10">
          <div className="flex items-center gap-3 mb-4">
            <Globe className="w-5 h-5 text-primary" />
            <h3 className="text-lg font-bold text-white">Site & SEO Settings</h3>
          </div>

          <p className="text-xs font-bold text-muted-foreground uppercase tracking-widest mb-3">Basic Identity</p>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Site Name</label>
              <input type="text" value={form.site_title}
                onChange={e => setForm({ ...form, site_title: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                placeholder="TMovie" data-testid="input-site-title" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Site Tagline / Description</label>
              <input type="text" value={form.site_description}
                onChange={e => setForm({ ...form, site_description: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
                placeholder="Stream movies and TV shows online" data-testid="input-site-description" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Favicon URL</label>
              <p className="text-xs text-muted-foreground/60 mb-1">Icon shown in browser tab. Use a direct .ico or .png link.</p>
              <div className="flex gap-2 items-center">
                {form.favicon_url && <img src={form.favicon_url} alt="favicon" className="w-6 h-6 rounded object-cover shrink-0 border border-white/10" onError={e => (e.currentTarget.style.display='none')} />}
                <input type="url" value={form.favicon_url}
                  onChange={e => setForm({ ...form, favicon_url: e.target.value })}
                  className="flex-1 bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                  placeholder="https://example.com/favicon.ico" data-testid="input-favicon-url" />
              </div>
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Site Logo URL</label>
              <p className="text-xs text-muted-foreground/60 mb-1">Replaces the header icon. Use a transparent PNG/SVG.</p>
              <div className="flex gap-2 items-center">
                {form.logo_url && <img src={form.logo_url} alt="logo" className="h-6 max-w-[60px] object-contain shrink-0 border border-white/10 rounded" onError={e => (e.currentTarget.style.display='none')} />}
                <input type="url" value={form.logo_url}
                  onChange={e => setForm({ ...form, logo_url: e.target.value })}
                  className="flex-1 bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                  placeholder="https://example.com/logo.png" data-testid="input-logo-url" />
              </div>
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Footer Copyright Text</label>
              <input type="text" value={form.footer_text}
                onChange={e => setForm({ ...form, footer_text: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg text-sm"
                placeholder="© 2026 TMovie. All rights reserved." data-testid="input-footer-text" />
            </div>
          </div>

          <p className="text-xs font-bold text-muted-foreground uppercase tracking-widest mb-3">SEO & Open Graph</p>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div className="md:col-span-2">
              <label className="text-sm text-muted-foreground mb-1 block">Meta Keywords</label>
              <input type="text" value={form.meta_keywords}
                onChange={e => setForm({ ...form, meta_keywords: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg text-sm"
                placeholder="movies, tv shows, streaming, anime, watch free" data-testid="input-meta-keywords" />
            </div>
            <div className="md:col-span-2">
              <label className="text-sm text-muted-foreground mb-1 block">Social Share Image URL (OG Image)</label>
              <p className="text-xs text-muted-foreground/60 mb-1">Shown when your site is shared on WhatsApp, Twitter, etc. Recommended: 1200×630px.</p>
              <div className="flex gap-2 items-start">
                {form.og_image_url && <img src={form.og_image_url} alt="og" className="w-16 h-10 rounded object-cover shrink-0 border border-white/10" onError={e => (e.currentTarget.style.display='none')} />}
                <input type="url" value={form.og_image_url}
                  onChange={e => setForm({ ...form, og_image_url: e.target.value })}
                  className="flex-1 bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                  placeholder="https://example.com/og-image.jpg" data-testid="input-og-image-url" />
              </div>
            </div>
          </div>

          <p className="text-xs font-bold text-muted-foreground uppercase tracking-widest mb-3">Analytics & Verification</p>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Google Analytics ID</label>
              <input type="text" value={form.google_analytics_id}
                onChange={e => setForm({ ...form, google_analytics_id: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="G-XXXXXXXXXX" data-testid="input-google-analytics-id" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 block">Google Search Console Verification</label>
              <p className="text-xs text-muted-foreground/60 mb-1">Paste only the <code className="text-primary/80">content</code> value from the meta tag</p>
              <input type="text" value={form.search_console_meta}
                onChange={e => setForm({ ...form, search_console_meta: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="abc123xyz_verification_code" data-testid="input-search-console-meta" />
            </div>
          </div>

          <p className="text-xs font-bold text-muted-foreground uppercase tracking-widest mb-3">Social & Community</p>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="text-sm text-muted-foreground mb-1 flex items-center gap-1.5 block">
                <span className="text-[#229ED9]">✈</span> Telegram Channel
              </label>
              <input type="url" value={form.telegram_link}
                onChange={e => setForm({ ...form, telegram_link: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="https://t.me/yourchannel" data-testid="input-telegram-link" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 flex items-center gap-1.5 block">
                <span className="text-[#1DA1F2]">𝕏</span> Twitter / X
              </label>
              <input type="url" value={form.twitter_url}
                onChange={e => setForm({ ...form, twitter_url: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="https://twitter.com/yourhandle" data-testid="input-twitter-url" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 flex items-center gap-1.5 block">
                <span className="text-[#E1306C]">◎</span> Instagram
              </label>
              <input type="url" value={form.instagram_url}
                onChange={e => setForm({ ...form, instagram_url: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="https://instagram.com/yourpage" data-testid="input-instagram-url" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 flex items-center gap-1.5 block">
                <span className="text-[#FF0000]">▶</span> YouTube
              </label>
              <input type="url" value={form.youtube_url}
                onChange={e => setForm({ ...form, youtube_url: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="https://youtube.com/@yourchannel" data-testid="input-youtube-url" />
            </div>
            <div>
              <label className="text-sm text-muted-foreground mb-1 flex items-center gap-1.5 block">
                <span className="text-[#1877F2]">f</span> Facebook
              </label>
              <input type="url" value={form.facebook_url}
                onChange={e => setForm({ ...form, facebook_url: e.target.value })}
                className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
                placeholder="https://facebook.com/yourpage" data-testid="input-facebook-url" />
            </div>
          </div>
        </div>

        <div className="flex items-center gap-3 mt-5">
          <button onClick={() => saveMutation.mutate(form)} disabled={saveMutation.isPending}
            className="px-4 py-2.5 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg"
            data-testid="button-save-settings">
            {saveMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
            Save Settings
          </button>
          {saved && <span className="text-sm text-green-400 flex items-center gap-1"><Check className="w-3.5 h-3.5" />Saved!</span>}
        </div>
      </div>

      <div className="neu-flat p-6 border border-white/5 rounded-xl">
        <div className="flex items-center gap-3 mb-5">
          <Mail className="w-5 h-5 text-primary" />
          <h3 className="text-lg font-bold text-white">Admin Email</h3>
        </div>
        <p className="text-sm text-muted-foreground mb-4">Set your admin email for password recovery via "Forgot Password" on login page.</p>
        <div className="max-w-md">
          <label className="text-sm text-muted-foreground mb-1 block">Admin Email</label>
          <input type="email" value={form.admin_email}
            onChange={e => setForm({ ...form, admin_email: e.target.value })}
            className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
            placeholder="admin@yoursite.com" data-testid="input-admin-email" />
        </div>
        <div className="flex items-center gap-3 mt-4">
          <button onClick={() => saveMutation.mutate(form)} disabled={saveMutation.isPending}
            className="px-4 py-2 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg text-sm">
            <Save className="w-4 h-4" /> Save Email
          </button>
        </div>
      </div>

      <div className="neu-flat p-6 border border-white/5 rounded-xl">
        <div className="flex items-center gap-3 mb-5">
          <Tv className="w-5 h-5 text-accent" />
          <h3 className="text-lg font-bold text-white">Video Ad Settings</h3>
        </div>
        <p className="text-sm text-muted-foreground mb-4">Configure pre-roll ads shown before video playback. Supports image or VAST URL type.</p>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">Enable Ads</label>
            <select value={form.ad_enabled} onChange={e => setForm({ ...form, ad_enabled: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
              data-testid="select-ad-enabled">
              <option value="true">Yes - Show Ads</option>
              <option value="false">No - Disabled</option>
            </select>
          </div>
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">Ad Type</label>
            <select value={form.ad_type} onChange={e => setForm({ ...form, ad_type: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
              data-testid="select-ad-type">
              <option value="image">Image Ad</option>
              <option value="vast">VAST URL</option>
            </select>
          </div>
          <div className="md:col-span-2">
            <label className="text-sm text-muted-foreground mb-1 block">
              {form.ad_type === 'vast' ? 'VAST Tag URL' : 'Ad Image URL (clickable banner)'}
            </label>
            <input type="url" value={form.ad_url}
              onChange={e => setForm({ ...form, ad_url: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg font-mono text-sm"
              placeholder={form.ad_type === 'vast' ? 'https://example.com/vast.xml' : 'https://example.com/ad.jpg'}
              data-testid="input-ad-url" />
          </div>
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">Skip After (seconds)</label>
            <input type="number" min="0" max="60" value={form.ad_skip_seconds}
              onChange={e => setForm({ ...form, ad_skip_seconds: e.target.value })}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
              placeholder="5" data-testid="input-ad-skip-seconds" />
            <p className="text-[11px] text-muted-foreground mt-1">Set 0 to allow instant skip</p>
          </div>
        </div>
        <div className="flex items-center gap-3 mt-4">
          <button onClick={() => saveMutation.mutate(form)} disabled={saveMutation.isPending}
            className="px-4 py-2 bg-accent text-white font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg text-sm">
            <Save className="w-4 h-4" /> Save Ad Settings
          </button>
        </div>
      </div>

      <div className="neu-flat p-6 border border-white/5 rounded-xl">
        <div className="flex items-center gap-3 mb-5">
          <Key className="w-5 h-5 text-accent" />
          <h3 className="text-lg font-bold text-white">Change Admin Password</h3>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">Current Password</label>
            <input type="password" value={currentPassword} onChange={e => setCurrentPassword(e.target.value)}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
              placeholder="••••••••" data-testid="input-current-password" />
          </div>
          <div>
            <label className="text-sm text-muted-foreground mb-1 block">New Password</label>
            <input type="password" value={newPassword} onChange={e => setNewPassword(e.target.value)}
              className="w-full bg-secondary/50 px-3 py-2.5 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg"
              placeholder="••••••••" data-testid="input-new-password" />
          </div>
        </div>

        <div className="flex items-center gap-3 mt-5">
          <button onClick={handleChangePassword} disabled={!currentPassword || !newPassword || changePassword.isPending}
            className="px-4 py-2.5 bg-accent text-white font-medium hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2 rounded-lg"
            data-testid="button-change-password">
            {changePassword.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Key className="w-4 h-4" />}
            Change Password
          </button>
          {pwMsg && <span className={`text-sm ${pwMsg.includes('success') ? 'text-green-400' : 'text-destructive'}`}>{pwMsg}</span>}
        </div>
      </div>
    </div>
  );
}

type MovieSort = 'newest' | 'oldest' | 'title_az' | 'title_za' | 'rating_high' | 'rating_low' | 'popularity';
type MovieFilter = 'all' | 'movie' | 'tv' | 'featured' | 'hidden';
type AdminTab = 'movies' | 'addpost' | 'widgets' | 'servers' | 'pages' | 'upi' | 'settings';

export default function Admin() {
  const { user, isAdmin, isLoading: authLoading } = useAuth();
  const logout = useLogout();

  const [activeTab, setActiveTab] = useState<AdminTab>('movies');
  const [showForm, setShowForm] = useState(false);
  const [editServer, setEditServer] = useState<CustomServer | null>(null);
  const [showImport, setShowImport] = useState(false);
  const [editMovie, setEditMovie] = useState<Movie | null>(null);
  const [selectedMovies, setSelectedMovies] = useState<Set<number>>(new Set());
  const [movieSearch, setMovieSearch] = useState('');
  const [movieFilter, setMovieFilter] = useState<MovieFilter>('all');
  const [movieSort, setMovieSort] = useState<MovieSort>('newest');

  const { data: stats } = useQuery({ queryKey: ['/api/admin/stats'], enabled: isAdmin });
  const { data: allServers = [] } = useQuery<CustomServer[]>({ queryKey: ['/api/admin/servers'], enabled: isAdmin });
  const { data: moviesList = [], isLoading: moviesLoading } = useQuery<Movie[]>({ queryKey: ['/api/admin/movies'], enabled: isAdmin });

  const toggleMutation = useMutation({
    mutationFn: async ({ id, isActive }: { id: number; isActive: boolean }) => {
      await apiRequest('PUT', `/api/admin/servers/${id}`, { isActive });
    },
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['/api/admin/servers'] }); },
  });

  const deleteServerMutation = useMutation({
    mutationFn: async (id: number) => { await apiRequest('DELETE', `/api/admin/servers/${id}`); },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/servers'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
    },
  });

  const deleteMovieMutation = useMutation({
    mutationFn: async (id: number) => { await apiRequest('DELETE', `/api/admin/movies/${id}`); return id; },
    onSuccess: (deletedId: number) => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/movies'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
      setSelectedMovies(prev => { const n = new Set(prev); n.delete(deletedId); return n; });
    },
  });

  const bulkDeleteMutation = useMutation({
    mutationFn: async (ids: number[]) => {
      for (const id of ids) {
        await apiRequest('DELETE', `/api/admin/movies/${id}`);
      }
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/movies'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
      setSelectedMovies(new Set());
    },
  });

  const bulkImportMutation = useMutation({
    mutationFn: async (type: string) => {
      const res = await apiRequest('POST', '/api/admin/movies/import-bulk', { type });
      return res.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/admin/movies'] });
      queryClient.invalidateQueries({ queryKey: ['/api/admin/stats'] });
    },
  });

  if (authLoading) {
    return (
      <Shell>
        <div className="h-screen flex items-center justify-center">
          <Loader2 className="w-12 h-12 text-primary animate-spin" />
        </div>
      </Shell>
    );
  }

  if (!isAdmin) {
    return <LoginForm />;
  }

  const toggleMovieSelect = (id: number) => {
    setSelectedMovies(prev => {
      const n = new Set(prev);
      if (n.has(id)) n.delete(id); else n.add(id);
      return n;
    });
  };

  let filteredMovies = moviesList.filter(m => {
    if (movieSearch && !m.title.toLowerCase().includes(movieSearch.toLowerCase())) return false;
    if (movieFilter === 'movie') return m.mediaType === 'movie';
    if (movieFilter === 'tv') return m.mediaType === 'tv';
    if (movieFilter === 'featured') return m.isFeatured;
    if (movieFilter === 'hidden') return !m.isActive;
    return true;
  });

  filteredMovies = [...filteredMovies].sort((a, b) => {
    switch (movieSort) {
      case 'newest': return new Date(b.createdAt || 0).getTime() - new Date(a.createdAt || 0).getTime();
      case 'oldest': return new Date(a.createdAt || 0).getTime() - new Date(b.createdAt || 0).getTime();
      case 'title_az': return a.title.localeCompare(b.title);
      case 'title_za': return b.title.localeCompare(a.title);
      case 'rating_high': return (b.voteAverage || 0) - (a.voteAverage || 0);
      case 'rating_low': return (a.voteAverage || 0) - (b.voteAverage || 0);
      case 'popularity': return (b.popularity || 0) - (a.popularity || 0);
      default: return 0;
    }
  });

  const selectAll = () => {
    if (selectedMovies.size === filteredMovies.length) {
      setSelectedMovies(new Set());
    } else {
      setSelectedMovies(new Set(filteredMovies.map(m => m.id)));
    }
  };

  const filterOptions: { value: MovieFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'movie', label: 'Movies' },
    { value: 'tv', label: 'TV Shows' },
    { value: 'featured', label: 'Featured' },
    { value: 'hidden', label: 'Hidden' },
  ];

  const sortOptions: { value: MovieSort; label: string }[] = [
    { value: 'newest', label: 'Newest' },
    { value: 'oldest', label: 'Oldest' },
    { value: 'title_az', label: 'A-Z' },
    { value: 'title_za', label: 'Z-A' },
    { value: 'rating_high', label: 'Top Rated' },
    { value: 'rating_low', label: 'Low Rated' },
    { value: 'popularity', label: 'Popular' },
  ];

  const tabs: { id: AdminTab; label: string; icon: any }[] = [
    { id: 'movies', label: 'Movies & TV', icon: Film },
    { id: 'addpost', label: 'Add Post', icon: PlusCircle },
    { id: 'widgets', label: 'Widgets', icon: LayoutGrid },
    { id: 'servers', label: 'Servers', icon: Server },
    { id: 'pages', label: 'Pages', icon: FileText },
    { id: 'upi', label: 'UPI Payments', icon: IndianRupee },
    { id: 'settings', label: 'Settings', icon: Settings },
  ];

  return (
    <Shell>
      <div className="pt-24 px-4 md:px-8 lg:px-16 pb-20 max-w-6xl mx-auto">
        <div className="flex flex-wrap items-center justify-between gap-4 mb-8">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 bg-primary flex items-center justify-center rounded-lg">
              <Shield className="w-6 h-6 text-black" />
            </div>
            <div>
              <h1 className="text-2xl md:text-3xl font-bold text-white" data-testid="text-admin-title">Admin Dashboard</h1>
              <p className="text-muted-foreground text-sm">Logged in as <span className="text-primary font-medium">{user?.username}</span></p>
            </div>
          </div>
          <button onClick={() => logout.mutate()} className="flex items-center gap-2 px-4 py-2 neu-flat text-muted-foreground hover:text-white border border-white/5 transition-colors rounded-lg" data-testid="button-logout">
            <LogOut className="w-4 h-4" /> Logout
          </button>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
          <StatCard title="Movies" value={(stats as any)?.moviesCount || 0} icon={Film} color="bg-purple-500/30" />
          <StatCard title="Streaming" value={(stats as any)?.streamingServers || 0} icon={Monitor} color="bg-primary/30" />
          <StatCard title="Download" value={(stats as any)?.downloadServers || 0} icon={Download} color="bg-accent/30" />
          <StatCard title="Servers" value={(stats as any)?.serversCount || 0} icon={Server} color="bg-blue-500/30" />
          <StatCard title="Widgets" value={(stats as any)?.widgetsCount || 0} icon={LayoutGrid} color="bg-orange-500/30" />
          <StatCard title="Watchlist" value={(stats as any)?.watchlistCount || 0} icon={Bookmark} color="bg-green-500/30" />
        </div>

        <div className="flex gap-1 mb-6 border-b border-white/10 overflow-x-auto">
          {tabs.map(tab => (
            <button key={tab.id} onClick={() => setActiveTab(tab.id)}
              className={`px-4 md:px-6 py-3 text-sm font-medium transition-colors whitespace-nowrap ${activeTab === tab.id ? 'text-primary border-b-2 border-primary' : 'text-muted-foreground hover:text-white'}`}
              data-testid={`tab-${tab.id}`}>
              <tab.icon className="w-4 h-4 inline mr-2" />{tab.label}
            </button>
          ))}
        </div>

        {activeTab === 'movies' && (
          <div>
            <div className="flex flex-wrap items-center gap-2 mb-4">
              <button onClick={() => setShowImport(true)}
                className="flex items-center gap-2 px-3 py-2 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity text-sm rounded-lg"
                data-testid="button-import-search">
                <Search className="w-4 h-4" />Import
              </button>
              <button onClick={() => bulkImportMutation.mutate('movie')} disabled={bulkImportMutation.isPending}
                className="flex items-center gap-2 px-3 py-2 bg-secondary text-white font-medium hover:bg-secondary/80 transition-colors disabled:opacity-50 text-sm rounded-lg"
                data-testid="button-import-trending-movies">
                {bulkImportMutation.isPending ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <Import className="w-3.5 h-3.5" />}
                + Movies
              </button>
              <button onClick={() => bulkImportMutation.mutate('tv')} disabled={bulkImportMutation.isPending}
                className="flex items-center gap-2 px-3 py-2 bg-secondary text-white font-medium hover:bg-secondary/80 transition-colors disabled:opacity-50 text-sm rounded-lg"
                data-testid="button-import-trending-tv">
                {bulkImportMutation.isPending ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <Import className="w-3.5 h-3.5" />}
                + TV
              </button>
              {selectedMovies.size > 0 && (
                <button onClick={() => bulkDeleteMutation.mutate(Array.from(selectedMovies))}
                  disabled={bulkDeleteMutation.isPending}
                  className="flex items-center gap-2 px-3 py-2 bg-destructive/20 text-destructive font-medium hover:bg-destructive/30 transition-colors disabled:opacity-50 text-sm rounded-lg ml-auto"
                  data-testid="button-bulk-delete">
                  {bulkDeleteMutation.isPending ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <Trash2 className="w-3.5 h-3.5" />}
                  Delete ({selectedMovies.size})
                </button>
              )}
            </div>

            <div className="flex flex-wrap items-center gap-2 mb-4">
              <div className="flex-1 min-w-[200px]">
                <div className="flex items-center bg-secondary/30 border border-white/5 px-3 py-2 rounded-lg">
                  <Search className="w-4 h-4 text-muted-foreground" />
                  <input type="text" value={movieSearch} onChange={e => setMovieSearch(e.target.value)}
                    className="flex-1 bg-transparent border-none outline-none text-white px-2 text-sm placeholder:text-muted-foreground"
                    placeholder="Filter movies..." data-testid="input-movie-filter" />
                </div>
              </div>
              <select value={movieFilter} onChange={e => setMovieFilter(e.target.value as MovieFilter)}
                className="bg-secondary/50 border border-white/10 px-3 py-2 text-white text-sm outline-none rounded-lg"
                data-testid="select-movie-filter">
                {filterOptions.map(f => (
                  <option key={f.value} value={f.value}>{f.label}</option>
                ))}
              </select>
              <select value={movieSort} onChange={e => setMovieSort(e.target.value as MovieSort)}
                className="bg-secondary/50 border border-white/10 px-3 py-2 text-white text-sm outline-none rounded-lg"
                data-testid="select-movie-sort">
                {sortOptions.map(s => (
                  <option key={s.value} value={s.value}>{s.label}</option>
                ))}
              </select>
            </div>

            {showImport && (
              <div className="mb-6">
                <MovieImportSearch onClose={() => setShowImport(false)} />
              </div>
            )}

            {editMovie && (
              <MovieEditorPanel movie={editMovie} onClose={() => setEditMovie(null)} />
            )}

            {moviesLoading ? (
              <div className="flex items-center justify-center py-12">
                <Loader2 className="w-8 h-8 animate-spin text-primary" />
              </div>
            ) : filteredMovies.length === 0 ? (
              <div className="neu-flat p-8 border border-white/5 text-center rounded-xl">
                <Film className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
                <p className="text-white font-medium mb-1">No movies found</p>
                <p className="text-sm text-muted-foreground">Use the import buttons above to add movies from TMDB</p>
              </div>
            ) : (
              <>
                <div className="flex items-center gap-3 mb-3">
                  <label className="flex items-center gap-2 cursor-pointer text-sm text-muted-foreground">
                    <input type="checkbox" checked={selectedMovies.size === filteredMovies.length && filteredMovies.length > 0}
                      onChange={selectAll} className="w-4 h-4 accent-primary" data-testid="checkbox-select-all" />
                    Select All ({filteredMovies.length})
                  </label>
                </div>
                <div className="grid gap-2" data-testid="movies-list">
                  {filteredMovies.map((movie) => (
                    <div key={movie.id} className={`neu-flat p-3 flex items-center gap-3 border rounded-lg transition-colors ${
                      selectedMovies.has(movie.id) ? 'border-primary/30 bg-primary/5' : 'border-white/5'
                    }`} data-testid={`movie-card-${movie.id}`}>
                      <input type="checkbox" checked={selectedMovies.has(movie.id)}
                        onChange={() => toggleMovieSelect(movie.id)}
                        className="w-4 h-4 accent-primary shrink-0" data-testid={`checkbox-movie-${movie.id}`} />
                      {movie.posterPath ? (
                        <img src={`${IMG_BASE}w92${movie.posterPath}`} alt={movie.title} className="w-12 h-16 object-cover shrink-0 rounded-sm" />
                      ) : (
                        <div className="w-12 h-16 bg-secondary flex items-center justify-center shrink-0 rounded-sm">
                          <Film className="w-5 h-5 text-muted-foreground" />
                        </div>
                      )}
                      <div className="flex-1 min-w-0">
                        <p className="text-white font-medium truncate">{movie.title}</p>
                        <div className="flex flex-wrap items-center gap-2 text-xs text-muted-foreground mt-0.5">
                          <span className="uppercase text-primary">{movie.mediaType}</span>
                          {movie.releaseDate && <span>{movie.releaseDate.slice(0, 4)}</span>}
                          {(movie.voteAverage ?? 0) > 0 && (
                            <span className="flex items-center gap-0.5">
                              <Star className="w-3 h-3 text-yellow-400" />{(movie.voteAverage ?? 0).toFixed(1)}
                            </span>
                          )}
                          {movie.isFeatured && <span className="bg-yellow-500/20 text-yellow-400 px-1.5 py-0.5 rounded-sm">FEATURED</span>}
                          {movie.isActive ? (
                            <span className="bg-green-500/20 text-green-400 px-1.5 py-0.5 rounded-sm">ACTIVE</span>
                          ) : (
                            <span className="bg-red-500/20 text-red-400 px-1.5 py-0.5 rounded-sm">HIDDEN</span>
                          )}
                        </div>
                      </div>
                      <div className="flex items-center gap-1 shrink-0">
                        <button onClick={() => setEditMovie(movie)} className="p-2 hover:bg-secondary transition-colors rounded-lg" data-testid={`edit-movie-${movie.id}`}>
                          <Edit className="w-4 h-4 text-muted-foreground" />
                        </button>
                        <button onClick={() => deleteMovieMutation.mutate(movie.id)} className="p-2 hover:bg-destructive/20 transition-colors rounded-lg" data-testid={`delete-movie-${movie.id}`}>
                          <Trash2 className="w-4 h-4 text-destructive" />
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </>
            )}
          </div>
        )}

        {activeTab === 'addpost' && (
          <div className="space-y-6">
            <ManualAddForm />
            <MovieImportSearch onClose={() => {}} />
          </div>
        )}

        {activeTab === 'widgets' && <WidgetsTab />}

        {activeTab === 'servers' && (
          <div>
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-xl font-bold text-white">Server Management</h2>
              <button onClick={() => { setEditServer(null); setShowForm(true); }}
                className="flex items-center gap-2 px-4 py-2.5 bg-primary text-primary-foreground font-medium hover:opacity-90 transition-opacity rounded-lg"
                data-testid="button-add-server">
                <Plus className="w-4 h-4" />Add Server
              </button>
            </div>

            {showForm && (
              <div className="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20 overflow-y-auto" style={{backgroundColor: 'rgba(0,0,0,0.85)'}} onClick={e => { if (e.target === e.currentTarget) { setShowForm(false); setEditServer(null); } }}>
                <div className="w-full max-w-2xl" data-testid="server-edit-modal">
                  <AddServerForm onClose={() => { setShowForm(false); setEditServer(null); }} editServer={editServer} />
                </div>
              </div>
            )}

            {allServers.length === 0 ? (
              <div className="neu-flat p-8 border border-white/5 text-center rounded-xl">
                <Server className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
                <p className="text-white font-medium mb-1">No servers found</p>
                <p className="text-sm text-muted-foreground">Add a server to get started</p>
              </div>
            ) : (
              <div className="grid gap-2" data-testid="servers-list">
                {allServers.map((server) => (
                  <ServerCard key={server.id} server={server}
                    onToggle={() => toggleMutation.mutate({ id: server.id, isActive: !server.isActive })}
                    onEdit={() => { setEditServer(server); setShowForm(true); }}
                    onDelete={() => deleteServerMutation.mutate(server.id)} />
                ))}
              </div>
            )}
          </div>
        )}

        {activeTab === 'pages' && <PagesTab />}

        {activeTab === 'upi' && <UpiPaymentsTab />}

        {activeTab === 'settings' && <SettingsTab />}
      </div>
    </Shell>
  );
}
