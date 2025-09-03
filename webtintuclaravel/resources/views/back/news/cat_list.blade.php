@extends('back.template.master')

@section('title', 'Quản lý danh mục tin tức')
@section('heading', 'Danh sách danh mục tin tức')
@section('news', 'active')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text_align_center">STT</th>
                        <th>Tên danh mục</th>
                        <th>Trạng thái</th>
                        <th class="text_align_center"><i class="fas fa-wrench"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($NewsCategory) && count($NewsCategory) > 0)
                    @foreach($NewsCategory as $k => $v)
                    <tr>
                        <td class="text_align_center">{{ $k+1 }}</td>
                        <td>{{ $v->Name }}</td>
                        <td>
                            @if($v->Status == 1)
                                Bật
                            @else
                                Tắt
                            @endif
                        </td>
                        <td class="text_align_center">
                            <a href="{{ url('admin/news_cat/edit/'. $v->RowID) }}" title="Chỉnh sửa" class="ad_button">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop