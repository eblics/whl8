//格式化字符串
String.prototype.format = function() {
    var formatted = this;
    for (var i = 0; i < arguments.length; i++) {
        var regexp = new RegExp('\\{' + i + '\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[i]);
    }
    return formatted;
};
var common = {
    init: function() {
        var _this = this;
        _this.uploadPlugin();
        _this.autoHeight();
        $(window).resize(_this.autoHeight);
    },
    getRptRootUrl: function() {
        var hostName = window.location.hostname;
            return '/rpt/'
    },
    autoHeight: function() {
        $('.leftbar,.rightmain').css('min-height', 'auto');
        var winH = $(window).height();
        var left = $('.leftbar');
        var right = $('.rightmain');
        if (left.length > 0 && right.length > 0) {
            var leftH = left.height() - 30;
            var rightH = right.height();
            var headH = $('.head').height();
            var footH = $('.foot').height();
            var maxH = winH - headH - footH - 40 - 80;
            if (maxH > rightH && maxH > leftH) {
                $('.rightmain').css('min-height', maxH);
                $('.leftbar').css('min-height', maxH + 30);
            } else {
                if (leftH >= rightH) {
                    $('.rightmain').css('min-height', leftH);
                } else {
                    $('.leftbar').css('min-height', rightH + 30);
                }
            }
        }
    },
    loading: function(stype) {
        var num = $('.loading').length;
        if (num == 0) {
            var html = '<div class="loading"><div class="layer"></div><div class="gif"></div></div>';
            $('body').append(html);
        }
        $('.loading').show();
        if(typeof stype!='undefined'){
            $('.loading').hide();
        }
    },
    unloading: function() {
        $('.loading').hide();
    },
    alert: function(txt, callback) {
        $('.alert').remove();
        var html = '<div class="alert" style="display:none"><div class="layer"></div><dl class="box"><dt>提示</dt><dd><div class="condiv">' + txt + '</div><div class="btndiv"><span class="btn btn-blue">确定</span></div></dd></dl></div>';
        $('body').append(html);
        $('.alert').fadeIn();
        $('.alert .btn').click(function() {
            $('.alert').remove();
            if (typeof callback != 'undefined') {
                callback('1');
            }
        });
        $('.alert').show();
        $('input').blur();
    },

    showAct: function(parentid, id, callback) {
        $('.info').remove();
        var html = '<div class="info" style="display:none"><div class="layer"></div><dl class="box"><dt>活动详情</dt><dd><div class="condiv">';
        var url = '/activity/getsubInfo';
        $.post(url, {
            'parentid': parentid,
            'id': id
        }, function(data) {
            var s = '<table class="table-form">';
            s += '<tr><td class="name" width="20%">活动名称：</td>';
            s += '<td class="value" width="30%"><span>';
            s += data.parentName;
            s += '</span></td> <td class="name" width="20%">子活动名称：</td><td class="value" width="30%"><span>';
            s += data.name;
            s += '</span></td></tr>';
            s += '<tr><td class="name">活动对象：</td><td class="value">';
            s += data.role > 0 ? "服务员" : "消费者";
            s += '</td><td class="name">策略类型：</td><td class="value">';
            switch (data.activityType) {
                case '0':
                    s += "红包策略";
                    break;
                case '1':
                    s += "乐币策略";
                    break;
                case '2':
                    s += "乐券策略";
                    break;
                case '3':
                    s += "组合策略";
                    break;
                case '4':
                    s += "积分策略";
                    break;
                case '5':
                    s += "叠加策略";
                    break;
                case '6':
                    s += "累计策略";
                    break;
                default:
                    s += data.activityType+"：活动类型未匹配，请联系管理员";
                    break;
            }
            s += '</td></tr>';
            s += '<tr><td class="name">策略内容：</td><td class="value">';
            s += data.details ? data.details : "无";
            s += '</td><td class="name">H5应用：</td><td class="value">';
            s += data.webAppName ? data.webAppName : "无";
            s += '</td></tr>';
            s += '<tr><td class="name">奖励发放条件：</td><td class="value">';
            s += data.subscribeNeeded == 0 ? "无需关注公众号" : "需要关注公众号";
            s += '</td><td class="name">活动时间：</td><td class="value">';
            s += data.startTime;
            s += '至';
            s += data.endTime;
            s += '</td></tr>';
            s += '<tr><td class="name">指定活动地区：</td><td class="value">';
            s += data.area ? data.area : "未指定";
            s += '</td><td class="name">关联乐码批次：</td><td class="value">';
            s += data.batchNo ? data.batchNo : "未指定";
            s += '</td></tr>';
            s += '<tr><td class="name">关联生产入库单：</td><td class="value">';
            s += data.prodInOrder ? data.prodInOrder : "未指定";
            s += '</td><td class="name">关联出库单：</td><td class="value">';
            s += data.prodOutOrder ? data.prodOutOrder : "未指定";
            s += '</td></tr>';
            s += '<tr><td class="name">关联销售区域：</td><td class="value">';
            s += data.saletoagc ? data.saletoagc : "未指定";
            s += '</td><td class="name">关联过期策略：</td><td class="value">';
            if (data.expireTime) {
                switch (data.expireOprt) {
                    case "=":
                        s += '保质期为  ';
                        s += data.expireTime;
                        s += '(当天) 的产品 参与活动';
                        break;
                    case "<":
                        s += '在此日期 ';
                        s += data.expireTime;
                        s += '(不包含此天)之前过期产品参与活动';
                        break;
                    case "<=":
                        s += '在此日期 ';
                        s += data.expireTime;
                        s += '(包含此天)之前过期产品参与活动';
                        break;
                    default:
                        s += "";
                }
            } else {
                s += "未指定";
            }
            s += '</td></tr></table>';
            html = html + s + '</div><div class="btndiv"><span class="btn btn-blue">关闭</span></div></dd></dl></div>';
            $('body').append(html);
            $('.info').fadeIn();
            $('.info .btn').click(function() {
                $('.info').remove();
                if (typeof callback != 'undefined') {
                    callback('1');
                }
            });
            $('.info').show();
            //鼠标可以拖拽 弹出的可视窗口
            $('.info dl dt').mousedown(function(ev) {
                var startX = ev.pageX;
                var startY = ev.pageY;
                $(document).mousemove(function(ev) {

                    var offset = new Object();
                    var disX = ev.pageX - startX;
                    var disY = ev.pageY - startY;
                    startX = ev.pageX;
                    startY = ev.pageY;
                    offset.left = $('.info dl').offset().left + disX;
                    offset.top = $('.info dl').offset().top + disY;
                    $(".info dl").offset(offset);
                });
                $(document).mouseup(function() {
                    $(document).off();
                });
            });
        }, 'json');
    },
    confirm: function(txt,confirmCallback) {
        var btn_ok = arguments[2] || '确定';
        var btn_cancle = arguments[3] || '取消';
        $('.confirm').remove();
        var html = '<div class="confirm" style="display:none"><div class="layer"></div><dl class="box"><dt>提示</dt><dd><div class="condiv">' + txt + '</div><div class="btndiv"><span class="btn btn-blue" data="1">' + btn_ok + '</span> <span class="btn btn-gray" data="0">' + btn_cancle + '</span></div></dd></dl></div>';
        $('body').append(html);
        $('.confirm').fadeIn();
        $('.confirm .btn').click(function() {
            var num = Number($(this).attr('data'));
            confirmCallback(num);
            $('.confirm').remove();
        });
        $('.confirm').show();
    },
    sendConfirm: function(id, txt, confirmCallback, tags) {
        $('.send-confirm').remove();
        var html = '<div class="send-confirm" style="display:none"><div class="layer"></div><dl class="box"><dt></dt><dd><div class="condiv"><div class="s1">订单ID号：<span class="span1"><input type="text" id="textid" value="'+id+'"></span></div><div class="s2">物流信息：<span class="span2"><textarea cols="2" rows="2"></textarea></span></div></div><div class="btndiv"><span class="btn btn-blue" data="1">保存</span> <span class="btn btn-gray" data="0">取消</span></div></dd></dl></div>';
        $('body').append(html);
        if(txt == 1){
            $('.send-confirm dl dt').text('编辑物流信息');
        }else{
            $('textarea').val(txt);
            $('.send-confirm dl dt').text('查看物流信息');
            $('.send-confirm .btn-blue').hide();
            $('.send-confirm .btn-gray').text('关闭');
            $('.send-confirm textarea').attr("disabled","disabled");
        }
        $('.send-confirm').fadeIn();
        $('.send-confirm .btn').click(function() {
            var num = Number($(this).attr('data'));
            var textarea = $('textarea').val();
            confirmCallback(num,textarea);
            $('.send-confirm').remove();
        });
        if (tags) {
            $('.send-confirm textarea').val("虚拟商品无需填写");
            $('.send-confirm textarea').attr("disabled", true);
        }
        $('.send-confirm').show();
    },
    formConfirm: function(title, domid, confirmCallback) {
        $('.confirm-form').remove();
        var content = $('#' + domid).html();
        var html = '<div class="confirm-form" style="display:none"><div class="layer"></div><dl class="box"><dt>' + title + '</dt><dd><div class="condiv">' + content + '</div><div class="btndiv"><span class="btn btn-blue" data="1">确定</span> <span class="btn btn-gray" data="0">取消</span></div></dd></dl></div>';
        $('body').append(html);
        $('.confirm-form').fadeIn();
        $('.confirm-form .btn').click(function() {
            var num = Number($(this).attr('data'));
            confirmCallback(num);
            $('.confirm-form').remove();
        });
        $('.confirm-form').show();
    },
    transDialog: function(callback) {
        $('.transDialog').remove();
        $('body').css('overflow','hidden');
        var html = '<div class="transDialog" style="display:none"><div class="layer"></div><div class="con"></div><div class="close"></div></div>';
        $('body').append(html);
        $('.transDialog').fadeIn('fast');
        $('.transDialog .layer,.transDialog .close').click(function() {
            $('.transDialog').fadeOut(function(){
                $('.transDialog').remove();
                $('body').css('overflow','visible');
            });
        });
        callback(function(d){
            $('.transDialog .con').html(d);
        });
    },
    ajaxSelect: function(domid, url, callback) {
        $.post(url, {}, function(d) {
            if (typeof callback != 'undefined') {
                callback(d);
            }
            var data = d.data;
            var html = '';
            $(data).each(function(index, el) {
                var nbsp = '';
                if (/webkit/.test(navigator.userAgent.toLowerCase())) {
                    for (var i = 1; i < el.level; i++) {
                        nbsp += '　　　';
                    }
                }
                html += '<option value="' + el.id + '" class="txti' + el.level + '">' + nbsp + el.name + '</option>';
            });
            $('#' + domid).html(html);
            var editVal = $('#' + domid).attr('edit-value');
            if (editVal) {
                $('#' + domid).val(editVal);
            } else {
                $('#' + domid + ' option:first').prop("selected", "selected");
            }
        }, 'json');
    },
    ajaxSelectSub: function(domid, url, subDomid, subUrl) {
        $('#' + domid + ',#' + subDomid).attr('disabled', 'disabled');
        $.post(url, {}, function(d) {
            $.post(subUrl, {}, function(v) {
                //父数据
                window['select_data_' + domid] = d.data;
                var html = '';
                $(d.data).each(function(index, el) {
                    var nbsp = '';
                    if (/webkit/.test(navigator.userAgent.toLowerCase())) {
                        for (var i = 1; i < el.level; i++) {
                            nbsp += '　　　';
                        }
                    }
                    html += '<option value="' + el.id + '" class="txti' + el.level + '">' + nbsp + el.name + '</option>';
                });
                $('#' + domid).html(html);
                var editVal = $('#' + domid).attr('edit-value');
                if (editVal) {
                    $('#' + domid).val(editVal);
                } else {
                    $('#' + domid + ' option:first').prop("selected", "selected");
                }
                setTimeout(function() {
                    $('#' + domid).trigger('change');
                }, 10);
                //子数据
                window['select_data_' + subDomid] = v.data;
                $('#' + domid).on('change', function() {
                    var selId = $('#' + domid).val();
                    var newData = [];
                    $(window['select_data_' + subDomid]).each(function(index, el) {
                        if (el.categoryId == selId) {
                            newData.push(el);
                        }
                    });
                    var html = '';
                    $(newData).each(function(index, el) {
                        html += '<option value="' + el.id + '" class="txti' + el.level + '">' + el.name + '</option>';
                    });
                    $('#' + subDomid).html(html);
                    var editVal = $('#' + subDomid).attr('edit-value');
                    if (editVal) {
                        $('#' + subDomid).val(editVal);
                    } else {
                        $('#' + subDomid + ' option:first').prop("selected", "selected");
                    }
                });
                $('#' + domid + ',#' + subDomid).removeAttr('disabled');
            }, 'json');
        }, 'json');
    },
    uploadInit: function(fileId, upUrl, isatt, widthCallback) {
        $('#' + fileId).on('change', function() {
            var _this = $(this);
            var feild = $(this);
            if (typeof isatt != 'undefined') {
                function callbackAtt(d) {
                    var x = $.trim(d);
                    if (x != '') {
                        if (x == 'toolarge') {
                            common.alert('上传附件大小超出了允许范围！');
                            return;
                        }
                        if (x == 'exterror') {
                            common.alert('上传文件格式不正确！');
                            return;
                        }
                        var img = '<a class="att" href="' + d + '" target=_blank><img src="/static/images/att.jpg" width=100% height=100% /></a>';
                        _this.siblings('.hls-upload').children('.img').html(img);
                        _this.siblings('.hls-upload').children('input[type=hidden]').val(d);
                    } else {
                        common.alert('失败,请重试！');
                    }
                }
                common.ajaxUpload(feild, upUrl, callbackAtt);
            } else {
                function callback(d) {
                    var x = $.trim(d);
                    if (x != '') {
                        if (x == 'toolarge') {
                            common.alert('上传图片大小超出了允许范围！');
                            return;
                        }
                        if (x == 'exterror') {
                            common.alert('上传文件格式不正确！');
                            return;
                        }
                        var img = '<img src="' + d + '" width=100% height=100% />';
                        _this.siblings('.hls-upload').children('.img').html(img);
                        _this.siblings('.hls-upload').children('input[type=hidden]').val(d);
                        if (widthCallback!=='undefined') {
                            widthCallback(d);
                        }
                    } else {
                        common.alert('失败,请重试！');
                    }
                }
                common.ajaxUpload(feild, upUrl, callback);
            }
        });
    },
    uploadPlugin: function() {
        $('.js-upload,.js-upload-att').each(function() {
            var name = $(this).attr('name');
            var editVal = $(this).attr('edit-value');
            var img = '';
            if (editVal) img = '<img src="' + editVal + '" width=100% height=100% />';
            if ($(this).hasClass('js-upload-att')) {
                if (editVal) {
                    img = '<a class="att" href="' + editVal + '" target=_blank><img src="/static/images/att.jpg" width=100% height=100% /></a>';
                } else {
                    img = '<div class="att"></div>';
                }
            }
            var html = '<div class="hls-upload textarea"><div class="img">' + img + '</div><div class="choose noselect">选择</div><input type="hidden" name="' + name + '" value="' + (editVal ? editVal : "") + '" /></div>';
            $(html).insertBefore($(this));
            $(this).removeAttr('name');
        });
        $('.hls-upload .choose').off().on('click', function() {
            $(this).parent('.hls-upload').siblings('.js-upload').trigger('click');
            $(this).parent('.hls-upload').siblings('.js-upload-att').trigger('click');
        });
    },
    ajaxUpload: function(feild, upUrl, callback) {
        common.loading();
        var fd = new FormData();
        var fdIf = new FormData();
        var file = feild.get(0).files[0];
        var name = file.name;
        var ext = name.substr(name.lastIndexOf(".") + 1);
        var size = file.size / 1024;
        fd.append("userfile", 1);
        fd.append("userfile", file);
        fd.append("fileSize", size);
        fd.append("fileExt", ext);
        fdIf.append("userfile", 1);
        fdIf.append("userfile", 'if');
        fdIf.append("fileSize", size);
        fdIf.append("fileExt", ext);

        function dopost(data) {
            $.ajax({
                url: upUrl,
                type: "POST",
                processData: false,
                contentType: false,
                data: data,
                success: function(d) {
                    feild.val('');
                    common.unloading();
                    if (d == 'ifok') {
                        dopost(fd);
                    } else {
                        callback(d);
                    }
                },
                error: function() {
                    common.unloading();
                    common.alert('请求失败');
                }
            });
        }
        dopost(fdIf);

    },
    wxauth: function($type) {
        var length = $('.wxauth').length;
        if (length > 0) $('.wxauth').remove();
        var name = '';
        if ($type == 1) name = '消费者：';
        if ($type == 2) name = '供应链：';
        var html = '<div class="wxauth" style="display:none"><div class="layer"></div><div class="conbox"><h2>' + name + '</h2><ul class="authbtn"><li></li><li class="end"></li></ul></div></div>';
        $('body').append(html);
        $('.wxauth .authbtn>li').off().on('click', function() {
            var index = $(this).index();
            if (index == 1) {
                $('.wxauth').remove();
            }
            if (index == 0) {
                location.href = '/wx3rd/authpage?type=' + $type;
            }
        });
        $('.wxauth').fadeIn('fast');
    },
    requestFullScreen: function(element) {
        var docElm = element;
        if (typeof element == 'undefined') {
            docElm = document.documentElement;
        }
        //W3C
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
        }
        //FireFox
        else if (docElm.mozRequestFullScreen) {
            docElm.mozRequestFullScreen();
        }
        //Chrome等
        else if (docElm.webkitRequestFullScreen) {
            docElm.webkitRequestFullScreen();
        }
        //IE11
        else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    },
    exitFullScreen: function() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
            document.webkitCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    },
    fullScressStatus: function(callback) {
        var _this = this;
        document.addEventListener("fullscreenchange", function() {
            callback((document.fullscreen) ? "yes" : "no");
        }, false);
        document.addEventListener("mozfullscreenchange", function() {
            callback((document.mozFullScreen) ? "yes" : "no");
        }, false);
        document.addEventListener("webkitfullscreenchange", function() {
            callback((document.webkitIsFullScreen) ? "yes" : "no");
        }, false);
        document.addEventListener("msfullscreenchange", function() {
            callback((document.msFullscreenElement) ? "yes" : "no");
        }, false);
        return;
    },
    //字符串截取
    /**参数说明：
     * 根据长度截取先使用字符串，超长部分追加…
     * str 对象字符串
     * len 目标字节长度
     * 返回值： 处理结果字符串
     */
    cutString: function(str, len) {
        //length属性读出来的汉字长度为1
        if (str.length * 2 <= len) {
            return str;
        }
        var strlen = 0;
        var s = "";
        for (var i = 0; i < str.length; i++) {
            s = s + str.charAt(i);
            if (str.charCodeAt(i) > 128) {
                strlen = strlen + 2;
                if (strlen >= len) {
                    return s.substring(0, s.length - 1) + "...";
                }
            } else {
                strlen = strlen + 1;
                if (strlen >= len) {
                    return s.substring(0, s.length - 2) + "...";
                }
            }
        }
        return s;
    },
    /**add by chenwei
     * 获取rpt的url
     */
    getRptUrl: function() {
        var hostName = window.location.hostname;
        return 'http://www.whl8.cn/';
    },
    // 金额格式化逗号分隔
    splitFormoney:function(money,num){
        num = num > 0 && num <= 20 ? num : 0;  
        money = parseFloat((money + "").replace(/[^\d\.-]/g, "")).toFixed(num) + "";  
        var l = money.split(".")[0].split("").reverse(),  
        r = money.split(".")[1];  
        t = "";  
        for(i = 0; i < l.length; i ++ )  
        {  
           t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");  
        } 
        console.log(num);
        if(parseInt(num)>0){
            return t.split("").reverse().join("") + "." + r; 
        }else{
            return t.split("").reverse().join(""); 
        }
        
    }
};
$(function() {
    common.init();
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        }
    });
});


(function(window, undefined) {
    window.netError = function(err) {
        common.alert('无法连接服务器。');
    };

    var hls = {};

    hls.utils = {};

    hls.utils.StringUtil = {
        isMobile: function(str) {
            var mobile = /^1[34578]\d{9}$/;
            return mobile.test(str);
        },
    };

    window.hls = hls;
})(this);
