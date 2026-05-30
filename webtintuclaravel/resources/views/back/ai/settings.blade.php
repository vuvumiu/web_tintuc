@extends('back.template.master')
@section('title', 'AI Settings')
@section('heading', 'AI Settings')
@section('ai_tools', 'active')

@section('content')
<style>
.vu-page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.vu-page-header h1 { font-size: 20px; font-weight: 700; margin: 0; }
.vu-card-dark { background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; }
.vu-card-header { background: rgba(0,0,0,0.2); border-bottom: 1px solid var(--border-subtle); padding: 14px 20px; }
.vu-card-title { font-size: 15px; font-weight: 600; color: var(--text-primary); }
.vu-card-body { padding: 20px; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.form-group input[type="text"], .form-group input[type="password"], .form-group select { width: 100%; height: 40px; padding: 0 12px; font-size: 13px; background: var(--bg-input); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); color: var(--text-primary); }
.form-group input:focus { outline: none; border-color: var(--accent-gold); box-shadow: 0 0 0 3px rgba(201,168,76,0.15); }
.form-hint { font-size: 11px; color: var(--text-muted); margin-top: 4px; }

.toggle-group { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-subtle); }
.toggle-group:last-child { border-bottom: none; }
.toggle-label { font-size: 13px; color: var(--text-primary); }
.toggle-desc { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.toggle-switch { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
.toggle-switch input { position: absolute; opacity: 0; width: 0; height: 0; }
.toggle-slider { position: absolute; cursor: pointer; inset: 0; background-color: #3a3a4a; transition: 0.3s; border-radius: 24px; }
.toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: 0.3s; border-radius: 50%; }
.toggle-switch input:checked ~ .toggle-slider { background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); }
.toggle-switch input:checked ~ .toggle-slider:before { transform: translateX(20px); }
</style>

<div class="vu-page-header">
    <h1><i class="fas fa-cog mr-2" style="color: var(--accent-gold);"></i>Cài đặt AI</h1>
    <a href="{{ url('admin/ai/dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Quay lại
    </a>
</div>

@if(session('success'))
<div class="alert mb-3" style="background: rgba(40,167,69,0.1); border: 1px solid rgba(40,167,69,0.3); color: #28a745; padding: 12px 16px; border-radius: 8px;">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<form method="POST">
    @csrf

    <div class="row">
        <div class="col-md-7">
            <div class="vu-card-dark mb-4">
                <div class="vu-card-header">
                    <span class="vu-card-title"><i class="fas fa-key mr-2" style="color: var(--accent-gold);"></i>Groq API Key</span>
                </div>
                <div class="vu-card-body">
                    <div class="form-group">
                        <label>API Key</label>
                        <input type="text" name="api_key" value="{{ old('api_key', config('gemini.api_key', '')) }}"
                            placeholder="AIza..." autocomplete="off">
                        <div class="form-hint">
                            Lấy API Key miễn phí tại <a href="https://aistudio.google.com/" target="_blank" style="color: var(--accent-gold);">https://aistudio.google.com/</a>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Model</label>
                        <select name="model">
                            <option value="gemini-2.0-flash" {{ config('gemini.model') == 'gemini-2.0-flash' ? 'selected' : '' }}>Gemini 2.0 Flash (Nhanh, miễn phí)</option>
                            <option value="gemini-1.5-flash" {{ config('gemini.model') == 'gemini-1.5-flash' ? 'selected' : '' }}>Gemini 1.5 Flash</option>
                            <option value="gemini-1.5-pro" {{ config('gemini.model') == 'gemini-1.5-pro' ? 'selected' : '' }}>Gemini 1.5 Pro (Chính xác hơn)</option>
                        </select>
                        <div class="form-hint">Gemini 2.0 Flash là lựa chọn tốt nhất - nhanh và miễn phí với free tier.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="vu-card-dark">
                <div class="vu-card-header">
                    <span class="vu-card-title"><i class="fas fa-toggle-on mr-2" style="color: var(--accent-gold);"></i>Tính năng AI</span>
                </div>
                <div class="vu-card-body" style="padding: 12px 20px;">
                    <div class="toggle-group">
                        <div>
                            <div class="toggle-label">Meta Tags Tự động</div>
                            <div class="toggle-desc">Tạo Meta SEO bằng AI trong form bài viết</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="meta_tags" {{ config('gemini.features.meta_tags') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-group">
                        <div>
                            <div class="toggle-label">Gợi ý Tags thông minh</div>
                            <div class="toggle-desc">AI gợi ý tags phù hợp cho bài viết</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="smart_tags" {{ config('gemini.features.smart_tags') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-group">
                        <div>
                            <div class="toggle-label">Kiểm duyệt bình luận</div>
                            <div class="toggle-desc">AI kiểm tra bình luận mới tự động</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="comment_moderation" {{ config('gemini.features.comment_moderation') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-group">
                        <div>
                            <div class="toggle-label">Chatbot người đọc</div>
                            <div class="toggle-desc">Trợ lý AI trên trang tin chính</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="chatbot" {{ config('gemini.features.chatbot') ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn" style="background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; font-weight: 600; padding: 8px 24px;">
            <i class="fas fa-save mr-2"></i> Lưu cài đặt
        </button>
    </div>
</form>
@endsection
