<div id="aiCommentModeration">
    <button type="button" class="btn btn-sm ai-check-single" style="display:none; background: rgba(138,43,226,0.1); border: 1px solid rgba(138,43,226,0.3); color: #9b59b6; font-size: 11px; padding: 3px 8px;"
        data-comment-id="">
        <i class="fas fa-robot mr-1"></i> Check AI
    </button>
</div>

<div id="aiModModal" class="modal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="background: #1a1a2e; border: 1px solid rgba(201,168,76,0.2);">
            <div class="modal-header" style="border-bottom: 1px solid rgba(201,168,76,0.2);">
                <h6 class="modal-title" style="color: var(--accent-gold);">
                    <i class="fas fa-robot mr-1"></i> Kết quả kiểm duyệt AI
                </h6>
                <button type="button" class="close" onclick="document.getElementById('aiModModal').style.display='none';" style="color: #fff; opacity: 0.5;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="color: var(--text-secondary); font-size: 13px;">
                <div id="aiModLoading" class="text-center py-3">
                    <div class="spinner-border text-warning" role="status" style="width:1.5rem;height:1.5rem;border-width:2px;"></div>
                    <div class="mt-2">AI đang phân tích...</div>
                </div>
                <div id="aiModResult" class="d-none">
                    <div class="mb-2">
                        <span class="badge" id="aiModBadge" style="font-size: 12px; padding: 5px 10px;"></span>
                    </div>
                    <div class="mb-2">
                        <strong style="color: var(--text-muted); font-size: 11px;">ĐỘ CHẮC CHẮN:</strong>
                        <div id="aiModConfidence" style="font-size: 13px;"></div>
                    </div>
                    <div>
                        <strong style="color: var(--text-muted); font-size: 11px;">LÝ DO:</strong>
                        <div id="aiModReason" style="font-size: 13px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(201,168,76,0.2);">
                <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('aiModModal').style.display='none';">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var modal = document.getElementById('aiModModal');
    var loadingDiv = document.getElementById('aiModLoading');
    var resultDiv = document.getElementById('aiModResult');

    window.showAIModModal = function() {
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.background = 'rgba(0,0,0,0.6)';
        loadingDiv.classList.remove('d-none');
        resultDiv.classList.add('d-none');
    };

    window.hideAIModModal = function() {
        modal.style.display = 'none';
    };

    window.checkAIComment = function(commentId, rowElement) {
        showAIModModal();

        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        formData.append('comment_id', commentId);

        fetch('/admin/ai/moderate-comment', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            loadingDiv.classList.add('d-none');
            resultDiv.classList.remove('d-none');

            var action = res.data?.action || 'FLAG';
            var confidence = res.data?.confidence || 0;
            var reason = res.data?.reason || '';
            var isActive = res.data?.comment_active;

            var badge = document.getElementById('aiModBadge');
            if (action === 'APPROVE') {
                badge.className = 'badge badge-success';
                badge.innerHTML = '<i class="fas fa-check mr-1"></i> DUYỆT';
            } else if (action === 'REJECT') {
                badge.className = 'badge badge-danger';
                badge.innerHTML = '<i class="fas fa-ban mr-1"></i> TỪ CHỐI';
            } else {
                badge.className = 'badge badge-warning';
                badge.innerHTML = '<i class="fas fa-flag mr-1"></i> CỜ CỜ';
            }

            document.getElementById('aiModConfidence').textContent = Math.round(confidence * 100) + '%';
            document.getElementById('aiModReason').textContent = reason;

            if (isActive !== undefined && !isActive) {
                var statusCell = rowElement?.querySelector('td:nth-child(7)');
                if (statusCell) {
                    statusCell.innerHTML = '<span class="vu-badge-sm neutral"><i class="fas fa-eye-slash mr-1"></i>Ẩn</span>';
                }
            }
        })
        .catch(function() {
            loadingDiv.classList.add('d-none');
            resultDiv.classList.remove('d-none');
            document.getElementById('aiModBadge').className = 'badge badge-secondary';
            document.getElementById('aiModBadge').textContent = 'LỖI';
            document.getElementById('aiModConfidence').textContent = '-';
            document.getElementById('aiModReason').textContent = 'Không thể kết nối AI. Vui lòng thử lại.';
        });
    };

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideAIModModal();
        }
    });
})();
</script>
