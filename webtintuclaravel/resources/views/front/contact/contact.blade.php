@extends('front.template.master')
@section('title', $PageInfo->MetaTitle)
@section('description', $PageInfo->MetaDescription)
@section('keywords', $PageInfo->MetaKeyword)
@section('url', url('/'.$PageInfo->Alias))

@section($PageInfo->Alias, 'active')

@section('images', url('images/page/'.$PageInfo->Images))
@section('content')

<div class="contact_wrap">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="contact_page">
                    <div class="heading">
                        {{$PageInfo->Name}}
                    </div>
                    <div class="contact_description">
                        {!!$PageInfo->Description!!}
                    </div>
                    <div class="contact_map">
                        {!!$Map->Description!!}
                    </div>
                    <div class="contact_form">
                        <input type="text" id="txtName" placeholder="Họ và tên..." />
                        <input type="email" id="txtEmail" placeholder="Email..." />
                        <input type="text" id="txtPhone" placeholder="Số điện thoại..." />
                        <textarea placeholder="Lời nhắn..." id="txtMessage"></textarea>
                        <button id="btnSendContact">GỬI LIÊN HỆ</button>
</div>
<div class="clearfix"></div>
</div>
</div>
@stop
