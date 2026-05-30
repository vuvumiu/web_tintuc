<div class="ai-tag-suggester mt-2">
    <button type="button" class="btn btn-sm" id="aiSuggestTagsBtn"
        style="background: rgba(201,168,76,0.1); border: 1px solid rgba(201,168,76,0.3); color: var(--accent-gold);">
        <i class="fas fa-lightbulb mr-1"></i> Gợi ý Tags
    </button>

    <div id="aiTagSuggestions" class="d-none mt-2 p-2 rounded"
        style="background: rgba(201,168,76,0.05); border: 1px solid rgba(201,168,76,0.2);">
        <small class="d-block mb-2" style="color: var(--text-muted); font-size: 11px;">
            Click để thêm tag:
        </small>
        <div id="aiTagList" class="d-flex flex-wrap gap-1"></div>
    </div>

    <div id="aiTagLoading" class="d-none mt-2">
        <small style="color: var(--text-muted); font-size: 11px;">
            <span class="spinner-border spinner-border-sm mr-1" role="status"></span>
            Đang gợi ý tags...
        </small>
    </div>
</div>

<script>
(function() {
    var btn = document.getElementById('aiSuggestTagsBtn');
    var suggestionsDiv = document.getElementById('aiTagSuggestions');
    var tagListDiv = document.getElementById('aiTagList');
    var loadingDiv = document.getElementById('aiTagLoading');
    var endpoint = @json(url('admin/ai/suggest-tags'));

    function getFormData() {
        return {
            title: (document.querySelector('[name="Name"]') || document.querySelector('#title'))?.value || '',
            content: window.CKEDITOR ? CKEDITOR.instances.ckeditor?.getData() || '' : '',
            category: document.querySelector('[name="RowIDCat"]')?.selectedOptions[0]?.textContent || '',
        };
    }

    function showError(msg) {
        var alert = document.createElement('div');
        alert.className = 'alert alert-danger mt-2';
        alert.style.cssText = 'font-size:12px; padding: 6px 10px;';
        alert.textContent = msg;
        btn.parentElement.insertBefore(alert, suggestionsDiv);
        setTimeout(function() { alert.remove(); }, 5000);
    }

    btn.addEventListener('click', function() {
        var data = getFormData();
        if (!data.title) {
            showError('Vui lòng nhập tiêu đề trước.');
            return;
        }

        btn.disabled = true;
        suggestionsDiv.classList.add('d-none');
        loadingDiv.classList.remove('d-none');

        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        formData.append('title', data.title);
        formData.append('content', data.content);
        formData.append('category', data.category);

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function(response) {
            return response.json().catch(function() {
                throw new Error('Endpoint gợi ý tag không trả về JSON hợp lệ.');
            }).then(function(json) {
                if (!response.ok) {
                    throw new Error(json.message || ('Không thể kết nối AI. HTTP ' + response.status));
                }
                return json;
            });
        })
        .then(function(res) {
            btn.disabled = false;
            loadingDiv.classList.add('d-none');

            if (res.success && res.data && res.data.length > 0) {
                renderSuggestions(res.data);
                suggestionsDiv.classList.remove('d-none');
            } else {
                showError(res.message || 'Không có gợi ý nào.');
            }
        })
        .catch(function(error) {
            btn.disabled = false;
            loadingDiv.classList.add('d-none');
            showError(error.message || 'Lỗi kết nối. Vui lòng thử lại.');
        });
    });

    function renderSuggestions(tags) {
        tagListDiv.innerHTML = '';

        if (!Array.isArray(window.tagNames)) {
            window.tagNames = [];
        }

        tags.forEach(function(tag) {
            var name = tag.name || tag.slug || '';
            var alreadyAdded = window.tagNames.some(function(item) {
                return item.toLowerCase() === name.toLowerCase();
            });
            var existsBadge = tag.exists ? ' <span class="badge badge-success" style="font-size:9px;">Có</span>' : '';

            var badge = document.createElement('span');
            badge.className = 'badge mr-1 mb-1';
            badge.style.cssText = alreadyAdded
                ? 'background: rgba(40,167,69,0.2); color: #28a745; cursor: default; padding: 4px 8px; font-size: 11px;'
                : 'background: rgba(201,168,76,0.15); color: var(--accent-gold); cursor: pointer; padding: 4px 8px; font-size: 11px;';

            badge.innerHTML = name + existsBadge + (alreadyAdded ? '' : ' <i class="fas fa-plus" style="font-size:9px;"></i>');

            if (!alreadyAdded) {
                badge.addEventListener('click', function() {
                    if (name && !window.tagNames.includes(name)) {
                        window.tagNames.push(name);
                        if (typeof window.renderTags === 'function') {
                            window.renderTags();
                        }
                        badge.style.cssText = 'background: rgba(40,167,69,0.2); color: #28a745; cursor: default; padding: 4px 8px; font-size: 11px;';
                        badge.innerHTML = name + ' <span class="badge badge-success" style="font-size:9px;">Thêm</span>';
                        badge.style.cursor = 'default';
                    }
                });
            }

            tagListDiv.appendChild(badge);
        });
    }
})();
</script>
