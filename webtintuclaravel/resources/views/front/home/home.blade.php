@extends('front.template.master')
@section('title', $PageInfo->MetaTitle)
@section('description', $PageInfo->MetaDescription)
@section('keywords', $PageInfo->MetaKeyword)
@section('url', url('/'))
@section('home', 'active')
@section('images', url('images/page/'.$PageInfo->Images))
@section('content')


@section('content')
<div class="home_page">
    <div class="slider_wrap">
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                @if(isset($Slider) && count($Slider) > 0)
                    @foreach($Slider as $k => $v)
                        <div class="item @if($k == 0) active @endif">
                            <a href="{{ $v->Alias }}" title="{{ $v->Name }}">
                                <img src="{{ url('images/slider/' . $v->Images) }}" alt="{{ $v->Name }}">
                            </a>
                        </div>
                    @endforeach
                @endif
            </div>

                <!-- Controls -->
                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                    <i class="fas fa-angle-left"></i>

                </a>
                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                    <i class="fas fa-angle-right"></i>

                </a>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="home_top">
                        <div class="home_top_left">
                            <div class="heading">
                                Blog mới nhất
                            </div>
                            <ul>
                                @if(isset($News) && count($News) > 0)
                                    @foreach($News as $k => $v)
                                        <li>
                                            <a href="{{ url('/' . $v->Alias) }}.html" title="{{ $v->Name }}">
                                                <img src="{{ url('images/news/' . $v->Images) }}" alt="{{ $v->Name }}" />
                                                <b>{{ $v->Name }}</b>
                                                <p>
                                                    {{ Str::limit($v->SmallDescription, 90, '...') }} 
                                                    <span style="color: #f54c0b">[read more]</span>
                                                </p>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        <div class="home_top_right">
                            <div class="heading">
                                Về chúng tôi
                            </div>
                            <img src="{{ url('images/about.jpg') }}" alt="About" />
                            <p>
                                <b>VnExpress</b>
                            </p>
                            <p>
                                <strong>Tiền thân của báo chí hiện đại</strong> 20 năm phát triển công nghệ của VnExpress
                            </p>
                            <p>
                                Tròn hai thập kỷ ra đời với mục tiêu phụng sự độc giả, VnExpress đã liên tục cập nhật những công nghệ làm báo mới nhất, trong đó có những thay đổi mang tính cách mạng.
                                <a href="{{ url('ve-chung-toi') }}" title="Xem thêm">[read more]</a>
                            </p>
                            <div class="home_social">
                                @if(isset($Social) && count($Social) > 0)
                                    @foreach($Social as $k => $v)
                                        <a href="{{ $v->Alias }}" title="{{ $v->Name }}">
                                            {!! $v->Font !!}
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="heading" style="margin-top: 55px; color: #FFF;">
                        Khuyến mại mới nhất
                    </div>
                </div>
        
                @if(isset($NewsSale) && count($NewsSale) > 0)
                    @foreach($NewsSale as $k => $v)
                        <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="home_center">
                                <a href="{{ url('/' . $v->Alias) }}.html" title="{{ $v->Name }}">
                                    <img src="{{ url('images/news/' . $v->Images) }}" alt="{{ $v->Name }}" />
                                    <b>{{ $v->Name }}</b>
                                
                                <p>
                                    {{ Str::limit($v->SmallDescription, 90, '...') }} 
                                    <span>[read more]</span>
                                </p>
                            </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="home_bottom">
                        <div class="heading">
                            Blog xem nhiều
                        </div>
                   <ul>
                   @if(isset($NewsViews) && count($NewsViews) > 0)
                        @foreach($NewsViews as $k => $v)
                            <li>
                                <a href="{{url('/'.$v->Alias)}}.html" title="{{$v->Name}}">
                                    <img src="{{url('images/news/'.$v->Images)}}" alt="{{$v->Name}}" />
                                    <b>{{$v->Name}}</b>
                                    <p>
                                        {{ Str::limit($v->SmallDescription, 90, '...') }} 
                                        <span>[read more]</span>
                                    </p>
                                </a>
                            </li>
                        @endforeach
                    @endif
                   </ul>
                </div>
            </div>
        </div>        
        
    </div>
@stop