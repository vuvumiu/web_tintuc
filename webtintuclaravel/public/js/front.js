// ========== HEADER USER DROPDOWN ==========
// Dùng click thay vì :hover để dropdown không bị mất khi di chuột ra ngoài
$(document).ready(function () {
    var $wrapper = $('#userLoggedWrapper');
    if ($wrapper.length) {
        var $button = $('#userDropdownBtn');
        var $dropdown = $('#userDropdown');
        var $overlay = $('#userOverlay');

        function openDropdown() {
            $wrapper.addClass('is-open');
        }

        function closeDropdown() {
            $wrapper.removeClass('is-open');
        }

        function toggleDropdown() {
            $wrapper.toggleClass('is-open');
        }

        $button.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleDropdown();
        });

        $overlay.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            closeDropdown();
        });

        $(document).on('mousedown touchstart', function (e) {
            var target = e.target;
            var clickedToggle = $button.length && $button[0].contains(target);
            var clickedMenu = $dropdown.length && $dropdown[0].contains(target);

            if (!clickedToggle && !clickedMenu) {
                closeDropdown();
            }
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDropdown();
            }
        });
    }
});

// ========== STAR RATING (Bootstrap form) ==========
function applyRatingStarsVisual($form, score) {
    if (!$form.length) return;
    $form.find('.rating_star_btn').each(function () {
        var s = $(this).data('score');
        var $icon = $(this).find('i');
        if (s <= score) {
            $(this).addClass('active');
            $icon.removeClass('far').addClass('fas');
        } else {
            $(this).removeClass('active');
            $icon.removeClass('fas').addClass('far');
        }
    });
}

$(document).on('mouseenter', '.rating-form-js .rating_star_btn', function () {
    var $form = $(this).closest('form');
    applyRatingStarsVisual($form, $(this).data('score'));
});

$(document).on('mouseleave', '.rating-form-js', function () {
    var ur = parseInt($(this).data('user-rating'), 10) || 0;
    applyRatingStarsVisual($(this), ur);
});

$(document).on('click', '.rating-form-js .rating_star_btn', function (e) {
    e.preventDefault();
    var score = $(this).data('score');
    var $form = $(this).closest('form');
    $form.find('#ratingScore').val(score);
    applyRatingStarsVisual($form, score);
    $form.submit();
});

// ========== COMMENT REPLY TOGGLE ==========
$(document).on('click', '.comment-reply-btn', function () {
    var commentId = $(this).data('comment-id');
    $('.reply-form-wrap').not('#replyForm-' + commentId).addClass('d-none');
    $('#replyForm-' + commentId).toggleClass('d-none');
    if (!$('#replyForm-' + commentId).hasClass('d-none')) {
        $('#replyForm-' + commentId).find('textarea').focus();
    }
});

$(document).on('click', '.comment-cancel-reply-btn', function () {
    $(this).closest('.reply-form-wrap').addClass('d-none');
});

// ========== COMMENT EDIT ==========
$(document).on('click', '.comment-edit-btn', function () {
    var $card = $(this).closest('.comment-root, .comment-card-root, .comment-card-reply').first();
    var commentId = $card.data('comment-id');
    if (!commentId) return;

    var $contentEl = $card.find('.news_comment_item__content').first();
    var currentContent = $contentEl.text().trim();

    var csrf = $('meta[name="csrf-token"]').attr('content');
    var $editForm = $(
        '<form action="' + url + '/binh-luan/sua/' + commentId + '" method="POST" class="comment-edit-form mt-2">' +
        '<input type="hidden" name="_token" value="' + csrf + '">' +
        '<textarea name="content" class="form-control rounded-3 shadow-none" rows="3" required>' + currentContent + '</textarea>' +
        '<div class="d-flex gap-2 mt-2 justify-content-end">' +
        '<button type="button" class="btn btn-sm btn-secondary rounded-pill comment-cancel-edit-btn">Hủy</button>' +
        '<button type="submit" class="btn btn-sm btn-primary rounded-pill"><i class="fas fa-check me-1"></i>Lưu</button>' +
        '</div>' +
        '</form>'
    );

    $contentEl.hide();
    $contentEl.after($editForm);
    $editForm.find('textarea').focus();
    $(this).closest('.btn-group').hide();
});

$(document).on('click', '.comment-cancel-edit-btn', function () {
    $(this).closest('.comment-edit-form').prev('.news_comment_item__content').show();
    $(this).closest('.comment-edit-form').remove();
    $('.comment-actions').show();
});

// ========== COMMENT DELETE ==========
$(document).on('click', '.comment-delete-btn', function () {
    if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;
    var $card = $(this).closest('.comment-root, .comment-card-root, .comment-card-reply').first();
    var commentId = $card.data('comment-id');
    if (!commentId) return;

    var csrf = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'POST',
        url: url + '/binh-luan/xoa/' + commentId,
        data: { _token: csrf },
        success: function () {
            $card.fadeOut(300, function () { $(this).remove(); });
        },
        error: function () {
            alert('Có lỗi xảy ra, vui lòng thử lại!');
        }
    });
});

// Đăng ký nhận tin khuyến mại footer
$('#btnSendSub').click(function () {
    var txtEmailSub = $('#txtEmailSub').val().trim();
    var _token = $('#_token').val();
    var $btn = $(this);
    var $msg = $('#subFormMsg');

    var reg = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/;
    if (!reg.test(txtEmailSub)) {
        $msg.removeClass('alert-success').addClass('alert-danger').text('Email không hợp lệ, vui lòng kiểm tra lại!').show();
        return false;
    }

    $btn.prop('disabled', true).text('Đang xử lý...');
    $msg.hide();

    $.ajax({
        type: 'POST',
        url: url + "/dang-ky-nhan-tin-khuyen-mai",
        data: {
            txtEmailSub: txtEmailSub,
            _token: _token
        },
        success: function (data) {
            if (data.success) {
                $msg.removeClass('alert-danger').addClass('alert-success').text(data.message).show();
                $('#txtEmailSub').val('');
            } else {
                $msg.removeClass('alert-success').addClass('alert-danger').text(data.message).show();
            }
        },
        error: function () {
            $msg.removeClass('alert-success').addClass('alert-danger').text('Có lỗi xảy ra, vui lòng thử lại!').show();
        },
        complete: function () {
            $btn.prop('disabled', false).text('Đăng ký');
        }
    });
});

// Gửi liên hệ
$('#btnSendContact').click(function () {
    var _token = $('#_token').val();
    var txtEmail = $('#txtEmail').val().trim();
    var txtName = $('#txtName').val().trim();
    var txtPhone = $('#txtPhone').val().trim();
    var txtSubject = $('#txtSubject').val().trim();
    var selCategory = $('#selCategory').val();
    var txtMessage = $('#txtMessage').val().trim();
    var $btn = $(this);
    var $msg = $('#contactFormMsg');

    if (!txtName || !txtEmail || !txtPhone || !txtMessage) {
        $msg.removeClass('alert-success').addClass('alert-danger').text('Vui lòng điền đầy đủ thông tin có dấu *.').show();
        return false;
    }

    var reg = /^([A-Za-z0-9\-\_\.]+)@([A-Za-z0-9\-\_\.]+)\.([A-Za-z]{2,4})$/;
    if (!reg.test(txtEmail)) {
        $msg.removeClass('alert-success').addClass('alert-danger').text('Email không hợp lệ, vui lòng kiểm tra lại!').show();
        return false;
    }

    if (txtPhone.length < 6) {
        $msg.removeClass('alert-success').addClass('alert-danger').text('Số điện thoại không hợp lệ!').show();
        return false;
    }

    if (txtMessage.length < 10) {
        $msg.removeClass('alert-success').addClass('alert-danger').text('Lời nhắn quá ngắn (tối thiểu 10 ký tự).').show();
        return false;
    }

    $btn.prop('disabled', true).text('Đang gửi...');
    $msg.hide();

    $.ajax({
        type: 'POST',
        url: url + '/gui-email-lien-he',
        data: {
            txtEmail: txtEmail,
            txtName: txtName,
            txtPhone: txtPhone,
            txtSubject: txtSubject,
            selCategory: selCategory,
            txtMessage: txtMessage,
            _token: _token
        },
        success: function (data) {
            if (data.success) {
                $msg.removeClass('alert-danger').addClass('alert-success').text(data.message).show();
                $('#txtName, #txtEmail, #txtPhone, #txtSubject, #txtMessage').val('');
                $('#selCategory').val('consult');
            } else {
                $msg.removeClass('alert-success').addClass('alert-danger').text(data.message).show();
            }
        },
        error: function (xhr) {
            var msg = 'Có lỗi xảy ra, vui lòng thử lại!';
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.message) msg = res.message;
                else if (res.errors) {
                    var firstErr = Object.values(res.errors)[0];
                    msg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                }
            } catch (e) {}
            $msg.removeClass('alert-success').addClass('alert-danger').text(msg).show();
        },
        complete: function () {
            $btn.prop('disabled', false).text('GỬI LIÊN HỆ');
        }
    });
});

$('#newSort').on('change', function () {
    var cat = $('#newsCat').val();
    var sort = this.value;
    if (sort != '') {
        window.location.href = url + '/' + cat + '/?sapxep=' + sort;
    }
});


$('#mobileMenuBar').click(function () {
    $('.header_menu').toggleClass("displayBlock");
});

// ========== COMMENT SUBMIT ==========
$(document).on('submit', '#commentForm', function (e) {
    e.preventDefault();
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var newsId = $(this).data('news-id');
    var content = $('#commentContentInput').val().trim();

    if (!content || content.length < 1) {
        alert('Vui lòng nhập nội dung bình luận.');
        return false;
    }

    $('#commentSubmitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Đang gửi...');

    $.ajax({
        type: 'POST',
        url: url + '/binh-luan',
        data: {
            _token: csrf,
            news_id: newsId,
            content: content
        },
        success: function (data) {
            if (data.success) {
                $('#commentContentInput').val('');
                location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
            }
        },
        error: function (xhr) {
            var msg = 'Có lỗi xảy ra!';
            try {
                var res = JSON.parse(xhr.responseText);
                msg = res.message || msg;
            } catch(e) {}
            alert(msg);
        },
        complete: function () {
            $('#commentSubmitBtn').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Gửi bình luận');
        }
    });
    return false;
});

// ========== RATING SUBMIT ==========
$(document).on('submit', '#ratingForm', function (e) {
    e.preventDefault();
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var newsId = $(this).data('news-id');
    var score = $('#ratingScoreInput').val();

    if (!score || score == '0') {
        alert('Vui lòng chọn số sao.');
        return false;
    }

    $.ajax({
        type: 'POST',
        url: url + '/danh-gia-sao',
        data: {
            _token: csrf,
            news_id: newsId,
            score: score
        },
        success: function (data) {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
            }
        },
        error: function (xhr) {
            var msg = 'Có lỗi xảy ra!';
            try {
                var res = JSON.parse(xhr.responseText);
                msg = res.message || msg;
            } catch(e) {}
            alert(msg);
        }
    });
    return false;
});

// ========== COMMENT VOTE ==========
$(document).on('click', '.btn-vote', function () {
    if (window.VNX_DETAIL_COMMENT_HANDLERS) return;
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var commentId = $(this).data('comment-id');
    var voteType = $(this).data('vote-type');

    $.ajax({
        type: 'POST',
        url: url + '/binh-luan/vote',
        data: {
            _token: csrf,
            comment_id: commentId,
            vote_type: voteType
        },
        success: function (data) {
            location.reload();
        },
        error: function () {
            alert('Có lỗi xảy ra!');
        }
    });
});

// ========== COMMENT REPLY SUBMIT ==========
$(document).on('click', '.btn-submit-reply', function () {
    if (window.VNX_DETAIL_COMMENT_HANDLERS) return;
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var parentId = $(this).data('parent-id');
    var replyText = $('#replyText-' + parentId).val().trim();
    var newsId = $('#commentForm input[name=news_id]').val();

    if (!replyText || replyText.length < 1) {
        alert('Vui lòng nhập nội dung phản hồi.');
        return;
    }

    $(this).prop('disabled', true).text('Đang gửi...');

    $.ajax({
        type: 'POST',
        url: url + '/binh-luan/phan-hoi',
        data: {
            _token: csrf,
            news_id: newsId,
            parent_id: parentId,
            content: replyText
        },
        success: function (data) {
            location.reload();
        },
        error: function (xhr) {
            var msg = 'Có lỗi xảy ra!';
            try {
                var res = JSON.parse(xhr.responseText);
                msg = res.message || msg;
            } catch(e) {}
            alert(msg);
            $('.btn-submit-reply').prop('disabled', false).text('Gửi');
        }
    });
});

// ========== COMMENT EDIT SAVE ==========
$(document).on('click', '.btn-save-edit', function () {
    if (window.VNX_DETAIL_COMMENT_HANDLERS) return;
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var commentId = $(this).data('comment-id');
    var newContent = $('#editText-' + commentId).val().trim();

    if (!newContent || newContent.length < 1) {
        alert('Nội dung không được để trống.');
        return;
    }

    $(this).prop('disabled', true).text('Đang lưu...');

    $.ajax({
        type: 'POST',
        url: url + '/binh-luan/sua/' + commentId,
        data: {
            _token: csrf,
            content: newContent
        },
        success: function (data) {
            location.reload();
        },
        error: function (xhr) {
            var msg = 'Có lỗi xảy ra!';
            try {
                var res = JSON.parse(xhr.responseText);
                msg = res.message || msg;
            } catch(e) {}
            alert(msg);
            $('.btn-save-edit').prop('disabled', false).text('Lưu');
        }
    });
});

// ========== FAVORITE TOGGLE ==========
// Note: detail.blade.php has its own inline JS for favBtn AJAX.
// This block is kept as fallback for other pages using favBtn.
$(document).on('click', '#favBtn', function () {
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var newsId = $(this).data('news-id');
    var $btn = $(this);

    $.ajax({
        type: 'POST',
        url: url + '/yeu-thich',
        data: {
            _token: csrf,
            news_id: newsId
        },
        success: function (data) {
            if (data.favorited) {
                $btn.removeClass('btn-outline-danger').addClass('btn-danger');
                $btn.find('i').removeClass('far').addClass('fas');
                $btn.find('#favLabel').text('Đã thích');
            } else {
                $btn.removeClass('btn-danger').addClass('btn-outline-danger');
                $btn.find('i').removeClass('fas').addClass('far');
                $btn.find('#favLabel').text('Yêu thích');
            }
        },
        error: function () {
            alert('Có lỗi xảy ra!');
        }
    });
});
