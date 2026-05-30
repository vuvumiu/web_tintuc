@extends('back.template.master')
@section('title', 'AI Tools Dashboard')
@section('heading', 'AI Tools Dashboard')
@section('ai_tools', 'active')

@section('content')
<style>
.vu-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.vu-page-header h1 { font-size: 20px; font-weight: 700; margin: 0; }

.vu-card-dark { background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; }
.vu-card-header { background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-subtle); padding: 14px 20px; }
.vu-card-title { font-size: 15px; font-weight: 600; color: var(--text-primary); }
.vu-card-body { padding: 20px; }

.feature-card { background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; transition: all 0.2s; }
.feature-card:hover { border-color: rgba(201,168,76,0.4); }
.feature-card .feature-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
.feature-card .feature-title { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
.feature-card .feature-desc { font-size: 12px; color: var(--text-muted); line-height: 1.4; }
.feature-card .feature-status { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 6px; }
.feature-card .feature-status.active { background: #28a745; box-shadow: 0 0 6px rgba(40,167,69,0.5); }
.feature-card .feature-status.inactive { background: #6c757d; }

.status-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.status-badge.success { background: rgba(40,167,69,0.15); color: #28a745; }
.status-badge.danger { background: rgba(239,68,68,0.15); color: #f87171; }
.status-badge.warning { background: rgba(255,193,7,0.15); color: #ffc107; }

.info-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid var(--border-subtle); font-size: 13px; }
.info-row:last-child { border-bottom: none; }
.info-label { color: var(--text-muted); }
.info-value { color: var(--text-primary); font-weight: 600; }
</style>

<div class="vu-page-header">
    <h1><i class="fas fa-robot mr-2" style="color: var(--accent-gold);"></i>AI Tools Dashboard</h1>
    <a href="{{ url('admin/ai/settings') }}" class="btn" style="background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; font-weight: 600;">
        <i class="fas fa-cog mr-1"></i> Cài đặt
    </a>
</div>

@if(session('success'))
<div class="alert alert-success" style="background: rgba(40,167,69,0.1); border: 1px solid rgba(40,167,69,0.3); color: #28a745;">
    {{ session('success') }}
</div>
@endif

<div class="row">
    <!-- Status -->
    <div class="col-md-12 mb-4">
        <div class="vu-card-dark">
            <div class="vu-card-header">
                <span class="vu-card-title">
                    <i class="fas fa-signal mr-2" style="color: var(--accent-gold);"></i>Trạng thái kết nối
                </span>
            </div>
            <div class="vu-card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-row">
                            <span class="info-label">Groq (Llama) API Key</span>
                            <span class="info-value">
                                @if($isConfigured)
                                    <span class="status-badge success"><i class="fas fa-check-circle mr-1"></i>Đã cấu hình</span>
                                @else
                                    <span class="status-badge danger"><i class="fas fa-times-circle mr-1"></i>Chưa cấu hình</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Model</span>
                            <span class="info-value">{{ config('gemini.model', 'gemini-2.0-flash') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Rate Limit</span>
                            <span class="info-value">Admin: {{ config('gemini.rate_limits.admin', 10) }}/phút | Chatbot: {{ config('gemini.rate_limits.chatbot', 5) }}/phút</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        @if(!$isConfigured)
                        <div class="alert" style="background: rgba(255,193,7,0.1); border: 1px solid rgba(255,193,7,0.3); color: #ffc107;">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Vui lòng cấu hình Groq (Llama) API Key</strong> để sử dụng các tính năng AI.
                            <a href="{{ url('admin/ai/settings') }}" class="btn btn-sm btn-warning ml-2">
                                <i class="fas fa-cog mr-1"></i> Cài đặt ngay
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feature Cards -->
<div class="row">
    <!-- Meta Tags -->
    <div class="col-md-4 mb-3">
        <div class="feature-card">
            <div class="vu-card-body">
                <div class="feature-icon" style="background: rgba(52,152,219,0.15); color: #3498db;">
                    <i class="fas fa-search"></i>
                </div>
                <div class="feature-title">
                    <span class="feature-status {{ config('gemini.features.meta_tags') ? 'active' : 'inactive' }}"></span>
                    Meta Tags Tự động
                </div>
                <div class="feature-desc">Tạo Meta Title, Description, Keywords tối ưu SEO tự động cho bài viết</div>
                <div class="mt-3">
                    @if(config('gemini.features.meta_tags'))
                        <span class="status-badge success"><i class="fas fa-check mr-1"></i>Đang bật</span>
                    @else
                        <span class="status-badge danger"><i class="fas fa-times mr-1"></i>Đang tắt</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Smart Tags -->
    <div class="col-md-4 mb-3">
        <div class="feature-card">
            <div class="vu-card-body">
                <div class="feature-icon" style="background: rgba(46,204,113,0.15); color: #2ecc71;">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="feature-title">
                    <span class="feature-status {{ config('gemini.features.smart_tags') ? 'active' : 'inactive' }}"></span>
                    Gợi ý Tags thông minh
                </div>
                <div class="feature-desc">AI tự động gợi ý tags phù hợp dựa trên nội dung bài viết</div>
                <div class="mt-3">
                    @if(config('gemini.features.smart_tags'))
                        <span class="status-badge success"><i class="fas fa-check mr-1"></i>Đang bật</span>
                    @else
                        <span class="status-badge danger"><i class="fas fa-times mr-1"></i>Đang tắt</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Comment Moderation -->
    <div class="col-md-4 mb-3">
        <div class="feature-card">
            <div class="vu-card-body">
                <div class="feature-icon" style="background: rgba(155,89,182,0.15); color: #9b59b6;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="feature-title">
                    <span class="feature-status {{ config('gemini.features.comment_moderation') ? 'active' : 'inactive' }}"></span>
                    Kiểm duyệt bình luận
                </div>
                <div class="feature-desc">Tự động kiểm duyệt bình luận: phát hiện spam, toxic, quảng cáo</div>
                <div class="mt-3">
                    @if(config('gemini.features.comment_moderation'))
                        <span class="status-badge success"><i class="fas fa-check mr-1"></i>Đang bật</span>
                    @else
                        <span class="status-badge danger"><i class="fas fa-times mr-1"></i>Đang tắt</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot -->
    <div class="col-md-4 mb-3">
        <div class="feature-card">
            <div class="vu-card-body">
                <div class="feature-icon" style="background: rgba(230,126,34,0.15); color: #e67e22;">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="feature-title">
                    <span class="feature-status {{ config('gemini.features.chatbot') ? 'active' : 'inactive' }}"></span>
                    Chatbot người đọc
                </div>
                <div class="feature-desc">Trợ lý AI hỗ trợ người đọc tìm kiếm bài viết và trả lời câu hỏi</div>
                <div class="mt-3">
                    @if(config('gemini.features.chatbot'))
                        <span class="status-badge success"><i class="fas fa-check mr-1"></i>Đang bật</span>
                    @else
                        <span class="status-badge danger"><i class="fas fa-times mr-1"></i>Đang tắt</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="col-md-8 mb-3">
        <div class="vu-card-dark">
            <div class="vu-card-header">
                <span class="vu-card-title">
                    <i class="fas fa-info-circle mr-2" style="color: var(--accent-gold);"></i>Hướng dẫn sử dụng
                </span>
            </div>
            <div class="vu-card-body" style="font-size: 13px; color: var(--text-secondary); line-height: 1.6;">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-2"><strong style="color: var(--accent-gold);">Meta Tags AI</strong></p>
                        <p class="mb-3">Khi thêm/sửa bài viết, nhấn nút <strong>"Tạo Meta"</strong> trong tab SEO. AI sẽ tạo Meta Title, Description và Keywords tối ưu cho bài viết.</p>

                        <p class="mb-2"><strong style="color: var(--accent-gold);">Gợi ý Tags</strong></p>
                        <p class="mb-0">Nhấn nút <strong>"Gợi ý Tags"</strong> bên cạnh ô nhập tags. Click vào tag được gợi ý để thêm vào bài viết.</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><strong style="color: var(--accent-gold);">Kiểm duyệt bình luận</strong></p>
                        <p class="mb-3">Trong trang quản lý bình luận, nhấn nút <strong>"Check AI"</strong> để kiểm duyệt từng bình luận hoặc dùng <strong>"Check All"</strong> để kiểm duyệt hàng loạt.</p>

                        <p class="mb-2"><strong style="color: var(--accent-gold);">Chatbot</strong></p>
                        <p class="mb-0">Chatbot tự động xuất hiện ở góc dưới phải trang người đọc. Người dùng có thể hỏi về nội dung bài viết đang xem.</p>
                    </div>
                </div>
                <hr style="border-color: var(--border-subtle);">
                <div class="mt-2">
                    <strong style="color: var(--accent-gold); font-size: 12px;">GROQ (LLAMA) API KEY</strong>
                    <p class="mb-0 mt-1" style="font-size: 12px;">
                        Groq (Llama) API dùng cho tạo meta, gợi ý tags và kiểm duyệt nội dung.<br>
                        Đăng ký tại: <a href="https://console.groq.com/" target="_blank" style="color: var(--accent-gold);">https://console.groq.com/</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
