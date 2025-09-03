//Đăng ký nhận tin khuyến mại footer
$('#btnSendSub').click(function () {
    var txtEmailSub = $('#txtEmailSub').val();
    var _token = $('#_token').val();

    // Kiểm tra email có hợp lệ hay không
    var reg = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/;

    if (reg.test(txtEmailSub) == false) {
        alert('Email không hợp lệ, vui lòng kiểm tra lại!');
        return false;
    }

    $.ajax({
        type: 'POST',
        url: url + "/dang-ky-nhan-tin-khuyen-mai",
        data: {
            txtEmailSub: txtEmailSub,
            _token: _token
        },
        success: function (data) {
            if (data == 'error_exit_email') {
                alert('Email này đã tồn tại, vui lòng kiểm tra lại!');
            } else if (data == 'error') {
                alert('Có lỗi trong quá trình thêm email, vui lòng kiểm tra lại!');
            } else {
                alert('Đăng ký nhận tin khuyến mại thành công!');
            }
        }
    });
});


//Gửi email liên hệ
$('#btnSendContact').click(function () {
    var _token = $('#_token').val();
    var txtEmail = $('#txtEmail').val();
    var txtName = $('#txtName').val();
    var txtPhone = $('#txtPhone').val();
    var txtMessage = $('#txtMessage').val();

    if (txtEmail == '' || txtName == '' || txtPhone == '' || txtMessage == '') {
        alert('Vui lòng điền đầy đủ thông tin.');
        return false;
    }

    // Check email có trong hay không
    var reg = /^([A-Za-z0-9\-\_\.]+)@([A-Za-z0-9\-\_\.]+)\.([A-Za-z]{2,4})$/;

    if (reg.test(txtEmail) == false) {
        alert('Email không hợp lệ, vui lòng kiểm tra lại!');
        return false;
    }
    $.ajax({
        type: 'POST',
        url: url + '/gui-email-lien-he',
        data: {
            txtEmail: txtEmail,
            txtName: txtName,
            txtPhone: txtPhone,
            txtMessage: txtMessage,
            _token: _token
        },
        success: function (data) {
            if (data == 'error_empty') {
                alert('Vui lòng điền đầy đủ thông tin.');
            } else if (data == 'error') {
                alert('Có lỗi trong quá trình gửi liên hệ, vui lòng kiểm tra lại !');
            } else {
                alert('Chúng tôi đã nhận được liên hệ của bạn và sẽ sớm trả lời !');
            }
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
