@extends('back.template.master')

@php
    $fmt = fn ($value, $decimals = 0) => number_format((float) $value, $decimals);
    $pct = function ($part, $total, $decimals = 0) {
        $total = (float) $total;
        return $total > 0 ? round(((float) $part / $total) * 100, $decimals) : 0;
    };
    $stars = function ($score) {
        $score = max(0, min(5, (int) round((float) $score)));
        return str_repeat('★', $score) . str_repeat('☆', 5 - $score);
    };
    $starClass = fn ($score) => (float) $score >= 4 ? '' : ((float) $score >= 3 ? 'mid' : 'low');
    $abbr = function ($value) {
        $value = (float) $value;
        if ($value >= 1000000) {
            return rtrim(rtrim(number_format($value / 1000000, 1), '0'), '.') . 'M';
        }
        if ($value >= 1000) {
            return number_format($value);
        }
        return number_format($value);
    };

    $welcomeName = $adminShell['user']['name'] ?? (auth()->user()->fullname ?: auth()->user()->username ?: 'Admin');
    $brandName = $adminShell['brand']['name'] ?? 'SCF corporation';

    $newsTotal = (int) ($stats['news_total'] ?? 0);
    $publishedTotal = (int) ($stats['news_published'] ?? 0);
    $pendingTotal = (int) ($stats['news_pending'] ?? 0);
    $draftTotal = (int) ($stats['news_draft'] ?? 0);
    $memberTotal = (int) ($stats['members_total'] ?? 0);
    $newsletterTotal = (int) ($stats['newsletter_total'] ?? 0);
    $commentTotal = (int) ($stats['comment_total'] ?? 0);
    $commentPending = (int) ($stats['comment_pending'] ?? 0);
    $commentApproved = max(0, $commentTotal - $commentPending);
    $contactsNew = (int) ($stats['contacts_new'] ?? 0);

    $ratingDistributionRaw = array_replace([1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0], $ratingOverview['distribution'] ?? []);
    $ratingTotal = (int) ($ratingOverview['total'] ?? 0);
    $ratingAverage = (float) ($ratingOverview['average'] ?? 0);
    $positiveTotal = (int) ($ratingOverview['positive_total'] ?? (($ratingDistributionRaw[4] ?? 0) + ($ratingDistributionRaw[5] ?? 0)));
    $negativeTotal = (int) ($ratingOverview['negative_total'] ?? (($ratingDistributionRaw[1] ?? 0) + ($ratingDistributionRaw[2] ?? 0)));
    $neutralTotal = (int) ($ratingDistributionRaw[3] ?? 0);
    $positiveRate = $pct($positiveTotal, $ratingTotal);
    $negativeRate = $pct($negativeTotal, $ratingTotal);
    $neutralRate = $pct($neutralTotal, $ratingTotal);
    $maxRatingDist = max(1, (int) ($ratingOverview['max_distribution'] ?? max($ratingDistributionRaw)));

    $weeklyToday = (int) ($weeklyViews['today'] ?? 0);
    $weeklyTotal = (int) ($weeklyViews['week'] ?? 0);
    $weeklyMonth = (int) ($weeklyViews['month'] ?? 0);
    $weeklyAllTime = (int) ($weeklyViews['all_time'] ?? 0);

    $statusDistribution = array_replace([
        'published' => 0, 'pending' => 0, 'featured' => 0, 'hot' => 0,
    ], $statusDistribution ?? []);

    $categoryRows = collect($categoryRatingStats ?? [])->values();
    if ($categoryRows->isEmpty()) {
        $labels = $categoryStats['labels'] ?? [];
        $counts = $categoryStats['data'] ?? [];
        $colors = $categoryStats['colors'] ?? [];
        $categoryRows = collect($labels)->map(function ($name, $index) use ($counts, $colors) {
            return [
                'name' => $name,
                'news_count' => (int) ($counts[$index] ?? 0),
                'color' => $colors[$index] ?? '#d1a53d',
                'avg_rating' => 0, 'rating_count' => 0,
            ];
        });
    }

    $authorPalette = ['#d1a53d', '#60a5fa', '#34d399', '#a78bfa', '#f87171', '#fb923c', '#e879f9'];
    $authorRows = collect($authorPerformance ?? [])->values()->map(function ($author, $index) use ($authorPalette) {
        $author['color'] = $authorPalette[$index % count($authorPalette)];
        return $author;
    });

    $articleCategoryOptions = collect($topViewedArticles ?? [])->pluck('category')->filter()->unique()->values();
    $chartSeriesSafe = $chartSeries ?? ['month' => ['labels' => [], 'views' => [], 'posts' => [], 'ratings' => []]];
    $dailySeriesSafe = $dailySeries ?? ['dates' => [], 'labels' => [], 'views' => [], 'posts' => [], 'ratings' => []];

    $authorDataJson = $authorRows->map(function ($a) {
        return [
            'id' => (int) ($a['id'] ?? 0),
            'name' => (string) ($a['name'] ?? 'Không rõ'),
            'posts' => (int) ($a['posts'] ?? 0),
            'views' => (int) ($a['views'] ?? 0),
            'rating' => (float) ($a['avg_rating'] ?? 0),
            'positive' => (int) ($a['positive_ratings'] ?? 0),
            'negative' => (int) ($a['negative_ratings'] ?? 0),
            'positiveRate' => (float) ($a['positive_rate'] ?? 0),
            'negativeRate' => (float) ($a['negative_rate'] ?? 0),
            'totalRatings' => (int) ($a['total_ratings'] ?? 0),
            'color' => (string) ($a['color'] ?? '#d1a53d'),
            'trend' => ((float) ($a['negative_rate'] ?? 0) > (float) ($a['positive_rate'] ?? 0)) ? 'down' : (((float) ($a['positive_rate'] ?? 0) > 0) ? 'up' : 'stable'),
            'url' => url('admin/authors/detail/' . (int) ($a['id'] ?? 0)),
        ];
    })->values();

    $topPositiveAuthorsJson = collect($authorsTopRated ?? [])->map(function ($a, $index) use ($authorPalette) {
        return [
            'id' => (int) ($a['id'] ?? 0),
            'name' => (string) ($a['name'] ?? 'Không rõ'),
            'rating' => (float) ($a['avg_rating'] ?? 0),
            'totalRatings' => (int) ($a['total_ratings'] ?? 0),
            'positiveRate' => (float) ($a['positive_rate'] ?? 0),
            'color' => $authorPalette[$index % count($authorPalette)],
            'url' => url('admin/authors/detail/' . (int) ($a['id'] ?? 0)),
        ];
    })->values();

    $topNegativeAuthorsJson = collect($authorsLowestRated ?? [])->map(function ($a, $index) use ($authorPalette) {
        return [
            'id' => (int) ($a['id'] ?? 0),
            'name' => (string) ($a['name'] ?? 'Không rõ'),
            'rating' => (float) ($a['avg_rating'] ?? 0),
            'negative' => (int) ($a['negative_ratings'] ?? 0),
            'negativeRate' => (float) ($a['negative_rate'] ?? 0),
            'color' => $authorPalette[$index % count($authorPalette)],
            'url' => url('admin/authors/detail/' . (int) ($a['id'] ?? 0)),
        ];
    })->values();

    $topProductiveAuthorsJson = collect($mostProlificAuthors ?? [])->map(function ($a, $index) use ($authorPalette) {
        return [
            'id' => (int) ($a['id'] ?? 0),
            'name' => (string) ($a['name'] ?? 'Không rõ'),
            'posts' => (int) ($a['published_count'] ?? 0),
            'totalCount' => (int) ($a['total_count'] ?? 0),
            'draftCount' => (int) ($a['draft_count'] ?? 0),
            'color' => $authorPalette[$index % count($authorPalette)],
            'url' => url('admin/authors/detail/' . (int) ($a['id'] ?? 0)),
        ];
    })->values();

    $categoriesJson = $categoryRows->map(fn ($c) => [
        'name' => (string) ($c['name'] ?? 'Chưa phân loại'),
        'count' => (int) ($c['news_count'] ?? 0),
        'color' => (string) ($c['color'] ?? '#d1a53d'),
        'rating' => (float) ($c['avg_rating'] ?? 0),
    ])->values();

    $activitiesJson = collect($recentActivities ?? [])->map(function ($a) {
        $color = match ((string) ($a['icon_bg'] ?? 'gold')) {
            'blue' => '#60a5fa', 'green' => '#34d399', 'red' => '#f87171', default => '#d1a53d',
        };
        $icon = (string) ($a['icon'] ?? '');
        $label = str_contains($icon, 'comment') ? 'Bình luận mới' : (str_contains($icon, 'envelope') ? 'Liên hệ mới' : 'Bài viết mới');
        return [
            'label' => $label,
            'title' => trim(strip_tags((string) ($a['title'] ?? 'Hoạt động mới'))),
            'sub' => trim((string) ($a['subtitle'] ?? '') . ' · ' . (optional($a['time'] ?? null)->diffForHumans() ?? '')),
            'color' => $color,
            'url' => (string) ($a['link'] ?? '#'),
        ];
    })->values();

    $positiveArticlesJson = collect($topRatedArticles ?? [])->map(fn ($a) => [
        'id' => (int) ($a['id'] ?? 0),
        'title' => (string) ($a['title'] ?? 'Không có tiêu đề'),
        'rating' => (float) ($a['avg_score'] ?? 0),
        'totalRatings' => (int) ($a['total_ratings'] ?? 0),
        'author' => (string) ($a['author'] ?? ''),
        'url' => url('admin/news/edit/' . (int) ($a['id'] ?? 0)),
    ])->values();

    $negativeArticlesJson = collect($lowestRatedArticles ?? [])->map(fn ($a) => [
        'id' => (int) ($a['id'] ?? 0),
        'title' => (string) ($a['title'] ?? 'Không có tiêu đề'),
        'rating' => (float) ($a['avg_score'] ?? 0),
        'totalRatings' => (int) ($a['total_ratings'] ?? 0),
        'author' => (string) ($a['author'] ?? ''),
        'url' => url('admin/news/edit/' . (int) ($a['id'] ?? 0)),
    ])->values();

    $articlesJson = collect($topViewedArticles ?? [])->map(fn ($a) => [
        'id' => (int) ($a['id'] ?? 0),
        'title' => (string) ($a['title'] ?? 'Không có tiêu đề'),
        'cat' => \Illuminate\Support\Str::slug((string) ($a['category'] ?? '')),
        'catLabel' => (string) ($a['category'] ?? 'Chưa phân loại'),
        'author' => (string) ($a['author'] ?? 'Không rõ'),
        'views' => (int) ($a['views'] ?? 0),
        'comments' => (int) ($a['comments'] ?? 0),
        'rating' => (float) ($a['rating'] ?? 0),
        'totalRatings' => (int) ($a['total_ratings'] ?? 0),
        'date' => (string) ($a['date'] ?? ''),
        'status' => (string) ($a['status'] ?? 'published'),
        'url' => url('admin/news/edit/' . (int) ($a['id'] ?? 0)),
    ])->values();

    $statusChartData = [
        (int) ($statusDistribution['published'] ?? 0),
        (int) ($statusDistribution['pending'] ?? 0),
        (int) ($statusDistribution['featured'] ?? 0),
        (int) ($statusDistribution['hot'] ?? 0),
    ];

    $ratingTrendSafe = $ratingTrend ?? ['labels' => [], 'data' => []];
@endphp

@push('styles')
<style>
:root {
  --bg:#0d0f14;--surface:#13161d;--surface2:#1a1e28;--surface3:#212535;
  --border:rgba(255,255,255,0.06);--border-active:rgba(209,165,61,0.4);
  --text:#e8eaf0;--text-muted:#6b7280;--text-dim:#9ca3af;
  --gold:#d1a53d;--gold-light:#f0c060;--gold-dim:rgba(209,165,61,0.15);
  --green:#34d399;--green-dim:rgba(52,211,153,0.12);
  --red:#f87171;--red-dim:rgba(248,113,113,0.12);
  --blue:#60a5fa;--blue-dim:rgba(96,165,250,0.12);
  --purple:#a78bfa;--purple-dim:rgba(167,139,250,0.12);
  --orange:#fb923c;--orange-dim:rgba(251,146,60,0.12);
  --radius:12px;--radius-sm:8px;
}
.dashboard-content { display:flex; flex-direction:column; gap:20px; }
.header-row { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; }
.greeting h1 { font-size:22px; font-weight:800; }
.greeting p { font-size:13px; color:var(--text-muted); margin-top:3px; }
.time-filter { display:flex; align-items:center; gap:0; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:4px; }
.tf-btn { padding:6px 14px; border-radius:var(--radius-sm); font-size:12px; font-weight:600; cursor:pointer; border:none; background:transparent; color:var(--text-muted); transition:all .15s; font-family:'Be Vietnam Pro',sans-serif; }
.tf-btn.active { background:var(--gold); color:#000; }
.tf-btn:hover:not(.active) { color:var(--text); }
.date-picker { padding:6px 12px; border-radius:var(--radius-sm); background:var(--surface2); border:1px solid var(--border); color:var(--text-dim); font-size:12px; cursor:pointer; font-family:'Be Vietnam Pro',sans-serif; }
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
.stat-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:18px; position:relative; overflow:hidden; transition:border-color .2s; display:block; text-decoration:none; color:var(--text-dim); }
.stat-card:hover { border-color:rgba(255,255,255,.12); }
.stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:var(--accent-color); opacity:.8; }
.stat-card.gold { --accent-color:var(--gold); }
.stat-card.green { --accent-color:var(--green); }
.stat-card.blue { --accent-color:var(--blue); }
.stat-card.red { --accent-color:var(--red); }
.stat-card.purple { --accent-color:var(--purple); }
.stat-card.orange { --accent-color:var(--orange); }
.stat-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
.stat-label { font-size:10px; letter-spacing:1px; font-weight:700; text-transform:uppercase; color:var(--text-muted); }
.stat-icon { width:32px; height:32px; border-radius:var(--radius-sm); display:flex; align-items:center; justify-content:center; font-size:15px; }
.stat-card.gold .stat-icon { background:var(--gold-dim); }
.stat-card.green .stat-icon { background:var(--green-dim); }
.stat-card.blue .stat-icon { background:var(--blue-dim); }
.stat-card.red .stat-icon { background:var(--red-dim); }
.stat-card.purple .stat-icon { background:var(--purple-dim); }
.stat-card.orange .stat-icon { background:var(--orange-dim); }
.stat-value { font-size:36px; font-weight:800; line-height:1; font-family:'JetBrains Mono',monospace; margin-bottom:6px; }
.stat-card.gold .stat-value { color:var(--gold); }
.stat-card.green .stat-value { color:var(--green); }
.stat-card.blue .stat-value { color:var(--blue); }
.stat-card.red .stat-value { color:var(--red); }
.stat-card.purple .stat-value { color:var(--purple); }
.stat-card.orange .stat-value { color:var(--orange); }
.stat-meta { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.stat-change { display:flex; align-items:center; gap:3px; font-size:11px; font-weight:600; padding:2px 7px; border-radius:4px; }
.up { background:var(--green-dim); color:var(--green); }
.down { background:var(--red-dim); color:var(--red); }
.stat-period { font-size:11px; color:var(--text-muted); }
.stat-sub { margin-top:10px; padding-top:10px; border-top:1px solid var(--border); display:flex; gap:12px; flex-wrap:wrap; }
.stat-sub-item { font-size:11px; color:var(--text-muted); }
.stat-sub-item span { color:var(--text-dim); font-weight:600; }
.stat-bar { margin-top:12px; height:3px; background:var(--surface3); border-radius:2px; overflow:hidden; }
.stat-card.gold .stat-bar-fill { background:var(--gold); }
.stat-card.green .stat-bar-fill { background:var(--green); }
.stat-card.blue .stat-bar-fill { background:var(--blue); }
.stat-card.red .stat-bar-fill { background:var(--red); }
.stat-card.purple .stat-bar-fill { background:var(--purple); }
.stat-card.orange .stat-bar-fill { background:var(--orange); }
.stat-bar-fill { height:100%; border-radius:2px; }
.section-divider { display:flex; align-items:center; gap:12px; margin:4px 0 2px; }
.section-divider-label { font-size:11px; letter-spacing:2px; text-transform:uppercase; font-weight:700; color:var(--text-muted); white-space:nowrap; }
.section-divider-line { flex:1; height:1px; background:var(--border); }
.section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.section-title { font-size:14px; font-weight:700; display:flex; align-items:center; gap:8px; }
.section-title .dot { width:6px; height:6px; border-radius:50%; background:var(--gold); }
.section-link { font-size:11px; color:var(--gold); cursor:pointer; background:none; border:none; font-family:inherit; font-weight:600; padding:0; }
.section-link:hover { text-decoration:underline; }
.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:18px; }
.chart-row { display:grid; grid-template-columns:2fr 1fr; gap:14px; }
.chart-wrap { height:240px; position:relative; }
.mini-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; }
.mini-stat { text-align:center; padding:10px; background:var(--surface2); border-radius:var(--radius-sm); border:1px solid var(--border); }
.mini-stat .val { font-size:22px; font-weight:800; font-family:'JetBrains Mono',monospace; color:var(--gold); }
.mini-stat .lbl { font-size:10px; color:var(--text-muted); margin-top:2px; }
.analytics-row { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:collapse; font-size:12.5px; }
thead th { text-align:left; font-size:10px; letter-spacing:.8px; text-transform:uppercase; color:var(--text-muted); font-weight:700; padding:0 8px 10px; border-bottom:1px solid var(--border); white-space:nowrap; }
tbody tr { border-bottom:1px solid var(--border); transition:background .12s; cursor:pointer; }
tbody tr:last-child { border-bottom:none; }
tbody tr:hover { background:var(--surface2); }
tbody td { padding:10px 8px; vertical-align:middle; }
.rank { font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:600; color:var(--text-muted); width:24px; }
.rank.top { color:var(--gold); }
.article-title { font-weight:600; color:var(--text); display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; max-width:260px; line-height:1.4; text-decoration:none; }
.tag { display:inline-block; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:600; white-space:nowrap; }
.tag-gold { background:var(--gold-dim); color:var(--gold); }
.tag-blue { background:var(--blue-dim); color:var(--blue); }
.tag-green { background:var(--green-dim); color:var(--green); }
.tag-red { background:var(--red-dim); color:var(--red); }
.tag-purple { background:var(--purple-dim); color:var(--purple); }
.tag-orange { background:var(--orange-dim); color:var(--orange); }
.num { font-family:'JetBrains Mono',monospace; font-weight:600; font-size:12px; }
.star-display { display:flex; align-items:center; gap:4px; white-space:nowrap; }
.stars { color:var(--gold); font-size:11px; letter-spacing:-1px; }
.stars.low { color:var(--red); }
.stars.mid { color:var(--orange); }
.star-val { font-family:'JetBrains Mono',monospace; font-size:11px; font-weight:600; }
.sentiment-bar { display:flex; height:5px; border-radius:3px; overflow:hidden; gap:1px; min-width:60px; }
.sentiment-bar .pos { background:var(--green); border-radius:3px 0 0 3px; }
.sentiment-bar .neg { background:var(--red); border-radius:0 3px 3px 0; }
.author-item { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid var(--border); text-decoration:none; color:inherit; }
.author-item:last-child { border-bottom:none; }
.author-avatar { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0; }
.author-info { flex:1; min-width:0; }
.author-name { font-size:12.5px; font-weight:600; }
.author-count { font-size:11px; color:var(--text-muted); }
.author-bar-wrap { width:60px; }
.author-bar { height:4px; background:var(--surface3); border-radius:2px; overflow:hidden; }
.author-bar-fill { height:100%; border-radius:2px; background:var(--gold); }
.author-views { font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--gold); font-weight:600; }
.cat-item { display:flex; align-items:center; gap:10px; padding:7px 0; }
.cat-name { font-size:12px; font-weight:600; min-width:90px; color:var(--text-dim); }
.cat-bar-wrap { flex:1; }
.cat-bar { height:6px; background:var(--surface3); border-radius:3px; overflow:hidden; }
.cat-bar-fill { height:100%; border-radius:3px; }
.cat-count { font-size:11px; font-family:'JetBrains Mono',monospace; font-weight:600; min-width:28px; text-align:right; }
.cat-pct { font-size:11px; color:var(--text-muted); min-width:34px; text-align:right; }
.activity-item { display:flex; gap:10px; align-items:flex-start; padding:10px 0; border-bottom:1px solid var(--border); text-decoration:none; color:inherit; }
.activity-item:last-child { border-bottom:none; }
.activity-dot { width:8px; height:8px; border-radius:50%; margin-top:5px; flex-shrink:0; }
.activity-body { flex:1; }
.activity-title { font-size:12.5px; font-weight:600; line-height:1.4; }
.activity-title em { font-style:normal; color:var(--gold); font-weight:700; }
.activity-meta { font-size:11px; color:var(--text-muted); margin-top:3px; }
.heatmap-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:3px; margin-top:10px; }
.hm-day { height:12px; border-radius:2px; background:var(--surface3); cursor:pointer; position:relative; }
.hm-day:hover::after { content:attr(data-tip); position:absolute; bottom:16px; left:50%; transform:translateX(-50%); background:var(--surface3); color:var(--text); font-size:10px; padding:3px 6px; border-radius:4px; white-space:nowrap; z-index:10; border:1px solid var(--border); }
.hm-labels { display:grid; grid-template-columns:repeat(7,1fr); gap:3px; margin-top:4px; }
.hm-label { font-size:9px; color:var(--text-muted); text-align:center; }
.status-row { display:flex; gap:8px; flex-wrap:wrap; }
.status-pill { display:flex; align-items:center; gap:6px; padding:6px 12px; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius-sm); font-size:12px; text-decoration:none; color:inherit; }
.pill-dot { width:7px; height:7px; border-radius:50%; }
.pill-count { font-weight:700; font-family:'JetBrains Mono',monospace; font-size:13px; }
.pill-label { color:var(--text-muted); }
.donut-wrap { display:flex; align-items:center; gap:16px; }
.donut-canvas { width:120px!important; height:120px!important; flex-shrink:0; }
.donut-legend { flex:1; display:flex; flex-direction:column; gap:6px; }
.legend-item { display:flex; align-items:center; gap:8px; font-size:12px; }
.legend-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.legend-name { flex:1; color:var(--text-dim); }
.legend-val { font-weight:700; font-family:'JetBrains Mono',monospace; }
.rating-overview { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
.rating-split-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:18px; }
.big-score { font-size:48px; font-weight:800; font-family:'JetBrains Mono',monospace; color:var(--gold); line-height:1; }
.big-stars { font-size:20px; color:var(--gold); letter-spacing:-2px; }
.big-stars.low { color:var(--red); }
.score-sub { font-size:12px; color:var(--text-muted); margin-top:4px; }
.rating-dist { display:flex; flex-direction:column; gap:5px; margin-top:14px; }
.rbar-row { display:flex; align-items:center; gap:8px; }
.rbar-label { font-size:10px; color:var(--text-muted); width:14px; text-align:right; flex-shrink:0; }
.rbar-track { flex:1; height:6px; background:var(--surface3); border-radius:3px; overflow:hidden; }
.rbar-fill-pos { height:100%; border-radius:3px; background:var(--green); }
.rbar-fill-neg { height:100%; border-radius:3px; background:var(--red); }
.rbar-fill-mid { height:100%; border-radius:3px; background:var(--gold); }
.rbar-count { font-size:10px; font-family:'JetBrains Mono',monospace; color:var(--text-muted); width:20px; text-align:right; }
.perf-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
.perf-card-inner { display:flex; flex-direction:column; gap:8px; }
.perf-item { display:flex; align-items:center; gap:10px; padding:9px; background:var(--surface2); border-radius:var(--radius-sm); border:1px solid var(--border); text-decoration:none; color:inherit; }
.perf-rank { font-size:11px; font-weight:800; font-family:'JetBrains Mono',monospace; width:20px; text-align:center; color:var(--text-muted); }
.perf-rank.g1 { color:var(--gold); }
.perf-rank.g2 { color:#aaa; }
.perf-rank.g3 { color:#a07850; }
.perf-body { flex:1; min-width:0; }
.perf-name { font-size:12px; font-weight:600; }
.perf-sub { font-size:10px; color:var(--text-muted); margin-top:2px; }
.perf-score { font-size:13px; font-weight:800; font-family:'JetBrains Mono',monospace; }
.tab-row { display:flex; gap:0; background:var(--surface2); border:1px solid var(--border); border-radius:var(--radius-sm); padding:3px; margin-bottom:14px; }
.tab-btn { flex:1; padding:5px 10px; border-radius:6px; border:none; background:transparent; color:var(--text-muted); font-size:11px; font-weight:600; cursor:pointer; font-family:'Be Vietnam Pro',sans-serif; transition:all .15s; }
.tab-btn.active { background:var(--surface); color:var(--text); box-shadow:0 1px 3px rgba(0,0,0,.3); }
@keyframes fadeUp { from{opacity:0;transform:translateY(12px);} to{opacity:1;transform:translateY(0);} }
.card, .stat-card, .rating-split-card { animation:fadeUp .35s ease both; }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
var authorData = @json($authorDataJson);
var topPositiveAuthors = @json($topPositiveAuthorsJson);
var topNegativeAuthors = @json($topNegativeAuthorsJson);
var topProductiveAuthors = @json($topProductiveAuthorsJson);
var categories = @json($categoriesJson);
var activities = @json($activitiesJson);
var positiveArticles = @json($positiveArticlesJson);
var negativeArticles = @json($negativeArticlesJson);
var articles = @json($articlesJson);
var chartData = @json($chartSeriesSafe);
var dailySeries = @json($dailySeriesSafe);
var statusChartData = @json($statusChartData);
var ratingTrend = @json($ratingTrendSafe);

function starStr(r){var f=Math.max(0,Math.min(5,Math.round(Number(r)||0)));return '★'.repeat(f)+'☆'.repeat(5-f);}
function starClass(r){r=Number(r)||0;return r>=4?'':r>=3?'mid':'low';}
function ratingColor(r){r=Number(r)||0;return r>=4?'var(--green)':r>=3?'var(--gold)':'var(--red)';}
function num(n){return new Intl.NumberFormat('en-US').format(Number(n)||0);}
function initial(name){var p=String(name||'A').trim().split(/\s+/);return (p[p.length-1]||p[0]||'A').charAt(0).toUpperCase();}
function esc(v){return String(v||'').replace(/[&<>"']/g,function(s){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[s];});}

function setTime(btn,range){
  document.querySelectorAll('.time-filter .tf-btn').forEach(function(b){b.classList.remove('active');});
  btn.classList.add('active');
  var now=new Date(),start=new Date(now);
  if(range==='today') start=new Date(now);
  else if(range==='week') start.setDate(now.getDate()-7);
  else if(range==='month') start.setMonth(now.getMonth()-1);
  else if(range==='quarter') start.setMonth(now.getMonth()-3);
  else if(range==='year') start.setFullYear(now.getFullYear()-1);
  document.getElementById('startDate').value=start.toISOString().slice(0,10);
  document.getElementById('endDate').value=now.toISOString().slice(0,10);
  updateCharts(range);
}

function applyDateRange(){
  var start=document.getElementById('startDate').value,end=document.getElementById('endDate').value;
  var labels=[],views=[],posts=[],ratings=[];
  (dailySeries.dates||[]).forEach(function(date,i){
    if((start&&date<start)||(end&&date>end)) return;
    var parts=date.split('-');
    labels.push(parts.length===3?parts[2]+'/'+parts[1]:date);
    views.push((dailySeries.views||[])[i]||0);
    posts.push((dailySeries.posts||[])[i]||0);
    ratings.push((dailySeries.ratings||[])[i]||0);
  });
  buildMainChart({labels:labels,views:views,posts:posts,ratings:ratings});
}

function renderAuthors(){
  var sorted=[].concat(authorData).sort(function(a,b){return b.views-a.views;});
  var max=sorted[0]?sorted[0].views:1;
  var html=sorted.slice(0,5).map(function(a){
    return '<a class="author-item" href="'+a.url+'">'+
      '<div class="author-avatar" style="background:'+a.color+'22;color:'+a.color+'">'+initial(a.name)+'</div>'+
      '<div class="author-info"><div class="author-name">'+esc(a.name)+'</div><div class="author-count">'+num(a.posts)+' bài · '+(Number(a.rating)||0).toFixed(1)+'★</div></div>'+
      '<div class="author-bar-wrap"><div class="author-bar"><div class="author-bar-fill" style="width:'+(a.views/max*100).toFixed(0)+'%;background:'+a.color+'"></div></div></div>'+
      '<div class="author-views" style="color:'+a.color+'">'+num(a.views)+'</div></a>';
  }).join('');
  document.getElementById('author-list').innerHTML=html||'<div style="padding:12px;color:var(--text-muted);font-size:12px;">Chưa có dữ liệu tác giả.</div>';
}

function renderCategories(){
  var total=categories.reduce(function(s,c){return s+(Number(c.count)||0);},0)||1;
  var html=categories.map(function(c){
    return '<div class="cat-item">'+
      '<div class="cat-name">'+esc(c.name)+'</div>'+
      '<div class="cat-bar-wrap"><div class="cat-bar"><div class="cat-bar-fill" style="width:'+((c.count||0)/total*100).toFixed(0)+'%;background:'+c.color+'"></div></div></div>'+
      '<div class="cat-count" style="color:'+c.color+'">'+num(c.count)+'</div>'+
      '<div class="cat-pct">'+((c.count||0)/total*100).toFixed(0)+'%</div></div>';
  }).join('');
  document.getElementById('cat-list').innerHTML=html||'<div style="padding:12px;color:var(--text-muted);font-size:12px;">Chưa có dữ liệu thể loại.</div>';
}

function renderActivities(){
  var html=activities.map(function(a){
    return '<a class="activity-item" href="'+a.url+'">'+
      '<div class="activity-dot" style="background:'+a.color+'"></div>'+
      '<div class="activity-body">'+
        '<div class="activity-title">'+esc(a.label)+': <em>'+esc(a.title)+'</em></div>'+
        '<div class="activity-meta">'+esc(a.sub)+'</div></div></a>';
  }).join('');
  document.getElementById('activity-list').innerHTML=html||'<div style="padding:12px;color:var(--text-muted);font-size:12px;">Chưa có hoạt động gần đây.</div>';
}

var ratingMode='pos';
function switchRating(btn,mode){
  document.querySelectorAll('[onclick^="switchRating"]').forEach(function(b){b.classList.remove('active');});
  btn.classList.add('active');
  ratingMode=mode;
  renderRatingArticles();
}

function renderRatingArticles(){
  var isPos=ratingMode==='pos';
  var list=(isPos?positiveArticles:negativeArticles).slice(0,5);
  var col=isPos?'var(--green)':'var(--red)';
  var rankBg=isPos?'rgba(52,211,153,0.5)':'rgba(248,113,113,0.5)';
  var html=list.map(function(a,i){
    return '<a class="activity-item" href="'+a.url+'" style="padding:8px;">'+
      '<div style="width:24px;height:24px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;font-family:\'JetBrains Mono\',monospace;color:'+col+';background:'+rankBg+';flex-shrink:0;">'+(i+1)+'</div>'+
      '<div class="activity-body">'+
        '<div class="activity-title" style="font-size:11.5px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">'+esc(a.title)+'</div>'+
        '<div class="activity-meta" style="display:flex;gap:10px;margin-top:4px;">'+
          '<span style="color:'+ratingColor(a.rating)+';font-weight:700;">'+starStr(a.rating)+' '+(Number(a.rating)||0).toFixed(1)+'</span>'+
          '<span>'+num(a.totalRatings)+' đánh giá</span>'+
          '<span>'+esc(a.author)+'</span></div></div></a>';
  }).join('');
  document.getElementById('rating-articles-list').innerHTML=html||'<div style="padding:12px;color:var(--text-muted);font-size:12px;">Chưa có dữ liệu đánh giá.</div>';
}

function renderPerfCards(){
  var rankCls=function(i){return i===0?'g1':i===1?'g2':i===2?'g3':'';};
  var prodHtml=topProductiveAuthors.map(function(a,i){
    return '<a class="perf-item" href="'+a.url+'">'+
      '<div class="perf-rank '+rankCls(i)+'">'+(i+1)+'</div>'+
      '<div class="author-avatar" style="width:28px;height:28px;background:'+a.color+'22;color:'+a.color+';font-size:10px;">'+initial(a.name)+'</div>'+
      '<div class="perf-body"><div class="perf-name">'+esc(a.name)+'</div><div class="perf-sub">'+num(a.totalCount)+' tổng bài · '+num(a.draftCount)+' nháp</div></div>'+
      '<div class="perf-score" style="color:var(--gold)">'+num(a.posts)+'<span style="font-size:10px;color:var(--text-muted);font-family:\'Be Vietnam Pro\',sans-serif;font-weight:400;"> bài</span></div></a>';
  }).join('');
  document.getElementById('perf-productivity').innerHTML=prodHtml||'<div style="padding:12px;color:var(--text-muted);font-size:12px;">Chưa có dữ liệu tác giả.</div>';

  var topRHtml=topPositiveAuthors.map(function(a,i){
    return '<a class="perf-item" href="'+a.url+'">'+
      '<div class="perf-rank '+rankCls(i)+'">'+(i+1)+'</div>'+
      '<div class="author-avatar" style="width:28px;height:28px;background:'+a.color+'22;color:'+a.color+';font-size:10px;">'+initial(a.name)+'</div>'+
      '<div class="perf-body"><div class="perf-name">'+esc(a.name)+'</div><div class="perf-sub">'+(Number(a.rating)||0).toFixed(1)+'★ · '+num(a.totalRatings)+' đánh giá</div></div>'+
      '<div class="perf-score" style="color:var(--green)">'+(Number(a.positiveRate)||0).toFixed(1)+'%</div></a>';
  }).join('');
  document.getElementById('perf-top-rating').innerHTML=topRHtml||'<div style="padding:12px;color:var(--text-muted);font-size:12px;">Chưa có dữ liệu tích cực.</div>';

  var lowRHtml=topNegativeAuthors.map(function(a,i){
    return '<a class="perf-item" href="'+a.url+'">'+
      '<div class="perf-rank '+rankCls(i)+'">'+(i+1)+'</div>'+
      '<div class="author-avatar" style="width:28px;height:28px;background:'+a.color+'22;color:'+a.color+';font-size:10px;">'+initial(a.name)+'</div>'+
      '<div class="perf-body"><div class="perf-name">'+esc(a.name)+'</div><div class="perf-sub">'+(Number(a.rating)||0).toFixed(1)+'★ · '+num(a.negative)+' tiêu cực</div></div>'+
      '<div class="perf-score" style="color:var(--red)">'+(Number(a.negativeRate)||0).toFixed(1)+'%</div></a>';
  }).join('');
  document.getElementById('perf-low-rating').innerHTML=lowRHtml||'<div style="padding:12px;color:var(--text-muted);font-size:12px;">Chưa có dữ liệu tiêu cực.</div>';
}

function renderAuthorTable(){
  var sort=document.getElementById('author-sort').value;
  var sorted=[].concat(authorData).sort(function(a,b){
    if(sort==='posts') return b.posts-a.posts;
    if(sort==='views') return b.views-a.views;
    if(sort==='rating') return b.rating-a.rating;
    if(sort==='positive') return(Number(b.positiveRate)||0)-(Number(a.positiveRate)||0);
    return 0;
  });
  var trendIcon={up:'<span style="color:var(--green)">▲</span>',down:'<span style="color:var(--red)">▼</span>',stable:'<span style="color:var(--text-muted)">—</span>'};
  var html=sorted.map(function(a,i){
    var posPct=Number(a.positiveRate)||0,negPct=Number(a.negativeRate)||0,rc=ratingColor(a.rating),sc=starClass(a.rating);
    return '<tr onclick="location.href=\''+a.url+'\'">'+
      '<td><div class="rank '+(i<3?'top':'')+'">'+(i+1)+'</div></td>'+
      '<td><div style="display:flex;align-items:center;gap:8px;"><div class="author-avatar" style="width:28px;height:28px;background:'+a.color+'22;color:'+a.color+';font-size:10px;">'+initial(a.name)+'</div><span style="font-weight:600;font-size:12.5px;">'+esc(a.name)+'</span></div></td>'+
      '<td><div class="num" style="color:var(--gold)">'+num(a.posts)+'</div></td>'+
      '<td><div class="num">'+num(a.views)+'</div></td>'+
      '<td><div class="star-display"><span class="stars '+sc+'">'+starStr(a.rating)+'</span><span class="star-val" style="color:'+rc+'">'+(Number(a.rating)||0).toFixed(1)+'</span></div></td>'+
      '<td><span class="tag tag-green">'+num(a.positive)+' ('+posPct.toFixed(1)+'%)</span></td>'+
      '<td><span class="tag tag-red">'+num(a.negative)+' ('+negPct.toFixed(1)+'%)</span></td>'+
      '<td><div class="num">'+num(a.totalRatings)+'</div></td>'+
      '<td><div class="sentiment-bar"><div class="pos" style="width:'+Math.min(100,posPct)+'%"></div><div class="neg" style="width:'+Math.min(100,negPct)+'%"></div></div></td>'+
      '<td>'+(trendIcon[a.trend]||trendIcon.stable)+'</td></tr>';
  }).join('');
  document.getElementById('author-tbody').innerHTML=html||'<tr><td colspan="10" style="padding:12px;color:var(--text-muted);text-align:center;">Chưa có dữ liệu tác giả.</td></tr>';
}

var currentFilter='all';
function filterTable(){currentFilter=document.getElementById('article-filter').value;renderTable();}
function renderTable(){
  var filtered=currentFilter==='all'?articles:articles.filter(function(a){return a.cat===currentFilter;});
  var colors=['tag-gold','tag-blue','tag-green','tag-red','tag-purple','tag-orange'];
  var html=filtered.map(function(a,i){
    var tagClass=colors[i%colors.length],stCls=a.status==='published'?'tag-green':'tag-gold',stLabel=a.status==='published'?'Xuất bản':'Chờ duyệt',rc=ratingColor(a.rating),sc=starClass(a.rating);
    return '<tr onclick="location.href=\''+a.url+'\'">'+
      '<td><div class="rank '+(i<3?'top':'')+'">'+(i+1)+'</div></td>'+
      '<td><div class="article-title">'+esc(a.title)+'</div></td>'+
      '<td><span class="tag '+tagClass+'">'+esc(a.catLabel)+'</span></td>'+
      '<td style="font-size:12px;color:var(--text-dim)">'+esc(a.author)+'</td>'+
      '<td><div class="num" style="color:var(--gold)">'+num(a.views)+'</div></td>'+
      '<td><div class="num">'+num(a.comments)+'</div></td>'+
      '<td><div class="star-display"><span class="stars '+sc+'" style="font-size:10px;">'+starStr(a.rating)+'</span><span class="star-val" style="color:'+rc+'">'+(Number(a.rating)||0).toFixed(1)+'</span><span style="font-size:10px;color:var(--text-muted)">('+num(a.totalRatings)+')</span></div></td>'+
      '<td style="font-size:11px;color:var(--text-muted);white-space:nowrap">'+esc(a.date)+'</td>'+
      '<td><span class="tag '+stCls+'">'+stLabel+'</span></td></tr>';
  }).join('');
  document.getElementById('article-tbody').innerHTML=html||'<tr><td colspan="9" style="padding:12px;color:var(--text-muted);text-align:center;">Chưa có bài viết phù hợp.</td></tr>';
}

function renderHeatmap(){
  var posts=dailySeries.posts||[],ratings=dailySeries.ratings||[],dates=dailySeries.dates||[],start=Math.max(0,dates.length-28);
  var colors=['var(--surface3)','#d1a53d33','#d1a53d55','#d1a53d99','#d1a53d'];
  var html='';
  for(var i=start;i<dates.length;i++){var v=(posts[i]||0)+((ratings[i]||0)>0?1:0);html+='<div class="hm-day" style="background:'+colors[Math.min(v,4)]+'" data-tip="'+v+' bài"></div>';}
  document.getElementById('heatmap').innerHTML=html||'<div style="padding:12px;color:var(--text-muted);font-size:12px;grid-column:1/-1;">Chưa có dữ liệu</div>';
}

var mainChartInst=null,donutInst=null,catChartInst=null,radarInst=null,trendInst=null;
var currentMode='views',currentRange='month';

function chartSet(rs){return typeof rs==='object'?rs:(chartData[rs]||chartData.month||{labels:[],views:[],posts:[],ratings:[]});}
function buildMainChart(rs){
  var d=chartSet(rs),isRating=currentMode==='ratings';
  var vals=currentMode==='views'?d.views:currentMode==='posts'?d.posts:d.ratings;
  var label=currentMode==='views'?'Lượt xem':currentMode==='posts'?'Bài viết':'Điểm đánh giá TB';
  var col=currentMode==='views'?'#d1a53d':currentMode==='posts'?'#60a5fa':'#34d399';
  var ds=[{label:label,data:vals||[],backgroundColor:col+'30',borderColor:col,borderWidth:2,borderRadius:4,borderSkipped:false,type:isRating?'line':'bar',tension:.4,pointRadius:isRating?4:0,fill:false}];
  if(!isRating){ds.push({label:'',data:(vals||[]).map(function(v){return v*0.6;}),type:'line',borderColor:'rgba(96,165,250,.5)',borderWidth:1.5,pointRadius:0,tension:.4,fill:false});}
  var cfg={type:'bar',data:{labels:d.labels||[],datasets:ds},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'#1a1e28',borderColor:'rgba(255,255,255,.08)',borderWidth:1,titleColor:'#e8eaf0',bodyColor:col}},scales:{x:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6b7280',font:{size:11}}},y:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6b7280',font:{size:11}},beginAtZero:!isRating}}}};
  if(isRating){cfg.options.scales.y.min=1;cfg.options.scales.y.max=5;}
  if(mainChartInst)mainChartInst.destroy();
  mainChartInst=new Chart(document.getElementById('mainChart'),cfg);
}
function buildDonut(){
  if(donutInst)donutInst.destroy();
  donutInst=new Chart(document.getElementById('donutChart'),{type:'doughnut',data:{labels:['Xuất bản','Chờ duyệt','Nổi bật','Tin nóng'],datasets:[{data:statusChartData,backgroundColor:['#34d399','#d1a53d','#60a5fa','#a78bfa'],borderWidth:0,hoverOffset:4}]},options:{responsive:false,maintainAspectRatio:false,cutout:'70%',plugins:{legend:{display:false},tooltip:{backgroundColor:'#1a1e28',titleColor:'#e8eaf0',bodyColor:'#9ca3af'}}}});
}
function buildCatChart(){
  if(catChartInst)catChartInst.destroy();
  catChartInst=new Chart(document.getElementById('catChart'),{type:'bar',data:{labels:categories.map(function(c){return c.name;}),datasets:[{data:categories.map(function(c){return c.count;}),backgroundColor:categories.map(function(c){return c.color+'44';}),borderColor:categories.map(function(c){return c.color;}),borderWidth:2,borderRadius:4,borderSkipped:false}]},options:{indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{backgroundColor:'#1a1e28',titleColor:'#e8eaf0',bodyColor:'#9ca3af'}},scales:{x:{grid:{color:'rgba(255,255,255,.04)'},ticks:{color:'#6b7280',font:{size:10}}},y:{grid:{display:false},ticks:{color:'#9ca3af',font:{size:11}}}}}});
}
function buildRadar(){
  if(radarInst)radarInst.destroy();
  radarInst=new Chart(document.getElementById('radarChart'),{type:'radar',data:{labels:categories.map(function(c){return c.name;}),datasets:[{label:'Điểm TB',data:categories.map(function(c){return c.rating;}),backgroundColor:'rgba(209,165,61,.15)',borderColor:'#d1a53d',borderWidth:2,pointBackgroundColor:'#d1a53d',pointRadius:4}]},options:{responsive:true,maintainAspectRatio:false,scales:{r:{min:0,max:5,ticks:{stepSize:1,color:'#6b7280',font:{size:9},backdropColor:'transparent'},grid:{color:'rgba(255,255,255,.06)'},angleLines:{color:'rgba(255,255,255,.06)'},pointLabels:{color:'#9ca3af',font:{size:10}}}},plugins:{legend:{display:false},tooltip:{backgroundColor:'#1a1e28',titleColor:'#e8eaf0',bodyColor:'#d1a53d'}}}});
}
function buildRatingTrend(){
  if(trendInst)trendInst.destroy();
  trendInst=new Chart(document.getElementById('ratingTrendChart'),{type:'line',data:{labels:ratingTrend.labels||[],datasets:[{data:ratingTrend.data||[],borderColor:'#d1a53d',borderWidth:2,pointRadius:0,tension:.4,fill:true,backgroundColor:'rgba(209,165,61,.1)'}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{enabled:false}},scales:{x:{display:false},y:{display:false,min:0,max:5}}}});
}
function switchChart(btn,mode){
  document.querySelectorAll('.chart-row .tf-btn').forEach(function(b){b.classList.remove('active');});
  btn.classList.add('active');
  currentMode=mode;
  buildMainChart(currentRange);
}
function updateCharts(range){currentRange=range;buildMainChart(range);}

window.addEventListener('DOMContentLoaded',function(){
  renderAuthors();
  renderCategories();
  renderActivities();
  renderRatingArticles();
  renderPerfCards();
  renderAuthorTable();
  renderTable();
  renderHeatmap();
  buildMainChart('month');
  buildDonut();
  buildCatChart();
  buildRadar();
  buildRatingTrend();
  var now=new Date(),start=new Date(now);start.setMonth(now.getMonth()-1);
  document.getElementById('startDate').value=start.toISOString().slice(0,10);
  document.getElementById('endDate').value=now.toISOString().slice(0,10);
});
</script>
@endpush

@section('title', 'Dashboard')
@section('hide_admin_heading', '1')

@section('content')
<div class="dashboard-content">
    <div class="header-row">
        <div class="greeting">
            <h1>Xin chào, {{ $welcomeName }} 👋</h1>
            <p>Tổng quan hệ thống quản trị {{ $brandName }}</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <div class="time-filter">
                <button class="tf-btn" type="button" onclick="setTime(this,'today')">Hôm nay</button>
                <button class="tf-btn" type="button" onclick="setTime(this,'week')">Tuần này</button>
                <button class="tf-btn active" type="button" onclick="setTime(this,'month')">Tháng này</button>
                <button class="tf-btn" type="button" onclick="setTime(this,'quarter')">Quý</button>
                <button class="tf-btn" type="button" onclick="setTime(this,'year')">Năm</button>
            </div>
            <input type="date" class="date-picker" id="startDate" value="{{ now()->copy()->subMonth()->toDateString() }}">
            <span style="color:var(--text-muted);font-size:12px;">—</span>
            <input type="date" class="date-picker" id="endDate" value="{{ now()->toDateString() }}">
            <button class="tf-btn active" type="button" onclick="applyDateRange()" style="background:var(--gold);color:#000;">Áp dụng</button>
        </div>
    </div>

    <div class="mini-stats">
        <div class="mini-stat"><div class="val">{{ $abbr($weeklyToday) }}</div><div class="lbl">Hôm nay</div></div>
        <div class="mini-stat"><div class="val">{{ $abbr($weeklyTotal) }}</div><div class="lbl">Tuần này</div></div>
        <div class="mini-stat"><div class="val">{{ $abbr($weeklyMonth) }}</div><div class="lbl">Tháng này</div></div>
        <div class="mini-stat"><div class="val">{{ $abbr($weeklyAllTime) }}</div><div class="lbl">Lũy kế</div></div>
    </div>

    <div class="stats-grid">
        <a class="stat-card gold" href="{{ url('admin/news/list') }}">
            <div class="stat-header"><div class="stat-label">Tổng bài viết</div><div class="stat-icon">📝</div></div>
            <div class="stat-value">{{ $fmt($newsTotal) }}</div>
            <div class="stat-meta"><span class="stat-change up">▲ {{ $fmt($publishedTotal) }}</span><span class="stat-period">đã xuất bản</span></div>
            <div class="stat-sub">
                <div class="stat-sub-item">Chờ duyệt: <span>{{ $fmt($pendingTotal) }}</span></div>
                <div class="stat-sub-item">Đã xuất bản: <span>{{ $fmt($publishedTotal) }}</span></div>
                <div class="stat-sub-item">Nháp: <span>{{ $fmt($draftTotal) }}</span></div>
            </div>
            <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $pct($publishedTotal, max(1, $newsTotal)) }}%"></div></div>
        </a>

        <a class="stat-card blue" href="{{ url('admin/member/list') }}">
            <div class="stat-header"><div class="stat-label">Thành viên</div><div class="stat-icon">👤</div></div>
            <div class="stat-value">{{ $fmt($memberTotal) }}</div>
            <div class="stat-meta"><span class="stat-change up">▲ {{ $fmt($newsletterTotal) }} email</span><span class="stat-period">đăng ký nhận tin</span></div>
            <div class="stat-sub">
                <div class="stat-sub-item">Hoạt động: <span>{{ $fmt($memberTotal) }}</span></div>
                <div class="stat-sub-item">Email sub: <span>{{ $fmt($newsletterTotal) }}</span></div>
            </div>
            <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ min(100, max(10, $memberTotal * 8)) }}%"></div></div>
        </a>

        <a class="stat-card green" href="{{ url('admin/comment/list') }}">
            <div class="stat-header"><div class="stat-label">Bình luận</div><div class="stat-icon">💬</div></div>
            <div class="stat-value">{{ $fmt($commentTotal) }}</div>
            <div class="stat-meta"><span class="stat-change up">▲ {{ $fmt($commentPending) }} chờ duyệt</span></div>
            <div class="stat-sub">
                <div class="stat-sub-item">Đã duyệt: <span>{{ $fmt($commentApproved) }}</span></div>
                <div class="stat-sub-item">Spam: <span>0</span></div>
            </div>
            <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $pct($commentApproved, max(1, $commentTotal)) }}%"></div></div>
        </a>

        <a class="stat-card orange" href="{{ url('admin/rating/list') }}">
            <div class="stat-header"><div class="stat-label">Đánh giá sao</div><div class="stat-icon">⭐</div></div>
            <div class="stat-value">{{ number_format($ratingAverage, 1) }}</div>
            <div class="stat-meta"><span class="stat-change up">▲ {{ $fmt($ratingTotal) }} đánh giá</span><span class="stat-period">tổng cộng</span></div>
            <div class="stat-sub">
                <div class="stat-sub-item">Tích cực (4-5★): <span>{{ $fmt($positiveTotal) }}</span></div>
                <div class="stat-sub-item">Tiêu cực (1-2★): <span>{{ $fmt($negativeTotal) }}</span></div>
            </div>
            <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $positiveRate }}%"></div></div>
        </a>
    </div>

    <div class="status-row">
        <a class="status-pill" href="{{ url('admin/news/list') }}"><div class="pill-dot" style="background:var(--green)"></div><div class="pill-count">{{ $fmt($publishedTotal) }}</div><div class="pill-label">Đã xuất bản</div></a>
        <a class="status-pill" href="{{ url('admin/news-approval/queue') }}"><div class="pill-dot" style="background:var(--gold)"></div><div class="pill-count">{{ $fmt($pendingTotal) }}</div><div class="pill-label">Chờ duyệt</div></a>
        <a class="status-pill" href="{{ url('admin/contact/list') }}"><div class="pill-dot" style="background:var(--red)"></div><div class="pill-count">{{ $fmt($contactsNew) }}</div><div class="pill-label">Liên hệ chờ</div></a>
        <a class="status-pill" href="{{ url('admin/newsletter/list') }}"><div class="pill-dot" style="background:var(--blue)"></div><div class="pill-count">{{ $fmt($newsletterTotal) }}</div><div class="pill-label">KM chưa xử lý</div></a>
        <a class="status-pill" href="{{ url('admin/rating/list') }}"><div class="pill-dot" style="background:var(--orange)"></div><div class="pill-count">{{ number_format($ratingAverage, 1) }}★</div><div class="pill-label">Điểm TB toàn trang</div></a>
        <div class="status-pill"><div class="pill-dot" style="background:var(--purple)"></div><div class="pill-count">{{ $positiveRate }}%</div><div class="pill-label">Tỉ lệ tích cực</div></div>
    </div>

    <div class="chart-row">
        <div class="card">
            <div class="section-header">
                <div class="section-title"><div class="dot"></div> Lượt xem theo thời gian</div>
                <div style="display:flex;gap:6px;">
                    <button class="tf-btn active" style="padding:4px 10px;font-size:11px;" type="button" onclick="switchChart(this,'views')">Lượt xem</button>
                    <button class="tf-btn" style="padding:4px 10px;font-size:11px;" type="button" onclick="switchChart(this,'posts')">Bài viết</button>
                    <button class="tf-btn" style="padding:4px 10px;font-size:11px;" type="button" onclick="switchChart(this,'ratings')">Đánh giá</button>
                </div>
            </div>
            <div class="chart-wrap"><canvas id="mainChart"></canvas></div>
        </div>
        <div class="card">
            <div class="section-header"><div class="section-title"><div class="dot"></div> Phân bổ trạng thái</div></div>
            <div class="donut-wrap">
                <canvas id="donutChart" class="donut-canvas"></canvas>
                <div class="donut-legend">
                    <div class="legend-item"><div class="legend-dot" style="background:var(--green)"></div><div class="legend-name">Xuất bản</div><div class="legend-val">{{ $fmt($statusDistribution['published'] ?? 0) }}</div></div>
                    <div class="legend-item"><div class="legend-dot" style="background:var(--gold)"></div><div class="legend-name">Chờ duyệt</div><div class="legend-val">{{ $fmt($statusDistribution['pending'] ?? 0) }}</div></div>
                    <div class="legend-item"><div class="legend-dot" style="background:var(--blue)"></div><div class="legend-name">Nổi bật</div><div class="legend-val">{{ $fmt($statusDistribution['featured'] ?? 0) }}</div></div>
                    <div class="legend-item"><div class="legend-dot" style="background:var(--purple)"></div><div class="legend-name">Tin nóng</div><div class="legend-val">{{ $fmt($statusDistribution['hot'] ?? 0) }}</div></div>
                </div>
            </div>
            <div style="margin-top:16px;">
                <div class="section-title" style="font-size:12px;margin-bottom:10px;"><div class="dot"></div> Heatmap hoạt động</div>
                <div class="heatmap-grid" id="heatmap"></div>
                <div class="hm-labels"><div class="hm-label">T2</div><div class="hm-label">T3</div><div class="hm-label">T4</div><div class="hm-label">T5</div><div class="hm-label">T6</div><div class="hm-label">T7</div><div class="hm-label">CN</div></div>
            </div>
        </div>
    </div>

    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-label">⭐ Phân tích đánh giá sao</div>
        <div class="section-divider-line"></div>
    </div>

    <div class="rating-overview">
        <div class="rating-split-card">
            <div class="section-header"><div class="section-title"><div class="dot" style="background:var(--gold)"></div> Tổng quan đánh giá</div></div>
            <div style="display:flex;gap:20px;align-items:flex-start;">
                <div>
                    <div class="big-score">{{ number_format($ratingAverage, 1) }}</div>
                    <div class="big-stars {{ $starClass($ratingAverage) }}">{{ $stars($ratingAverage) }}</div>
                    <div class="score-sub">{{ $fmt($ratingTotal) }} lượt đánh giá</div>
                </div>
                <div class="rating-dist">
                    @foreach([5,4,3,2,1] as $score)
                        @php
                            $count = (int) ($ratingDistributionRaw[$score] ?? 0);
                            $width = $count > 0 ? max(4, round($count / $maxRatingDist * 100)) : 0;
                            $fillClass = $score >= 4 ? 'rbar-fill-pos' : ($score === 3 ? 'rbar-fill-mid' : 'rbar-fill-neg');
                        @endphp
                        <div class="rbar-row"><div class="rbar-label">{{ $score }}★</div><div class="rbar-track"><div class="{{ $fillClass }}" style="width:{{ $width }}%"></div></div><div class="rbar-count">{{ $fmt($count) }}</div></div>
                    @endforeach
                </div>
            </div>
            <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--border);display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
                <div style="text-align:center;">
                    <div style="font-size:18px;font-weight:800;color:var(--green);font-family:'JetBrains Mono',monospace;">{{ $positiveRate }}%</div>
                    <div style="font-size:10px;color:var(--text-muted);margin-top:2px;">Tích cực (4-5★)</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:18px;font-weight:800;color:var(--gold);font-family:'JetBrains Mono',monospace;">{{ $neutralRate }}%</div>
                    <div style="font-size:10px;color:var(--text-muted);margin-top:2px;">Trung lập (3★)</div>
                </div>
                <div style="text-align:center;">
                    <div style="font-size:18px;font-weight:800;color:var(--red);font-family:'JetBrains Mono',monospace;">{{ $negativeRate }}%</div>
                    <div style="font-size:10px;color:var(--text-muted);margin-top:2px;">Tiêu cực (1-2★)</div>
                </div>
                <div style="flex:1;display:flex;align-items:flex-end;height:50px;">
                    <canvas id="ratingTrendChart" style="max-height:50px;width:100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="rating-split-card">
            <div class="section-header">
                <div class="section-title"><div class="dot" style="background:var(--green)"></div> Bài viết đánh giá cao nhất</div>
                <div class="tab-row" style="margin-bottom:0;width:auto;">
                    <button class="tab-btn active" type="button" onclick="switchRating(this,'pos')">Tích cực</button>
                    <button class="tab-btn" type="button" onclick="switchRating(this,'neg')">Tiêu cực</button>
                </div>
            </div>
            <div id="rating-articles-list"></div>
        </div>

        <div class="rating-split-card">
            <div class="section-header"><div class="section-title"><div class="dot" style="background:var(--blue)"></div> Điểm đánh giá theo thể loại</div></div>
            <canvas id="radarChart" style="max-height:200px;"></canvas>
        </div>
    </div>

    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-label">👤 Hiệu suất Tác giả</div>
        <div class="section-divider-line"></div>
    </div>

    <div class="perf-grid">
        <div class="card">
            <div class="section-header">
                <div class="section-title"><div class="dot" style="background:var(--gold)"></div> Năng suất cao nhất</div>
                <a class="section-link" href="{{ url('admin/authors/list?sort=published') }}">Xem tất cả</a>
            </div>
            <div class="perf-card-inner" id="perf-productivity"></div>
        </div>
        <div class="card">
            <div class="section-header">
                <div class="section-title"><div class="dot" style="background:var(--green)"></div> Tỉ lệ tích cực cao nhất</div>
                <a class="section-link" href="{{ url('admin/rating/list') }}">Chi tiết</a>
            </div>
            <div class="perf-card-inner" id="perf-top-rating"></div>
        </div>
        <div class="card">
            <div class="section-header">
                <div class="section-title"><div class="dot" style="background:var(--red)"></div> Tỉ lệ tiêu cực cao nhất</div>
                <a class="section-link" href="{{ url('admin/rating/list') }}">Chi tiết</a>
            </div>
            <div class="perf-card-inner" id="perf-low-rating"></div>
        </div>
    </div>

    <div class="card">
        <div class="section-header">
            <div class="section-title"><div class="dot"></div> Bảng tổng hợp tác giả</div>
            <div style="display:flex;gap:8px;align-items:center;">
                <select class="date-picker" id="author-sort" onchange="renderAuthorTable()" style="min-width:140px;">
                    <option value="posts">Sắp xếp: Bài viết</option>
                    <option value="views">Sắp xếp: Lượt xem</option>
                    <option value="rating">Sắp xếp: Đánh giá</option>
                    <option value="positive">Sắp xếp: Tích cực %</option>
                </select>
                <button class="section-link" type="button" onclick="window.print()">Xuất Excel</button>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Tác giả</th><th>Bài viết</th><th>Lượt xem</th><th>Điểm TB</th><th>Tích cực</th><th>Tiêu cực</th><th>Tổng đánh giá</th><th>Sentiment</th><th>Xu hướng</th></tr>
                </thead>
                <tbody id="author-tbody"></tbody>
            </table>
        </div>
    </div>

    <div class="section-divider">
        <div class="section-divider-line"></div>
        <div class="section-divider-label">📊 Phân tích nội dung</div>
        <div class="section-divider-line"></div>
    </div>

    <div class="analytics-row">
        <div class="card">
            <div class="section-header">
                <div class="section-title"><div class="dot"></div> Top Tác giả - Lượt xem</div>
                <a class="section-link" href="{{ url('admin/authors/list?sort=views') }}">Xem tất cả</a>
            </div>
            <div id="author-list"></div>
        </div>
        <div class="card">
            <div class="section-header">
                <div class="section-title"><div class="dot"></div> Thể loại</div>
                <a class="section-link" href="{{ url('admin/news_cat/list') }}">Chi tiết</a>
            </div>
            <div id="cat-list"></div>
            <div style="margin-top:14px;"><canvas id="catChart" style="max-height:140px;"></canvas></div>
        </div>
        <div class="card">
            <div class="section-header">
                <div class="section-title"><div class="dot"></div> Hoạt động gần đây</div>
                <a class="section-link" href="{{ url('admin/news/list') }}">Xem tất cả</a>
            </div>
            <div id="activity-list"></div>
        </div>
    </div>

    <div class="card">
        <div class="section-header">
            <div class="section-title"><div class="dot"></div> Bài viết nhiều lượt xem nhất</div>
            <div style="display:flex;gap:6px;align-items:center;">
                <select class="date-picker" id="article-filter" onchange="filterTable()" style="min-width:130px;">
                    <option value="all">Tất cả thể loại</option>
                    @foreach($articleCategoryOptions as $category)
                        <option value="{{ \Illuminate\Support\Str::slug($category) }}">{{ $category }}</option>
                    @endforeach
                </select>
                <button class="section-link" type="button" onclick="window.print()">Xuất Excel</button>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>#</th><th>Tiêu đề bài viết</th><th>Thể loại</th><th>Tác giả</th><th>Lượt xem</th><th>Bình luận</th><th>Đánh giá</th><th>Ngày đăng</th><th>Trạng thái</th></tr>
                </thead>
                <tbody id="article-tbody"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection
