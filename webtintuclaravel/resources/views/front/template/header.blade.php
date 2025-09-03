<div class="header">
    <div class="header_top">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="header_logo">
                        <a href="{{ url('/') }}" title="Trang chủ">
                            <img src="{{ url('images/logo/' . $logo->Description) }}" alt="Logo" />
                        </a>
                        <i class="fas fa-bars mobileMenuBar" id="mobileMenuBar"></i>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6">
                    <div class="header_social">
                        @if(isset($Social) && count($Social) > 0)
                            @foreach($Social as $k => $v)
                                <a href="{{ $v->Alias }}" title="{{ $v->Name }}">
                                    {!! $v->Font !!}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header_bottom">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-9">
                    <div class="header_menu">
                        <ul>
                            @if(isset($Page) && count($Page) > 0)
                            @foreach($Page as $k => $v)
                                <li>
                                    @if($v->Alias == "/")
                                        <a href="{{ url('/') }}" title="{{ $v->Name }}" class="@yield('home')"
                                            >
                                            {!! $v->Font !!}
                                        </a>
                                    @else
                                        <a href="{{ url('/' . $v->Alias) }}" title="{{ $v->Name }}" class="@yield($v->Alias)"
                                            >
                                            {{ $v->Name }}
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        @endif
                        
                        </ul>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-3">
                    <div class="header_search">
                        <form action="{{ url('tim-kiem') }}" method="GET">
                            <input type="text" id="btnSearch" placeholder="Nhập từ khóa tìm kiếm" name="keyword"/>
                            <button>
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>