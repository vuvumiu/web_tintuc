{{-- NEWSLETTER SECTION --}}
<div class="newsletter">
    <div class="nl-eyebrow">Bản tin hàng ngày</div>
    <div class="nl-title">Không bỏ lỡ tin tức quan trọng</div>
    <div class="nl-sub">Nhận tóm tắt tin tức chọn lọc mỗi sáng trực tiếp vào hộp thư của bạn.</div>
    <div class="nl-form">
        <input type="email" class="nl-input" id="txtEmailSub" placeholder="Nhập địa chỉ email của bạn" autocomplete="email"/>
        <button type="button" class="nl-btn" id="btnSendSub">Đăng ký</button>
    </div>
    <div id="subFormMsg" class="form-message" style="display:none; margin-top:12px; text-align:center;"></div>
</div>

{{-- FOOTER --}}
<footer class="footer">
    @php
        $brandName = $siteName ?? 'VNXpress';
        $brandLogoText = $logoText ?? 'VN●XPRESS';
        $useImageLogo = ($logoType ?? 'text') === 'image' && !empty($logo->Description);
        $brandLogoTextHtml = str_replace('●', '<span>●</span>', e($brandLogoText));
    @endphp

    <div class="footer-top">
        <div>
            @if($useImageLogo)
                <a href="{{ url('/') }}" class="footer-logo">
                    <img src="{{ asset($logo->Description) }}" alt="{{ $brandName }}" style="height:48px; max-width:220px; object-fit:contain;">
                </a>
            @else
                <a href="{{ url('/') }}" class="footer-logo">{!! $brandLogoTextHtml !!}</a>
            @endif

            <p class="footer-desc">
                Trang tin tức hàng đầu Việt Nam. Cập nhật tin tức chính xác, nhanh chóng và đáng tin cậy mỗi ngày từ khắp nơi trên thế giới.
            </p>
        </div>

        <div>
            <div class="footer-col-title">Chuyên mục</div>
            <ul class="footer-links">
                @if(isset($NewsCategoriesGlobal) && count($NewsCategoriesGlobal) > 0)
                    @foreach($NewsCategoriesGlobal as $cat)
                        <li><a href="{{ url('/' . $cat->Alias) }}">{{ $cat->Name }}</a></li>
                    @endforeach
                @else
                    <li><a href="#">Thời sự</a></li>
                    <li><a href="#">Công nghệ</a></li>
                    <li><a href="#">Kinh tế</a></li>
                    <li><a href="#">Giải trí</a></li>
                    <li><a href="#">Thể thao</a></li>
                @endif
            </ul>
        </div>

        <div>
            <div class="footer-col-title">Về chúng tôi</div>
            <ul class="footer-links">
                <li><a href="{{ url('/ve-chung-toi') }}">Giới thiệu</a></li>
                <li><a href="{{ url('/lien-he') }}">Liên hệ</a></li>
                <li><a href="#">Tuyển dụng</a></li>
                <li><a href="#">Quảng cáo</a></li>
            </ul>
        </div>

        <div>
            <div class="footer-col-title">Theo dõi</div>
            <ul class="footer-links">
                @if(isset($Social) && count($Social) > 0)
                    @foreach($Social as $v)
                        <li>
                            <a href="{{ $v->Alias }}" target="_blank" rel="noopener">
                                {!! $v->Font !!} {{ $v->Name }}
                            </a>
                        </li>
                    @endforeach
                @else
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">YouTube</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">TikTok</a></li>
                @endif
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <span class="footer-copy">
            &copy; {{ date('Y') }} {{ $brandName }}.
            @if(isset($copyright) && $copyright && $copyright->Description)
                {{ $copyright->Description }}
            @endif
        </span>
        <div class="footer-social">
            @if(isset($Social) && count($Social) > 0)
                @foreach($Social as $v)
                    <a href="{{ $v->Alias }}" class="social-btn" title="{{ $v->Name }}" target="_blank" rel="noopener">
                        {!! $v->Font !!}
                    </a>
                @endforeach
            @else
                <a href="#" class="social-btn" title="Facebook">f</a>
                <a href="#" class="social-btn" title="YouTube">yt</a>
                <a href="#" class="social-btn" title="Instagram">in</a>
                <a href="#" class="social-btn" title="TikTok">tt</a>
            @endif
        </div>
    </div>
</footer>
