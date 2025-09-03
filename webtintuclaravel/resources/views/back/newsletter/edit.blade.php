@extends('back.template.master')

@section('title', 'Quản lý nhận tin khuyến mại')

@section('heading', 'Chỉnh sửa nhận tin khuyến mại')

@section('newsletter', 'active')

@section('content')
<div class="col-md-12">
  <div class="card-header">
    <a class="btn btn-primary" href="{{ url('admin/newsletter/list') }}" title="Quay lại">
      Quay lại
    </a>      
  </div>
 
    <!-- general form elements -->
    <div class="card card-primary">
      <!-- /.card-header -->
      <!-- form start -->
      <form role="form" action="{{ url('admin/newsletter/edit/'.$Newsletter->RowID) }}" method="POST">
        <div class="card-body">
            {!! csrf_field() !!}

            <div class="form-group">
              <select class="form-control" name="IsViews">
                  <option value="1" @if($Newsletter->IsViews == 1) selected="" @endif> 
                      Trạng thái: Đã xem
                  </option>
                  <option value="0" @if($Newsletter->IsViews == 0) selected="" @endif> 
                      Trạng thái: Chưa xem
                  </option>
              </select>
          </div>
          
          <div class="form-group">
              <label for="exampleInputEmail1">Tên email <span class="color_red">*</span></label>
              <input type="text" class="form-control" name="Email" value="{{$Newsletter->Email}}">
          </div>
          
        </div>
          
        <!-- /.card-body -->

        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Chỉnh sửa</button>
        </div>
      </form>
    </div>
    <!-- /.card -->

  </div>
@stop
