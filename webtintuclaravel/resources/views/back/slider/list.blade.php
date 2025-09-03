@extends('back.template.master')

@section('title', 'Quản lý slideshow')
@section('heading', 'Danh sách slideshow')
@section('slider', 'active')

@section('content')
<div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <a class="btn btn-primary" href="{{ url('admin/slider/add') }}" title="Thêm">
          Thêm
        </a>      
      </div>
        <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text_align_center">STT</th>
                        <th>Tên slider</th>
                        <th>Ảnh đại diện</th>
                        <th>Trạng thái</th>
                        <td class="text_align_center">Sắp xếp</td>
                        <th class="text_align_center"><i class="fas fa-wrench"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($Slider) && count($Slider) > 0)
                    @foreach($Slider as $k => $v)
                    <tr>
                        <td class="text_align_center">{{ $k+1 }}</td>
                        <td>{{ $v->Name }}</td>
                        <td>
                            <img src="{{ url('images/slider/'. $v->Images) }}" width="100" alt="Avata" >
                        </td>
                        <td>
                            @if($v->Status == 1)
                                Bật
                            @else
                                Tắt
                            @endif
                        </td>
                        <td class="text_align_center">{{ $v->Sort }}</td>
                        <td class="text_align_center">
                            <a href="{{ url('admin/slider/edit/'. $v->RowID) }}" title="Chỉnh sửa" class="ad_button">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ url('admin/slider/delete/'. $v->RowID) }}" title="Xóa" class="ad_button ad_button_delete">
                              <i class="fas fa-trash-alt"></i>
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