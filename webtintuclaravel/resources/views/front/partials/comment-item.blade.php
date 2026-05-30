<!-- Partial comment item for AJAX load-more -->
@php
    $isOwner = Auth::check() && Auth::id() == $comment->user_id;
    $isAuthorAdmin = $comment->user && $comment->user->canAccessAdmin();
    $userColor = $isAuthorAdmin ? 'var(--accent)' : '#ffffff';
    $userBg = $isAuthorAdmin ? 'linear-gradient(135deg, #c9a84c 0%, #b8941f 100%)' : 'linear-gradient(135deg, #c9a84c 0%, #b8941f 100%)';
    $userVote = Auth::check() ? (\App\Models\CommentVote::getUserVote($comment->id, Auth::id()) ?? 0) : 0;
@endphp

<div class="comment-root" data-comment-id="{{ $comment->id }}" id="comment-{{ $comment->id }}">
    <div class="d-flex gap-3">
        <div class="flex-shrink-0">
            @if($comment->user && $comment->user->avatar && file_exists(public_path($comment->user->avatar)))
                <img src="{{ asset($comment->user->avatar) }}" class="comment-avatar" alt="{{ $comment->user->username }}"
                     style="object-fit:cover;">
            @else
                <div class="comment-avatar" style="background:{{ $userBg }};">
                    {{ strtoupper(substr($comment->user->username ?? 'U', 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="flex-grow-1 min-width-0">
            {{-- Bubble --}}
            <div class="comment-content-display">
                <div class="comment-bubble">
                    <span class="comment-bubble-name" style="color:{{ $userColor }}">
                        {{ $comment->user->username ?? '—' }}
                        @if($isAuthorAdmin)
                            <span class="badge" style="background:var(--accent);color:#0a0a0f;font-size:.65rem;padding:1px 6px;margin-left:4px;font-weight:700;">Admin</span>
                        @endif
                    </span>
                    <p class="comment-bubble-text" style="color:rgba(255,255,255,0.65);margin:6px 0 0;font-size:14px;line-height:1.6;">{{ $comment->content }}</p>
                </div>

                {{-- Vote buttons --}}
                <div class="d-flex align-items-center gap-3 mt-2">
                    @auth
                        <button type="button" class="btn-vote vote-btn {{ $userVote === 1 ? 'active' : '' }}"
                                data-comment-id="{{ $comment->id }}" data-vote-type="1" title="Thích"
                                style="background:none;border:none;cursor:pointer;padding:2px 8px;border-radius:6px;display:flex;align-items:center;gap:5px;font-size:12px;color:rgba(255,255,255,0.45);transition:all .15s;">
                            <i class="fas fa-thumbs-up {{ $userVote === 1 ? 'text-warning' : 'text-muted' }}"></i>
                            <span>{{ $comment->upvote_count ?? 0 }}</span>
                        </button>
                        <button type="button" class="btn-vote vote-btn {{ $userVote === -1 ? 'active' : '' }}"
                                data-comment-id="{{ $comment->id }}" data-vote-type="-1" title="Không thích"
                                style="background:none;border:none;cursor:pointer;padding:2px 8px;border-radius:6px;display:flex;align-items:center;gap:5px;font-size:12px;color:rgba(255,255,255,0.45);transition:all .15s;">
                            <i class="fas fa-thumbs-down {{ $userVote === -1 ? 'text-danger' : 'text-muted' }}"></i>
                            <span>{{ $comment->downvote_count ?? 0 }}</span>
                        </button>
                    @else
                        <span style="font-size:12px;color:rgba(255,255,255,0.35);">
                            <i class="fas fa-thumbs-up mr-1" style="color:rgba(255,255,255,0.25);"></i>{{ $comment->upvote_count ?? 0 }}
                            <i class="fas fa-thumbs-down ml-2 mr-1" style="color:rgba(255,255,255,0.25);"></i>{{ $comment->downvote_count ?? 0 }}
                        </span>
                    @endauth
                </div>

                <div style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:6px;">
                    <span>{{ $comment->created_at instanceof \Carbon\Carbon ? $comment->created_at->diffForHumans() : $comment->created_at }}</span>
                    @if($comment->created_at instanceof \Carbon\Carbon && $comment->updated_at instanceof \Carbon\Carbon && $comment->created_at->ne($comment->updated_at))
                        <span>· Đã sửa</span>
                    @endif
                </div>

                <div style="display:flex;gap:4px;margin-top:8px;flex-wrap:wrap;">
                    @auth
                        <button type="button" class="comment-action-btn btn-reply"
                                data-parent-id="{{ $comment->id }}"
                                data-username="{{ $comment->user->username ?? '' }}"
                                style="background:none;border:none;cursor:pointer;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;color:rgba(255,255,255,0.4);transition:all .15s;">
                            Phản hồi
                        </button>
                        @if($isOwner)
                            <button type="button" class="comment-action-btn btn-edit"
                                    data-comment-id="{{ $comment->id }}"
                                    style="background:none;border:none;cursor:pointer;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;color:rgba(255,255,255,0.4);transition:all .15s;">
                                Sửa
                            </button>
                        @endif
                        @if($isOwner || $isAdmin)
                            <button type="button" class="comment-action-btn text-danger btn-delete"
                                    data-comment-id="{{ $comment->id }}"
                                    data-username="{{ $comment->user->username ?? '' }}"
                                    style="background:none;border:none;cursor:pointer;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;color:rgba(239,68,68,0.6);transition:all .15s;">
                                Xóa
                            </button>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Edit inline --}}
            @if($isOwner)
                <div class="comment-edit-area" id="editArea-{{ $comment->id }}" style="display:none;margin-top:12px;">
                    <textarea style="width:100%;background:rgba(255,255,255,0.05);border:1.5px solid rgba(201,168,76,0.3);border-radius:8px;padding:12px;font-size:14px;color:#fff;font-family:inherit;resize:vertical;min-height:70px;outline:none;" id="editText-{{ $comment->id }}"
                              maxlength="2000">{{ $comment->content }}</textarea>
                    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;">
                        <button type="button" class="btn-cancel-edit"
                                data-comment-id="{{ $comment->id }}"
                                style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.6);padding:6px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                            Hủy
                        </button>
                        <button type="button" class="btn-save-edit"
                                data-comment-id="{{ $comment->id }}"
                                style="background:var(--accent);border:none;color:#0a0a0f;padding:6px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                            Lưu
                        </button>
                    </div>
                </div>
            @endif

            {{-- Reply form --}}
            @auth
                <div class="reply-form-wrap mt-2" id="replyForm-{{ $comment->id }}" style="display:none;margin-top:12px;">
                    <form class="reply-form-inner" novalidate>
                        @csrf
                        <input type="hidden" name="news_id" value="{{ $comment->news_id }}">
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="flex-shrink-0 d-none d-sm-block">
                                @php $authUser = Auth::user(); @endphp
                                @if($authUser->avatar && file_exists(public_path($authUser->avatar)))
                                    <img src="{{ asset($authUser->avatar) }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                                @else
                                    <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#c9a84c,#b8941f);color:#0a0a0f;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;">
                                        {{ strtoupper(substr($authUser->username, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <textarea style="width:100%;background:rgba(255,255,255,0.05);border:1.5px solid rgba(255,255,255,0.1);border-radius:8px;padding:12px;font-size:14px;color:#fff;font-family:inherit;resize:vertical;min-height:60px;outline:none;"
                                          name="content" rows="2"
                                          maxlength="2000"
                                          placeholder="Phản hồi {{ $comment->user->username ?? '' }}…"></textarea>
                                <div class="d-flex gap-2 mt-2 justify-content-end">
                                    <button type="button" class="btn-cancel-reply"
                                            data-parent-id="{{ $comment->id }}"
                                            style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:rgba(255,255,255,0.6);padding:6px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                                        Hủy
                                    </button>
                                    <button type="submit" class="btn-submit-reply"
                                            data-parent-id="{{ $comment->id }}"
                                            style="background:var(--accent);border:none;color:#0a0a0f;padding:6px 16px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                                        Gửi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endauth

            {{-- Replies --}}
            @if($comment->replies && $comment->replies->count() > 0)
                <div class="comment-replies" id="replies-{{ $comment->id }}" style="margin-top:16px;padding-left:20px;border-left:2px solid rgba(255,255,255,0.06);">
                    @foreach($comment->replies as $reply)
                        @include('front.partials.comment-item', ['comment' => $reply])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
