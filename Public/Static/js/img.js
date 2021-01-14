$(function () {
    $(window).scroll(function () {
        var scroHei = $(window).scrollTop();
        if (scroHei > 500) {
            $('.back-to-top').css('top', '-200px');
        } else {
            $('.back-to-top').css('top', '-999px');
        }
    });
    $('.back-to-top').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 600);
    });
});

try {
    $.ajax({
        url: '/api/upload_config',
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            initFileInput('file-Portrait', '/api/upload', res.allowtype, res.max_upload, res.max_uploads);
            $('.file-caption-name').attr('placeholder', '请选择图片进行上传');
            $('.close').remove();
            $('.file-caption-name').focus(function () {
                $('#file-Portrait').click();
            });
        },
        error: function () {
            layer.msg('网络错误！');
            console.error('Upload Js Error!');
        }
    });
} catch {
    console.warn('Upload Js Error!');
}


function initFileInput(ctrlName, uploadUrl, allowedFileExtensions, maxFileSize, maxFileCount) {
    var control = $('#' + ctrlName);
    control.fileinput({
        language: 'zh',
        uploadUrl: uploadUrl,
        allowedFileExtensions: allowedFileExtensions,
        overwriteInitial: false,
        uploadAsync: true,
        previewFileType: "image",
        maxFileSize: maxFileSize,
        maxFileCount: maxFileCount,
        autoOrientImage: true,
        fileActionSettings: {
            showRemove: true,
            showUpload: false,
            showZoom: false,
            showDrag: false,
        },
        browseClass: "btn btn-success",
        browseIcon: "<i class='glyphicon glyphicon-picture'></i>",
        removeClass: "btn btn-danger",
        uploadClass: "btn btn-info",
    }).on('fileuploaded', function (event, data) {
        var clipboard = new Clipboard('#copy-btn');
        clipboard.on('success', function(e) {
            layer.msg('复制成功');
        });
        clipboard.on('error', function(e) {
            layer.msg('复制失败，请长按链接后手动复制');
        });
        var form = data.form, files = data.files, extra = data.extra, response = data.response, reader = data.reader;
        if (response.success == true) {
            if ($('#showurl').html()) {
                $("#showurl").show();
            }
            $('#urlcode').append(response.url + '&nbsp;<button id="copy-btn" data-clipboard-text="' + response.url + '" class="btn btn-info btn-xs">一键复制</button>\n');
            $('#htmlcode').append("&lt;a href=\"" + response.url + "\" target=\"_blank\"&gt;&lt;img src=\"" + response.url + "\" /&gt;&lt;/a&gt;" + '\n');
            $('#bbcode').append("[url=" + response.url + "][img]" + response.url + "[/img][/url]" + '\n');
            $('#markdown').append("![" + response.url + "](" + response.url + ")" + '\n');
            $('#urlcodePanel-tab').click();
        }
    }).on('filecleared', function (event) {
        if ($("#showurl").is(":visible")) {
            layer.confirm('是否要清空历史上传', {
                btn: ['Yes', 'No'] //按钮
            }, function (index) {
                $('#imagedetail').html("");
                $('#urlcode').html("");
                $('#linkcode').html("");
                $('#htmlcode').html("");
                $('#bbcode').html("");
                $('#markdown').html("");
                $('#removelink').html("");
                $("#showurl").hide();
                layer.close(index);
            }, function () {
            });
        }
    });
    VerifyFile();
    SexVerify();
}

;!function (e, t, a) {
    function r() {
        for (var e = 0; e < s.length; e++) {
            s[e].alpha <= 0 ? (t.body.removeChild(s[e].el), s.splice(e, 1)) : (s[e].y--, s[e].scale += 0.004, s[e].alpha -= 0.013, s[e].el.style.cssText = "left:" + s[e].x + "px;top:" + s[e].y + "px;opacity:" + s[e].alpha + ";transform:scale(" + s[e].scale + "," + s[e].scale + ") rotate(45deg);background:" + s[e].color + ";z-index:99999")
        }
        requestAnimationFrame(r)
    }

    function n() {
        var t = "function" == typeof e.onclick && e.onclick;
        e.onclick = function (e) {
            t && t(), o(e)
        }
    }

    function o(e) {
        var a = t.createElement("div");
        a.className = "heart", s.push({
            el: a,
            x: e.clientX - 5,
            y: e.clientY - 5,
            scale: 1,
            alpha: 1,
            color: c()
        }), t.body.appendChild(a)
    }

    function i(e) {
        var a = t.createElement("style");
        a.type = "text/css";
        try {
            a.appendChild(t.createTextNode(e))
        } catch (t) {
            a.styleSheet.cssText = e
        }
        t.getElementsByTagName("head")[0].appendChild(a)
    }

    function c() {
        return "rgb(" + ~~(255 * Math.random()) + "," + ~~(255 * Math.random()) + "," + ~~(255 * Math.random()) + ")"
    }

    var s = [];
    e.requestAnimationFrame = e.requestAnimationFrame || e.webkitRequestAnimationFrame || e.mozRequestAnimationFrame || e.oRequestAnimationFrame || e.msRequestAnimationFrame || function (e) {
        setTimeout(e, 1000 / 60)
    }, i(".heart{width: 10px;height: 10px;position: fixed;background: #f00;transform: rotate(45deg);-webkit-transform: rotate(45deg);-moz-transform: rotate(45deg);}.heart:after,.heart:before{content: '';width: inherit;height: inherit;background: inherit;border-radius: 50%;-webkit-border-radius: 50%;-moz-border-radius: 50%;position: fixed;}.heart:after{top: -5px;}.heart:before{left: -5px;}"), n(), r()
}(window, document);

function VerifyFile() {
    try {
        $.ajax({
            url: '/api/verify_file',
            type: 'GET',
            dataType: 'json'
        });
    } catch {
        console.warn('Verify File Error!');
    }
}

function SexVerify() {
    try {
        $.ajax({
            url: '/api/sex_verify',
            type: 'GET',
            dataType: 'json'
        });
    } catch {
        console.warn('Verify Sex Error!');
    }
}
