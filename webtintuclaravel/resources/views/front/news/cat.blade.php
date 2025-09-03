@extends('front.template.master')
@section('title', $newsCat->MetaTitle)
@section('description', $newsCat->MetaDescription)
@section('keywords', $newsCat->MetaKeyword)
@section('url', url('/'.$newsCat->Alias))

@section($newsCat->Alias, 'active')

@section('images', url('images/page/'.$newsCat->Images))
@section('content')

<div class="contact_wrap">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="contact_page">
                    <div class="heading">
                        {{ $newsCat->Name }}
                        <select id="newSort" class="news_cat_sort">
                                <option value="tinmoi" @if($sort == 'tinmoi') selected @endif>Sắp xếp theo tin mới</option>
                                <option value="luotxem" @if($sort == 'luotxem') selected @endif>Sắp xếp theo lượt xem</option>
                            </select>
                            
                        </select>     
                        <input type="hidden" id="newsCat" value="{{$newsCat->Alias}}">                   
                    </div>
                    <ul class="news_cat_wrap">
                        @if(isset($listNews) && count($listNews) > 0)
                            @foreach($listNews as $k => $v)
                                <li>
                                    <a href="{{ url('/'.$v->Alias) }}.html" title="{{ $v->Name }}">
                                        <img src="{{ url('images/news/'.$v->Images) }}" alt="{{ $v->Name }}" />
                                        <b>{{ $v->Name }}</b>
                                        <p>
                                            {{ Str::limit($v->SmallDescription, 90, '...') }}
                                            <span>[read more]</span>
                                        </p>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <div class="page_pagination">
                        {{ $listNews->links('pagination::bootstrap-4') }} {{-- Phân trang --}}
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

