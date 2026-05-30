@extends('front.template.master')

@section('title', $PageInfo->MetaTitle ?? 'Giới thiệu')
@section('description', $PageInfo->MetaDescription ?? '')
@section('keywords', $PageInfo->MetaKeyword ?? '')
@if($PageInfo && $PageInfo->Alias)
    @section('url', url('/'.$PageInfo->Alias))
    @section($PageInfo->Alias, 'active')
@endif
@if($PageInfo && $PageInfo->Images)
    @section('images', url('images/page/'.$PageInfo->Images))
@endif

@section('content')
<div class="section" style="padding-top:3rem;padding-bottom:3rem;">
    <div class="container">
        <nav class="breadcrumb-nav" aria-label="breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span class="breadcrumb-sep">&rsaquo;</span>
            <span>{{ $PageInfo->Name ?? 'Giới thiệu' }}</span>
        </nav>

        <div class="page-header">
            <div class="page-header-line"></div>
            <h1 class="page-header-title">{{ $PageInfo->Name ?? 'Giới thiệu' }}</h1>
            <div class="page-header-line"></div>
        </div>

        <div class="about-content">
            {!! $PageInfo->Description ?? '' !!}
        </div>
    </div>
</div>
@endsection
