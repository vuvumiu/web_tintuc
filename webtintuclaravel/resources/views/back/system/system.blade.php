@extends('back.template.master')

@section('title', 'Cấu hình hệ thống')
@section('heading', 'Cấu hình hệ thống')
@section('breadcrumb', 'Thương hiệu & liên hệ')
@section('subheading', 'Quản lý tên công ty, logo chữ, logo ảnh, favicon và thông tin liên hệ dùng chung cho trang chính và phần quản trị.')

@section('page_actions')
    <a class="btn btn-outline" href="{{ url('/') }}" target="_blank" rel="noopener">Xem website</a>
@endsection

@php
    $logoUrl = $logo->Description ? asset('images/logo/' . $logo->Description) : '';
    $faviconUrl = $favicon->Description ? asset('images/favicon/' . $favicon->Description) : '';
    $currentLogoType = old('logo_type', $logo_type->Description ?: 'text');
    $companyName = old('name', $name->Description);
    $logoTextValue = old('logo_text', $logo_text->Description ?: 'VN●XPRESS');
    $logoTextPreview = str_replace('●', '<span>●</span>', e($logoTextValue ?: 'VN●XPRESS'));
@endphp

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Nhận diện thương hiệu và thông tin liên hệ</h3>
                </div>

                <form action="{{ url('admin/system') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label for="brandNameInput">Tên công ty <span class="text-danger">*</span></label>
                            <input id="brandNameInput" type="text" class="form-control" name="name" value="{{ $companyName }}" placeholder="Ví dụ: SCF Corporation">
                            <small class="qs-sub">Đây là tên pháp lý hoặc tên thương hiệu của doanh nghiệp. Không dùng trường này để thay logo chữ.</small>
                        </div>

                        <div class="form-group">
                            <label for="logoTextInput">Logo chữ <span class="text-danger">*</span></label>
                            <input id="logoTextInput" type="text" class="form-control" name="logo_text" value="{{ $logoTextValue }}" placeholder="Ví dụ: VN●XPRESS">
                            <small class="qs-sub">Đây là logo dạng chữ đang hiển thị trên trang chính. Bạn có thể nhập đúng logo chữ hiện tại, ví dụ <strong>VN●XPRESS</strong>.</small>
                        </div>

                        <div class="form-group">
                            <label>Kiểu hiển thị logo</label>
                            <div class="brand-mode-grid">
                                <label class="brand-mode-card {{ $currentLogoType === 'text' ? 'is-active' : '' }}" data-brand-mode-card="text">
                                    <input type="radio" name="logo_type" value="text" {{ $currentLogoType === 'text' ? 'checked' : '' }}>
                                    <strong>Logo chữ</strong>
                                    <span>Dùng trường <em>logo chữ</em> để hiển thị trên trang chính và trong admin.</span>
                                </label>

                                <label class="brand-mode-card {{ $currentLogoType === 'image' ? 'is-active' : '' }}" data-brand-mode-card="image">
                                    <input type="radio" name="logo_type" value="image" {{ $currentLogoType === 'image' ? 'checked' : '' }}>
                                    <strong>Logo ảnh</strong>
                                    <span>Dùng file ảnh logo đã tải lên. Khi cập nhật ở đây, cả trang chính và admin sẽ đổi theo.</span>
                                </label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label for="logoInput">Logo ảnh</label>
                                    <input id="logoInput" type="file" class="form-control" name="logo" accept=".png,.jpg,.jpeg,.gif,.svg,.webp">
                                    <small class="qs-sub">Khuyến nghị nền trong suốt, chiều ngang lớn hơn chiều cao. Ảnh hiện tại sẽ vẫn được giữ nếu bạn không tải file mới.</small>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="faviconInput">Favicon</label>
                                    <input id="faviconInput" type="file" class="form-control" name="favicon" accept=".ico,.png,.jpg,.jpeg,.gif,.svg">
                                    <small class="qs-sub">Dùng cho tab trình duyệt và biểu tượng bookmark.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="systemEmail">Email <span class="text-danger">*</span></label>
                                    <input id="systemEmail" type="email" class="form-control" name="email" value="{{ old('email', $email->Description) }}" placeholder="contact@example.com">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="systemPhone">Số điện thoại <span class="text-danger">*</span></label>
                                    <input id="systemPhone" type="text" class="form-control" name="phone" value="{{ old('phone', $phone->Description) }}" placeholder="Nhập số điện thoại liên hệ">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="systemAddress">Địa chỉ</label>
                            <input id="systemAddress" type="text" class="form-control" name="address" value="{{ old('address', $address->Description) }}" placeholder="Nhập địa chỉ văn phòng hoặc trụ sở">
                        </div>

                        <div class="form-group">
                            <label for="systemMap">Google Maps</label>
                            <textarea id="systemMap" class="form-control" name="map" rows="6" placeholder="Dán iframe hoặc mã nhúng Google Maps">{{ old('map', $map->Description) }}</textarea>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label for="systemCopyright">Copyright</label>
                            <input id="systemCopyright" type="text" class="form-control" name="copyright" value="{{ old('copyright', $copyright->Description) }}" placeholder="Ví dụ: © 2026 SCF Corporation. All rights reserved.">
                        </div>
                    </div>

                    <div class="card-footer" style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                        <div class="qs-sub">Một nguồn cấu hình duy nhất sẽ áp dụng đồng thời cho trang chính và phần quản trị.</div>
                        <button type="submit" class="btn btn-gold">Lưu cấu hình hệ thống</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-head">
                    <span class="panel-title">Xem trước thương hiệu</span>
                </div>

                <div class="card-body">
                    <div class="brand-preview-card">
                        <div class="brand-preview-frame">
                            <div class="brand-preview-text {{ $currentLogoType === 'text' ? 'is-visible' : '' }}" data-brand-preview="text">
                                <div class="brand-preview-text__icon" id="brandTextInitial">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($companyName ?: 'S', 0, 1)) }}</div>
                                <div>
                                    <div class="brand-preview-text__name" id="brandTextLogoPreview">{!! $logoTextPreview !!}</div>
                                    <div class="qs-sub" id="brandCompanyPreview">{{ $companyName ?: 'Tên công ty' }}</div>
                                </div>
                            </div>

                            <div class="brand-preview-image {{ $currentLogoType === 'image' ? 'is-visible' : '' }}" data-brand-preview="image">
                                <img id="brandImagePreview" src="{{ $logoUrl }}" alt="Logo hiện tại" @if(!$logoUrl) style="display:none;" @endif>
                                <div id="brandImagePlaceholder" class="brand-preview-image__placeholder" @if($logoUrl) style="display:none;" @endif>
                                    Chưa có logo ảnh. Hãy tải file mới lên nếu bạn muốn dùng chế độ logo ảnh.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="brand-asset-grid">
                        <div class="brand-asset-card">
                            <div class="brand-asset-card__label">Logo ảnh hiện tại</div>
                            <div class="brand-asset-card__body">
                                @if($logoUrl)
                                    <img id="logoThumbnailPreview" src="{{ $logoUrl }}" alt="Logo ảnh hiện tại" class="brand-asset-card__image">
                                @else
                                    <div id="logoThumbnailPlaceholder" class="brand-asset-card__placeholder">Chưa có logo ảnh</div>
                                @endif
                            </div>
                        </div>

                        <div class="brand-asset-card">
                            <div class="brand-asset-card__label">Favicon hiện tại</div>
                            <div class="brand-asset-card__body" id="faviconPreviewWrap">
                                @if($faviconUrl)
                                    <img id="faviconPreview" src="{{ $faviconUrl }}" alt="Favicon hiện tại" class="brand-asset-card__favicon">
                                @else
                                    <div id="faviconPreviewText" class="brand-asset-card__placeholder">Chưa có favicon</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="qs-item">
                        <div class="qs-left">
                            <div class="qs-dot" style="background:#c9a84c"></div>
                            Cách dùng đúng
                        </div>
                        <div class="qs-right">
                            <div class="qs-sub" style="max-width:190px;text-align:right;">
                                Tên công ty là thông tin doanh nghiệp. Logo chữ là text thương hiệu. Hai trường này độc lập với nhau.
                            </div>
                        </div>
                    </div>

                    <div class="qs-item">
                        <div class="qs-left">
                            <div class="qs-dot" style="background:#4a9eff"></div>
                            Đồng bộ hiển thị
                        </div>
                        <div class="qs-right">
                            <div class="qs-sub" style="max-width:190px;text-align:right;">
                                Khi lưu cấu hình, header và footer trang chính cùng phần thương hiệu trong admin sẽ cập nhật theo chế độ bạn chọn.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    (function () {
        var brandNameInput = document.getElementById('brandNameInput');
        var logoTextInput = document.getElementById('logoTextInput');
        var textLogoPreview = document.getElementById('brandTextLogoPreview');
        var companyPreview = document.getElementById('brandCompanyPreview');
        var textInitial = document.getElementById('brandTextInitial');
        var logoInput = document.getElementById('logoInput');
        var logoPreview = document.getElementById('brandImagePreview');
        var logoPlaceholder = document.getElementById('brandImagePlaceholder');
        var logoThumbnailPreview = document.getElementById('logoThumbnailPreview');
        var logoThumbnailPlaceholder = document.getElementById('logoThumbnailPlaceholder');
        var faviconInput = document.getElementById('faviconInput');
        var faviconPreview = document.getElementById('faviconPreview');
        var faviconPreviewWrap = document.getElementById('faviconPreviewWrap');
        var logoModeCards = document.querySelectorAll('[data-brand-mode-card]');
        var logoModeInputs = document.querySelectorAll('input[name="logo_type"]');

        function escapeHtml(value) {
            return value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderLogoText(value) {
            return escapeHtml(value || 'VN●XPRESS').replace(/●/g, '<span>●</span>');
        }

        function updateTextPreview() {
            var companyName = brandNameInput ? brandNameInput.value.trim() : '';
            var logoText = logoTextInput ? logoTextInput.value.trim() : '';

            if (textLogoPreview) {
                textLogoPreview.innerHTML = renderLogoText(logoText || 'VN●XPRESS');
            }

            if (companyPreview) {
                companyPreview.textContent = companyName || 'Tên công ty';
            }

            if (textInitial) {
                textInitial.textContent = (companyName ? companyName.charAt(0) : 'S').toUpperCase();
            }
        }

        function updateModePreview() {
            var selected = document.querySelector('input[name="logo_type"]:checked');
            var value = selected ? selected.value : 'text';

            logoModeCards.forEach(function (card) {
                card.classList.toggle('is-active', card.getAttribute('data-brand-mode-card') === value);
            });

            document.querySelectorAll('[data-brand-preview]').forEach(function (preview) {
                preview.classList.toggle('is-visible', preview.getAttribute('data-brand-preview') === value);
            });
        }

        function ensureLogoThumbnail() {
            if (!logoThumbnailPreview && logoThumbnailPlaceholder) {
                logoThumbnailPreview = document.createElement('img');
                logoThumbnailPreview.id = 'logoThumbnailPreview';
                logoThumbnailPreview.alt = 'Logo ảnh hiện tại';
                logoThumbnailPreview.className = 'brand-asset-card__image';
                logoThumbnailPlaceholder.replaceWith(logoThumbnailPreview);
                logoThumbnailPlaceholder = null;
            }
        }

        function updateLogoPreview(file) {
            if (!file) {
                return;
            }

            ensureLogoThumbnail();

            var reader = new FileReader();
            reader.onload = function (event) {
                [logoPreview, logoThumbnailPreview].forEach(function (image) {
                    if (!image) {
                        return;
                    }

                    image.src = event.target.result;
                    image.style.display = 'block';
                });

                if (logoPlaceholder) {
                    logoPlaceholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }

        if (brandNameInput) {
            brandNameInput.addEventListener('input', updateTextPreview);
        }

        if (logoTextInput) {
            logoTextInput.addEventListener('input', updateTextPreview);
        }

        updateTextPreview();

        logoModeInputs.forEach(function (input) {
            input.addEventListener('change', updateModePreview);
        });
        updateModePreview();

        if (logoInput) {
            logoInput.addEventListener('change', function () {
                updateLogoPreview(logoInput.files && logoInput.files[0]);
            });
        }

        if (faviconInput) {
            faviconInput.addEventListener('change', function () {
                var file = faviconInput.files && faviconInput.files[0];
                if (!file) {
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    if (!faviconPreview) {
                        faviconPreview = document.createElement('img');
                        faviconPreview.id = 'faviconPreview';
                        faviconPreview.alt = 'Favicon hiện tại';
                        faviconPreview.className = 'brand-asset-card__favicon';

                        if (faviconPreviewWrap) {
                            faviconPreviewWrap.innerHTML = '';
                            faviconPreviewWrap.appendChild(faviconPreview);
                        }
                    }

                    faviconPreview.src = event.target.result;
                    faviconPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        }
    })();
</script>
@endsection
