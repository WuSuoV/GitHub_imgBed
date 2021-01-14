var page = 1;
$(function () {
    load_data(page);
});

var is_load = true;

function load_data(page) {
    is_load = false;
    $.ajax({
        'type': 'GET',
        'url': '/api/output/' + page,
        'dataType': 'json',
        'data': '',
        'success': function (res) {
            if (res.code == 1) {
                var html = '<div class="box">';
                for (var i = 0; i < res.data.length; i++) {
                    html += `
<div class="item" style="">
     <a style="border: 0;" data-fancybox="gallery" href="` + res.data[i].url + `" data-caption="<b>上传时间：` + timeStampTurnTime(res.data[i].addtime) + `</b><br/>上传者IP：` + res.data[i].ip + `">
        <img style="width: 100%;height: 100%;box-shadow: 0 2px 15px 0 rgba(0, 0, 0, 0.2);-moz-box-shadow: 0 2px 15px 0 rgba(0, 0, 0, 0.2);border-radius:5px" class="lazy" lazyLoadSrc="` + res.data[i].url + `" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAAPSURBVBhXY/gPBVDG//8Aj4IP8dFqTzkAAAAASUVORK5CYII=">
     </a>
</div>
`;
                }
                html += '</div>';
                $('#list').append(html);
                $('#count').text(res.count);
                $(function () {
                    $("[lazyLoadSrc]").YdxLazyLoad({
                        onShow: function () {
                            $(this).parent().next().hide()
                        }
                    });
                });
                is_load = true;
            } else {
                layer.msg(res.msg);
            }
        },
        'error': function () {
            layer.msg('网络错误！');
        }
    });
}

//时间戳转时间类型
function timeStampTurnTime(timeStamp) {
    if (timeStamp > 0) {
        var date = new Date();
        date.setTime(timeStamp * 1000);
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        m = m < 10 ? ('0' + m) : m;
        var d = date.getDate();
        d = d < 10 ? ('0' + d) : d;
        var h = date.getHours();
        h = h < 10 ? ('0' + h) : h;
        var minute = date.getMinutes();
        var second = date.getSeconds();
        minute = minute < 10 ? ('0' + minute) : minute;
        second = second < 10 ? ('0' + second) : second;
        return y + '-' + m + '-' + d + ' ' + h + ':' + minute + ':' + second;
    } else {
        return "";
    }
}

$(window).scroll(function () {
    var totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
    var content_height = parseFloat($("#list").height());
    if (totalheight - content_height >= 100) {
        if (is_load) {
            page = page + 1;
            load_data(page);
        }
    }
});


(function (window, $) {
    var YdxLazyLoad = function (window, $) {
        var defaultOption = {
            threshold: 0,
            failure_limit: 0,
            event: "scroll resize",
            effect: "fadeIn",
            container: window,
            effectTime: 300,
            callback: null
        };
        var optionHandel = {
            setOption: function (element, opt) {
                return element.data("_YdxLazyLoadOption_", opt);
            }, getOption: function (element) {
                return element.data("_YdxLazyLoadOption_");
            }, removeOption: function (element) {
                return element.removeData("_YdxLazyLoadOption_");
            }
        };
        var checkPosition = {
            above: function (element) {
                var fold, $window = $(window), option = optionHandel.getOption(element);
                if (option.container === undefined || option.container === window) {
                    fold = $window.height() + $window.scrollTop();
                } else {
                    fold = $(option.container).offset().top + $(option.container).height();
                }
                return fold >= $(element).offset().top + option.threshold;
            }, below: function (element) {
                var fold, $window = $(window), option = optionHandel.getOption(element);
                if (option.container === undefined || option.container === window) {
                    fold = $window.height() + $window.scrollTop();
                } else {
                    fold = $(option.container).offset().top + $(option.container).height();
                }
                return fold <= $(element).offset().top - option.threshold;
            }, left: function (element) {
                var fold, $window = $(window), option = optionHandel.getOption(element);
                if (option.container === undefined || option.container === window) {
                    fold = $window.width() + $window.scrollLeft();
                } else {
                    fold = $(option.container).offset().left + $(option.container).width();
                }
                return fold >= $(element).offset().left + option.threshold;
            }, right: function (element) {
                var fold, $window = $(window), option = optionHandel.getOption(element);
                if (option.container === undefined || option.container === window) {
                    fold = $window.width() + $window.scrollLeft();
                } else {
                    fold = $(option.container).offset().left + $(option.container).width();
                }
                return fold <= $(element).offset().left - option.threshold;
            }, flag: function (element) {
                var option = optionHandel.getOption(element);
                return !$.rightoffold(element, element) && !$.leftofbegin(element, element) && !$.belowthefold(element, element) && !$.abovethetop(element, element);
            }
        };

        function showImg() {
            var $this = $(this), opt = optionHandel.getOption($this);
            if (!opt.isLoad) {
                var currentImgSrc = opt.src || $this.attr("lazyLoadSrc");
                $(new Image()).attr("src", currentImgSrc).load([opt, $this], function (e) {
                    var para = e.data, opt = para[0], element = para[1];
                    element.attr("src", currentImgSrc).hide()[opt.effect](opt.effectTime);
                    opt.isLoad = true;
                    opt.callback && opt.callback.call(element, currentImgSrc);
                    $(this).unbind("load");
                    opt.onShow && opt.onShow.call(element);
                });
            }
        }

        function init() {
            $("[lazyLoadSrc]:visible").each(function (i, element) {
                add($(element));
            });
        }

        function add(element, opt) {
            if (optionHandel.getOption(element)) {
                return;
            }
            opt = $.extend(true, {}, defaultOption, opt);
            optionHandel.setOption(element, opt).bind("showImg", showImg);
            var $container = $(opt.container), containerData = {elementMap: {}, num: 0};
            if (!$container.data("_YdxLazyLoad_container_")) {
                $container.data("_YdxLazyLoad_container_", containerData);
            } else {
                containerData = $container.data("_YdxLazyLoad_container_");
            }
            opt._index = containerData.num;
            containerData.elementMap[containerData.num++] = element;
            if (!containerData.isBind || containerData.event !== opt.event) {
                $container.bind(opt.event, function (e) {
                    var data = $(this).data("_YdxLazyLoad_container_"), elementMap = data.elementMap;
                    $.each(elementMap, function (key, el) {
                        if (el.data("_YdxLazyLoadOption_")) {
                            if (checkPosition.above(el) && checkPosition.left(el)) {
                                el.trigger("showImg");
                                delete elementMap[key];
                            }
                        } else {
                            delete elementMap[key];
                            el.remove();
                        }
                    });
                    return false;
                });
                containerData.isBind = true;
                containerData.event = opt.event;
            }
            $.each(opt.event.split(" "), function (i, event) {
                if (event === 'scroll') {
                    var e = $.Event(event, {scrollTop: $('body').scrollTop()});
                    $container.trigger(e);
                    return;
                }
                $container.trigger(event);
            });
        }

        function remove(element) {
            var opt = optionHandel.getOption(element);
            delete $(opt.container).data("_YdxLazyLoad_container_").elementMap[opt._index];
            optionHandel.removeOption(element);
        }

        return {init: init, add: add, remove: remove};
    }(window, $);
    $.fn.YdxLazyLoad = function (opt) {
        return this.each(function () {
            switch ($.type(opt)) {
                case "undefined":
                case "object":
                    YdxLazyLoad.add($(this), opt);
                    break;
                case "string":
                    var args = Array.prototype.slice.call(arguments, 1);
                    args.unshift($(this));
                    YdxLazyLoad[opt].call(YdxLazyLoad, args);
                    break;
            }
        });
    };
})(window, jQuery)