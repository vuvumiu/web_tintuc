<!DOCTYPE html>
<html dir="ltr" lang="vi">
<head>
    @php
        $brandName = $siteName ?? 'VNXpress';
        $faviconHref = $faviconUrl ?? ($favicon && $favicon->Description ? asset($favicon->Description) : asset('favicon.ico'));
        $frontCss = 'css/style.css';
        $frontJs = 'js/front.js';
        $frontCssVersion = @filemtime(public_path($frontCss)) ?: time();
        $frontJsVersion = @filemtime(public_path($frontJs)) ?: time();
    @endphp
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noodp,index,follow" />
    <meta name="revisit-after" content="1 days" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', $brandName)</title>
    <meta name="description" content="@yield('description', '')" />
    <meta name="keywords" content="@yield('keywords', '')" />
    <link rel="icon" href="{{ $faviconHref }}"/>
    <link rel="shortcut icon" type="image/x-icon" href="{{ $faviconHref }}"/>
    <link rel="canonical" href="@yield('url', url('/'))" />
    <meta property="og:locale" itemprop="inlanguage" content="vi_VN" />
    <meta property="og:url" content="@yield('url')" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:image" content="@yield('images')" />
    <meta property="og:site_name" content="{{ $brandName }}" />
    <meta name="copyright" content="{{ $brandName }}" />
    <meta name="author" content="{{ $brandName }}" />
    <meta name="geo.placename" content="Ho Chi Minh, Viet Nam" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="{{ asset($frontCss) }}?v={{ $frontCssVersion }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <script type="text/javascript">var url = "{!! url('/') !!}";</script>
    <style>
        .form-message { padding: 10px 15px; border-radius: 6px; margin-top: 8px; font-size: 14px; }
        .form-message.alert-success { background: rgba(34, 197, 94, 0.15); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }
        .form-message.alert-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
    </style>
</head>
<body class="vnx-body has-fixed-nav">
<input type="hidden" id="_token" value="{{ csrf_token() }}">

    <div id="wrapper">
        @include('front.template.header')
        <main class="content">
            @yield('content')
        </main>
        @include('front.template.footer')
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset($frontJs) }}?v={{ $frontJsVersion }}"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.Laravel = window.Laravel || {}; window.Laravel.baseUrl = "{{ url('/') }}";</script>
    @if(config('gemini.features.chatbot'))
    @include('front.partials.ai-chatbot')
    @endif

    {{-- Popup Ads (hiển thị ngẫu nhiên) --}}
    @if(isset($popupAd))
        @include('front.partials.popup_ad', ['popupAd' => $popupAd])
    @endif
</body>
</html>
