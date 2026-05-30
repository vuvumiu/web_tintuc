<style>
.ai-chatbot-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: 'Roboto', sans-serif;
}

.ai-chat-toggle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffd60a, #ffc300);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(255, 214, 10, 0.4);
    transition: all 0.3s ease;
    position: relative;
}
.ai-chat-toggle:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 28px rgba(255, 214, 10, 0.55);
}
.ai-chat-toggle i {
    font-size: 22px;
    color: #1a1a1a;
}
.ai-chat-toggle .ai-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 14px;
    height: 14px;
    background: #e74c3c;
    border-radius: 50%;
    border: 2px solid #fff;
    display: none;
}
.ai-chat-badge-dot {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 16px;
    height: 16px;
    background: #e74c3c;
    border-radius: 50%;
    animation: aiPulse 2s infinite;
}
@keyframes aiPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.7; }
}

.ai-chat-window {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 360px;
    max-width: calc(100vw - 48px);
    height: 520px;
    max-height: calc(100vh - 120px);
    background: #1a1a2e;
    border-radius: 16px;
    box-shadow: 0 12px 48px rgba(0,0,0,0.5);
    display: none;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(255,214,10,0.15);
}

.ai-chat-window.open {
    display: flex;
    animation: aiSlideUp 0.25s ease-out;
}
@keyframes aiSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.ai-chat-header {
    background: linear-gradient(135deg, #ffd60a, #ffc300);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 16px 16px 0 0;
}
.ai-chat-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
}
.ai-chat-avatar {
    width: 36px;
    height: 36px;
    background: #1a1a2e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffd60a;
    font-size: 16px;
}
.ai-chat-header-info h4 {
    margin: 0;
    font-size: 14px;
    font-weight: 700;
    color: #1a1a1a;
}
.ai-chat-header-info span {
    font-size: 11px;
    color: #444;
}
.ai-chat-header-actions {
    display: flex;
    gap: 8px;
}
.ai-chat-header-btn {
    width: 28px;
    height: 28px;
    border: none;
    background: rgba(26,26,46,0.15);
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1a1a1a;
    font-size: 13px;
    transition: background 0.2s;
}
.ai-chat-header-btn:hover { background: rgba(26,26,46,0.25); }
.ai-chat-header-btn.close:hover { background: rgba(231,76,60,0.2); color: #c0392b; }

.ai-chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}
.ai-chat-body::-webkit-scrollbar { width: 4px; }
.ai-chat-body::-webkit-scrollbar-track { background: transparent; }
.ai-chat-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

.ai-msg {
    display: flex;
    gap: 8px;
    max-width: 85%;
    animation: aiFadeIn 0.3s ease;
}
@keyframes aiFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.ai-msg.user { align-self: flex-end; flex-direction: row-reverse; }
.ai-msg-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}
.ai-msg.bot .ai-msg-avatar { background: linear-gradient(135deg, #ffd60a, #ffc300); color: #1a1a1a; }
.ai-msg.user .ai-msg-avatar { background: rgba(255,255,255,0.1); color: #aaa; }
.ai-msg-content {
    background: rgba(255,255,255,0.06);
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 13px;
    line-height: 1.5;
    color: #d1d1d1;
    word-break: break-word;
}
.ai-msg.user .ai-msg-content {
    background: linear-gradient(135deg, #ffd60a, #ffc300);
    color: #1a1a1a;
}
.ai-msg-time {
    font-size: 10px;
    color: #555;
    margin-top: 2px;
    padding: 0 4px;
}
.ai-msg.user .ai-msg-time { text-align: right; }

.ai-typing {
    display: flex;
    gap: 8px;
    max-width: 85%;
    align-items: flex-end;
}
.ai-typing-dots {
    display: flex;
    gap: 3px;
    padding: 12px 14px;
    background: rgba(255,255,255,0.06);
    border-radius: 12px;
}
.ai-typing-dot {
    width: 6px;
    height: 6px;
    background: #888;
    border-radius: 50%;
    animation: aiTypingBounce 1.4s infinite ease-in-out;
}
.ai-typing-dot:nth-child(1) { animation-delay: 0s; }
.ai-typing-dot:nth-child(2) { animation-delay: 0.2s; }
.ai-typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes aiTypingBounce {
    0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

.ai-chat-footer {
    padding: 12px 16px;
    background: rgba(0,0,0,0.2);
    border-radius: 0 0 16px 16px;
    border-top: 1px solid rgba(255,255,255,0.05);
}
.ai-chat-input-row {
    display: flex;
    gap: 8px;
    align-items: flex-end;
}
.ai-chat-input {
    flex: 1;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 10px 14px;
    color: #d1d1d1;
    font-size: 13px;
    resize: none;
    outline: none;
    font-family: 'Roboto', sans-serif;
    max-height: 80px;
    line-height: 1.4;
    overflow-y: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    -webkit-appearance: none;
    appearance: none;
}
.ai-chat-input::-webkit-scrollbar { display: none; width: 0; height: 0; }
.ai-chat-input::placeholder { color: #666; }
.ai-chat-input:focus { border-color: rgba(255,214,10,0.4); }
.ai-chat-send {
    width: 38px;
    height: 38px;
    border: none;
    background: linear-gradient(135deg, #ffd60a, #ffc300);
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1a1a1a;
    font-size: 14px;
    transition: all 0.2s;
    flex-shrink: 0;
}
.ai-chat-send:hover { transform: scale(1.05); }
.ai-chat-send:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

.ai-chat-welcome {
    text-align: center;
    padding: 20px 10px;
    color: #888;
    font-size: 13px;
}
.ai-chat-welcome i { font-size: 32px; color: #ffd60a; margin-bottom: 10px; display: block; }

@media (max-width: 480px) {
    .ai-chat-window {
        width: calc(100vw - 32px);
        right: -12px;
        bottom: 70px;
        height: calc(100vh - 100px);
    }
}/* =============================================
   AI CHATBOT - Text Selection Popup (ChatGPT-style)
   ============================================= */

.ai-text-selection-popup {
    position: fixed;
    z-index: 100001;
    background: #2a2a3e;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 12px;
    padding: 0;
    display: none;
    box-shadow: 0 8px 32px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05);
    overflow: hidden;
    animation: ai-sel-popup-in 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    min-width: 300px;
    max-width: 340px;
}

/* Ẩn hoàn toàn tất cả scrollbar trong popup */
.ai-text-selection-popup * {
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}
.ai-text-selection-popup *::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

@keyframes ai-sel-popup-in {
    from { opacity: 0; transform: translateY(6px) scale(0.97); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}


.ai-sel-header {
    padding: 10px 14px 8px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ai-sel-header-title {
    font-size: 12px;
    font-weight: 600;
    color: #ffd60a;
    display: flex;
    align-items: center;
    gap: 6px;
}

.ai-sel-close-btn {
    background: none;
    border: none;
    color: rgba(255,255,255,0.4);
    cursor: pointer;
    padding: 2px;
    font-size: 14px;
    line-height: 1;
    transition: color 0.2s;
}

.ai-sel-close-btn:hover { color: rgba(255,255,255,0.7); }

.ai-sel-selected-text {
    padding: 10px 14px;
    background: rgba(255,255,255,0.04);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    max-height: 72px;
    overflow: hidden;
    -webkit-line-clamp: 3;
    line-clamp: 3;
}

.ai-sel-selected-text p {
    margin: 0;
    font-size: 12px;
    color: rgba(255,255,255,0.6);
    font-style: italic;
    line-height: 1.5;
    word-break: break-word;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ai-sel-selected-text::-webkit-scrollbar { display: none !important; }
.ai-sel-selected-text { scrollbar-width: none !important; -ms-overflow-style: none !important; }

.ai-sel-input-row {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    gap: 8px;
    overflow: hidden;
}

.ai-sel-input {
    flex: 1;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    color: #fff;
    font-size: 13px;
    padding: 8px 12px;
    outline: none;
    transition: border-color 0.2s;
    font-family: inherit;
    white-space: normal;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
    -webkit-appearance: none !important;
    appearance: none !important;
}
.ai-sel-input::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; }
.ai-sel-input::-webkit-outer-spin-button,
.ai-sel-input::-webkit-inner-spin-button,
.ai-chat-input::-webkit-outer-spin-button,
.ai-chat-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.ai-sel-input:focus {
    border-color: #ffd60a;
    background: rgba(255,255,255,0.08);
}

.ai-sel-input::placeholder { color: rgba(255,255,255,0.3); }

.ai-sel-send-btn {
    width: 34px;
    height: 34px;
    background: linear-gradient(135deg, #ffd60a, #ffc300);
    border: none;
    border-radius: 8px;
    color: #1a1a2e;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.2s;
    flex-shrink: 0;
}

.ai-sel-send-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 12px rgba(255,214,10,0.3);
}

.ai-sel-send-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
}

</style>

<!-- AI Text Selection Popup (ChatGPT-style) -->
<div class="ai-text-selection-popup" id="aiTextSelectionPopup">
    <div class="ai-sel-header">
        <span class="ai-sel-header-title">
            <i class="fas fa-robot"></i> Hoi ve doan chon
        </span>
        <button class="ai-sel-close-btn" id="aiSelCloseBtn">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="ai-sel-selected-text">
        <p id="aiSelPreviewText"></p>
    </div>
    <div class="ai-sel-input-row">
        <input type="text" class="ai-sel-input" id="aiSelInput" placeholder="Nhap cau hoi..." autocomplete="off" maxlength="500" />
        <button class="ai-sel-send-btn" id="aiSelSendBtn">
            <i class="fas fa-paper-plane"></i>
        </button>
    </div>
</div>

<div class="ai-chatbot-widget" id="aiChatbotWidget">
    <div class="ai-chat-window" id="aiChatWindow">
        <div class="ai-chat-header">
            <div class="ai-chat-header-left">
                <div class="ai-chat-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="ai-chat-header-info">
                    <h4>Tro ly AI</h4>
                    <span>Powered by Groq (Llama)</span>
                </div>
            </div>
            <div class="ai-chat-header-actions">
                <button class="ai-chat-header-btn" id="aiClearChat" title="Xoa cuoc tro chuyen">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button class="ai-chat-header-btn close" id="aiCloseChat" title="Dong">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="ai-chat-body" id="aiChatBody">
            <div class="ai-chat-welcome">
                <i class="fas fa-comment-dots"></i>
                <p>Xin chao! Toi la tro ly AI (Groq/Llama).<br><br>
                <strong>Cach hoi:</strong><br>
                <span style="font-size:12px;">1. Nhan tin truc tiep<br>
                2. Boi den text bat ky -> nhap cau hoi<br>
                3. Hoi ve bai viet dang doc</span></p>
            </div>
        </div>

        <div class="ai-chat-footer">
            <div class="ai-chat-input-row">
                <textarea class="ai-chat-input" id="aiChatInput" placeholder="Nhap cau hoi..." rows="1" maxlength="500"></textarea>
                <button class="ai-chat-send" id="aiSendBtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <button class="ai-chat-toggle" id="aiChatToggle" title="Chat voi AI">
        <i class="fas fa-comment-dots" id="aiToggleIcon"></i>
        <div class="ai-chat-badge-dot" id="aiNewMsgBadge" style="display:none;"></div>
    </button>
</div>

<script>
(function() {
    var chatWindow = document.getElementById('aiChatWindow');
    var chatToggle = document.getElementById('aiChatToggle');
    var chatBody = document.getElementById('aiChatBody');
    var chatInput = document.getElementById('aiChatInput');
    var sendBtn = document.getElementById('aiSendBtn');
    var clearBtn = document.getElementById('aiClearChat');
    var closeBtn = document.getElementById('aiCloseChat');
    var badge = document.getElementById('aiNewMsgBadge');
    var chatEndpoint = @json(url('ai/chat'));
    var clearEndpoint = @json(url('ai/chat/clear'));
    var selectableSelector = '[data-ai-selectable="1"], .article-content';

    var isOpen = false;
    var isSending = false;
    var selectionTimer = null;
    var newsId = resolveNewsId();

    var selPopup = document.getElementById('aiTextSelectionPopup');
    var selInput = document.getElementById('aiSelInput');
    var selSendBtn = document.getElementById('aiSelSendBtn');
    var selCloseBtn = document.getElementById('aiSelCloseBtn');
    var selPreview = document.getElementById('aiSelPreviewText');
    var selCurSelection = '';

    function resolveNewsId() {
        var fromWindow = parseInt(window.AI_NEWS_ID || '', 10);
        if (Number.isFinite(fromWindow) && fromWindow > 0) {
            return fromWindow;
        }

        var contextualNode = document.querySelector('[data-ai-news-id]');
        if (!contextualNode) {
            return null;
        }

        var fromDom = parseInt(contextualNode.getAttribute('data-ai-news-id') || '', 10);
        return Number.isFinite(fromDom) && fromDom > 0 ? fromDom : null;
    }

    function openChat() {
        isOpen = true;
        chatWindow.classList.add('open');
        if (badge) {
            badge.style.display = 'none';
        }
        chatInput.focus();
    }

    function closeChat() {
        isOpen = false;
        chatWindow.classList.remove('open');
    }

    function hideSelectionPopup(clearSelection) {
        selPopup.style.display = 'none';
        selPopup.style.visibility = '';

        if (clearSelection && window.getSelection) {
            window.getSelection().removeAllRanges();
        }
    }

    function normalizeText(value) {
        return (value || '').replace(/\s+/g, ' ').trim();
    }

    function isEditableTarget(target) {
        if (!target || !target.closest) {
            return false;
        }

        return !!target.closest('input, textarea, [contenteditable="true"], .ai-text-selection-popup, .ai-chat-window');
    }

    function isInsideSelectableArea(node) {
        if (!node) {
            return false;
        }

        var element = node.nodeType === 1 ? node : node.parentElement;
        return !!(element && element.closest(selectableSelector));
    }

    function getSelectionData() {
        var selection = window.getSelection ? window.getSelection() : null;
        if (!selection || selection.rangeCount === 0 || selection.isCollapsed) {
            return null;
        }

        var text = normalizeText(selection.toString());
        if (text.length < 4 || text.length > 1200) {
            return null;
        }

        var range = selection.getRangeAt(0);
        if (!isInsideSelectableArea(range.commonAncestorContainer)) {
            return null;
        }

        var rect = range.getBoundingClientRect();
        if (!rect || (!rect.width && !rect.height)) {
            return null;
        }

        return {
            text: text,
            rect: rect
        };
    }

    function showSelectionPopup(selectionData) {
        selCurSelection = selectionData.text;
        selPreview.textContent = selectionData.text.length > 220 ? selectionData.text.substring(0, 220) + '...' : selectionData.text;
        selInput.value = '';

        selPopup.style.display = 'block';
        selPopup.style.visibility = 'hidden';

        var popupWidth = selPopup.offsetWidth || 320;
        var popupHeight = selPopup.offsetHeight || 150;
        var left = selectionData.rect.left + (selectionData.rect.width / 2) - (popupWidth / 2);
        var top = selectionData.rect.top - popupHeight - 10;

        if (top < 12) {
            top = selectionData.rect.bottom + 10;
        }

        left = Math.max(12, Math.min(left, window.innerWidth - popupWidth - 12));
        top = Math.max(12, Math.min(top, window.innerHeight - popupHeight - 12));

        selPopup.style.left = Math.round(left) + 'px';
        selPopup.style.top = Math.round(top) + 'px';
        selPopup.style.visibility = 'visible';

        // #region DEBUG: scan ALL elements for scrollbar-causing properties
        (function() {
            var findings = [];
            function checkEl(el, depth) {
                if (!el || el === document || el === window) return;
                var tag = el.tagName || 'WINDOW';
                var id = el.id || '';
                var cls = el.className || '';
                var cs = null;
                try { cs = window.getComputedStyle(el); } catch(e) { return; }
                if (!cs) return;

                var ov = cs.overflow, ovX = cs.overflowX, ovY = cs.overflowY;
                var sh = 0, ch = 0, sw = 0, cw = 0;
                try { sh = el.scrollHeight; ch = el.clientHeight; sw = el.scrollWidth; cw = el.clientWidth; } catch(e) {}

                var scrollY = sh > ch && ov !== 'hidden' && ovY !== 'hidden' && ov !== 'clip' && ovY !== 'clip';
                var scrollX = sw > cw && ov !== 'hidden' && ovX !== 'hidden' && ov !== 'clip' && ovX !== 'clip';

                if (scrollY || scrollX || ov === 'scroll' || ovX === 'scroll' || ovY === 'scroll') {
                    findings.push({ tag: tag, id: id, cls: cls.substring(0, 60), ov: ov, ovX: ovX, ovY: ovY, sh: sh, ch: ch, sw: sw, cw: cw, scrollY: scrollY, scrollX: scrollX, depth: depth });
                }

                // Check children too
                if (el.children) {
                    for (var i = 0; i < el.children.length; i++) {
                        checkEl(el.children[i], depth + 1);
                    }
                }
            }
            checkEl(selPopup, 0);
            fetch('http://127.0.0.1:7308/ingest/bb3f8ec0-4487-458c-930f-9a1d20e4f0aa', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-Debug-Session-Id': 'a0e8e4' }, body: JSON.stringify({ sessionId: 'a0e8e4', id: 'log_' + Date.now(), timestamp: Date.now(), location: 'ai-chatbot.blade.php:showSelectionPopup', message: 'SCROLLBAR_SCAN_ALL', data: { findings: findings, count: findings.length, popupVisible: selPopup.style.visibility }, runId: 'debug', hypothesisId: 'H2' }) }).catch(function() {});
        })();
        // #endregion

        requestAnimationFrame(function() {
            selInput.focus();
        });
    }

    function scheduleSelectionPopup() {
        clearTimeout(selectionTimer);
        selectionTimer = setTimeout(function() {
            if (document.activeElement === selInput) {
                return;
            }

            var selectionData = getSelectionData();
            if (!selectionData) {
                hideSelectionPopup(false);
                return;
            }

            showSelectionPopup(selectionData);
        }, 60);
    }

    function buildSelectionDisplayText(question, selectedText) {
        var preview = selectedText.length > 280 ? selectedText.substring(0, 280) + '...' : selectedText;
        return 'Doan da chon:\n"' + preview + '"\n\n' + question;
    }

    function normalizeSendPayload(options) {
        if (typeof options === 'string') {
            return {
                message: options,
                displayText: options,
                selectedText: ''
            };
        }

        options = options || {};

        return {
            message: normalizeText(options.message || ''),
            displayText: normalizeText(options.displayText || options.message || ''),
            selectedText: normalizeText(options.selectedText || ''),
            fromComposer: !!options.fromComposer
        };
    }

    chatToggle.addEventListener('click', function() {
        if (isOpen) {
            closeChat();
            return;
        }

        openChat();
    });

    closeBtn.addEventListener('click', closeChat);

    clearBtn.addEventListener('click', function() {
        fetch(clearEndpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            }
        }).then(function() {
            chatBody.innerHTML = '<div class="ai-chat-welcome"><i class="fas fa-comment-dots"></i><p>Da xoa cuoc tro chuyen. Hay hoi toi dieu gi do nhe.</p></div>';
        });
    });

    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 80) + 'px';
    }

    chatInput.addEventListener('input', function() {
        autoResize(this);
    });

    chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage({ fromComposer: true });
        }
    });

    sendBtn.addEventListener('click', function() {
        sendMessage({ fromComposer: true });
    });

    document.addEventListener('mouseup', function(e) {
        if (isEditableTarget(e.target)) {
            return;
        }

        scheduleSelectionPopup();
    });

    document.addEventListener('keyup', function(e) {
        if (e.key === 'Escape') {
            hideSelectionPopup(false);
            return;
        }

        if (e.key.indexOf('Arrow') === 0 || e.key === 'Shift') {
            scheduleSelectionPopup();
        }
    });

    document.addEventListener('mousedown', function(e) {
        if (selPopup.style.display === 'block' && !selPopup.contains(e.target)) {
            hideSelectionPopup(false);
        }
    });

    window.addEventListener('resize', function() {
        if (selPopup.style.display === 'block') {
            scheduleSelectionPopup();
        }
    });

    document.addEventListener('scroll', function() {
        if (selPopup.style.display === 'block') {
            scheduleSelectionPopup();
        }
    }, true);

    selPopup.addEventListener('mousedown', function(e) {
        e.stopPropagation();
    });

    selCloseBtn.addEventListener('click', function() {
        hideSelectionPopup(false);
    });

    selSendBtn.addEventListener('click', function() {
        var question = normalizeText(selInput.value);
        if (!question) {
            selInput.focus();
            return;
        }

        if (!selCurSelection) {
            alert('Khong co text duoc chon.');
            return;
        }

        hideSelectionPopup(true);
        openChat();

        sendMessage({
            message: question,
            displayText: buildSelectionDisplayText(question, selCurSelection),
            selectedText: selCurSelection
        });
    });

    selInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            selSendBtn.click();
        }

        if (e.key === 'Escape') {
            hideSelectionPopup(false);
        }
    });

    function sendMessage(options) {
        var payload = normalizeSendPayload(options);

        if (payload.fromComposer) {
            payload.message = normalizeText(chatInput.value);
            payload.displayText = payload.message;
        }

        if (!payload.message || isSending) {
            return;
        }

        if (payload.message.length > 500) {
            alert('Cau hoi qua dai. Vui long rut gon duoi 500 ky tu.');
            return;
        }

        if (payload.fromComposer) {
            chatInput.value = '';
            chatInput.style.height = 'auto';
        }

        isSending = true;
        sendBtn.disabled = true;

        appendMessage('user', payload.displayText || payload.message);
        scrollToBottom();

        var typingDiv = document.createElement('div');
        typingDiv.className = 'ai-typing';
        typingDiv.id = 'aiTypingIndicator';
        typingDiv.innerHTML = '<div class="ai-msg-avatar ai-msg-bot" style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg, #ffd60a, #ffc300);color:#1a1a2e;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0;"><i class="fas fa-robot"></i></div><div class="ai-typing-dots"><div class="ai-typing-dot"></div><div class="ai-typing-dot"></div><div class="ai-typing-dot"></div></div>';
        chatBody.appendChild(typingDiv);
        scrollToBottom();

        fetch(chatEndpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                _token: document.querySelector('meta[name="csrf-token"]')?.content || '',
                message: payload.message,
                news_id: newsId || undefined,
                selected_text: payload.selectedText || undefined
            })
        })
            .then(function(r) {
                if (!r.ok) {
                    return r.json().catch(function() {
                        if (r.status === 429) {
                            throw new Error('Dich vu AI tam thoi het quota. Vui long thu lai sau vai phut.');
                        }
                        if (r.status === 419) {
                            throw new Error('Phien lam viec da het han. Vui long tai lai trang.');
                        }
                        throw new Error('May chu gap su co (loi ' + r.status + '). Vui long thu lai.');
                    }).then(function(data) {
                        throw new Error(data.message || 'Da co loi xay ra (loi ' + r.status + ')');
                    });
                }

                return r.json();
            })
            .then(function(res) {
                var typing = document.getElementById('aiTypingIndicator');
                if (typing) {
                    typing.remove();
                }

                if (res.success && res.reply) {
                    appendMessage('bot', res.reply);
                    if (!isOpen && badge) {
                        badge.style.display = 'block';
                    }
                } else {
                    appendMessage('bot', res.message || 'Xin loi, toi khong the tra loi luc nay.');
                }
            })
            .catch(function(err) {
                var typing = document.getElementById('aiTypingIndicator');
                if (typing) {
                    typing.remove();
                }

                appendMessage('bot', err.message || 'Da co loi ket noi. Vui long kiem tra mang va thu lai.');
            })
            .finally(function() {
                isSending = false;
                sendBtn.disabled = false;
            });
    }

    function appendMessage(role, text) {
        var now = new Date();
        var timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

        var msgDiv = document.createElement('div');
        msgDiv.className = 'ai-msg ' + role;

        var avatarClass = role === 'bot' ? 'fa-robot' : 'fa-user';
        var avatarBg = role === 'bot' ? 'linear-gradient(135deg, #ffd60a, #ffc300)' : 'rgba(255,255,255,0.1)';
        var avatarColor = role === 'bot' ? '#1a1a1a' : '#aaa';

        msgDiv.innerHTML =
            '<div class="ai-msg-avatar" style="background:' + avatarBg + ';color:' + avatarColor + ';width:28px;height:28px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;">' +
            '<i class="fas ' + avatarClass + '"></i></div>' +
            '<div><div class="ai-msg-content">' + escapeHtml(text).replace(/\n/g, '<br>') + '</div><div class="ai-msg-time">' + timeStr + '</div></div>';

        chatBody.appendChild(msgDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
})();
</script>
