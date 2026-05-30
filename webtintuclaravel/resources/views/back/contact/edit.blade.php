@extends('back.template.master')

@section('title', 'Quản lý liên hệ')

@section('heading', 'Chi tiết liên hệ')
@section('contact', 'active')

@section('content')
<style>
.vu-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.vu-page-header h1 { font-size: 20px; font-weight: 700; margin: 0; }

.vu-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: var(--radius-md); font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s; border: 1px solid transparent; }
.vu-btn-back { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: var(--text-secondary); }
.vu-btn-back:hover { border-color: rgba(201,168,76,0.35); color: var(--text-primary); background: rgba(255,255,255,0.08); }
.vu-btn-primary { background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; border-color: var(--accent-gold); }
.vu-btn-primary:hover { box-shadow: var(--shadow-gold); transform: translateY(-1px); }
.vu-btn-success { background: rgba(92,185,123,0.15); border-color: rgba(92,185,123,0.3); color: var(--status-success); }
.vu-btn-success:hover { background: rgba(92,185,123,0.22); border-color: rgba(92,185,123,0.5); }
.vu-btn-danger { background: rgba(229,115,115,0.12); border-color: rgba(229,115,115,0.25); color: var(--status-danger); }
.vu-btn-danger:hover { background: rgba(229,115,115,0.2); border-color: rgba(229,115,115,0.4); }

.vu-card-dark { background: var(--bg-tertiary); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 18px; }
.vu-card-header { padding: 14px 20px; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: rgba(0,0,0,0.2); }
.vu-card-title { font-size: 14px; font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
.vu-card-body { padding: 20px; }
.vu-card-footer { padding: 14px 20px; border-top: 1px solid var(--border-subtle); background: rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }

.vu-badge-sm { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
.vu-badge-sm.success { background: rgba(92,185,123,0.14); color: var(--status-success); }
.vu-badge-sm.warning { background: rgba(201,168,76,0.14); color: var(--status-warning); }
.vu-badge-sm.danger { background: rgba(229,115,115,0.14); color: var(--status-danger); }
.vu-badge-sm.info { background: rgba(73,158,255,0.14); color: var(--status-info); }
.vu-badge-sm.neutral { background: rgba(255,255,255,0.06); color: var(--text-secondary); }
.vu-badge-sm.gold { background: rgba(201,168,76,0.12); color: var(--accent-gold); }

.vu-form-group { margin-bottom: 18px; }
.vu-form-group label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 7px; }
.vu-form-control { width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); color: var(--text-primary); font-size: 13px; transition: all 0.15s; }
.vu-form-control:focus { outline: none; border-color: rgba(201,168,76,0.45); box-shadow: 0 0 0 3px rgba(201,168,76,0.12); }
.vu-form-control::placeholder { color: var(--text-muted); }
.vu-form-control[readonly] { opacity: 0.6; cursor: not-allowed; }

.vu-row { display: grid; gap: 18px; }
.vu-row-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.vu-row-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
.vu-row-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

.contact-header-card { background: var(--bg-tertiary); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 18px; }
.contact-header-inner { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
.contact-avatar-lg { width: 52px; height: 52px; border-radius: 50%; background: rgba(201,168,76,0.12); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: var(--accent-gold); flex-shrink: 0; }
.contact-header-info h2 { font-size: 17px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px; }
.contact-header-info .contact-email { font-size: 13px; color: var(--accent-gold); margin-bottom: 6px; }
.contact-header-info .contact-meta { display: flex; gap: 6px; flex-wrap: wrap; }
.contact-header-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }

.reply-history-item { border-left: 3px solid var(--accent-green); background: rgba(92,185,123,0.04); padding: 16px 20px; }
.reply-history-item + .reply-history-item { border-top: 1px solid rgba(255,255,255,0.04); }
.reply-history-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; gap: 10px; flex-wrap: wrap; }
.reply-history-header .reply-count-badge { background: rgba(92,185,123,0.14); color: var(--status-success); padding: 3px 10px; border-radius: 999px; font-size: 10px; font-weight: 700; }
.reply-history-header .reply-staff { font-size: 12px; color: var(--text-muted); }
.reply-history-header .reply-time { font-size: 11px; color: var(--text-muted); }
.reply-recipient { font-size: 12px; color: var(--text-muted); margin-bottom: 8px; }
.reply-recipient strong { color: var(--accent-gold); }
.reply-intro { font-size: 13px; color: var(--status-success); font-style: italic; margin-bottom: 8px; padding-left: 8px; border-left: 2px solid rgba(92,185,123,0.3); }
.reply-content { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: var(--radius-md); padding: 14px; font-size: 13px; line-height: 1.7; color: var(--text-secondary); white-space: pre-wrap; margin-bottom: 8px; }
.reply-outro { font-size: 12px; color: var(--text-muted); font-style: italic; text-align: right; }

.callout-dark { background: rgba(73,158,255,0.08); border: 1px solid rgba(73,158,255,0.2); border-radius: var(--radius-md); padding: 14px 16px; margin: 14px 0 0; display: flex; align-items: flex-start; gap: 10px; font-size: 12px; color: var(--status-info); }
.callout-dark i { margin-top: 1px; flex-shrink: 0; }

.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; }
.info-item { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: var(--radius-md); padding: 12px 14px; }
.info-item label { display: block; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 5px; }
.info-item .info-value { font-size: 13px; color: var(--text-primary); font-weight: 500; }

.message-display { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: var(--radius-md); padding: 16px; font-size: 13px; line-height: 1.7; color: var(--text-secondary); white-space: pre-wrap; }

.divider { height: 1px; background: var(--border-subtle); margin: 18px 0; }

@media (max-width: 768px) {
    .vu-row-2, .vu-row-3, .vu-row-4 { grid-template-columns: 1fr; }
    .contact-header-actions { margin-left: 0; width: 100%; }
}
</style>

<div class="vu-page-header">
    <h1><i class="fas fa-envelope-open-text mr-2" style="color: var(--accent-gold);"></i>Chi tiết liên hệ</h1>
    <a href="{{ url('admin/contact/list') }}" class="vu-btn vu-btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

{{-- Contact Header Card --}}
<div class="contact-header-card">
    <div class="contact-header-inner">
        <div class="contact-avatar-lg">{{ strtoupper(substr($Contact->Name, 0, 1)) }}</div>
        <div class="contact-header-info">
            <h2>{{ $Contact->Name }}</h2>
            <div class="contact-email"><i class="fas fa-envelope" style="font-size: 11px; margin-right: 4px;"></i> {{ $Contact->Email }}
                @if($Contact->Phone)
                    <span style="color: var(--text-muted); margin-left: 10px;"><i class="fas fa-phone" style="font-size: 10px; margin-right: 3px;"></i> {{ $Contact->Phone }}</span>
                @endif
            </div>
            <div class="contact-meta">
                @if(isset($categoryLabels[$Contact->category]))
                    <span class="vu-badge-sm {{ $categoryColors[$Contact->category] ?? 'neutral' }}">{{ $categoryLabels[$Contact->category] }}</span>
                @endif
                <span class="vu-badge-sm {{ $priorityColors[$Contact->priority] ?? 'neutral' }}">{{ $priorityLabels[$Contact->priority] ?? 'Trung bình' }}</span>
                @if($Contact->replied_at)
                    <span class="vu-badge-sm success"><i class="fas fa-check" style="font-size: 8px;"></i> Đã phản hồi</span>
                @elseif($Contact->is_reviewed)
                    <span class="vu-badge-sm info"><i class="fas fa-eye" style="font-size: 8px;"></i> Đã xem</span>
                @else
                    <span class="vu-badge-sm danger"><i class="fas fa-bell" style="font-size: 8px;"></i> Mới</span>
                @endif
            </div>
        </div>
        <div class="contact-header-actions">
            @if(!$Contact->is_reviewed)
                <a href="{{ url('admin/contact/mark-read/' . $Contact->RowID) }}" class="vu-btn vu-btn-success"
                   onclick="return confirm('Đánh dấu là đã xem?')">
                    <i class="fas fa-eye"></i> Đánh dấu đã xem
                </a>
            @endif
        </div>
    </div>
</div>

{{-- Lịch sử phản hồi --}}
@if(isset($replies) && $replies->count() > 0)
<div class="vu-card-dark" id="reply-history">
    <div class="vu-card-header">
        <div class="vu-card-title">
            <i class="fas fa-history" style="color: var(--status-success);"></i>
            Lịch sử phản hồi ({{ $replies->count() }} lần)
        </div>
    </div>
    <div style="padding: 0;">
        @foreach($replies as $idx => $reply)
        <div class="reply-history-item">
            <div class="reply-history-header">
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <span class="reply-count-badge">
                        <i class="fas fa-paper-plane" style="font-size: 8px; margin-right: 4px;"></i> Phản hồi #{{ $replies->count() - $idx }}
                    </span>
                    @if($reply->staff_name)
                        <span class="reply-staff"><i class="fas fa-user" style="font-size: 10px; margin-right: 3px;"></i> {{ $reply->staff_name }}</span>
                    @endif
                </div>
                <span class="reply-time"><i class="fas fa-clock" style="font-size: 10px; margin-right: 3px;"></i> {{ $reply->sent_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="reply-recipient">
                <i class="fas fa-at" style="font-size: 10px;"></i> Gửi đến: <strong>{{ $reply->recipient_email }}</strong>
            </div>
            @if($reply->reply_intro)
                <div class="reply-intro"><i class="fas fa-comment-dots" style="font-size: 10px; margin-right: 4px;"></i> {{ $reply->reply_intro }}</div>
            @endif
            <div class="reply-content">{{ $reply->reply_content }}</div>
            @if($reply->reply_outro)
                <div class="reply-outro">{{ $reply->reply_outro }}</div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Form phản hồi --}}
@if(!$Contact->replied_at)
<div class="vu-card-dark" id="reply-form">
    <div class="vu-card-header">
        <div class="vu-card-title">
            <i class="fas fa-paper-plane" style="color: var(--status-success);"></i>
            Gửi phản hồi cho khách hàng
        </div>
        <span class="vu-badge-sm gold">Gửi đến: <strong>{{ $Contact->Email }}</strong></span>
    </div>
    <form role="form" action="{{ url('admin/contact/reply/' . $Contact->RowID) }}" method="POST">
        <div class="vu-card-body">
            {!! csrf_field() !!}
            <div class="vu-form-group">
                <label>Lời mở đầu (tùy chọn)</label>
                <input type="text" name="reply_intro" class="vu-form-control"
                       placeholder="Ví dụ: Xin chào {{ $Contact->Name }}, cảm ơn bạn đã liên hệ..."
                       value="{{ old('reply_intro') }}">
            </div>
            <div class="vu-form-group">
                <label>Nội dung phản hồi <span style="color: var(--accent-red);">*</span></label>
                <textarea name="reply_content" class="vu-form-control" rows="6"
                          placeholder="Nhập nội dung phản hồi cho khách hàng..."
                          required>{{ old('reply_content') }}</textarea>
                <small style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">Tối thiểu 5 ký tự, tối đa 5000 ký tự.</small>
            </div>
            <div class="vu-form-group" style="margin-bottom: 0;">
                <label>Lời kết (tùy chọn)</label>
                <input type="text" name="reply_outro" class="vu-form-control"
                       placeholder="Ví dụ: Chúc bạn một ngày tốt lành!..."
                       value="{{ old('reply_outro') }}">
            </div>
            <div class="callout-dark">
                <i class="fas fa-info-circle"></i>
                <span>Email phản hồi sẽ được gửi đến <strong>{{ $Contact->Email }}</strong>. Tin nhắn gốc của khách sẽ được trích dẫn trong email.</span>
            </div>
        </div>
        <div class="vu-card-footer">
            <button type="submit" class="vu-btn vu-btn-success">
                <i class="fas fa-paper-plane"></i> Gửi phản hồi qua Email
            </button>
        </div>
    </form>
</div>
@else
<div class="vu-card-dark" id="reply-form">
    <div class="vu-card-header">
        <div class="vu-card-title">
            <i class="fas fa-plus-circle" style="color: var(--status-success);"></i>
            Gửi phản hồi thêm
        </div>
        <span class="vu-badge-sm gold">Gửi đến: <strong>{{ $Contact->Email }}</strong></span>
    </div>
    <form role="form" action="{{ url('admin/contact/reply/' . $Contact->RowID) }}" method="POST">
        <div class="vu-card-body">
            {!! csrf_field() !!}
            <div class="vu-form-group">
                <label>Lời mở đầu (tùy chọn)</label>
                <input type="text" name="reply_intro" class="vu-form-control"
                       placeholder="Ví dụ: Cảm ơn bạn đã phản hồi..."
                       value="{{ old('reply_intro') }}">
            </div>
            <div class="vu-form-group">
                <label>Nội dung phản hồi <span style="color: var(--accent-red);">*</span></label>
                <textarea name="reply_content" class="vu-form-control" rows="5"
                          placeholder="Nhập nội dung phản hồi..."
                          required>{{ old('reply_content') }}</textarea>
            </div>
            <div class="vu-form-group" style="margin-bottom: 0;">
                <label>Lời kết (tùy chọn)</label>
                <input type="text" name="reply_outro" class="vu-form-control"
                       placeholder="Ví dụ: Chúc bạn một ngày tốt lành!..."
                       value="{{ old('reply_outro') }}">
            </div>
        </div>
        <div class="vu-card-footer">
            <button type="submit" class="vu-btn vu-btn-success">
                <i class="fas fa-paper-plane"></i> Gửi thêm phản hồi
            </button>
        </div>
    </form>
</div>
@endif

{{-- Thông tin liên hệ + Form chỉnh sửa --}}
<div class="vu-card-dark">
    <div class="vu-card-header">
        <div class="vu-card-title">
            <i class="fas fa-user-circle" style="color: var(--accent-gold);"></i>
            Thông tin liên hệ &amp; Ghi chú
        </div>
        <div style="display: flex; gap: 6px;">
            @if($Contact->replied_at)
                <span class="vu-badge-sm success"><i class="fas fa-check" style="font-size: 8px;"></i> Đã phản hồi {{ $Contact->replied_at->format('d/m/Y H:i') }}</span>
            @elseif($Contact->is_reviewed)
                <span class="vu-badge-sm info"><i class="fas fa-eye" style="font-size: 8px;"></i> Đã xem</span>
            @else
                <span class="vu-badge-sm danger"><i class="fas fa-bell" style="font-size: 8px;"></i> Mới</span>
            @endif
        </div>
    </div>
    <form role="form" action="{{ url('admin/contact/edit/'.$Contact->RowID) }}" method="POST">
        <div class="vu-card-body">
            {!! csrf_field() !!}

            <div class="vu-row vu-row-2">
                <div class="vu-form-group">
                    <label>Họ và tên <span style="color: var(--accent-red);">*</span></label>
                    <input type="text" class="vu-form-control" name="Name1" value="{{ $Contact->Name }}" required>
                </div>
                <div class="vu-form-group">
                    <label>Email <span style="color: var(--accent-red);">*</span></label>
                    <input type="email" class="vu-form-control" name="Email" value="{{ $Contact->Email }}" required>
                </div>
            </div>

            <div class="vu-row vu-row-3">
                <div class="vu-form-group">
                    <label>Số điện thoại <span style="color: var(--accent-red);">*</span></label>
                    <input type="text" class="vu-form-control" name="txtPhone" value="{{ $Contact->Phone }}" required>
                </div>
                <div class="vu-form-group">
                    <label>Phân loại</label>
                    <select name="selCategory" class="vu-form-control">
                        @foreach($categoryLabels as $key => $label)
                            <option value="{{ $key }}" {{ $Contact->category == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="vu-form-group">
                    <label>Mức ưu tiên</label>
                    <select name="selPriority" class="vu-form-control">
                        @foreach($priorityLabels as $key => $label)
                            <option value="{{ $key }}" {{ $Contact->priority == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="vu-row vu-row-2">
                <div class="vu-form-group">
                    <label>Người phụ trách</label>
                    <select name="assigned_to" class="vu-form-control">
                        <option value="">-- Chưa phân công --</option>
                        @foreach($staffs as $staff)
                            <option value="{{ $staff->id }}" {{ $Contact->assigned_to == $staff->id ? 'selected' : '' }}>
                                {{ $staff->fullname }} ({{ $staff->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="vu-form-group">
                    <label>Tiêu đề</label>
                    <input type="text" class="vu-form-control" name="txtSubject"
                           value="{{ $Contact->subject ?? '' }}" placeholder="Tiêu đề liên hệ (tùy chọn)">
                </div>
            </div>

            <div class="vu-form-group">
                <label>Lời nhắn <span style="color: var(--accent-red);">*</span></label>
                <div class="message-display">{{ $Contact->Message }}</div>
                <textarea name="Message" class="vu-form-control" rows="3" style="margin-top: 8px;"
                          placeholder="Chỉnh sửa lời nhắn nếu cần..." required>{{ $Contact->Message }}</textarea>
            </div>

            <div class="vu-form-group" style="margin-bottom: 0;">
                <label>Ghi chú nội bộ</label>
                <textarea name="admin_note" class="vu-form-control" rows="3"
                          placeholder="Ghi chú của admin về liên hệ này (chỉ hiển thị trong trang quản trị)">{{ $Contact->admin_note ?? '' }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Ngày gửi</label>
                    <div class="info-value">{{ $Contact->created_at ? $Contact->created_at->format('d/m/Y H:i:s') : '-' }}</div>
                </div>
                @if($Contact->ip_address)
                <div class="info-item">
                    <label>IP người gửi</label>
                    <div class="info-value" style="font-family: monospace; font-size: 12px;">{{ $Contact->ip_address }}</div>
                </div>
                @endif
                <div class="info-item">
                    <label>Đã xem</label>
                    <div class="info-value">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; font-size: 13px; color: var(--text-primary); font-weight: 400; text-transform: none; letter-spacing: 0;">
                            <input type="checkbox" class="vu-checkbox" name="is_reviewed" id="is_reviewed"
                                   value="1" {{ $Contact->is_reviewed ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--accent-gold);">
                            {{ $Contact->is_reviewed ? 'Có' : 'Không' }}
                        </label>
                    </div>
                </div>
                <div class="info-item">
                    <label>Đã phản hồi</label>
                    <div class="info-value">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; font-size: 13px; color: var(--text-primary); font-weight: 400; text-transform: none; letter-spacing: 0;">
                            <input type="checkbox" class="vu-checkbox" name="is_replied"
                                   id="is_replied" value="1" {{ $Contact->replied_at ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--accent-gold);">
                            {{ $Contact->replied_at ? 'Có' : 'Không' }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="vu-card-footer">
            <div style="font-size: 11px; color: var(--text-muted);">
                <i class="fas fa-shield-alt" style="font-size: 10px; margin-right: 4px;"></i>
                Ghi chú nội bộ chỉ hiển thị trong trang quản trị, không gửi đến khách hàng.
            </div>
            <div style="display: flex; gap: 8px;">
                <form action="{{ url('admin/contact/delete/' . $Contact->RowID) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa liên hệ này?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="vu-btn vu-btn-danger">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </form>
                <button type="submit" class="vu-btn vu-btn-primary">
                    <i class="fas fa-save"></i> Lưu chỉnh sửa
                </button>
            </div>
        </div>
    </form>
</div>
@stop
