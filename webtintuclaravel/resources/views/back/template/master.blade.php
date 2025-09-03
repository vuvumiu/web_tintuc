<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Daterange picker -->
  <!-- DataTables -->
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


  <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <!-- Link về trang chủ -->
        <a href="{{ url('/') }}" class="nav-link" target="_blank">Xem website</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fas fa-user fa-fw"></i>
            Xin chào: <b>{{ Auth::user()->fullname }}</b>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          @if(isset(Auth::user()->level) && Auth::user()->level == 1)
            <a class="dropdown-item" href="{{ url('admin/staff/list') }}">
                <i class="fas fa-users fa-fw"></i> Quản lý nhân viên
            </a>
            <div class="dropdown-divider"></div>
            @endif
            <a href="{{ url('admin/staff/profile') }}" class="dropdown-item">
                <i class="fas fa-user-edit fa-fw"></i> Thông tin tài khoản
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{ url('logout') }}" class="dropdown-item dropdown-footer">Thoát</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('admin/home') }}" class="brand-link">
      <img src="{{ asset('dist/img/AdminLTELogo.png') }}"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">QUẢN TRỊ WEBSITE</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" 
            data-widget="treeview" 
            role="menu" 
            data-accordion="false">


            @if(isset(Auth::user()->level) && Auth::user()->level == 1)
          
          <!-- Cấu hình hệ thống -->
          <li class="nav-item has-treeview menu-open">
            <a href="{{ url('admin/system') }}" class="nav-link @yield('system')"> 
              <i class="fas fa-cog fa-fw"></i> 
              <p>Cấu hình hệ thống</p>
            </a>
          </li>

          <!-- Quản lý trang -->
          <li class="nav-item has-treeview menu-open">
            <a href="{{ url('admin/page/list') }}" class="nav-link @yield('page')">
              <i class="fas fa-sitemap fa-fw"></i> 
              <p>Quản lý trang</p>
            </a>
          </li>
           <!-- Quản lý tin tức -->
           <li class="nav-item has-treeview">
            <a href="#" class="nav-link @yield('news')">
              <i class="fas fa-newspaper fa-fw"></i>
              <p>
                Quản lý tin tức
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <!-- Nếu đây là file tĩnh trong thư mục public/pages/layout -->
                <a href="{{ url('admin/news_cat/list') }}" class="nav-link" >
                  Danh mục tin tức
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ url('admin/news/list') }}" class="nav-link">
                  Danh sách tin tức
                </a>
              </li>
            </ul>
          </li>

          @endif 

          @if(isset(Auth::user()->level) && Auth::user()->level == 1)

          <li class="nav-item has-treeview menu-open">
            <a href="{{ url('admin/slider/list') }}" class="nav-link @yield('slider')">
              <i class="fas fa-sliders-h fa-fw"></i> 
              <p>Quản lý slideshow</p>
            </a>
          </li>
          <!-- Quản lý mạng xã hội -->
          <li class="nav-item has-treeview menu-open">
            <a href="{{ url('admin/social/list') }}" class="nav-link @yield('social')">
              <i class="fas fa-share-alt fa-fw"></i> 
              <p>Quản lý mạng xã hội</p>
            </a>
          </li>
          @endif

          <!-- Quản lý nhận tin khuyến mãi -->
          <li class="nav-item has-treeview menu-open">
            <a href="{{ url('admin/newsletter/list') }}" class="nav-link @yield('newsletter')">
              <i class="fas fa-mail-bulk fa-fw"></i> 
              <p>Quản lý nhận tin khuyến mãi</p>
            </a>
          </li>

          <!-- Quản lý liên hệ -->
          <li class="nav-item has-treeview menu-open">
            <a href="{{ url('admin/contact/list') }}" class="nav-link @yield('contact')">
              <i class="fas fa-envelope-open-text fa-fw"></i> 
              <p>Quản lý liên hệ</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <!-- /.main-sidebar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0 text-dark">@yield('heading')</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="container-fluid">
        <div class="col-md-12">
        @if(Session::has('flash_message'))
<div class="ad_message alert alert-{!! Session::get('flash_level') !!}">
    {!! Session::get('flash_message') !!}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
        </div>

        <!-- Main content -->
        @yield('content')
        <!-- /.content -->
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <strong>
      Copyright &copy; 2025 <a href="https://www.facebook.com/QuaHongDo/">VUvumiu</a>
    </strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.0.5
    </div>
  </footer>  
 
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content -->
  </aside>
  <!-- /.control-sidebar -->

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('dist/js/demo.js') }}"></script>
<script src="{{ asset('ckeditor4.14/ckeditor.js') }}"></script>

  
<script>
  CKEDITOR.replace('ckeditor', {
    filebrowserBrowseUrl: '{!! url("responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=") !!}',
    filebrowserUploadUrl: '{!! url("responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=") !!}',
    filebrowserImageBrowseUrl: '{!! url("responsive_filemanager/filemanager/dialog.php?type=1&editor=ckeditor&fldr=") !!}'
});
</script>



<script type="text/javascript"> 
  function ChangeToSlug()
  {
      var title, slug;
  
      //Lấy text từ thẻ input title
      title = document.getElementById("title").value;
  
      //Đổi chữ hoa thành chữ thường
      slug = title.toLowerCase();
  
      //Đổi ký tự có dấu thành không dấu
      slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
      slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
      slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
      slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
      slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
      slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
      slug = slug.replace(/đ/gi, 'd');
      //Xóa các ký tự đặt biệt
      slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|'|\"|\:|\;|_/gi, '');
      //Đổi khoảng trắng thành ký tự gạch ngang
      slug = slug.replace(/ /gi, "-");
      //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
      //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
      slug = slug.replace(/\-\-\-\-\-/gi, '-');
      slug = slug.replace(/\-\-\-\-/gi, '-');
      slug = slug.replace(/\-\-\-/gi, '-');
      slug = slug.replace(/\-\-/gi, '-');
      //Xóa các ký tự gạch ngang ở đầu và cuối
      slug = '@' + slug + '@';
      slug = slug.replace(/\@\-|\-\@|\@/gi, '');
      //In slug ra textbox có id “slug”
      document.getElementById('slug').value = slug;
  };
</script>






<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>


<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>


</body>
</html>


