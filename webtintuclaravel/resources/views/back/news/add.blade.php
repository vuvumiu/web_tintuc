@extends('back.template.master')

@section('title', 'Thêm bài viết mới')
@section('news', 'active')

@section('content')
<div class="col-md-12">
    <div class="card-header">
        <a class="btn btn-secondary" href="{{ url('admin/news/list') }}">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Thêm bài viết mới</h3>
        </div>

        <form role="form" action="{{ url('admin/news/add') }}" method="POST" enctype="multipart/form-data" id="newsForm">
            @csrf
            <div class="card-body">

                {{-- Tabs --}}
                <div class="nav-tabs-custom mb-3">
                    <ul class="nav nav-tabs" id="newsTab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-content">Nội dung</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-seo">SEO</a></li>
                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-workflow">Xuất bản</a></li>
                    </ul>
                </div>

                <div class="tab-content" id="newsTabContent">
                    {{-- TAB 1: Nội dung --}}
                    <div class="tab-pane fade show active" id="tab-content">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Tiêu đề bài viết <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="Name" id="title" onkeyup="ChangeToSlug()" placeholder="Nhập tiêu đề..." required>
                                </div>

                                <div class="form-group">
                                    <label>Đường dẫn (Slug)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="Alias" id="slug" placeholder="tu-dong-tao-neu-trong">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-secondary" onclick="ChangeToSlug()" title="Tạo lại slug từ tiêu đề">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Tóm tắt</label>
                                    <textarea name="SmallDescription" rows="3" class="form-control" placeholder="Mô tả ngắn gọn..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Nội dung bài viết <span class="text-danger">*</span></label>
                                    <textarea id="ckeditor" name="Description" rows="10" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Danh mục</label>
                                    <select class="form-control" name="RowIDCat">
                                        @if(isset($NewsCategory) && count($NewsCategory) > 0)
                                            @foreach($NewsCategory as $v)
                                                <option value="{{ $v->RowID }}">{{ $v->Name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tác giả</label>
                                    <select class="form-control" name="author_id">
                                        <option value="">-- Tác giả mặc định --</option>
                                        @if(isset($authors) && count($authors) > 0)
                                            @foreach($authors as $a)
                                                <option value="{{ $a->id }}">{{ $a->fullname ?? $a->username }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tags</label>
                                    <input type="text" class="form-control" name="tags_input" id="tagsInput" placeholder="Nhập tag, Enter để thêm">
                                    <div id="tagsList" class="mt-2 d-flex flex-wrap gap-1"></div>
                                    <input type="hidden" name="tags" id="tagsHidden" value="">
                                    @include('back.partials.ai-tag-suggester')
                                </div>

                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select class="form-control" name="Status">
                                        <option value="0">Nháp (Không hiển thị)</option>
                                        <option value="1" selected>Xuất bản ngay</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Lượt xem ban đầu</label>
                                    <input type="number" name="Views" value="0" class="form-control" min="0">
                                </div>

                                <div class="form-group">
                                    <label>Ảnh đại diện</label>
                                    <input type="file" name="Images" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: SEO --}}
                    <div class="tab-pane fade" id="tab-seo">
                        @include('back.partials.ai-meta-generator')

                        <div class="form-group">
                            <label>Meta Title</label>
                            <textarea name="MetaTitle" rows="2" maxlength="70" class="form-control seo-source" data-target="seo-preview-title" placeholder="SEO Title..."></textarea>
                            <small class="text-muted">Khuyến nghị 50-60 ký tự, tối đa 70.</small>
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="MetaDescription" rows="5" maxlength="180" class="form-control seo-source" data-target="seo-preview-desc" placeholder="SEO Description..."></textarea>
                            <small class="text-muted">Khuyến nghị 140-160 ký tự, tối đa 180.</small>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <textarea name="MetaKeyword" rows="2" maxlength="500" class="form-control" placeholder="Keyword1, Keyword2..."></textarea>
                        </div>

                        {{-- Google Snippet Preview --}}
                        <div class="border rounded p-3 mt-3" style="background:#fafafa;">
                            <p class="mb-2 text-muted small"><i class="fas fa-search mr-1"></i> Google Xem trước</p>
                            <div id="seo-preview-title" style="color:#1a0dab;font-size:18px;line-height:1.3;margin-bottom:4px;"></div>
                            <div style="color:#006621;font-size:14px;margin-bottom:2px;" id="seo-preview-url"></div>
                            <div id="seo-preview-desc" style="color:#545454;font-size:13px;line-height:1.4;"></div>
                        </div>
                    </div>

                    {{-- TAB 3: Workflow Xuất bản --}}
                    <div class="tab-pane fade" id="tab-workflow">
                        <div class="form-group">
                            <label>Hình thức xuất bản</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="publishNow" name="publish_type" value="now" checked>
                                        <label class="custom-control-label" for="publishNow">
                                            <strong>Xuất bản ngay</strong><br>
                                            <small class="text-muted">Bài viết sẽ hiển thị ngay lập tức</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="publishSchedule" name="publish_type" value="schedule">
                                        <label class="custom-control-label" for="publishSchedule">
                                            <strong>Hẹn giờ xuất bản</strong><br>
                                            <small class="text-muted">Bài viết sẽ được xuất bản tự động theo lịch</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="scheduleTimeGroup" style="display:none;">
                            <label>Thời gian xuất bản <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scheduled_at" id="scheduledAt" class="form-control">
                            <small class="text-muted">Chọn thời gian trong tương lai. Bài viết sẽ được xuất bản tự động khi đến giờ.</small>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Quy trình xuất bản:</strong>
                            <ol class="mb-0 mt-2">
                                <li><strong>Nháp:</strong> Lưu bài viết nhưng chưa xuất bản</li>
                                <li><strong>Chờ duyệt:</strong> Gửi bài viết để được duyệt trước khi xuất bản</li>
                                <li><strong>Đã duyệt:</strong> Bài viết đã được phê duyệt</li>
                                <li><strong>Đã từ chối:</strong> Bài viết bị từ chối với lý do cụ thể</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" name="submit_action" value="save_draft" class="btn btn-secondary">
                    <i class="fas fa-save mr-1"></i> Lưu nháp
                </button>
                <button type="submit" name="submit_action" value="submit_review" class="btn btn-warning ml-2">
                    <i class="fas fa-paper-plane mr-1"></i> Gửi duyệt
                </button>
                @if($canPublishDirectly ?? false)
                    <button type="submit" name="submit_action" value="publish_now" class="btn btn-success ml-2">
                        <i class="fas fa-globe mr-1"></i> Xuất bản
                    </button>
                @endif
                <a href="{{ url('admin/news/list') }}" class="btn btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@stop

@push('scripts')
<script>
(function() {
    // Publish type toggle
    $('input[name="publish_type"]').on('change', function() {
        if ($(this).val() === 'schedule') {
            $('#scheduleTimeGroup').show();
        } else {
            $('#scheduleTimeGroup').hide();
        }
    });

    // Tags input
    window.tagNames = window.tagNames || [];
    $('#tagsInput').on('keydown', function(e) {
        if (e.key === 'Enter' || e.which === 13) {
            e.preventDefault();
            var val = $(this).val().trim();
            if (val && !window.tagNames.includes(val)) {
                window.tagNames.push(val);
                window.renderTags();
            }
            $(this).val('');
        }
    });

    window.renderTags = function() {
        $('#tagsList').empty();
        window.tagNames.forEach(function(tag, i) {
            $('#tagsList').append(
                '<span class="badge badge-info mr-1" style="font-size:.85rem;padding:4px 8px;">' +
                escapeHtml(tag) +
                ' <a href="#" onclick="removeTag(' + i + ');return false;" style="color:#fff;">&times;</a></span>'
            );
        });
        $('#tagsHidden').val(window.tagNames.join(','));
    };

    window.removeTag = function(i) {
        window.tagNames.splice(i, 1);
        window.renderTags();
    };

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // SEO Live Preview
    var siteUrl = '{{ url("/") }}/';
    var baseSlug = '';

    function updateSeoPreview() {
        var titleVal = $('[name="MetaTitle"]').val() || $('[name="Name"]').val() || '';
        var descVal = $('[name="MetaDescription"]').val() || '';
        var slugVal = $('#slug').val() || baseSlug;

        var displayTitle = titleVal.length > 60 ? titleVal.substring(0, 57) + '...' : titleVal;
        $('#seo-preview-title').text(displayTitle);

        var slugSafe = slugVal.replace(/^\/+/, '').replace(/\/+$/, '');
        $('#seo-preview-url').text(siteUrl + slugSafe);

        var displayDesc = descVal.length > 160 ? descVal.substring(0, 157) + '...' : descVal;
        $('#seo-preview-desc').text(displayDesc);
    }

    updateSeoPreview();
    $('[name="MetaTitle"], [name="MetaDescription"], [name="Name"], #slug').on('input', updateSeoPreview);
    $('#slug').closest('.input-group').find('button').on('click', function() {
        setTimeout(updateSeoPreview, 10);
    });
})();
</script>
@endpush
