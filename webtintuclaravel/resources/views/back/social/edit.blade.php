@extends('back.template.master')

@section('title', 'Quản lý mạng xã hội')

@section('heading', 'Chỉnh sửa mạng xã hội')

@section('social', 'active')

@section('content')
<div class="col-md-12">
  <div class="card-header">
    <a class="btn btn-primary" href="{{ url('admin/social/list') }}" title="Quay lại">
      Quay lại
    </a>      
  </div>
 
    <!-- general form elements -->
    <div class="card card-primary">
      <!-- /.card-header -->
      <!-- form start -->
      <form role="form" action="{{ url('admin/social/edit/'.$Social->RowID) }}" method="POST">
        <div class="card-body">
            {!! csrf_field() !!}

            @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
            @endif

            <div class="form-group">
              <select class="form-control @error('Status') is-invalid @enderror" name="Status">
                  <option value="1" @if((string) old('Status', $Social->Status) === '1') selected @endif>
                      Trạng thái: Bật
                  </option>
                  <option value="0" @if((string) old('Status', $Social->Status) === '0') selected @endif>
                      Trạng thái: Tắt
                  </option>
              </select>
              @error('Status')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
          </div>
          
          <div class="form-group">
              <label for="social-alias">Đường dẫn <span class="color_red">*</span></label>
              <input type="text" class="form-control @error('Alias') is-invalid @enderror" id="social-alias" name="Alias" value="{{ old('Alias', $Social->Alias) }}" placeholder="https://...">
              @error('Alias')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
          </div>

          <div class="form-group">
            <label for="social-name">Tên mạng xã hội <span class="color_red">*</span></label>
            <input type="text" class="form-control @error('Name') is-invalid @enderror" id="social-name" name="Name" value="{{ old('Name', $Social->Name) }}">
            @error('Name')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
        </div>
          
          <div class="form-group">
              <label for="social-font">Font <span class="color_red">*</span></label>
              <input type="text" class="form-control @error('Font') is-invalid @enderror" id="social-font" name="Font" value="{{ old('Font', $Social->Font) }}">
              @error('Font')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
          </div>
          
          <div class="form-group">
              <label for="social-sort">Sắp xếp</label>
              <input type="number" min="0" class="form-control @error('Sort') is-invalid @enderror" id="social-sort" name="Sort" value="{{ old('Sort', $Social->Sort) }}">
              @error('Sort')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
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
