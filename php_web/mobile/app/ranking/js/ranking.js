$(function() {

    var appPath = 'ranking',
        provincesCache,
        currentAreaCode = 0, // 全国
        currentDate = 0,
        currentPage = 1,
        lastPage = 0,
        loaded = false,
        appConfig,
        currentMember,
        inSelectArea = false;

    function init() {
        bindEvent();
        hlsApp.getCurrentMember(appPath, function(resp) {
            $('.hls-page-ranking').show();
            currentMember = resp;
            appConfig = JSON.parse(resp.data.app_config);
            var i = 1;
            if (appConfig.show_day) {
                i++;
                $('#1').addClass('show');
            }
            if (appConfig.show_week) {
                i++;
                $('#2').addClass('show');
            }
            if (appConfig.show_month) {
                i++;
                $('#3').addClass('show');
            }
            if (appConfig.show_year) {
                i++;
                $('#4').addClass('show');
            }
            if (i == 1) {
                $('.hls-page-ranking nav').hide();
            }
            $('.hls-page-ranking nav a').css('width', (100 / i) + '%')
            loadAreas(areasAdapter);
            if (! appConfig.location_area) {
                getLevelList(currentMember);
            } else {
                getIpAddress(function(locateRest) {
                    var cityName = locateRest.city;
                    $.get('/appif/ranking/area_code', {"city_name": cityName}, function(data) {
                        currentAreaCode = data.data.code;
                        $('#select_city h3').text(data.data.name);
                        getLevelList(currentMember);
                    }).error(function(err) {
                        alert('无法连接服务器！');
                    });
                });
            }
        }, function(err) {
            $('.hls-page').hide();
            $('.hls-page-error').show();
        });
    }

    function bindEvent() {
        $('nav a').on('touchend', function() {
            $('nav a').removeClass('selected');
            $(this).addClass('selected');
            currentDate = $(this).prop('id');
            currentPage = 1;
             $('#hls_ranking_list').empty();
            getLevelList(currentMember);
        });

        $('#select_city').click(function() {
            $('.hls-page-ranking').hide();
            $('.hls-layer-province').show();
            inSelectArea = true;
        });

        $(document).scroll(function() {
            var h = $(this).height(); // 内容的高度
            var c = $('.hls-page').height();
            if (h - c - $(this).scrollTop() <= 0) {
                if (! loaded) {
                    return;
                }
                if (inSelectArea) {
                    return;
                }
                currentPage++;
                getLevelList(currentMember);
            }
        });
    }

    function areasAdapter(provinces) {
        provincesCache = provinces;
        $('#areas_list').append('<dt id="0">全国</dt>');
        provinces.forEach(function(province) {
            var row;
            row = '<dt id="' + province.code + '">' + province.name + '</dt>';
            $('#areas_list').append(row);
        });

        $('#areas_list dt').click(function() {
            $('.hls-layer-province').hide();
            var code = $(this).prop('id');
            currentAreaCode = 0;
            if (code == 0) {
                $('.hls-page-ranking').show();
                inSelectArea = false;
                $('#select_city h3').text($(this).text());

                currentPage = 1;
                $('#hls_ranking_list').empty();
                getLevelList(currentMember);
                scrollTo(0, 0);
                return;
            }
            $('.hls-layer-city').show();
            provincesCache.forEach(function(item) {
                if (item.code == code) {
                    cityAdapter(item.cities);
                }
            });
        });
    }

    function cityAdapter(cities) {
        $('#city_list').empty();
        cities.forEach(function(cities) {
            var row;
            row = '<dt id="' + cities.code + '">' + cities.name + '</dt>';
            $('#city_list').append(row);
        });
        scrollTo(0, 0);

        $('#city_list dt').click(function() {
            $('.hls-page-ranking').show();
            inSelectArea = false;
            $('.hls-layer-city').hide();
            currentAreaCode = $(this).prop('id');
        $('#select_city h3').text($(this).text());

            currentPage = 1;
            $('#hls_ranking_list').empty();
            getLevelList(currentMember);
            scrollTo(0, 0);
        });

    }

    /**
     * 获取排行列表
     *
     * @param range 排行地理位置
     */
    function getLevelList(currentMember) {
        loaded = false;
        showLoading();
        var user = currentMember.data;

        // -------------------------------------------------------------------------
        // Changed by shizq start
        var params = {
            "user_id": user.id,
            "mch_id": hlsApp.getMchId(),
            "range": currentDate,
            "city_code": currentAreaCode,
            "page": currentPage,
            "page_size": appConfig.per_page_num
        };
        $.ajax({
            url: '/appif/ranking',
            data: params,
        // Changed by shizq end
        // --------------------------------------------------------------------------
        }).done(function(resp) {
            closeLoading();
            if (! resp.errcode) {
                $('#total_scan').text(resp.data.total_num || 0);
                var row;
                if (resp.data.myself.rank_id > 500) {
                    resp.data.myself.rank_id = '500+';
                }
                if (resp.data.myself != undefined && (resp.data.myself.rank_id > 3 || resp.data.myself.rank_id == '500+')) {
                    row  = '<li class="hls-ranking-row mine">';
                    row +=   '<span id="my_level">' + resp.data.myself.rank_id + '</span>';
                    row +=   '<img class="head-img" id="my_head" src="';
                    row +=   (resp.data.myself.headimgurl || 'images/default_head_img.png?v=3') + '" />';
                    row +=   '<div class="text-content">';
                    row +=     '<p id="my_name" class="title">' + (resp.data.myself.nickname || '红码用户') + '</p>';
                    row +=     '<p class="content">累计扫码<span id="my_scan_num">';
                    row +=     (resp.data.myself.scanNum || 0) + '</span>次</p>';
                    row +=   '</div>';
                    row +=   '<span class="get-up">加油！</span>';
                    row += '<li>';
                    if (currentPage == 1 && appConfig.show_self) {
                        $('#hls_ranking_list').append(row);
                    }
                }
                var i = 0, myRank =(resp.data.myself!=undefined)?resp.data.myself.rank_id:-1;
                resp.data.ranking_list.forEach(function(item) {
                    var row, level, levelImg;
                    i = item.rank_id;
                    if (i <= 3) {
                        levelImg =   '<img class="img-level" src="images/' + i + '.png" />';
                    } else {
                        levelImg = '';
                    }
                    level =   '<span>' + i + '</span>';
                    if (myRank == i) {
                        row =  '<li class="hls-ranking-row mine">' + level;
                    } else {
                        row =  '<li class="hls-ranking-row">' + level;
                    }
                    item.headimgurl = item.headimgurl || 'images/default_head_img.png';
                    row +=   '<img class="head-img" src=' + item.headimgurl + ' />';
                    row +=   '<div class="text-content">';
                    row +=     '<p class="title">' + (item.nickname || '红码用户') + '</p>';
                    row +=     '<p class="content">累计扫码<span>' + item.scanNum + '</span>次</p>';
                    row +=   '</div>';
                    row +=   levelImg;
                    row += '</li>';
                    $('#hls_ranking_list').append(row);
                    loaded = true;
                });
            } else {
                closeLoading();
                $('#total_scan').text(resp.data.total_num || 0);
                var err = '<li class="err-item">' + resp.errmsg + '。</li>';
                $('#hls_ranking_list').append(err);
                loaded = true;
            }
        }).fail(function(err) {
            closeLoading();
            $('#total_scan').text(0);
            var err = '<li class="err-item">无法连接服务器。</li>';
            $('#hls_ranking_list').append(err);
            loaded = true;
        });
    }

    function loadAreas(callback) {
        $.get('/appif/ranking/areas', {}, function(resp) {
            if (! resp.errcode) {
                callback.call(window, resp.data);
            } else {
                callback.call(window, []);
            }
        }).error(function(err) {
            // alert('无法连接服务器！');
        });
    }

    function showLoading() {
        var loading = '<img class="hls-loading" src="/static/images/loading.gif" />';
        $('body').append(loading);
    };

    function closeLoading() {
        $('body .hls-loading').remove();
    }

    function getIpAddress(callback) {
        $.getScript('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js', function(_result) {
            if (remote_ip_info.ret == '1') {
                var obj = {
                    province: remote_ip_info.province,
                    city: remote_ip_info.city,
                    errcode: 0,
                    errmsg: 'success'
                }
                callback.call(window, obj);
            } else {
                callback.call(window, {errcode: 1, errmsg: '无法获取地理位置'});
            }
        });
    }
    init();
});