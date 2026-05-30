<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentVote extends Model
{
    protected $table = 'comment_votes';

    protected $fillable = ['comment_id', 'user_id', 'vote_type'];

    protected $casts = ['vote_type' => 'integer'];

    const UPVOTE   = 1;
    const DOWNVOTE = -1;

    public function comment(): BelongsTo
    {
        return $this->belongsTo(NewsComment::class, 'comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getUserVote(int $commentId, int $userId): ?int
    {
        $vote = static::where('comment_id', $commentId)->where('user_id', $userId)->first();
        return $vote ? $vote->vote_type : null;
    }

    public static function toggleVote(int $commentId, int $userId, int $voteType): array
    {
        $comment = NewsComment::where('id', $commentId)->lockForUpdate()->first();
        if (!$comment) {
            throw new \RuntimeException('Comment not found');
        }

        $voteType = in_array($voteType, [self::UPVOTE, self::DOWNVOTE]) ? $voteType : self::UPVOTE;
        $existing = static::where('comment_id', $commentId)
                          ->where('user_id', $userId)
                          ->first();

        // Lấy counts trực tiếp từ DB để đảm bảo đúng nhất
        $currentUp   = (int) $comment->upvote_count;
        $currentDown = (int) $comment->downvote_count;

        if ($existing) {
            if ($existing->vote_type === $voteType) {
                // Bỏ vote: xóa record rồi giảm count
                $existing->delete();
                if ($voteType === self::UPVOTE) {
                    $newUp   = max(0, $currentUp - 1);
                    $newDown = $currentDown;
                } else {
                    $newUp   = $currentUp;
                    $newDown = max(0, $currentDown - 1);
                }
                $comment->update(['upvote_count' => $newUp, 'downvote_count' => $newDown]);
                return [
                    'action'    => 'removed',
                    'new_vote'  => 0,
                    'upvotes'   => $newUp,
                    'downvotes' => $newDown,
                ];
            }

            // Đổi vote: cập nhật record rồi điều chỉnh count
            if ($existing->vote_type === self::UPVOTE) {
                $newUp   = max(0, $currentUp - 1);
                $newDown = $currentDown + 1;
            } else {
                $newUp   = $currentUp + 1;
                $newDown = max(0, $currentDown - 1);
            }
            $existing->update(['vote_type' => $voteType]);
            $comment->update(['upvote_count' => $newUp, 'downvote_count' => $newDown]);
            return [
                'action'    => 'switched',
                'new_vote'  => $voteType,
                'upvotes'   => $newUp,
                'downvotes' => $newDown,
            ];
        } else {
            // Vote mới: tạo record rồi tăng count
            static::create(['comment_id' => $commentId, 'user_id' => $userId, 'vote_type' => $voteType]);
            if ($voteType === self::UPVOTE) {
                $newUp   = $currentUp + 1;
                $newDown = $currentDown;
            } else {
                $newUp   = $currentUp;
                $newDown = $currentDown + 1;
            }
            $comment->update(['upvote_count' => $newUp, 'downvote_count' => $newDown]);
            return [
                'action'   => 'added',
                'new_vote' => $voteType,
                'upvotes'  => $newUp,
                'downvotes'=> $newDown,
            ];
        }
    }
}
