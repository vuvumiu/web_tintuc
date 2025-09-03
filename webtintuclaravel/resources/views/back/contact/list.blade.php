@extends('back.template.master')

@section('title', 'Quản lý liên hệ')

@section('heading', 'Danh sách liên hệ')
@section('contact', 'active')

@section('content')
<div class="col-md-12">
    <!-- general form elements -->
    <div class="card">
      
      <!-- /.card-header -->
      <div class="card-body">
    
      <table id="example2" class="table table-bordered table-hover">
        <thead>
          <tr>
            <th class="text_align_center">STT</th>
            <th>Họ và tên</th>
            <th>Email</th>
            <th>Trạng thái</th>
            <th class="text_align_center"><i class="fas fa-wrench"></i></th>
          </tr>
        </thead>
        <tbody>
          @if(isset($Contact) && count($Contact) > 0)
          @foreach($Contact as $k => $v)
          <tr>
            <td class="text_align_center">{{ $k+1 }}</td>
            <td>{{ $v->Name }}</td>
            <td>{{ $v->Email }}</td>
            <td>
              @if($v->IsViews == 1)
                <p style="color: red"> Đã xem </p>
              @else
               <p style="color: green"> Chưa xem </p>
              @endif
            </td>
            <td class="text_align_center">
              <a href="{{ url('admin/contact/edit/' . $v->RowID) }}" title="Chỉnh sửa" class="ad_button">
                  <i class="fas fa-edit"></i>
              </a>
              <a href="{{ url('admin/contact/delete/' . $v->RowID) }}" title="Xóa" class="ad_button ad_button_delete">
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
    <!-- /.card -->
  </div>
@stop