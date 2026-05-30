@extends('front.template.master')
@section('title', 'Cài đặt thông báo')
@section('content')

<style>
.notif-settings-page { padding: 24px 0 60px; }
.notif-settings-wrap {
    max-width: 920px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
    gap: 20px;
}
.notif-settings-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
    overflow: hidden;
}
.notif-settings-side {
    background: linear-gradient(180deg, #24313d 0%, #1b2430 100%);
    color: #fff;
    padding: 24px;
}
.notif-settings-side__name { font-size: 1.05rem; font-weight: 800; margin-bottom: 4px; }
.notif-settings-side__mail { color: rgba(255,255,255,.62); font-size: .85rem; margin-bottom: 18px; }
.notif-settings-side__link {
    display: flex;
    align-items: center;
    gap: 10px;
    color: rgba(255,255,255,.74);
    text-decoration: none;
    padding: 12px 14px;
    border-radius: 10px;
    background: rgba(255,255,255,.04);
    margin-bottom: 10px;
}
.notif-settings-side__link.is-active {
    color: #f5c84c;
    background: rgba(245, 200, 76, .12);
}
.notif-settings-main__head {
    padding: 24px 28px 16px;
    border-bottom: 1px solid #edf0f4;
}
.notif-settings-main__head h1 {
    margin: 0 0 6px;
    font-size: 1.25rem;
    color: #18212b;
}
.notif-settings-main__head p { margin: 0; color: #667085; }
.notif-settings-form { padding: 10px 28px 28px; }
.notif-settings-group { padding: 18px 0; border-bottom: 1px solid #edf0f4; }
.notif-settings-group:last-child { border-bottom: 0; }
.notif-settings-group__title {
    font-size: .92rem;
    font-weight: 800;
    color: #18212b;
    margin-bottom: 4px;
}
.notif-settings-group__desc {
    color: #667085;
    font-size: .86rem;
    margin-bottom: 14px;
}
.notif-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 0;
}
.notif-toggle__text strong {
    display: block;
    color: #18212b;
    font-size: .9rem;
    margin-bottom: 4px;
}
.notif-toggle__text span {
    display: block;
    color: #667085;
    font-size: .84rem;
}
.notif-toggle__check {
    width: 48px;
    height: 28px;
    position: relative;
    flex-shrink: 0;
}
.notif-toggle__check input {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.notif-toggle__check span {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #d0d5dd;
    transition: .18s ease;
}
.notif-toggle__check span::after {
    content: "";
    position: absolute;
    width: 22px;
    height: 22px;
    left: 3px;
    top: 3px;
    border-radius: 50%;
    background: #fff;
    transition: .18s ease;
    box-shadow: 0 2px 6px rgba(15, 23, 42, .2);
}
.notif-toggle__check input:checked + span {
    background: #f5c84c;
}
.notif-toggle__check input:checked + span::after {
    transform: translateX(20px);
}
.notif-settings-actions {
    padding-top: 22px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.notif-settings-btn {
    border: 0;
    border-radius: 10px;
    padding: 11px 22px;
    font-weight: 700;
}
.notif-settings-btn--primary {
    background: #f5c84c;
    color: #1b2430;
}
.notif-settings-btn--secondary {
    background: #eef2f6;
    color: #344054;
    text-decoration: none;
}
@media (max-width: 991px) {
    .notif-settings-wrap { grid-template-columns: 1fr; }
}
</style>

@php
    $toggleGroups = [
        [
            'title' => 'Bình luận',
            'description' => 'Thông báo khi có tương tác liên quan đến bình luận của bạn.',
            'items' => [
                ['field' => 'notify_comment_new', 'label' => 'Bình luận mới', 'text' => 'Khi bài viết của bạn nhận thêm bình luận mới.'],
                ['field' => 'notify_comment_reply', 'label' => 'Phản hồi bình luận', 'text' => 'Khi ai đó trả lời bình luận của bạn.'],
                ['field' => 'notify_comment_upvote', 'label' => 'Lượt thích bình luận', 'text' => 'Khi bình luận của bạn được upvote.'],
                ['field' => 'notify_comment_downvote', 'label' => 'Đánh giá tiêu cực', 'text' => 'Khi bình luận của bạn bị downvote.'],
            ],
        ],
        [
            'title' => 'Nội dung',
            'description' => 'Thông báo về đánh giá, yêu thích và trạng thái bài viết.',
            'items' => [
                ['field' => 'notify_news_rated', 'label' => 'Đánh giá bài viết', 'text' => 'Khi bài viết nhận thêm đánh giá mới.'],
                ['field' => 'notify_news_favorited', 'label' => 'Yêu thích bài viết', 'text' => 'Khi bài viết được lưu vào danh sách yêu thích.'],
                ['field' => 'notify_news_approved', 'label' => 'Bài viết được duyệt', 'text' => 'Khi bài viết được chấp nhận xuất bản.'],
                ['field' => 'notify_news_rejected', 'label' => 'Bài viết bị từ chối', 'text' => 'Khi bài viết không được duyệt.'],
            ],
        ],
        [
            'title' => 'Hệ thống',
            'description' => 'Thông báo chung từ website và hệ thống quản trị.',
            'items' => [
                ['field' => 'notify_system', 'label' => 'Thông báo hệ thống', 'text' => 'Thông báo bảo trì, nhắc việc và cập nhật quan trọng.'],
            ],
        ],
    ];
@endphp

<div class="notif-settings-page">
    <div class="container">
        <div class="notif-settings-wrap">
            <aside class="notif-settings-card notif-settings-side">
                <div class="notif-settings-side__name">{{ $user->fullname ?: $user->username }}</div>
                <div class="notif-settings-side__mail">{{ $user->email ?: 'Chưa cập nhật email' }}</div>

                <a href="{{ url('/tai-khoan') }}" class="notif-settings-side__link">
                    <i class="fas fa-user-circle"></i>
                    <span>Tài khoản của tôi</span>
                </a>
                <a href="{{ url('/thong-bao') }}" class="notif-settings-side__link">
                    <i class="fas fa-bell"></i>
                    <span>Danh sách thông báo</span>
                </a>
                <a href="{{ url('/thong-bao/cai-dat') }}" class="notif-settings-side__link is-active">
                    <i class="fas fa-sliders-h"></i>
                    <span>Cài đặt thông báo</span>
                </a>
            </aside>

            <section class="notif-settings-card">
                <div class="notif-settings-main__head">
                    <h1>Cài đặt thông báo</h1>
                    <p>Chọn loại thông báo bạn muốn tiếp tục nhận trong tài khoản này.</p>
                </div>

                <form class="notif-settings-form" action="{{ url('/thong-bao/cai-dat') }}" method="POST">
                    @csrf

                    @foreach($toggleGroups as $group)
                        <div class="notif-settings-group">
                            <div class="notif-settings-group__title">{{ $group['title'] }}</div>
                            <div class="notif-settings-group__desc">{{ $group['description'] }}</div>

                            @foreach($group['items'] as $item)
                                <label class="notif-toggle">
                                    <div class="notif-toggle__text">
                                        <strong>{{ $item['label'] }}</strong>
                                        <span>{{ $item['text'] }}</span>
                                    </div>
                                    <div class="notif-toggle__check">
                                        <input type="checkbox" name="{{ $item['field'] }}" value="1" {{ old($item['field'], $preferences->{$item['field']} ?? false) ? 'checked' : '' }}>
                                        <span></span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endforeach

                    <div class="notif-settings-actions">
                        <button type="submit" class="notif-settings-btn notif-settings-btn--primary">Lưu cài đặt</button>
                        <a href="{{ url('/thong-bao') }}" class="notif-settings-btn notif-settings-btn--secondary">Quay lại thông báo</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
