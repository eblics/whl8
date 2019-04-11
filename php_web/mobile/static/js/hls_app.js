/**
 * 平台应用通用js
 *
 * @author shizq
 */
(function() {

    var search = location.search, mchId, env = location.host, userId;

    if (search.split('=')[0] === '?mch_id') {
        mchId = search.split('=')[1];
    } else {
        mchId = 0;
    }

    // 声明命名空间
    var hls = window.hls || {};

    hls.h5util = {};

    function showLoading() {
        var loading = '<img class="hls-loading" src="/app/turntable/images/loading.gif" />';
        $('body').append(loading);
    }
    hls.h5util.showLoading = showLoading;

    function closeLoading() {
        $('body .hls-loading').remove();
    }
    hls.h5util.closeLoading = closeLoading;

    // ====================================================================================================

    hls.app = {};

    hls.app.getMchId = function() {
        return mchId;
    };

    hls.app.getUserId = function() {
        return userId;
    };

    // ---------------------------------------------------------------------
    // 获取当前用户信息
    hls.app.getCurrentMember = function(appPath, successCallback, faildCallback) {
        $.get('/hls_app/get_member', {"mch_id": mchId, "app_path": appPath}, function(resp) {
            if (! resp.errcode) {
                userId = resp.data.id;
                var objData = JSON.parse(resp.data.app_config);
                $('title').text(objData.name);
                if (typeof successCallback === 'function') {
                    successCallback.call(window, resp);
                }
            } else if (resp.errcode == 10002) {
                location.href = '/login?mch_id=' + mchId + '&hls_app=' + encodeURIComponent(location.pathname + location.search);
            } else {
                var err_page = '<section class="hls-page hls-page-error">';
                err_page +=      '<article>';
                err_page +=        '<img src="/static/images/bg_error.png" />';
                err_page +=        '<h3>红码</h3>';
                err_page +=        '<p>抱歉：' + resp.errmsg + '。</p>';
                err_page +=      '</article>';
                err_page +=    '</section>';
                $('body').append(err_page);
                $('title').text('出错了！！！');
                faildCallback.call(window, resp);
            }
        }).fail(function(err) {
            faildCallback.call(window, {"errmsg": '无法连接服务器。'});
        });
    };

    // ---------------------------------------------------------------------
    // 获取当前应用的配置
    hls.app.initTurntableItem = function(appPath, successCallback, faildCallback) {
        $.get('/appif/turntable/bonus_item', {"mch_id": mchId, "app_path": appPath}, function(resp) {
            if (! resp.errcode) {
                successCallback.call(window, resp);
            } else {
                faildCallback.call(window, resp);
            }
        }).error(function(err) {
            alert('无法连接服务器！');
        });
    };

    window.hls = hls;
    window.hlsApp = hls.app;

    // ====================================================================================================

    window.originAler = window.alert;

    // ---------------------------------------------------------------------
    // 重写window的alert方法
    window.alert = function(msg) {
        var element  = '';
            element += '<div class="hls-alert">';
            element +=   '<h3>消息</h3>';
            element +=   '<p>' + msg + '</p>';
            element += '</div>';
        $('.hls-alert').remove();
        $('body').append(element);
        if (msg.length > 20) {
            setTimeout(function() {
                $('.hls-alert').fadeOut();
            }, 10000);
        } else {
            setTimeout(function() {
                $('.hls-alert').fadeOut();
            }, 2000);
        }
    };
})();
