<!DOCTYPE html>
<html dir="ltr" lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noodp,index,follow" />
    <meta name="revisit-after" content="1 days" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    <meta name="description" content="@yield('description')" />
    <meta name="keywords" content="@yield('keywords')" />
    <link rel="shortcut icon" type="image/x-icon" href="{{url('images/favicon/'.$favicon->Description)}}"/>
    <link rel="canonical" href="@yield('url')" />
    <meta property="og:locale" itemprop="inlanguage" content="vi_VN" />
    <meta property="og:url" content="@yield('url')" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="@yield('title')" />
    <meta property="og:description" content="@yield('description')" />
    <meta property="og:image" content="@yield('images')" />
    <meta property="og:site_name" content="Hướng dẫn lập trình web tin tức cơ bản" />
    <meta name="copyright" content="Hướng dẫn lập trình web tin tức cơ bản" />
    <meta name="author" content="Hướng dẫn lập trình web tin tức cơ bản" />
    <meta name="geo.placename" content="Ho Chi Minh, Viet Nam" />

    <link href="{{ asset('fontawesome-free-5.15.4-web/css/all.css') }}" rel="stylesheet" />
    <link href="{{ asset('bootstrap-3.4.1/dist/css/bootstrap.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" rel="stylesheet">
    <script type="text/javascript">var url = "{!! url('/') !!}";</script>

</head>
<body>
<input type="hidden" id="_token" value="{{ csrf_token() }}">

    <div id="wrapper">
        @include('front.template.header')    
        <div class="content">
            @yield('content')
        </div>
        @include('front.template.footer')  
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="{{ asset('bootstrap-3.4.1/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/front.js') }}"></script>
</body>
</html>