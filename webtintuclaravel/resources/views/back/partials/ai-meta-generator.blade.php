<div class="ai-meta-panel mt-3 p-3 border rounded" style="background: rgba(201,168,76,0.05); border-color: rgba(201,168,76,0.2) !important;">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h6 class="mb-1" style="color: var(--accent-gold);">
                <i class="fas fa-magic mr-1"></i> Tạo Meta bằng AI
            </h6>
            <small class="text-muted">Dùng Gemini AI để tạo Meta Title, Description và Keywords tối ưu SEO</small>
        </div>
        <button type="button" class="btn btn-sm" id="aiGenerateMetaBtn"
            style="background: linear-gradient(135deg, var(--accent-gold), var(--accent-gold-light)); color: #000; font-weight: 600;">
            <i class="fas fa-sparkles mr-1"></i> Tạo Meta
        </button>
    </div>

    <div id="aiMetaResult" class="d-none">
        <div class="alert mb-2" style="background: rgba(40,167,69,0.1); border: 1px solid rgba(40,167,69,0.3); border-radius: 6px;">
            <div class="mb-2">
                <strong style="font-size: 11px; color: var(--accent-gold);">META TITLE:</strong>
                <div id="aiMetaTitle" style="font-size: 13px; color: #1a0dab;"></div>
            </div>
            <div class="mb-2">
                <strong style="font-size: 11px; color: var(--accent-gold);">META DESCRIPTION:</strong>
                <div id="aiMetaDesc" style="font-size: 12px; color: #545454;"></div>
            </div>
            <div>
                <strong style="font-size: 11px; color: var(--accent-gold);">META KEYWORDS:</strong>
                <div id="aiMetaKeywords" style="font-size: 12px; color: var(--text-secondary);"></div>
            </div>
        </div>
        <button type="button" class="btn btn-success btn-sm" id="aiApplyMeta">
            <i class="fas fa-check mr-1"></i> Áp dụng
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm ml-2" id="aiCancelMeta">
            Hủy
        </button>
    </div>

    <div id="aiMetaLoading" class="d-none text-center py-3">
        <div class="spinner-border text-warning" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 2px;">
            <span class="sr-only">Loading...</span>
        </div>
        <span class="ml-2" style="font-size: 13px; color: var(--text-muted);">AI đang tạo Meta Tags...</span>
    </div>
</div>

<style>
#aiMetaResult .alert { padding: 12px 14px; }
#aiMetaResult strong { text-transform: uppercase; letter-spacing: 0.5px; }
</style>

<script>
(function() {
    var btn = document.getElementById('aiGenerateMetaBtn');
    var resultDiv = document.getElementById('aiMetaResult');
    var loadingDiv = document.getElementById('aiMetaLoading');
    var applyBtn = document.getElementById('aiApplyMeta');
    var cancelBtn = document.getElementById('aiCancelMeta');

    var generatedData = null;
    var endpoint = @json(url('admin/ai/generate-meta'));

    function getFormData() {
        return {
            title: (document.querySelector('[name="Name"]') || document.querySelector('#title'))?.value || '',
            description: document.querySelector('[name="SmallDescription"]')?.value || '',
            content: window.CKEDITOR ? CKEDITOR.instances.ckeditor?.getData() || '' : '',
        };
    }

    function showLoading() {
        btn.disabled = true;
        loadingDiv.classList.remove('d-none');
        resultDiv.classList.add('d-none');
    }

    function hideLoading() {
        btn.disabled = false;
        loadingDiv.classList.add('d-none');
    }

    function showError(msg) {
        hideLoading();
        var alert = document.createElement('div');
        alert.className = 'alert alert-danger mt-2';
        alert.style.cssText = 'font-size:13px; padding: 8px 12px;';
        alert.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> ' + msg;
        resultDiv.parentElement.insertBefore(alert, resultDiv);
        setTimeout(function() { alert.remove(); }, 5000);
    }

    btn.addEventListener('click', function() {
        var data = getFormData();
        if (!data.title) {
            showError('Vui lòng nhập tiêu đề bài viết trước.');
            return;
        }

        showLoading();

        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        formData.append('title', data.title);
        formData.append('description', data.description);
        formData.append('content', data.content);

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            hideLoading();
            if (res.success && res.data) {
                generatedData = res.data;
                document.getElementById('aiMetaTitle').textContent = res.data.meta_title || '';
                document.getElementById('aiMetaDesc').textContent = res.data.meta_description || '';
                document.getElementById('aiMetaKeywords').textContent = res.data.meta_keywords || '';
                resultDiv.classList.remove('d-none');
            } else {
                showError(res.message || 'Có lỗi xảy ra.');
            }
        })
        .catch(function() {
            hideLoading();
            showError('Lỗi kết nối. Vui lòng thử lại.');
        });
    });

    applyBtn.addEventListener('click', function() {
        if (!generatedData) return;
        var metaTitle = document.querySelector('[name="MetaTitle"]');
        var metaDesc = document.querySelector('[name="MetaDescription"]');
        var metaKeywords = document.querySelector('[name="MetaKeyword"]');
        if (metaTitle && generatedData.meta_title) metaTitle.value = generatedData.meta_title;
        if (metaDesc && generatedData.meta_description) metaDesc.value = generatedData.meta_description;
        if (metaKeywords && generatedData.meta_keywords) metaKeywords.value = generatedData.meta_keywords;
        resultDiv.classList.add('d-none');
        generatedData = null;
    });

    cancelBtn.addEventListener('click', function() {
        resultDiv.classList.add('d-none');
        generatedData = null;
    });
})();
</script>
