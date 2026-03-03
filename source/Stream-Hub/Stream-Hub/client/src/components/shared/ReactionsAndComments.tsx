import { useState } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { queryClient, apiRequest } from "@/lib/queryClient";
import { MessageCircle, Send, Loader2, Trash2 } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import type { Comment, Reaction } from "@shared/schema";
import { useAuth } from "@/hooks/use-auth";

const EMOJIS = ['❤️', '😂', '😮', '😢', '😡', '👍'];
const REACTION_KEY = (tmdbId: number, mediaType: string) => `tmovie_rx1_${mediaType}_${tmdbId}`;

function getMyReaction(tmdbId: number, mediaType: string): string | null {
  try {
    return localStorage.getItem(REACTION_KEY(tmdbId, mediaType)) || null;
  } catch { return null; }
}

function saveMyReaction(tmdbId: number, mediaType: string, emoji: string | null) {
  if (emoji) {
    localStorage.setItem(REACTION_KEY(tmdbId, mediaType), emoji);
  } else {
    localStorage.removeItem(REACTION_KEY(tmdbId, mediaType));
  }
}

function timeAgo(dateStr: string) {
  const diff = Date.now() - new Date(dateStr).getTime();
  const mins = Math.floor(diff / 60000);
  const hrs = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);
  if (mins < 1) return 'just now';
  if (mins < 60) return `${mins}m ago`;
  if (hrs < 24) return `${hrs}h ago`;
  return `${days}d ago`;
}

interface Props {
  tmdbId: number;
  mediaType: string;
}

export function ReactionsAndComments({ tmdbId, mediaType }: Props) {
  const { isAdmin } = useAuth();
  const [name, setName] = useState(() => localStorage.getItem('tmovie_comment_name') || '');
  const [text, setText] = useState('');
  const [myReaction, setMyReaction] = useState<string | null>(() => getMyReaction(tmdbId, mediaType));

  const { data: reactions = [] } = useQuery<Reaction[]>({
    queryKey: ['/api/reactions', mediaType, tmdbId],
    queryFn: async () => {
      const res = await fetch(`/api/reactions/${mediaType}/${tmdbId}`);
      return res.json();
    },
  });

  const { data: commentsList = [], isLoading: commentsLoading } = useQuery<Comment[]>({
    queryKey: ['/api/comments', mediaType, tmdbId],
    queryFn: async () => {
      const res = await fetch(`/api/comments/${mediaType}/${tmdbId}`);
      return res.json();
    },
  });

  const reactMutation = useMutation({
    mutationFn: async (emoji: string) => {
      await apiRequest('POST', `/api/reactions/${mediaType}/${tmdbId}`, { emoji });
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/reactions', mediaType, tmdbId] });
    },
  });

  const commentMutation = useMutation({
    mutationFn: async () => {
      await apiRequest('POST', `/api/comments/${mediaType}/${tmdbId}`, { userName: name, content: text });
    },
    onSuccess: () => {
      setText('');
      localStorage.setItem('tmovie_comment_name', name);
      queryClient.invalidateQueries({ queryKey: ['/api/comments', mediaType, tmdbId] });
    },
  });

  const deleteMutation = useMutation({
    mutationFn: async (id: number) => {
      await apiRequest('DELETE', `/api/comments/${id}`, {});
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['/api/comments', mediaType, tmdbId] });
    },
  });

  const handleReact = (emoji: string) => {
    if (myReaction === emoji) {
      setMyReaction(null);
      saveMyReaction(tmdbId, mediaType, null);
    } else if (!myReaction) {
      setMyReaction(emoji);
      saveMyReaction(tmdbId, mediaType, emoji);
      reactMutation.mutate(emoji);
    }
  };

  const handleComment = () => {
    if (!name.trim() || !text.trim()) return;
    commentMutation.mutate();
  };

  const reactionMap: Record<string, number> = {};
  reactions.forEach(r => { reactionMap[r.emoji] = r.count; });

  return (
    <div className="mt-10 space-y-6">
      <div className="neu-flat p-5 border border-white/5 rounded-xl">
        <h3 className="text-sm font-bold text-white uppercase tracking-widest mb-1">Reactions</h3>
        {myReaction && (
          <p className="text-[10px] text-muted-foreground mb-3">
            You reacted with {myReaction} · <button onClick={() => handleReact(myReaction)} className="text-primary hover:underline">Remove</button>
          </p>
        )}
        {!myReaction && (
          <p className="text-[10px] text-muted-foreground mb-3">Pick one reaction</p>
        )}
        <div className="flex flex-wrap gap-2">
          {EMOJIS.map(emoji => {
            const cnt = reactionMap[emoji] || 0;
            const isMine = myReaction === emoji;
            const isDisabled = !!myReaction && !isMine;
            return (
              <button
                key={emoji}
                onClick={() => handleReact(emoji)}
                disabled={isDisabled}
                className={`flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium transition-all border ${
                  isMine
                    ? 'bg-primary/20 border-primary text-white'
                    : isDisabled
                    ? 'bg-white/3 border-white/5 text-white/20 cursor-not-allowed'
                    : 'bg-white/5 border-white/10 text-white/70 hover:bg-white/10 hover:border-white/20'
                }`}
                data-testid={`reaction-${emoji}`}
              >
                <span className="text-base leading-none">{emoji}</span>
                {cnt > 0 && <span className={`text-xs ${isMine ? 'text-primary' : 'text-muted-foreground'}`}>{cnt}</span>}
              </button>
            );
          })}
        </div>
      </div>

      <div className="neu-flat p-5 border border-white/5 rounded-xl">
        <div className="flex items-center gap-2 mb-4">
          <MessageCircle className="w-4 h-4 text-primary" />
          <h3 className="text-sm font-bold text-white uppercase tracking-widest">
            Comments {commentsList.length > 0 && <span className="text-muted-foreground font-normal">({commentsList.length})</span>}
          </h3>
        </div>

        <div className="space-y-2 mb-5">
          <input
            type="text"
            value={name}
            onChange={e => setName(e.target.value)}
            placeholder="Your name"
            className="w-full bg-secondary/50 px-3 py-2 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg text-sm"
            data-testid="input-comment-name"
          />
          <div className="flex gap-2">
            <textarea
              value={text}
              onChange={e => setText(e.target.value)}
              placeholder="Write a comment..."
              rows={2}
              maxLength={500}
              className="flex-1 bg-secondary/50 px-3 py-2 text-white border border-white/10 focus:border-primary focus:outline-none rounded-lg text-sm resize-none"
              data-testid="input-comment-text"
              onKeyDown={e => { if (e.key === 'Enter' && e.ctrlKey) handleComment(); }}
            />
            <button
              onClick={handleComment}
              disabled={!name.trim() || !text.trim() || commentMutation.isPending}
              className="px-4 py-2 bg-primary text-primary-foreground font-medium rounded-lg hover:opacity-90 transition-opacity disabled:opacity-40 flex items-center gap-1.5 self-end"
              data-testid="button-submit-comment"
            >
              {commentMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Send className="w-4 h-4" />}
            </button>
          </div>
          <p className="text-xs text-muted-foreground/50 text-right">{text.length}/500 · Ctrl+Enter to send</p>
        </div>

        {commentsLoading ? (
          <div className="flex justify-center py-4">
            <Loader2 className="w-5 h-5 text-primary animate-spin" />
          </div>
        ) : commentsList.length === 0 ? (
          <p className="text-sm text-muted-foreground text-center py-4">No comments yet. Be the first!</p>
        ) : (
          <AnimatePresence>
            <div className="space-y-3 max-h-96 overflow-y-auto">
              {commentsList.map(c => (
                <motion.div
                  key={c.id}
                  initial={{ opacity: 0, y: 8 }}
                  animate={{ opacity: 1, y: 0 }}
                  className="flex gap-3 p-3 bg-secondary/30 border border-white/5 rounded-xl"
                >
                  <div className="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center shrink-0 text-sm font-bold text-primary">
                    {c.userName[0].toUpperCase()}
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1">
                      <span className="text-xs font-bold text-white">{c.userName}</span>
                      <span className="text-xs text-muted-foreground">{timeAgo(c.createdAt as unknown as string)}</span>
                      {isAdmin && (
                        <button
                          onClick={() => deleteMutation.mutate(c.id)}
                          className="ml-auto text-muted-foreground hover:text-red-400 transition-colors"
                          data-testid={`delete-comment-${c.id}`}
                        >
                          <Trash2 className="w-3 h-3" />
                        </button>
                      )}
                    </div>
                    <p className="text-sm text-white/80 break-words">{c.content}</p>
                  </div>
                </motion.div>
              ))}
            </div>
          </AnimatePresence>
        )}
      </div>
    </div>
  );
}
