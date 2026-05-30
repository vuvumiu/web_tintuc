@extends('back.template.master')

@section('title', 'Chỉnh sửa bài viết')
@section('news', 'active')

@section('content')
<div class="col-md-12">
    <div class="card-header">
        <a class="btn btn-secondary" href="{{ url('admin/news/list') }}">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
        <a class="btn btn-info" href="{{ url('admin/news/preview/' . $News->RowID) }}" target="_blank">
            <i class="fas fa-eye mr-1"></i> Xem trước
        </a>
        <a class="btn btn-success" href="{{ url('admin/news/duplicate/' . $News->RowID) }}">
            <i class="fas fa-copy mr-1"></i> Sao chép bài
        </a>
    </div>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Chỉnh sửa bài viết</h3>
        </div>

        <form role="form" action="{{ url('admin/news/edit/' . $News->RowID) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">

                {{-- Status badges --}}
                @if(isset($schedule))
                <div class="mb-3">
                    @if($schedule->status === 'draft')
                        <span class="badge badge-secondary"><i class="fas fa-file mr-1"></i> Nháp</span>
                    @elseif($schedule->status === 'pending')
                        <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Chờ duyệt</span>
                    @elseif($schedule->status === 'approved')
                        <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Đã duyệt</span>
                    @elseif($schedule->status === 'rejected')
                        <span class="badge badge-danger"><i class="fas fa-times mr-1"></i> Từ chối</span>
                        @if($schedule->reject_reason)
                            <small class="text-danger ml-2">Lý do: {{ $schedule->reject_reason }}</small>
                        @endif
                    @elseif($schedule->status === 'scheduled')
                        <span class="badge badge-info"><i class="fas fa-calendar mr-1"></i> Hẹn giờ:
                            {{ $schedule->scheduled_at ? $schedule->scheduled_at->format('d/m/Y H:i') : '' }}
                        </span>
                    @elseif($schedule->status === 'published')
                        <span class="badge badge-success"><i class="fas fa-globe mr-1"></i> Đã xuất bản</span>
                    @endif
                </div>
                @endif

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
                                    <input type="text" class="form-control" name="Name" id="title" onkeyup="ChangeToSlug()" value="{{ $News->Name }}" required>
                                </div>

                                <div class="form-group">
                                    <label>Đường dẫn (Slug)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="Alias" id="slug" value="{{ $News->Alias }}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-secondary" onclick="ChangeToSlug()" title="Tạo lại slug từ tiêu đề">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Tóm tắt</label>
                                    <textarea name="SmallDescription" rows="3" class="form-control">{{ $News->SmallDescription }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Nội dung bài viết <span class="text-danger">*</span></label>
                                    <textarea id="ckeditor" name="Description" rows="10" class="form-control">{{ $News->Description }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Danh mục</label>
                                    <select class="form-control" name="RowIDCat">
                                        @if(isset($NewsCategory) && count($NewsCategory) > 0)
                                            @foreach($NewsCategory as $v)
                                                <option value="{{ $v->RowID }}" {{ $News->RowIDCat == $v->RowID ? 'selected' : '' }}>{{ $v->Name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tác giả</label>
                                    <select class="form-control" name="author_id">
                                        <option value="">-- Mặc định --</option>
                                        @if(isset($authors) && count($authors) > 0)
                                            @foreach($authors as $a)
                                                <option value="{{ $a->id }}" {{ $News->author_id == $a->id ? 'selected' : '' }}>
                                                    {{ $a->fullname ?? $a->username }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tags</label>
                                    @php
                                        $newsTags = $News->relationLoaded('tags') ? $News->getRelation('tags') : collect();
                                        $tagNames = $newsTags instanceof \Illuminate\Support\Collection
                                            ? $newsTags->pluck('name')->filter()->values()->toArray()
                                            : [];
                                        if (empty($tagNames) && !empty($News->getAttributes()['tags'] ?? null)) {
                                            $tagNames = array_values(array_filter(array_map('trim', explode(',', $News->getAttributes()['tags']))));
                                        }
                                    @endphp
                                    <input type="text" class="form-control" name="tags_input" id="tagsInput" placeholder="Nhập tag, Enter để thêm">
                                    <div id="tagsList" class="mt-2 d-flex flex-wrap gap-1"></div>
                                    <input type="hidden" name="tags" id="tagsHidden" value="{{ implode(',', $tagNames) }}">
                                    @include('back.partials.ai-tag-suggester')
                                </div>

                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select class="form-control" name="Status">
                                        <option value="0" {{ $News->Status == 0 ? 'selected' : '' }}>Nháp</option>
                                        <option value="1" {{ $News->Status == 1 ? 'selected' : '' }}>Xuất bản</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Lượt xem</label>
                                    <input type="number" name="Views" value="{{ $News->Views ?? 0 }}" class="form-control" min="0">
                                </div>

                                <div class="form-group">
                                    <label>Ảnh đại diện</label>
                                    @if($News->Images)
                                        <div class="mb-2">
                                            <img src="{{ url('images/news/' . $News->Images) }}" width="120" class="img-thumbnail">
                                            <small class="d-block text-muted">Ảnh hiện tại</small>
                                        </div>
                                    @endif
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
                            <textarea name="MetaTitle" rows="2" class="form-control seo-source" data-target="seo-preview-title">{{ $News->MetaTitle }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Meta Description</label>
                            <textarea name="MetaDescription" rows="5" class="form-control seo-source" data-target="seo-preview-desc">{{ $News->MetaDescription }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Meta Keywords</label>
                            <textarea name="MetaKeyword" rows="2" class="form-control">{{ $News->MetaKeyword }}</textarea>
                        </div>

                        {{-- Google Snippet Preview --}}
                        <div class="border rounded p-3 mt-3" style="background:#fafafa;">
                            <p class="mb-2 text-muted small"><i class="fas fa-search mr-1"></i> Google Xem trước</p>
                            <div id="seo-preview-title" style="color:#1a0dab;font-size:18px;line-height:1.3;margin-bottom:4px;"></div>
                            <div style="color:#006621;font-size:14px;margin-bottom:2px;" id="seo-preview-url"></div>
                            <div id="seo-preview-desc" style="color:#545454;font-size:13px;line-height:1.4;"></div>
                        </div>
                    </div>

                    {{-- TAB 3: Workflow --}}
                    <div class="tab-pane fade" id="tab-workflow">
                        <div class="form-group">
                            <label>Hình thức xuất bản</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="publishNow" name="publish_type" value="now"
                                            {{ (isset($schedule) && $schedule->publish_type === 'now') || !isset($schedule) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="publishNow">
                                            <strong>Xuất bản ngay</strong>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="publishSchedule" name="publish_type" value="schedule"
                                            {{ isset($schedule) && $schedule->publish_type === 'schedule' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="publishSchedule">
                                            <strong>Hẹn giờ xuất bản</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="scheduleTimeGroup" style="{{ isset($schedule) && $schedule->publish_type === 'schedule' ? '' : 'display:none' }}">
                            <label>Thời gian xuất bản <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scheduled_at" id="scheduledAt" class="form-control"
                                value="{{ isset($schedule) && $schedule->scheduled_at ? str_replace(' ', 'T', $schedule->scheduled_at->format('Y-m-d H:i')) : '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Lưu thay đổi
                </button>
                @if(isset($schedule) && ($schedule->status === 'draft' || $schedule->status === 'rejected'))
                    <a href="{{ url('admin/news/submit-review/' . $News->RowID) }}" class="btn btn-warning ml-2">
                        <i class="fas fa-paper-plane mr-1"></i> Gửi duyệt
                    </a>
                @endif
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

    // Tags input - pre-load existing tags
    window.tagNames = [];
    var existingTags = $('#tagsHidden').val();
    if (existingTags) {
        window.tagNames = existingTags.split(',').filter(function(t){ return t.trim(); });
    }

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

    window.renderTags();

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // SEO Live Preview
    var siteUrl = '{{ url("/") }}/';
    var baseSlug = $('#slug').val() || 'ten-bai-viet';

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

    // Init
    updateSeoPreview();
    $('[name="MetaTitle"], [name="MetaDescription"], [name="Name"], #slug').on('input', updateSeoPreview);
    // When user clicks the regenerate-slug button
    $('#slug').closest('.input-group').find('button').on('click', function() {
        setTimeout(updateSeoPreview, 10);
    });

    baseSlug = $('#slug').val() || baseSlug;
})();
</script>
@endpush
