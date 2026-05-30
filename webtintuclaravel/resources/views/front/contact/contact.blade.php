@extends('front.template.master')

@section('title', $PageInfo->MetaTitle ?? 'Liên hệ')
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
            <span>{{ $PageInfo->Name ?? 'Liên hệ' }}</span>
        </nav>

        <div class="page-header">
            <div class="page-header-line"></div>
            <h1 class="page-header-title">{{ $PageInfo->Name ?? 'Liên hệ' }}</h1>
            <div class="page-header-line"></div>
        </div>

        @if(!empty($PageInfo->Description))
        <div class="about-content" style="margin-bottom:40px;">
            {!! $PageInfo->Description !!}
        </div>
        @endif

        <div class="row g-4">
            @if(!empty($Map->Description))
            <div class="col-12 col-lg-6">
                <div class="contact-map-wrap">
                    <div class="contact_map">{!! $Map->Description !!}</div>
                </div>
            </div>
            @endif

            <div class="col-12 col-lg-{{ !empty($Map->Description) ? '6' : '8 mx-auto' }}">
                <div class="contact-form-wrap">
                    <div class="contact-form-card">
                        <h3 class="contact-form-title">Gửi liên hệ</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" id="txtName" class="contact-input" placeholder="Họ và tên *" />
                            </div>
                            <div class="col-md-6">
                                <input type="email" id="txtEmail" class="contact-input" placeholder="Email *" />
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="txtPhone" class="contact-input" placeholder="Số điện thoại *" />
                            </div>
                            <div class="col-md-6">
                                <select id="selCategory" class="contact-input">
                                    <option value="">-- Phân loại --</option>
                                    <option value="consult">Tư vấn</option>
                                    <option value="cooperation">Hợp tác</option>
                                    <option value="complaint">Khiếu nại</option>
                                    <option value="other">Khác</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <input type="text" id="txtSubject" class="contact-input" placeholder="Tiêu đề (tùy chọn)" />
                            </div>
                            <div class="col-12">
                                <textarea id="txtMessage" class="contact-textarea" rows="5" placeholder="Lời nhắn *"></textarea>
                            </div>
                            <div class="col-12">
                                <div id="contactFormMsg" class="form-message" style="display:none;"></div>
                            </div>
                            <div class="col-12">
                                <button id="btnSendContact" type="button" class="contact-submit-btn">
                                    <i class="fas fa-paper-plane"></i> GỬI LIÊN HỆ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
