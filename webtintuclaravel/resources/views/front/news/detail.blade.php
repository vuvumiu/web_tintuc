@extends('front.template.master')
@section('title', $newsDetail->MetaTitle ?? $newsDetail->Name)
@section('description', $newsDetail->MetaDescription ?? '')
@section('keywords', $newsDetail->MetaKeyword ?? '')
@section('url', url('/'.$newsDetail->Alias.'.html'))
@php
    $newsDetailData = $newsDetail;
@endphp
<script>window.AI_NEWS_ID = {{ $newsDetail->RowID }};</script>

@if($newsDetail->NewsCatAlias)
    @section($newsDetail->NewsCatAlias, 'active')
@endif
@section('images', url('images/news/'.$newsDetail->Images))
@section('content')

<style>
    /* CSS variables scoped for news detail */
    :root {
        --news-bg: #1a1a1a;
        --news-bg-tertiary: #222222;
        --news-bg-elevated: #2a2a2a;
        --news-border: #2e2e2e;
        --news-border-default: #3a3a3a;
        --news-text: #ffffff;
        --news-text-sec: #cccccc;
        --news-text-dim: #888888;
        --news-accent: #ffd60a;
        --news-accent-glow: rgba(255, 214, 10, 0.2);
        --news-radius-xl: 20px;
        --news-radius-lg: 14px;
        --news-radius-md: 10px;
        --news-radius-full: 999px;
        --news-shadow-lg: 0 8px 32px rgba(0,0,0,0.4);
    }

    /* Dark theme overrides for detail page inline styles */
    .news-detail-hero-img,
    .article-container,
    .rating-card,
    .comments-section {
        background-color: var(--news-bg) !important;
        border-color: var(--news-border) !important;
        color: var(--news-text-sec);
    }

    .article-breadcrumb a,
    .article-meta-item a {
        color: var(--news-text-dim) !important;
    }
    .article-breadcrumb a:hover {
        color: var(--news-accent) !important;
    }
    .article-breadcrumb .current {
        color: var(--news-accent) !important;
    }

    .article-title {
        color: var(--news-text) !important;
    }

    .article-meta {
        border-top-color: var(--news-border) !important;
        border-bottom-color: var(--news-border) !important;
    }

    .article-meta-item,
    .article-meta-item i {
        color: var(--news-text-sec) !important;
    }
    .article-meta-item i {
        color: var(--news-accent) !important;
    }

    .view-count-badge {
        color: var(--news-text) !important;
    }

    .article-tags {
        margin: 24px 0;
    }

    .article-tag {
        background: #222 !important;
        color: var(--news-text-sec) !important;
        border-color: var(--news-border) !important;
    }
    .article-tag:hover {
        background: var(--news-accent) !important;
        color: #000 !important;
        border-color: var(--news-accent) !important;
    }

    .article-content {
        color: var(--news-text-sec) !important;
    }
    .article-content h2,
    .article-content h3,
    .article-content h4 {
        color: var(--news-text) !important;
    }
    .article-content a {
        color: var(--news-accent) !important;
    }
    .article-content blockquote {
        background: #222 !important;
        border-left-color: var(--news-accent) !important;
        color: var(--news-text-sec) !important;
    }

    .article-footer {
        border-top-color: var(--news-border) !important;
    }

    .rating-card,
    .rating-header {
        background: var(--news-bg) !important;
        border-color: var(--news-border) !important;
    }

    .rating-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rating-header-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 214, 10, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--news-accent) !important;
    }

    .rating-header-title {
        color: var(--news-text) !important;
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
    }

    .rating-badge {
        background: var(--news-accent) !important;
        color: #000 !important;
    }

    .rating-score-section {
        background: #222 !important;
        border-radius: 14px;
    }

    .rating-score {
        color: var(--news-text) !important;
    }

    .rating-stars i.empty {
        color: var(--news-text-dim) !important;
    }

    .rating-form {
        background: #222 !important;
    }

    .rating-form-hint {
        color: var(--news-text-sec) !important;
    }

    .rating-star-btn i {
        color: var(--news-text-dim) !important;
    }

    .rating-login-prompt {
        background: #222 !important;
        border-color: var(--news-border) !important;
    }

    .rating-login-prompt i {
        color: var(--news-text-dim) !important;
    }

    .rating-login-prompt-text strong {
        color: var(--news-text) !important;
    }

    .rating-login-prompt-text span {
        color: var(--news-text-sec) !important;
    }

    .rating-login-prompt-text a {
        color: var(--news-accent) !important;
    }

    .rating-distribution {
        border-top-color: var(--news-border) !important;
    }

    .rating-dist-title {
        color: var(--news-text-sec) !important;
    }

    .rating-dist-label {
        color: var(--news-text-sec) !important;
    }

    .rating-dist-count {
        color: var(--news-text-dim) !important;
    }

    .related-news-section {
        margin-top: 60px;
    }

    .related-news-title {
        color: var(--news-text) !important;
    }

    .related-news-card {
        background: #222 !important;
        border-color: var(--news-border) !important;
    }
    .related-news-card:hover {
        border-color: var(--news-accent) !important;
    }

    .related-news-card .rn-title {
        color: var(--news-text) !important;
    }
    .related-news-card:hover .rn-title {
        color: var(--news-accent) !important;
    }

    .related-news-card .rn-meta {
        color: var(--news-text-dim) !important;
    }
    .related-news-card .rn-meta i {
        color: var(--news-accent) !important;
    }

    .comments-section {
        margin-top: 40px;
    }

    .comments-header {
        background: #222 !important;
        border-bottom-color: var(--news-border) !important;
    }

    .comments-title {
        color: var(--news-text) !important;
    }

    .comments-badge {
        background: var(--news-accent) !important;
        color: #000 !important;
    }

    .comment-form-wrap {
        border-bottom-color: var(--news-border) !important;
    }

    .comment-avatar {
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
        color: #000;
    }

    .comment-input textarea {
        background: #222 !important;
        border-color: var(--news-border) !important;
        color: var(--news-text) !important;
    }
    .comment-input textarea::placeholder {
        color: var(--news-text-dim) !important;
    }
    .comment-input textarea:focus {
        border-color: var(--news-accent) !important;
    }

    .comment-login-prompt {
        background: #222 !important;
        border-color: var(--news-border) !important;
    }
    .comment-login-prompt i {
        color: var(--news-accent) !important;
    }
    .comment-login-prompt-text span {
        color: var(--news-text-sec) !important;
    }
    .comment-login-prompt-text a {
        color: var(--news-accent) !important;
    }

    .comment-item {
        border-bottom-color: var(--news-border) !important;
    }

    .comment-user-name {
        color: var(--news-text) !important;
    }

    .comment-admin-chip {
        background: var(--news-accent) !important;
        color: #000 !important;
    }

    .comment-text {
        color: var(--news-text-sec) !important;
    }

    .comment-meta {
        color: var(--news-text-dim) !important;
    }

    .comment-action-btn {
        color: var(--news-text-dim) !important;
    }
    .comment-action-btn:hover {
        color: var(--news-accent) !important;
        background: rgba(255, 214, 10, 0.1) !important;
    }

    .comment-edit-area textarea,
    .comment-reply-form textarea {
        background: #222 !important;
        border-color: var(--news-border) !important;
        color: var(--news-text) !important;
    }
    .comment-edit-area textarea:focus,
    .comment-reply-form textarea:focus {
        border-color: var(--news-accent) !important;
    }

    .comment-cancel-btn {
        background: #222 !important;
        color: var(--news-text-sec) !important;
        border-color: var(--news-border) !important;
    }

    .comment-empty {
        color: var(--news-text-dim) !important;
    }
    .comment-empty i {
        color: var(--news-text-dim) !important;
    }

    .modal-box {
        background: #1a1a1a !important;
        border-color: var(--news-border) !important;
    }
    .modal-box h5 {
        color: var(--news-text) !important;
    }
    .modal-box p {
        color: var(--news-text-sec) !important;
    }

    .modal-cancel-btn {
        background: #222 !important;
        color: var(--news-text) !important;
        border-color: var(--news-border) !important;
    }

    .toast-alert.success {
        background: rgba(34, 197, 94, 0.15);
        color: #22c55e;
        border-color: rgba(34, 197, 94, 0.3);
    }

    .toast-alert.error {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.3);
    }
.news-detail-hero {
    position: relative;
    padding: 0;
    margin-bottom: 40px;
    border-radius: var(--news-radius-xl);
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    overflow: hidden;
    background: #1a1a1a;
}

.news-detail-hero-img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    object-position: center;
    image-rendering: -webkit-optimize-quality;
    image-rendering: high-quality;
    transform: none;
    backface-visibility: hidden;
    display: block;
}

.news-detail-hero-img.lazy-loaded {
    animation: heroImgFadeIn 0.5s ease-out forwards;
}

@keyframes heroImgFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

@media (max-width: 768px) {
    .news-detail-hero-img {
        height: 280px;
        border-radius: var(--news-radius-lg);
    }
}

/* Article Container */
.article-container {
    background: var(--news-bg);
    border: 1px solid var(--news-border);
    border-radius: var(--news-radius-xl);
    padding: 40px;
    margin-bottom: 40px;
}

@media (max-width: 768px) {
    .article-container {
        padding: 24px 16px;
        border-radius: var(--news-radius-lg);
    }
}

/* Breadcrumb */
.article-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    font-size: 13px;
}

.article-breadcrumb a {
    color: var(--news-text-dim);
    transition: color var(--transition-fast);
}

.article-breadcrumb a:hover {
    color: var(--news-accent);
}

.article-breadcrumb span {
    color: var(--news-text-dim);
}

.article-breadcrumb .current {
    color: var(--news-accent);
    font-weight: 600;
}

/* Article Category Badge */
.article-category {
    display: inline-flex;
    align-items: center;
    gap: 6px;
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: var(--news-radius-full);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 16px;
}

/* Article Title */
.article-title {
    font-family: var(--font-heading);
    font-size: 2rem;
    font-weight: 800;
    color: var(--news-text);
    line-height: 1.3;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .article-title {
        font-size: 1.5rem;
    }
}

/* Article Meta */
.article-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 20px;
    padding: 16px 0;
    border-top: 1px solid var(--news-border);
    border-bottom: 1px solid var(--news-border);
    margin-bottom: 24px;
}

.article-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--news-text-sec);
}

.article-meta-item i {
    color: var(--news-accent);
    font-size: 14px;
}

.article-meta-item a {
    color: var(--news-text);
    font-weight: 600;
}

.article-meta-item a:hover {
    color: var(--news-accent);
}

/* View Count */
.view-count-badge {
    font-weight: 700;
    color: var(--news-text);
    transition: all 0.3s ease;
}

.view-count-badge.pop {
    animation: viewPop 0.5s cubic-bezier(.36, .07, .19, .97);
}

@keyframes viewPop {
    0% { transform: scale(1); }
    30% { transform: scale(1.35); color: var(--news-accent); }
    60% { transform: scale(.95); }
    100% { transform: scale(1); }
}

.view-increase-tag {
    display: inline-block;
    color: var(--news-accent);
    font-weight: 700;
    font-size: 14px;
    animation: viewFloat 0.9s ease-out forwards;
}

@keyframes viewFloat {
    0% { opacity: 1; transform: translateY(0); }
    60% { opacity: 1; transform: translateY(-8px); }
    100% { opacity: 0; transform: translateY(-16px); }
}

/* Favorite Button */
.fav-btn {
    background: transparent;
    color: #ef4444;
    border: 1px solid #ef4444;
    padding: 6px 16px;
    border-radius: var(--news-radius-full);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.fav-btn:hover {
    background: #ef4444;
    color: white;
}

.fav-btn.active {
    background: #ef4444;
    color: white;
}

/* Share buttons */
.share-buttons {
    display: flex;
    gap: 10px;
    margin-left: auto;
}

.share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--news-bg-tertiary);
    border: 1.5px solid var(--news-border);
    color: var(--news-text-sec);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    font-size: 1rem;
    position: relative;
}

.share-btn:hover {
    transform: translateY(-3px) scale(1.1);
}

.share-btn:active {
    transform: scale(0.92);
}

/* Facebook */
.share-btn.share-facebook:hover {
    background: #1877F2;
    border-color: #1877F2;
    color: white;
    box-shadow: 0 6px 20px rgba(24, 119, 242, 0.4);
}

/* Messenger */
.share-btn.share-messenger:hover {
    background: #0084FF;
    border-color: #0084FF;
    color: white;
    box-shadow: 0 6px 20px rgba(0, 132, 255, 0.4);
}

/* Twitter/X */
.share-btn.share-twitter:hover {
    background: #000000;
    border-color: #000000;
    color: white;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
}

/* Link */
.share-btn.share-link:hover {
    background: var(--news-accent);
    border-color: var(--news-accent);
    color: var(--news-bg);
    box-shadow: 0 6px 20px rgba(255, 214, 10, 0.4);
}

/* Tooltip */
.share-btn::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%) translateY(4px);
    background: #222;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
    font-family: sans-serif;
}

.share-btn::before {
    content: '';
    position: absolute;
    bottom: calc(100% + 3px);
    left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #222;
    opacity: 0;
    transition: all 0.2s ease;
}

.share-btn:hover::after,
.share-btn:hover::before {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

/* Ripple effect on click */
.share-btn .ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: scale(0);
    animation: shareRipple 0.6s ease-out;
    pointer-events: none;
}

@keyframes shareRipple {
    to {
        transform: scale(2.5);
        opacity: 0;
    }
}

/* Staggered entrance animation */
@keyframes shareSlideIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.share-btn {
    opacity: 0;
    animation: shareSlideIn 0.4s ease forwards;
}
.share-btn:nth-child(1) { animation-delay: 0.05s; }
.share-btn:nth-child(2) { animation-delay: 0.1s; }
.share-btn:nth-child(3) { animation-delay: 0.15s; }
.share-btn:nth-child(4) { animation-delay: 0.2s; }

/* Tags */
.comments-section,
.comment-section,
.comments-wrapper,
.comments-card {
    margin-top: 20px !important;
    margin-bottom: 64px !important;
}

.comments-body {
    margin-top: 0 !important;
    margin-bottom: 56px !important;
    padding-top: 14px !important;
    padding-bottom: 28px !important;
}

.comments-body + *,
.comments-section + *,
.comment-section + *,
.comments-wrapper + *,
.comments-card + * {
    margin-top: 48px !important;
}

.newsletter-section,
.newsletter-area,
.newsletter-wrap,
.newsletter-wrapper,
.newsletter-box {
    margin-top: 56px !important;
}

.related-news,
.related-news-section,
.related-posts,
.related-articles,
.related-posts-section {
    color: #1f2937 !important;
}

.related-news h2,
.related-news h3,
.related-news a,
.related-news-title,
.related-post-title,
.related-article-title,
.related-news-section h2,
.related-news-section h3,
.related-news-section a,
.related-posts h2,
.related-posts h3,
.related-posts a,
.related-articles h2,
.related-articles h3,
.related-articles a,
.related-posts-section h2,
.related-posts-section h3,
.related-posts-section a {
    color: #111827 !important;
}

.related-news a:hover,
.related-news-section a:hover,
.related-posts a:hover,
.related-articles a:hover,
.related-posts-section a:hover {
    color: #0d6efd !important;
}

html body .comments-section.comments-section,
html body .comment-section.comment-section,
html body .comments-wrapper.comments-wrapper,
html body .comments-card.comments-card {
    margin-top: 18px !important;
    margin-bottom: 76px !important;
}

html body .comments-body.comments-body {
    margin-top: 0 !important;
    margin-bottom: 68px !important;
    padding-top: 10px !important;
    padding-bottom: 34px !important;
}

html body .comments-body.comments-body + *,
html body .comments-section.comments-section + *,
html body .comment-section.comment-section + *,
html body .comments-wrapper.comments-wrapper + *,
html body .comments-card.comments-card + * {
    margin-top: 56px !important;
}

html body .newsletter-section.newsletter-section,
html body .newsletter-area.newsletter-area,
html body .newsletter-wrap.newsletter-wrap,
html body .newsletter-wrapper.newsletter-wrapper,
html body .newsletter-box.newsletter-box {
    margin-top: 64px !important;
}

html body .related-news.related-news,
html body .related-news-section.related-news-section,
html body .related-posts.related-posts,
html body .related-articles.related-articles,
html body .related-posts-section.related-posts-section {
    color: #1f2937 !important;
}

html body .related-news.related-news h2,
html body .related-news.related-news h3,
html body .related-news.related-news a,
html body .related-news-title.related-news-title,
html body .related-post-title.related-post-title,
html body .related-article-title.related-article-title,
html body .related-news-section.related-news-section h2,
html body .related-news-section.related-news-section h3,
html body .related-news-section.related-news-section a,
html body .related-posts.related-posts h2,
html body .related-posts.related-posts h3,
html body .related-posts.related-posts a,
html body .related-articles.related-articles h2,
html body .related-articles.related-articles h3,
html body .related-articles.related-articles a,
html body .related-posts-section.related-posts-section h2,
html body .related-posts-section.related-posts-section h3,
html body .related-posts-section.related-posts-section a {
    color: #111827 !important;
}

.article-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 24px 0;
}

.article-tag {
    background: var(--news-bg-tertiary);
    color: var(--news-text-sec);
    padding: 6px 14px;
    border-radius: var(--news-radius-full);
    font-size: 12px;
    font-weight: 500;
    border: 1px solid var(--news-border);
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.article-tag:hover {
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
    color: white;
    border-color: var(--news-accent);
}

/* Article Content */
.article-content {
    color: var(--news-text);
    line-height: 1.8;
    font-size: 16px;
}

.article-content p {
    margin-bottom: 20px;
}

.article-content img {
    max-width: 100%;
    border-radius: var(--news-radius-lg);
    margin: 24px 0;
    image-rendering: -webkit-optimize-quality;
    image-rendering: high-quality;
}

.article-content h2,
.article-content h3,
.article-content h4 {
    font-family: var(--font-heading);
    color: var(--news-text);
    margin-top: 32px;
    margin-bottom: 16px;
}

.article-content a {
    color: var(--news-accent);
    text-decoration: underline;
}

.article-content a:hover {
    color: var(--news-accent);
}

.article-content blockquote {
    background: var(--news-bg-tertiary);
    border-left: 4px solid var(--news-accent);
    padding: 20px 24px;
    margin: 24px 0;
    border-radius: 0 var(--news-radius-lg) var(--news-radius-lg) 0;
    font-style: italic;
    color: var(--news-text-sec);
}

/* Article Footer */
.article-footer {
    margin-top: 40px;
    padding-top: 24px;
    border-top: 1px solid var(--news-border);
}

/* Rating Section */
.rating-card {
    background: var(--news-bg);
    border: 1px solid var(--news-border);
    border-radius: var(--news-radius-xl);
    overflow: hidden;
    margin-bottom: 60px;
}

.rating-header {
    background: var(--news-bg-tertiary);
    padding: 24px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--news-border);
}

.rating-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.rating-header-icon {
    width: 48px;
    height: 48px;
    background: rgba(255, 77, 0, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--news-accent);
    font-size: 1.2rem;
}

.rating-header-title {
    color: var(--news-text);
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
}

.rating-badge {
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
    color: white;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 16px;
    border-radius: var(--news-radius-full);
}

.rating-body {
    padding: 32px;
}

.rating-score-section {
    text-align: center;
    padding: 32px 24px;
    background: var(--news-bg-tertiary);
    border-radius: var(--news-radius-lg);
}

.rating-score {
    font-size: 4.5rem;
    font-weight: 800;
    color: var(--news-text);
    line-height: 1;
    margin-bottom: 12px;
}

.rating-stars {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-bottom: 12px;
}

.rating-stars i {
    font-size: 1.4rem;
}

.rating-stars i.filled {
    color: #ffc107;
}

.rating-stars i.empty {
    color: var(--news-text-dim);
}

.rating-count {
    color: var(--news-text-sec);
    font-size: 14px;
}

/* Rating Form */
.rating-form {
    padding: 28px;
    background: var(--news-bg-tertiary);
    border-radius: var(--news-radius-lg);
}

.rating-form-hint {
    color: var(--news-text-sec);
    font-size: 14px;
    margin-bottom: 20px;
}

.rating-stars-interactive {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.rating-star-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px 6px;
    transition: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.rating-star-btn i {
    font-size: 2.4rem;
    color: var(--news-text-dim);
}

.star-filled {
    color: #ffc107 !important;
}

.rating-star-btn.active i,
.rating-star-btn.is-hovered i {
    color: #ffc107 !important;
}

.rating-star-btn:hover i {
    color: #ffc107 !important;
}

.rating-login-prompt {
    background: var(--news-bg-tertiary);
    border: 1px solid var(--news-border-default);
    border-radius: var(--news-radius-lg);
    padding: 24px 28px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.rating-login-prompt i {
    color: var(--news-text-dim);
    font-size: 1.75rem;
}

.rating-login-prompt-text strong {
    display: block;
    color: var(--news-text);
    margin-bottom: 6px;
    font-size: 1.05rem;
}

.rating-login-prompt-text span {
    color: var(--news-text-sec);
    font-size: 14px;
}

.rating-login-prompt-text a {
    color: var(--news-accent);
    font-weight: 700;
}

/* Rating Distribution */
.rating-distribution {
    margin-top: 32px;
    padding-top: 28px;
    border-top: 1px solid var(--news-border);
}

.rating-dist-title {
    color: var(--news-text-sec);
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
}

.rating-dist-row {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 14px;
}

.rating-dist-label {
    width: 28px;
    text-align: right;
    color: var(--news-text-sec);
    font-size: 14px;
    font-weight: 600;
}

.rating-dist-bar-container {
    flex: 1;
    height: 12px;
    background: var(--news-bg-elevated);
    border-radius: var(--news-radius-full);
    overflow: hidden;
}

.rating-dist-bar {
    height: 100%;
    background: linear-gradient(90deg, #ffc107, #ffb300);
    border-radius: var(--news-radius-full);
    transition: width 0.4s ease;
}

.rating-dist-count {
    width: 36px;
    color: var(--news-text-dim);
    font-size: 13px;
    text-align: right;
}

/* Related News */
.related-news-section {
    margin-top: 80px;
    padding-bottom: 20px;
}

.related-news-title {
    font-family: var(--font-heading);
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--news-text);
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 14px;
}

.related-news-title i {
    color: var(--news-accent);
    font-size: 1.2rem;
}

.related-news-title::after {
    content: '';
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, var(--news-accent-glow), transparent);
    margin-left: 8px;
    border-radius: var(--news-radius-full);
}

.related-news-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}

.related-news-card {
    background: var(--news-bg-tertiary);
    border: 1px solid var(--news-border);
    border-radius: var(--news-radius-lg);
    overflow: hidden;
    transition: all var(--transition-base);
}

.related-news-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--news-shadow-lg);
    border-color: var(--news-accent);
}

.related-news-card a {
    text-decoration: none;
    color: inherit;
    display: block;
    height: 100%;
}

.related-news-card .rn-thumb {
    aspect-ratio: 16 / 10;
    overflow: hidden;
    background: linear-gradient(145deg, var(--news-bg-tertiary), var(--news-bg-elevated));
}

.related-news-card .rn-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
    image-rendering: -webkit-optimize-quality;
    image-rendering: high-quality;
}

.related-news-card:hover .rn-thumb img {
    transform: scale(1.06);
}

.related-news-card .rn-body {
    padding: 20px;
}

.related-news-card .rn-title {
    font-family: var(--font-heading);
    font-size: 15px;
    font-weight: 700;
    color: var(--news-text);
    line-height: 1.45;
    margin: 0 0 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color var(--transition-fast);
}

.related-news-card:hover .rn-title {
    color: var(--news-accent);
}

.related-news-card .rn-meta {
    font-size: 13px;
    color: var(--news-text-dim);
    display: flex;
    align-items: center;
    gap: 8px;
}

.related-news-card .rn-meta i {
    color: var(--news-accent);
}

@media (max-width: 992px) {
    .related-news-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .related-news-grid {
        grid-template-columns: 1fr;
    }
}

/* Comments Section */
.comments-section {
    background: var(--news-bg);
    border: 1px solid var(--news-border);
    border-radius: var(--news-radius-xl);
    overflow: hidden;
    margin-top: 40px;
}

.comments-header {
    background: var(--news-bg-tertiary);
    padding: 20px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--news-border);
}

.comments-title {
    color: var(--news-text);
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.comments-title i {
    color: var(--news-accent);
}

.comments-badge {
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
    color: white;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: var(--news-radius-full);
}

.comments-body {
    padding: 28px;
}

/* Comment Form */
.comment-form-wrap {
    display: flex;
    gap: 16px;
    margin-bottom: 28px;
    padding-bottom: 28px;
    border-bottom: 1px solid var(--news-border);
}

.comment-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.comment-input {
    flex: 1;
}

.comment-input textarea {
    width: 100%;
    background: var(--news-bg-tertiary);
    border: 1.5px solid var(--news-border-default);
    border-radius: var(--news-radius-lg);
    padding: 14px 18px;
    font-size: 14px;
    resize: none;
    min-height: 80px;
    color: var(--news-text);
    font-family: var(--font-primary);
    line-height: 1.5;
    transition: all var(--transition-base);
}

.comment-input textarea:focus {
    outline: none;
    border-color: var(--news-accent);
    box-shadow: 0 0 0 3px rgba(255, 214, 10, 0.2);
}

.comment-input textarea::placeholder {
    color: var(--news-text-dim);
}

.comment-form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 12px;
}

.comment-submit-btn {
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: var(--news-radius-md);
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.comment-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px var(--news-accent-glow);
}

/* Login Prompt */
.comment-login-prompt {
    background: var(--news-bg-tertiary);
    border: 1px solid var(--news-border-default);
    border-radius: var(--news-radius-lg);
    padding: 20px 24px;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.comment-login-prompt i {
    color: var(--news-accent);
    font-size: 1.5rem;
}

.comment-login-prompt-text span {
    color: var(--news-text-sec);
    font-size: 14px;
}

.comment-login-prompt-text a {
    color: var(--news-accent);
    font-weight: 700;
}

/* Comment List */
.comment-list {
    display: flex;
    flex-direction: column;
}

.comment-item {
    display: flex;
    gap: 16px;
    padding: 20px 0;
    border-bottom: 1px solid var(--news-border);
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-item-reply {
    padding-left: 64px;
    border-left: 2px solid var(--news-border-default);
    margin-left: 24px;
}

.comment-bubble {
    flex: 1;
}

.comment-user-name {
    font-weight: 700;
    font-size: 14px;
    color: var(--news-text);
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.comment-admin-chip {
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
    color: white;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: var(--news-radius-full);
    font-weight: 700;
}

.comment-text {
    font-size: 14px;
    color: var(--news-text-sec);
    line-height: 1.6;
    word-break: break-word;
    margin: 0 0 8px;
}

.comment-meta {
    font-size: 12px;
    color: var(--news-text-dim);
    display: flex;
    align-items: center;
    gap: 12px;
}

.comment-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    flex-wrap: wrap;
}

.comment-action-btn i.fa {
    color: #9ca3af !important;
}

.comment-action-btn {
    background: none;
    border: none;
    font-size: 12px;
    font-weight: 600;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px 10px;
    border-radius: var(--news-radius-md);
    transition: color 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.comment-action-btn:hover {
    background: rgba(255, 255, 255, 0.05);
}

/* Like active: xanh nổi bật */
.comment-action-btn.active-up,
.comment-action-btn.btn-vote.active-up {
    color: #4F9DFF !important;
}
.comment-action-btn.active-up i,
.comment-action-btn.btn-vote.active-up i {
    color: #4F9DFF !important;
}

/* Dislike active: đỏ nổi bật */
.comment-action-btn.active-down,
.comment-action-btn.btn-vote.active-down {
    color: #FF6B6B !important;
}
.comment-action-btn.active-down i,
.comment-action-btn.btn-vote.active-down i {
    color: #FF6B6B !important;
}

/* Comment Forms */
.comment-edit-area,
.comment-reply-form {
    margin-top: 12px;
    display: none;
}

.comment-edit-area.active,
.comment-reply-form.active {
    display: block;
}

.comment-edit-area textarea,
.comment-reply-form textarea {
    width: 100%;
    background: var(--news-bg-tertiary);
    border: 1.5px solid var(--news-border-default);
    border-radius: var(--news-radius-lg);
    padding: 12px 16px;
    font-size: 14px;
    resize: vertical;
    min-height: 70px;
    color: var(--news-text);
    font-family: var(--font-primary);
    transition: all var(--transition-base);
}

.comment-edit-area textarea:focus,
.comment-reply-form textarea:focus {
    outline: none;
    border-color: var(--news-accent);
    box-shadow: 0 0 0 3px rgba(255, 214, 10, 0.2);
}

.comment-edit-actions,
.comment-reply-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    margin-top: 10px;
}

.comment-edit-actions button,
.comment-reply-actions button {
    padding: 8px 18px;
    border-radius: var(--news-radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    border: none;
}

.comment-cancel-btn {
    background: var(--news-bg-tertiary);
    color: var(--news-text-sec);
    border: 1px solid var(--news-border-default);
}

.comment-cancel-btn:hover {
    background: var(--news-bg-elevated);
}

.comment-save-btn {
        background: linear-gradient(135deg, #ffd60a 0%, #ffaa00 100%);
    color: white;
}

.comment-save-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px var(--news-accent-glow);
}

/* Empty State */
.comment-empty {
    text-align: center;
    padding: 48px 20px;
}

.comment-empty i {
    font-size: 3rem;
    color: var(--news-text-dim);
    opacity: 0.3;
    margin-bottom: 16px;
    display: block;
}

.comment-empty p {
    color: var(--news-text-dim);
    font-size: 14px;
    margin: 0;
}

/* Pagination */
.comment-pagination {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}

/* Delete Modal */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9998;
    display: none;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

.modal-backdrop.show {
    display: flex;
}

.modal-box {
    background: var(--news-bg-elevated);
    border-radius: var(--news-radius-xl);
    padding: 32px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: var(--news-shadow-lg);
    border: 1px solid var(--news-border);
    animation: modalIn 0.2s ease-out;
}

@keyframes modalIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.modal-box h5 {
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--news-text);
    margin-bottom: 10px;
}

.modal-box p {
    color: var(--news-text-sec);
    font-size: 14px;
    margin-bottom: 28px;
    line-height: 1.5;
}

.modal-btns {
    display: flex;
    gap: 12px;
}

.modal-btns button {
    flex: 1;
    padding: 12px;
    border-radius: var(--news-radius-md);
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    border: none;
    transition: all var(--transition-base);
}

.modal-cancel-btn {
    background: var(--news-bg-tertiary);
    color: var(--news-text);
    border: 1px solid var(--news-border-default);
}

.modal-cancel-btn:hover {
    background: var(--news-bg-elevated);
}

.modal-confirm-btn {
    background: #ef4444;
    color: white;
}

.modal-confirm-btn:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* Toast */
.toast-alert {
    position: fixed !important;
    top: 24px !important;
    right: 24px !important;
    z-index: 100000 !important;
    border-radius: 12px;
    padding: 14px 20px;
    font-size: 14px;
    font-weight: 600;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    animation: toastIn 0.3s ease;
    max-width: 360px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.toast-alert.success {
    background: #1a1a2e !important;
    color: #4ade80 !important;
    border: 1px solid rgba(74, 222, 128, 0.3) !important;
}

.toast-alert.error {
    background: #1a1a2e !important;
    color: #f87171 !important;
    border: 1px solid rgba(248, 113, 113, 0.3) !important;
}

.toast-alert .toast-icon {
    font-size: 16px;
    flex-shrink: 0;
}

@keyframes toastIn {
    from { opacity: 0; transform: translateY(-12px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes toastOut {
    from { opacity: 1; transform: translateY(0) scale(1); }
    to { opacity: 0; transform: translateY(-12px) scale(0.95); }
}

/* New comment animation */
.comment-new-item {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

{{-- ====== ARTICLE HERO IMAGE ====== --}}
@if($newsDetail->Images)
@php
    $heroImageUrl = url('images/news/'.$newsDetail->Images);
    $heroAlt = $newsDetail->Name ?? 'Hình ảnh bài viết';
@endphp
<section class="container">
    <div class="news-detail-hero">
        <img src="{{ $heroImageUrl }}"
             alt="{{ $heroAlt }}"
             class="news-detail-hero-img"
             loading="eager"
             decoding="async"
             fetchpriority="high">
    </div>
</section>
@endif

{{-- ====== ARTICLE CONTENT ====== --}}
<div class="container">
    <article class="article-container">
        {{-- Breadcrumb --}}
        <nav class="article-breadcrumb">
            <a href="{{ url('/') }}"><i class="fas fa-home"></i> Trang chủ</a>
            <span>/</span>
            @if($newsDetail->NewsCatName)
                <a href="{{ url('/' . $newsDetail->NewsCatAlias) }}">{{ $newsDetail->NewsCatName }}</a>
                <span>/</span>
            @endif
            <span class="current">{{ Str::limit($newsDetail->Name, 50, '...') }}</span>
        </nav>

        {{-- Category Badge --}}
        @if($newsDetail->NewsCatName)
            <span class="article-category">
                <i class="fas fa-folder-open"></i>
                {{ $newsDetail->NewsCatName }}
            </span>
        @endif

        {{-- Title --}}
        <h1 class="article-title">{{ $newsDetail->Name }}</h1>

        {{-- Meta --}}
        <div class="article-meta">
            @if(!empty($newsDetail->AuthorName))
                <div class="article-meta-item">
                    <i class="fas fa-user-edit"></i>
                    <a href="#">{{ $newsDetail->AuthorName }}</a>
                </div>
            @endif
            <div class="article-meta-item">
                <i class="fas fa-calendar-alt"></i>
                {{ date('d/m/Y H:i', strtotime($newsDetail->created_at)) }}
            </div>
            <div class="article-meta-item">
                <i class="fas fa-eye"></i>
                <span id="viewCountDisplay" class="view-count-badge" data-count="{{ $newsDetail->Views }}" data-incremented="{{ isset($isNewView) && $isNewView ? '1' : '0' }}">
                    {{ number_format($newsDetail->Views) }}
                </span>
                <span id="viewIncreaseTag" class="view-increase-tag" style="display:none;">+1</span>
                lượt xem
            </div>
            @auth
                <div class="article-meta-item">
                    <button type="button" class="fav-btn {{ $newsDetail->user_favorite ? 'active' : '' }}" id="favBtn" data-news-id="{{ $newsDetail->RowID }}">
                        <i class="{{ $newsDetail->user_favorite ? 'fas' : 'far' }} fa-heart"></i>
                        <span id="favLabel">{{ $newsDetail->user_favorite ? 'Đã lưu' : 'Lưu bài' }}</span>
                    </button>
                </div>
            @endauth

            {{-- Share --}}
            <div class="share-buttons">
                <button class="share-btn share-facebook" data-tooltip="Facebook" onclick="shareFacebook()">
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button class="share-btn share-messenger" data-tooltip="Messenger" onclick="shareMessenger()">
                    <i class="fa-solid fa-message"></i>
                </button>
                <button class="share-btn share-twitter" data-tooltip="X (Twitter)" onclick="shareTwitter()">
                    <i class="fab fa-x-twitter"></i>
                </button>
                <button class="share-btn share-link" data-tooltip="Sao chép link" onclick="copyLink(this)">
                    <i class="fas fa-link"></i>
                </button>
            </div>
        </div>

        {{-- Tags --}}
        @if(isset($newsTags) && count($newsTags) > 0)
            <div class="article-tags">
                <i class="fas fa-tags" style="color: var(--news-text-dim); margin-right: 4px;"></i>
                @foreach($newsTags as $tag)
                    <a href="{{ url('tag/' . $tag->slug) }}" class="article-tag">
                        <i class="fas fa-tag"></i>
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Content --}}
        <div class="article-content" data-ai-selectable="1" data-ai-news-id="{{ $newsDetail->RowID }}">
            {!! $newsDetail->Description !!}
        </div>

        {{-- Article Footer --}}
        <div class="article-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                @if(isset($newsTags) && count($newsTags) > 0)
                    <div class="article-tags mb-0">
                        <i class="fas fa-tags" style="color: var(--news-text-dim); margin-right: 4px;"></i>
                        @foreach($newsTags as $tag)
                            <a href="{{ url('tag/' . $tag->slug) }}" class="article-tag">
                                <i class="fas fa-tag"></i>
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
                <div class="share-buttons mb-0" style="margin-left: auto;">
                    <button class="share-btn share-facebook" data-tooltip="Facebook" onclick="shareFacebook()">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button class="share-btn share-messenger" data-tooltip="Messenger" onclick="shareMessenger()">
                        <i class="fa-solid fa-message"></i>
                    </button>
                    <button class="share-btn share-twitter" data-tooltip="X (Twitter)" onclick="shareTwitter()">
                        <i class="fab fa-x-twitter"></i>
                    </button>
                    <button class="share-btn share-link" data-tooltip="Sao chép link" onclick="copyLink(this)">
                        <i class="fas fa-link"></i>
                    </button>
                </div>
            </div>
        </div>
    </article>

    {{-- ====== RELATED NEWS ====== --}}
    @if(isset($relatedNews) && count($relatedNews) > 0)
    <section class="related-news-section">
        <h3 class="related-news-title">
            <i class="fas fa-newspaper"></i>
            Bài viết liên quan
        </h3>
        <div class="related-news-grid">
            @foreach($relatedNews as $rel)
            <article class="related-news-card">
                <a href="{{ url($rel->Alias . '.html') }}">
                    @if($rel->Images && file_exists(public_path('images/news/' . $rel->Images)))
                        <div class="rn-thumb">
                            <img src="{{ asset('images/news/' . $rel->Images) }}" alt="{{ $rel->Name }}">
                        </div>
                    @else
                        <div class="rn-thumb d-flex align-items-center justify-content-center">
                            <i class="fas fa-newspaper" style="color: var(--news-text-dim); font-size: 2.5rem;"></i>
                        </div>
                    @endif
                    <div class="rn-body">
                        <h4 class="rn-title">{{ $rel->Name }}</h4>
                        <div class="rn-meta">
                            <i class="fas fa-eye"></i> {{ number_format($rel->Views) }} lượt xem
                        </div>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
    </section>
    @endif
</div>

{{-- ====== RATING SECTION ====== --}}
@php
    $avgRating = $newsDetail->average_rating;
    $totalRating = $newsDetail->total_rating;
    $userRating = $newsDetail->user_rating ?? 0;
    $scoreCounts = $newsDetail->rating_distribution ?? [1=>0,2=>0,3=>0,4=>0,5=>0];
    $maxCount = max(1, max(array_values($scoreCounts)));
@endphp
<div class="container">
    <div class="rating-card">
        <div class="rating-header">
            <div class="rating-header-left">
                <div class="rating-header-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h4 class="rating-header-title">Đánh giá bài viết</h4>
            </div>
            @if($totalRating > 0)
                <span class="rating-badge" id="ratingHeaderBadge">
                    <span id="ratingHeaderCount">{{ $totalRating }}</span> đánh giá
                </span>
            @endif
        </div>

        <div class="rating-body">
            <div class="row g-4">
                {{-- Score Display --}}
                <div class="col-md-4">
                    <div class="rating-score-section">
                        <div class="rating-score" id="avgScoreDisplay">
                            {{ number_format($avgRating, 1) }}
                        </div>
                        <div class="rating-stars" id="avgStarsRow">
                            @for($i=1;$i<=5;$i++)
                                <i class="fas fa-star {{ $i <= round($avgRating) ? 'filled' : 'empty' }}" data-avg-star="{{ $i }}"></i>
                            @endfor
                        </div>
                        <p class="rating-count" id="ratingSubtext">
                            {{ $totalRating > 0 ? $totalRating.' người đánh giá' : 'Chưa có đánh giá' }}
                        </p>
                    </div>
                </div>

                {{-- Interactive Rating --}}
                <div class="col-md-8">
                    @auth
                        <div class="rating-form">
                            <p class="rating-form-hint" id="ratingHint">
                                @if($userRating > 0)
                                    Bạn đã chọn <strong id="yourRatingLabel">{{ $userRating }}/5 sao</strong>.
                                @else
                                    Chọn số sao để gửi đánh giá của bạn.
                                @endif
                            </p>
                            <form id="ratingForm" data-news-id="{{ $newsDetail->RowID }}">
                                @csrf
                                <input type="hidden" name="news_id" value="{{ $newsDetail->RowID }}">
                                <input type="hidden" name="score" id="ratingScoreInput" value="{{ $userRating > 0 ? $userRating : 0 }}">
                                <div class="rating-stars-interactive" role="group" aria-label="Chọn số sao">
                                    @for($i=1;$i<=5;$i++)
                                        <button type="button" class="rating-star-btn {{ $i <= $userRating ? 'active' : '' }}"
                                                data-score="{{ $i }}"
                                                title="{{ $i }} sao"
                                                id="starBtn-{{ $i }}">
                                            <i class="{{ $i <= $userRating ? 'fas' : 'far' }} fa-star star-filled"
                                               id="starIcon-{{ $i }}"></i>
                                        </button>
                                    @endfor
                                </div>
                            </form>

                            {{-- Rating Distribution --}}
                            @if($totalRating > 0)
                            <div class="rating-distribution">
                                <p class="rating-dist-title">Phân bổ đánh giá</p>
                                @foreach([5,4,3,2,1] as $star)
                                    @php $pct = round((($scoreCounts[$star] ?? 0) / $maxCount) * 100); @endphp
                                    <div class="rating-dist-row" data-star="{{ $star }}">
                                        <span class="rating-dist-label">{{ $star }}</span>
                                        <i class="fas fa-star" style="color: #ffc107; font-size: 12px;"></i>
                                        <div class="rating-dist-bar-container">
                                            <div class="rating-dist-bar" style="width: {{ $pct }}%;"></div>
                                        </div>
                                        <span class="rating-dist-count">{{ $scoreCounts[$star] ?? 0 }}</span>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="rating-login-prompt">
                            <i class="fas fa-lock"></i>
                            <div class="rating-login-prompt-text">
                                <strong>Đăng nhập để đánh giá</strong>
                                <span>Thành viên mới gửi được đánh giá. </span>
                                <a href="{{ url('/dang-nhap') }}">Đăng nhập</a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ====== COMMENTS SECTION ====== --}}
<div class="container">
    <section class="comments-section" id="commentSection">
        <div class="comments-header">
            <h4 class="comments-title">
                <i class="far fa-comments"></i>
                Bình luận
            </h4>
            <span class="comments-badge" id="commentCountBadge" style="{{ !isset($comments) || $comments->count() == 0 ? 'display:none' : '' }}">
                {{ $comments->total() ?? 0 }} bình luận
            </span>
        </div>

        <div class="comments-body">
            {{-- Comment Form --}}
            @auth
                <form id="commentForm" data-news-id="{{ $newsDetail->RowID }}">
                    @csrf
                    <input type="hidden" name="news_id" value="{{ $newsDetail->RowID }}">
                    <div class="comment-form-wrap">
                        <div class="comment-avatar">
                            {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                        </div>
                        <div class="comment-input">
                            <textarea id="commentContentInput"
                                      name="content"
                                      rows="3"
                                      placeholder="Chia sẻ ý kiến của bạn về bài viết này…"
                                      maxlength="2000"></textarea>
                            <div class="comment-form-actions">
                                <button type="button" id="commentSubmitBtn" class="comment-submit-btn">
                                    <i class="fas fa-paper-plane"></i> Gửi bình luận
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="comment-login-prompt">
                    <i class="fas fa-comments"></i>
                    <div class="comment-login-prompt-text">
                        <span>Vui lòng <a href="{{ url('/dang-nhap') }}">đăng nhập</a> để thảo luận. Chưa có tài khoản? <a href="{{ url('/dang-ky') }}">Đăng ký</a>.</span>
                    </div>
                </div>
            @endauth

            {{-- Comment List --}}
            <div class="comment-list" id="commentList">
                @if(isset($comments) && $comments->count() > 0)
                    @foreach($comments as $comment)
                        @php
                            $isOwner = Auth::check() && Auth::id() == $comment->user_id;
                            $isAdmin = Auth::check() && Auth::user()->canAccessAdmin();
                            $isAdminCmt = $comment->user && $comment->user->canAccessAdmin();
                            $avatarBg = $isAdminCmt ? 'var(--news-accent)' : '#3b82f6';
                        @endphp
                        <div class="comment-item" data-comment-id="{{ $comment->id }}" id="comment-{{ $comment->id }}">
                            <input type="hidden" name="news_id" value="{{ $newsDetail->RowID }}">
                            <div class="comment-avatar" style="background: {{ $avatarBg }};">
                                {{ strtoupper(substr($comment->user->username, 0, 1)) }}
                            </div>
                            <div class="comment-bubble">
                                <div class="comment-user-name">
                                    {{ $comment->user->username }}
                                    @if($isAdminCmt)
                                        <span class="comment-admin-chip">Admin</span>
                                    @endif
                                </div>
                                <p class="comment-text" id="cmtText-{{ $comment->id }}">{{ $comment->content }}</p>
                                <div class="comment-meta">
                                    <span>{{ $comment->created_at instanceof \Carbon\Carbon ? $comment->created_at->diffForHumans() : $comment->created_at }}</span>
                                    @if($comment->created_at instanceof \Carbon\Carbon && $comment->updated_at instanceof \Carbon\Carbon && $comment->created_at->ne($comment->updated_at))
                                        <span>· Đã sửa</span>
                                    @endif
                                </div>
                                <div class="comment-actions">
                                    @auth
                                        <button type="button" class="comment-action-btn btn-vote @if(isset($commentVoteMap[$comment->id]) && $commentVoteMap[$comment->id] == 1) active-up @endif"
                                                data-comment-id="{{ $comment->id }}" data-vote-type="1" title="Thích">
                                            <i class="fas fa-thumbs-up fa-xs"></i>
                                            <span>{{ $comment->upvote_count ?? 0 }}</span>
                                        </button>
                                        <button type="button" class="comment-action-btn btn-vote @if(isset($commentVoteMap[$comment->id]) && $commentVoteMap[$comment->id] == -1) active-down @endif"
                                                data-comment-id="{{ $comment->id }}" data-vote-type="-1" title="Không thích">
                                            <i class="fas fa-thumbs-down fa-xs"></i>
                                            <span>{{ $comment->downvote_count ?? 0 }}</span>
                                        </button>
                                        <span style="color: var(--news-text-dim);">·</span>
                                        <button type="button" class="comment-action-btn btn-reply"
                                                data-parent-id="{{ $comment->id }}" data-username="{{ $comment->user->username }}">
                                            Phản hồi
                                        </button>
                                        @if($isOwner)
                                            <button type="button" class="comment-action-btn btn-edit" data-comment-id="{{ $comment->id }}">Sửa</button>
                                        @endif
                                        @if($isOwner || $isAdmin)
                                            <button type="button" class="comment-action-btn btn-delete"
                                                    data-comment-id="{{ $comment->id }}" data-username="{{ $comment->user->username }}">Xóa</button>
                                        @endif
                                    @endauth
                                </div>

                                {{-- Edit Form --}}
                                @if($isOwner)
                                    <div class="comment-edit-area" id="editArea-{{ $comment->id }}">
                                        <textarea id="editText-{{ $comment->id }}" maxlength="2000">{{ $comment->content }}</textarea>
                                        <div class="comment-edit-actions">
                                            <button type="button" class="comment-cancel-btn btn-cancel-edit" data-comment-id="{{ $comment->id }}">Hủy</button>
                                            <button type="button" class="comment-save-btn btn-save-edit" data-comment-id="{{ $comment->id }}">Lưu</button>
                                        </div>
                                    </div>
                                @endif

                                {{-- Reply Form --}}
                                @auth
                                    <div class="comment-reply-form" id="replyForm-{{ $comment->id }}">
                                        <textarea id="replyText-{{ $comment->id }}" maxlength="2000"
                                                  placeholder="Phản hồi {{ $comment->user->username }}…"></textarea>
                                        <div class="comment-reply-actions">
                                            <button type="button" class="comment-cancel-btn btn-cancel-reply" data-parent-id="{{ $comment->id }}">Hủy</button>
                                            <button type="button" class="comment-save-btn btn-submit-reply" data-parent-id="{{ $comment->id }}">Gửi</button>
                                        </div>
                                    </div>
                                @endauth

                                {{-- Replies --}}
                                @if($comment->replies->count() > 0)
                                    <div class="comment-list" style="margin-top: 16px;">
                                        @foreach($comment->replies as $reply)
                                            @php
                                                $rIsOwner = Auth::check() && Auth::id() == $reply->user_id;
                                                $rIsAdmin = Auth::check() && Auth::user()->canAccessAdmin();
                                                $rIsAdminR = $reply->user && $reply->user->canAccessAdmin();
                                                $rAvatarBg = $rIsAdminR ? 'var(--news-accent)' : '#64748b';
                                            @endphp
                                            <div class="comment-item" data-comment-id="{{ $reply->id }}" id="comment-{{ $reply->id }}"
                                                 style="padding: 16px 0; border-bottom: 1px solid var(--news-border);">
                                                <div class="comment-avatar" style="width: 36px; height: 36px; font-size: 0.85rem; background: {{ $rAvatarBg }};">
                                                    {{ strtoupper(substr($reply->user->username, 0, 1)) }}
                                                </div>
                                                <div class="comment-bubble">
                                                    <div class="comment-user-name" style="font-size: 13px;">
                                                        {{ $reply->user->username }}
                                                        @if($rIsAdminR)
                                                            <span class="comment-admin-chip">Admin</span>
                                                        @endif
                                                    </div>
                                                    <p class="comment-text" style="font-size: 13px;" id="cmtText-{{ $reply->id }}">{{ $reply->content }}</p>
                                                    <div class="comment-meta">
                                                        <span>{{ $reply->created_at instanceof \Carbon\Carbon ? $reply->created_at->diffForHumans() : $reply->created_at }}</span>
                                                    </div>
                                                    @auth
                                                        @if($rIsOwner || $rIsAdmin)
                                                            <div class="comment-actions" style="margin-top: 8px;">
                                                                <button type="button" class="comment-action-btn btn-vote @if(isset($commentVoteMap[$reply->id]) && $commentVoteMap[$reply->id] == 1) active-up @endif"
                                                                        data-comment-id="{{ $reply->id }}" data-vote-type="1">
                                                                    <i class="fas fa-thumbs-up fa-xs"></i> {{ $reply->upvote_count ?? 0 }}
                                                                </button>
                                                                <button type="button" class="comment-action-btn btn-vote @if(isset($commentVoteMap[$reply->id]) && $commentVoteMap[$reply->id] == -1) active-down @endif"
                                                                        data-comment-id="{{ $reply->id }}" data-vote-type="-1">
                                                                    <i class="fas fa-thumbs-down fa-xs"></i> {{ $reply->downvote_count ?? 0 }}
                                                                </button>
                                                                @if($rIsOwner)
                                                                    <button type="button" class="comment-action-btn btn-edit" data-comment-id="{{ $reply->id }}" style="font-size: 11px;">Sửa</button>
                                                                @endif
                                                                <button type="button" class="comment-action-btn btn-delete"
                                                                        data-comment-id="{{ $reply->id }}" data-username="{{ $reply->user->username }}" style="font-size: 11px;">Xóa</button>
                                                            </div>
                                                        @endif
                                                        @if($rIsOwner)
                                                            <div class="comment-edit-area" id="editArea-{{ $reply->id }}" style="margin-top: 10px;">
                                                                <textarea id="editText-{{ $reply->id }}" maxlength="2000" style="min-height: 56px;">{{ $reply->content }}</textarea>
                                                                <div class="comment-edit-actions">
                                                                    <button type="button" class="comment-cancel-btn btn-cancel-edit" data-comment-id="{{ $reply->id }}">Hủy</button>
                                                                    <button type="button" class="comment-save-btn btn-save-edit" data-comment-id="{{ $reply->id }}">Lưu</button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endauth
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                {{-- Empty State --}}
                <div id="noCommentsMsg" style="{{ isset($comments) && $comments->count() > 0 ? 'display:none' : '' }}">
                    <div class="comment-empty">
                        <i class="far fa-comment-dots"></i>
                        <p>Chưa có bình luận nào.<br>Hãy là người đầu tiên chia sẻ ý kiến!</p>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            @if(isset($comments) && $comments->hasPages())
                <nav class="comment-pagination">
                    {{ $comments->links('pagination::bootstrap-5') }}
                </nav>
            @endif
        </div>
    </section>
</div>

{{-- ====== DELETE MODAL ====== --}}
<div class="modal-backdrop" id="deleteModal">
    <div class="modal-box">
        <h5>Xóa bình luận?</h5>
        <p id="deleteModalText">Bạn có chắc muốn xóa bình luận này?<br>Hành động này không thể hoàn tác.</p>
        <div class="modal-btns">
            <button type="button" class="modal-cancel-btn" id="deleteCancelBtn">Hủy</button>
            <button type="button" class="modal-confirm-btn" id="deleteConfirmBtn">Xóa</button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="fbAlert" class="toast-alert" style="display:none;"></div>

@php
    $csrfToken = csrf_token();
@endphp

<script>
// Share functions
function shareFacebook() {
    var url = encodeURIComponent(window.location.href);
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, '_blank', 'width=600,height=400');
    showToast('Đang mở chia sẻ Facebook...', 'success');
}

function shareMessenger() {
    var url = encodeURIComponent(window.location.href);
    window.open('https://www.facebook.com/dialog/send?app_id=APP_ID&link=' + url, '_blank', 'width=600,height=400');
    showToast('Đang mở Messenger...', 'success');
}

function shareTwitter() {
    var url = encodeURIComponent(window.location.href);
    var text = encodeURIComponent(document.title);
    window.open('https://twitter.com/intent/tweet?url=' + url + '&text=' + text, '_blank', 'width=600,height=400');
    showToast('Đang mở chia sẻ Twitter...', 'success');
}

function shareZalo() {
    var url = encodeURIComponent(window.location.href);
    window.open('https://zalo.me/share?url=' + url, '_blank', 'width=600,height=500');
    showToast('Đang mở chia sẻ Zalo...', 'success');
}

function copyLink(btn) {
    navigator.clipboard.writeText(window.location.href).then(function() {
        showToast('Đã sao chép link!', 'success');
    }).catch(function() {
        showToast('Không thể sao chép link.', 'error');
    });
}

(function () {
    'use strict';
    var csrf = '{{ $csrfToken }}';
    var isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
    var loginUrl = @json(url('/dang-nhap'));
    var apiUrls = {
        binhLuan: @json(url('binh-luan')),
        binhLuanPhanHoi: @json(url('binh-luan/phan-hoi')),
        binhLuanSua: @json(url('binh-luan/sua')),
        binhLuanXoa: @json(url('binh-luan/xoa')),
        danhGiaSao: @json(url('danh-gia-sao')),
        binhLuanVote: @json(url('binh-luan/vote')),
        yeuThich: @json(url('yeu-thich')),
    };
    var currentNewsId = {{ $newsDetail->RowID }};

    // Helpers
    function ajax(url, data, onSuccess, onAlways) {
        var xhr = new XMLHttpRequest();
        var method = (data && data._method) ? data._method : 'POST';
        xhr.open(method, url, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function () {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.success) {
                    onSuccess(res);
                } else {
                    showToast(res.message || 'Có lỗi xảy ra.', 'error');
                }
            } catch (e) {
                showToast('Phản hồi không hợp lệ từ server.', 'error');
            }
            if (typeof onAlways === 'function') onAlways();
        };
        xhr.onerror = function () {
            showToast('Không thể kết nối server.', 'error');
            if (typeof onAlways === 'function') onAlways();
        };
        if (data instanceof FormData) {
            xhr.send(data);
        } else {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded;charset=UTF-8');
            xhr.send(Object.keys(data).map(function (k) {
                return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]);
            }).join('&'));
        }
    }

    function showToast(msg, type) {
        var el = document.getElementById('fbAlert');
        var icon = type === 'error'
            ? '<i class="fas fa-circle-xmark toast-icon"></i>'
            : '<i class="fas fa-circle-check toast-icon"></i>';
        el.innerHTML = icon + '<span>' + msg + '</span>';
        el.className = 'toast-alert ' + (type || 'success');
        el.style.display = 'flex';
        clearTimeout(el._timer);
        el._timer = setTimeout(function () {
            el.style.animation = 'toastOut 0.25s ease forwards';
            setTimeout(function () {
                el.style.display = 'none';
                el.style.animation = '';
            }, 250);
        }, 3000);
    }

    // Delete Modal
    var deleteTargetId = null;
    var deleteModal = document.getElementById('deleteModal');

    function openDeleteModal(commentId) {
        deleteTargetId = commentId;
        deleteModal.classList.add('show');
    }
    function closeDeleteModal() {
        deleteTargetId = null;
        deleteModal.classList.remove('show');
    }

    document.getElementById('deleteCancelBtn').addEventListener('click', closeDeleteModal);
    deleteModal.addEventListener('click', function (e) {
        if (e.target === deleteModal) closeDeleteModal();
    });
    document.getElementById('deleteConfirmBtn').addEventListener('click', function () {
        if (!deleteTargetId) return;
        var id = deleteTargetId;
        closeDeleteModal();
        ajax(apiUrls.binhLuanXoa + '/' + id, {_token: csrf, _method: 'DELETE'}, function (res) {
            showToast(res.message);
            var card = document.getElementById('comment-' + id);
            if (card) {
                card.style.transition = 'opacity .3s, transform .3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(.95)';
                setTimeout(function () {
                    card.remove();
                    updateEmptyState();
                }, 300);
            }
            var badge = document.getElementById('commentCountBadge');
            if (badge) {
                var n = parseInt(badge.textContent.replace(/\s.*/,'') || '1') - 1;
                badge.textContent = n + ' bình luận';
                badge.style.display = n > 0 ? '' : 'none';
            }
        });
    });

    // Comment Submit
    (function () {
        var textarea = document.getElementById('commentContentInput');
        var btn = document.getElementById('commentSubmitBtn');
        if (!textarea || !btn) return;

        btn.addEventListener('click', function () {
            var content = textarea.value.trim();
            if (!content) { showToast('Vui lòng nhập nội dung bình luận.', 'error'); return; }
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi…';
            function resetBtn() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi bình luận';
            }
            ajax(apiUrls.binhLuan, {
                _token: csrf,
                news_id: currentNewsId,
                content: content
            }, function (res) {
                textarea.value = '';
                var newHtml = buildCmtRow(res.comment, false);
                var noMsg = document.getElementById('noCommentsMsg');
                if (noMsg) noMsg.style.display = 'none';
                var list = document.getElementById('commentList');
                list.insertAdjacentHTML('afterbegin', '<div class="comment-item comment-new-item" id="comment-' + res.comment.id + '">' + newHtml + '</div>');
                bindNewCmtRow(document.getElementById('comment-' + res.comment.id));
                var badge = document.getElementById('commentCountBadge');
                if (badge) {
                    var n = parseInt(badge.textContent.replace(/\s.*/,'') || '0') + 1;
                    badge.textContent = n + ' bình luận';
                    badge.style.display = '';
                }
                showToast(res.message);
                resetBtn();
            }, resetBtn);
        });
    })();

    // Reply submit
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('btn-submit-reply')) return;
        var btn = e.target;
        var parentId = btn.getAttribute('data-parent-id');
        var textarea = document.getElementById('replyText-' + parentId);
        if (!textarea) return;
        var content = textarea.value.trim();
        if (!content) { showToast('Vui lòng nhập nội dung phản hồi.', 'error'); return; }
        btn.disabled = true;
        ajax(apiUrls.binhLuanPhanHoi, {
            _token: csrf,
            news_id: currentNewsId,
            parent_id: parentId,
            content: content
        }, function (res) {
            textarea.value = '';
            var wrap = document.getElementById('replyForm-' + parentId);
            if (wrap) wrap.classList.remove('active');
            var root = document.getElementById('comment-' + parentId);
            var bubble = root ? root.querySelector('.comment-bubble') : null;
            if (bubble) {
                var replyHtml = buildCmtRow(res.reply, true);
                var repliesWrap = bubble.querySelector('.comment-list');
                if (repliesWrap) {
                    repliesWrap.insertAdjacentHTML('beforeend', '<div class="comment-item" style="padding: 16px 0; border-bottom: 1px solid var(--news-border);" id="comment-' + res.reply.id + '">' + replyHtml + '</div>');
                    bindNewCmtRow(document.getElementById('comment-' + res.reply.id));
                } else {
                    var inner = '<div class="comment-list" style="margin-top:16px;">' +
                        '<div class="comment-item" style="padding: 16px 0; border-bottom: 1px solid var(--news-border);" id="comment-' + res.reply.id + '">' + replyHtml + '</div>' +
                        '</div>';
                    bubble.insertAdjacentHTML('beforeend', inner);
                    bindNewCmtRow(document.getElementById('comment-' + res.reply.id));
                }
            }
            showToast(res.message);
            btn.disabled = false;
        }, function () { btn.disabled = false; });
    });

    // Vote buttons
    var commentList = document.getElementById('commentList');
    if (commentList) {
        commentList.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-vote');
            if (!btn || btn.disabled) return;
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            var commentId = btn.getAttribute('data-comment-id');
            var voteType = parseInt(btn.getAttribute('data-vote-type'));
            var allBtns = commentList.querySelectorAll('.btn-vote[data-comment-id="' + commentId + '"]');
            allBtns.forEach(function (b) { b.disabled = true; });
            ajax(apiUrls.binhLuanVote, {
                _token: csrf,
                comment_id: commentId,
                vote_type: voteType
            }, function (res) {
                var upBtn = commentList.querySelector('.btn-vote[data-comment-id="' + commentId + '"][data-vote-type="1"]');
                var downBtn = commentList.querySelector('.btn-vote[data-comment-id="' + commentId + '"][data-vote-type="-1"]');
                if (upBtn) {
                    var upIcon = upBtn.querySelector('i');
                    var upSpan = upBtn.querySelector('span');
                    var downSpan = downBtn ? downBtn.querySelector('span') : null;
                    var downIcon = downBtn ? downBtn.querySelector('i') : null;

                    // Update vote counts
                    if (upSpan) upSpan.textContent = res.upvotes;
                    if (downSpan) downSpan.textContent = res.downvotes;

                    // Update active classes and colors
                    upBtn.classList.toggle('active-up', res.user_vote === 1);
                    upBtn.classList.toggle('active-down', false);
                    if (downBtn) {
                        downBtn.classList.toggle('active-down', res.user_vote === -1);
                        downBtn.classList.toggle('active-up', false);
                    }
                }
            }, function () {
                allBtns.forEach(function (b) { b.disabled = false; });
            });
        });
    }

    // Rating
    (function () {
        var form = document.getElementById('ratingForm');
        if (!form) return;
        var scoreInput = document.getElementById('ratingScoreInput');

        function updateStars(targetScore) {
            form.querySelectorAll('.rating-star-btn').forEach(function (b) {
                var s = parseInt(b.getAttribute('data-score'));
                var icon = b.querySelector('i');
                if (s <= targetScore) {
                    b.classList.add('active');
                    icon.className = 'fas fa-star star-filled';
                } else {
                    b.classList.remove('active');
                    icon.className = 'far fa-star';
                }
            });
        }

        form.querySelectorAll('.rating-star-btn').forEach(function (btn) {
            btn.addEventListener('mouseenter', function () {
                var hoverScore = parseInt(btn.getAttribute('data-score'));
                form.querySelectorAll('.rating-star-btn').forEach(function (b) {
                    var s = parseInt(b.getAttribute('data-score'));
                    var icon = b.querySelector('i');
                    if (s <= hoverScore) {
                        icon.className = 'fas fa-star star-filled';
                    } else {
                        icon.className = 'far fa-star';
                    }
                });
            });

            btn.addEventListener('mouseleave', function () {
                var currentScore = parseInt(scoreInput.value) || 0;
                updateStars(currentScore);
            });

            btn.addEventListener('click', function () {
                if (!isLoggedIn) { window.location.href = loginUrl; return; }
                var score = parseInt(btn.getAttribute('data-score'));
                scoreInput.value = score;
                updateStars(score);
                ajax(apiUrls.danhGiaSao, {
                    _token: csrf,
                    news_id: form.getAttribute('data-news-id'),
                    score: score
                }, function (res) {
                    var avgEl = document.getElementById('avgScoreDisplay');
                    if (avgEl) avgEl.textContent = Number(res.avg_score).toFixed(1);
                    var hint = document.getElementById('ratingHint');
                    if (hint) hint.innerHTML = 'Bạn đã chọn <strong id="yourRatingLabel">' + res.user_score + '/5 sao</strong>.';
                    var badge = document.getElementById('ratingHeaderBadge');
                    if (badge) badge.classList.remove('d-none');
                    var hc = document.getElementById('ratingHeaderCount');
                    if (hc) hc.textContent = res.total;
                    var sub = document.getElementById('ratingSubtext');
                    if (sub) sub.textContent = res.total + ' người đánh giá';
                    var starsRow = document.getElementById('avgStarsRow');
                    if (starsRow) {
                        var rounded = Math.round(parseFloat(res.avg_score));
                        starsRow.querySelectorAll('[data-avg-star]').forEach(function (icon) {
                            var i = parseInt(icon.getAttribute('data-avg-star'), 10);
                            icon.className = 'fas fa-star ' + (i <= rounded ? 'filled' : 'empty');
                        });
                    }
                    showToast(res.message, 'success');
                });
            });
        });
    })();

    // Reply toggle
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-reply');
        if (!btn) return;
        if (!isLoggedIn) { window.location.href = loginUrl; return; }
        var id = btn.getAttribute('data-parent-id');
        document.querySelectorAll('.comment-reply-form').forEach(function (w) { w.classList.remove('active'); });
        var wrap = document.getElementById('replyForm-' + id);
        if (wrap) {
            wrap.classList.add('active');
            setTimeout(function () {
                var t = document.getElementById('replyText-' + id);
                if (t) t.focus();
            }, 100);
        }
    });

    // Cancel reply
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('btn-cancel-reply')) return;
        var id = e.target.getAttribute('data-parent-id');
        var wrap = document.getElementById('replyForm-' + id);
        if (wrap) {
            wrap.classList.remove('active');
            var t = document.getElementById('replyText-' + id);
            if (t) t.value = '';
        }
    });

    // Edit toggle
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-edit');
        if (!btn) return;
        var id = btn.getAttribute('data-comment-id');
        document.querySelectorAll('.comment-edit-area').forEach(function (w) { w.classList.remove('active'); });
        var area = document.getElementById('editArea-' + id);
        if (area) area.classList.add('active');
        setTimeout(function () {
            var t = document.getElementById('editText-' + id);
            if (t) { t.focus(); t.setSelectionRange(t.value.length, t.value.length); }
        }, 50);
    });

    // Cancel edit
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('btn-cancel-edit')) return;
        var id = e.target.getAttribute('data-comment-id');
        var area = document.getElementById('editArea-' + id);
        if (area) area.classList.remove('active');
    });

    // Save edit
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('btn-save-edit')) return;
        var id = e.target.getAttribute('data-comment-id');
        var textarea = document.getElementById('editText-' + id);
        var content = textarea.value.trim();
        if (!content) { showToast('Nội dung không được trống.', 'error'); return; }
        ajax(apiUrls.binhLuanSua + '/' + id, {
            _token: csrf,
            _method: 'POST',
            content: content
        }, function (res) {
            var textEl = document.getElementById('cmtText-' + id);
            if (textEl) textEl.textContent = res.content;
            var area = document.getElementById('editArea-' + id);
            if (area) area.classList.remove('active');
            showToast(res.message);
        });
    });

    // Delete
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        if (!isLoggedIn) { window.location.href = loginUrl; return; }
        openDeleteModal(btn.getAttribute('data-comment-id'));
    });

    // Bind new rows
    function bindNewCmtRow(row) {
        if (!row) return;
        row.querySelectorAll('.btn-reply').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-parent-id');
                document.querySelectorAll('.comment-reply-form').forEach(function (w) { w.classList.remove('active'); });
                var wrap = document.getElementById('replyForm-' + id);
                if (wrap) { wrap.classList.add('active'); wrap.querySelector('textarea').focus(); }
            });
        });
        row.querySelectorAll('.btn-delete').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openDeleteModal(btn.getAttribute('data-comment-id'));
            });
        });
        row.querySelectorAll('.btn-edit').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-comment-id');
                document.querySelectorAll('.comment-edit-area').forEach(function (w) { w.classList.remove('active'); });
                var area = document.getElementById('editArea-' + id);
                if (area) area.classList.add('active');
            });
        });
    }

    document.querySelectorAll('.comment-item').forEach(bindNewCmtRow);

    // Empty state
    function updateEmptyState() {
        var list = document.getElementById('commentList');
        var noMsg = document.getElementById('noCommentsMsg');
        if (list.querySelectorAll('.comment-item').length === 0) {
            if (noMsg) noMsg.style.display = '';
        } else {
            if (noMsg) noMsg.style.display = 'none';
        }
    }

    // Build comment row
    function buildCmtRow(c, isReply) {
        var isOwner = {{ Auth::check() ? 'true' : 'false' }};
        var isAdmin = {{ Auth::check() ? (Auth::user()->canAccessAdmin() ? 'true' : 'false') : 'false' }};
        var avatarBg = '#3b82f6';
        var isAuthorAdmin = c.user && c.user.is_author_admin;
        if (isAuthorAdmin) avatarBg = 'var(--news-accent)';
        var avatarSize = isReply ? 'width:36px;height:36px;font-size:0.85rem;' : 'width:48px;height:48px;font-size:1.1rem;';
        var adminChip = isAuthorAdmin ? '<span class="comment-admin-chip">Admin</span>' : '';
        var voteActions = '';
        if (isLoggedIn) {
            var upActive = (c.user_vote === 1) ? ' active-up' : '';
            var downActive = (c.user_vote === -1) ? ' active-down' : '';
            voteActions = '<button type="button" class="comment-action-btn btn-vote' + upActive + '" data-comment-id="' + c.id + '" data-vote-type="1"><i class="fas fa-thumbs-up fa-xs"></i> <span>' + (c.upvotes || 0) + '</span></button>' +
                '<button type="button" class="comment-action-btn btn-vote' + downActive + '" data-comment-id="' + c.id + '" data-vote-type="-1"><i class="fas fa-thumbs-down fa-xs"></i> <span>' + (c.downvotes || 0) + '</span></button>' +
                '<span style="color:var(--news-text-dim);">·</span>';
        }
        var editActions = isOwner ? '<button type="button" class="comment-action-btn btn-edit" data-comment-id="' + c.id + '">Sửa</button>' : '';
        var deleteActions = (isOwner || isAdmin) ? '<button type="button" class="comment-action-btn btn-delete" data-comment-id="' + c.id + '">Xóa</button>' : '';
        var replyBtn = isLoggedIn ? '<button type="button" class="comment-action-btn btn-reply" data-parent-id="' + c.id + '">Phản hồi</button>' : '';
        var replyForm = isLoggedIn ? '<div class="comment-reply-form" id="replyForm-' + c.id + '"><textarea id="replyText-' + c.id + '" placeholder="Phản hồi..."></textarea><div class="comment-reply-actions"><button type="button" class="comment-cancel-btn btn-cancel-reply" data-parent-id="' + c.id + '">Hủy</button><button type="button" class="comment-save-btn btn-submit-reply" data-parent-id="' + c.id + '">Gửi</button></div></div>' : '';
        var editArea = isOwner ? '<div class="comment-edit-area" id="editArea-' + c.id + '"><textarea id="editText-' + c.id + '">' + escapeHtml(c.content) + '</textarea><div class="comment-edit-actions"><button type="button" class="comment-cancel-btn btn-cancel-edit" data-comment-id="' + c.id + '">Hủy</button><button type="button" class="comment-save-btn btn-save-edit" data-comment-id="' + c.id + '">Lưu</button></div></div>' : '';
        return '<input type="hidden" name="news_id" value="' + currentNewsId + '"><div class="comment-avatar" style="background:' + avatarBg + ';' + avatarSize + '">' + (c.user.initial || c.user.username[0].toUpperCase()) + '</div><div class="comment-bubble"><div class="comment-user-name">' + escapeHtml(c.user.username) + adminChip + '</div><p class="comment-text" id="cmtText-' + c.id + '">' + escapeHtml(c.content) + '</p><div class="comment-meta"><span>' + c.time_ago + '</span></div><div class="comment-actions">' + voteActions + replyBtn + editActions + deleteActions + '</div>' + editArea + replyForm + '</div>';
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // View count animation
    (function () {
        var viewBadge = document.getElementById('viewCountDisplay');
        var viewTag = document.getElementById('viewIncreaseTag');
        if (!viewBadge) return;
        var isNewView = viewBadge.getAttribute('data-incremented') === '1';
        if (!isNewView) return;
        var currentViews = parseInt(viewBadge.getAttribute('data-count'), 10) || 0;
        var start = Math.max(0, currentViews - 5);
        var end = currentViews;
        var step = 0;
        var total = end - start;
        if (total <= 0) return;
        viewBadge.classList.add('pop');
        var interval = setInterval(function () {
            step++;
            var val = start + Math.round((step / 10) * total);
            if (step >= 10) {
                val = end;
                clearInterval(interval);
                viewBadge.textContent = currentViews.toLocaleString('vi-VN');
            } else {
                viewBadge.textContent = val.toLocaleString('vi-VN');
            }
        }, 50);
        if (viewTag) {
            viewTag.style.display = 'inline-block';
            setTimeout(function () { if (viewTag) viewTag.style.display = 'none'; }, 1000);
        }
    })();

    // Favorite
    (function () {
        var favBtn = document.getElementById('favBtn');
        var favLabel = document.getElementById('favLabel');
        if (!favBtn) return;
        favBtn.addEventListener('click', function () {
            if (!isLoggedIn) { window.location.href = loginUrl; return; }
            var newsId = favBtn.getAttribute('data-news-id');
            favBtn.disabled = true;
            ajax(apiUrls.yeuThich, {
                _token: csrf,
                news_id: newsId
            }, function (res) {
                if (res.favorited) {
                    favBtn.classList.add('active');
                    favBtn.querySelector('i').classList.remove('far');
                    favBtn.querySelector('i').classList.add('fas');
                    if (favLabel) favLabel.textContent = 'Đã lưu';
                } else {
                    favBtn.classList.remove('active');
                    favBtn.querySelector('i').classList.remove('fas');
                    favBtn.querySelector('i').classList.add('far');
                    if (favLabel) favLabel.textContent = 'Lưu bài';
                }
                showToast(res.message);
            }, function () { favBtn.disabled = false; });
        });
    })();

})();
</script>
@stop
