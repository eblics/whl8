// $('head').append('<script type="text/javascript" src="/h5/mchdata/?t=' + new Date().getTime() + Math.random().toString(36).substr(2) + '"></script>');
//抽奖方法
function getReward(){
    hlsjs.ready(function() {
        hlsjs.takeActivity(function(result) {
            var html='';
            var html0='<div class="li cash"><div class="li-in"><div class="tip">中奖啦</div><div class="name">?<em>元</em></div><div class="type">现金红包</div></div></div>';
            var html2='<div class="li card"><div class="li-in"><div class="tip">中奖啦</div><div class="name">?</em></div><div class="type">乐券</div></div></div>';
            var html3='<div class="li point"><div class="li-in"><div class="tip">中奖啦</div><div class="name">?</div><div class="type">积分</div></div></div>';
            var htmlerr='<div class="error">?</div>';
            if (result.errcode == 0) {
                if (result.datatype == 0)
                    html=html0.replace('?',(Number(result.amount) / 100).toFixed(2)+'');
                if (result.datatype == 2)
                    html=html2.replace('?',result.data.name+'');
                if (result.datatype == 3)
                    html=html3.replace('?',result.amount+'');
                if (result.datatype == 100){
                    var reHtml='';
                    if(result.multiData.length>0){
                        for(var i=0;i<result.multiData.length;i++){
                            if(result.multiData[i].strategyType==0) html+=html0.replace('?',(Number(result.multiData[i].value) / 100).toFixed(2)+'');
                            if(result.multiData[i].strategyType==2) html+=html2.replace('?',result.multiData[i].value+'');
                            if(result.multiData[i].strategyType==3) html+=html3.replace('?',result.multiData[i].value+'');
                        }
                    }else{
                        html=htmlerr.replace('?','运气不够好哦~');
                        $('#reward dd').html(html);
                    }
                }
                $('#reward dd').html(html);
            } else if (result.errcode == 20) {
                $('#reward dd').html(htmlerr.replace('?',result.errmsg+''));
            } else if (result.errcode == 2) {
                $('#reward dd').html(htmlerr.replace('?','此码已被他人扫过'));
            } else if (result.errcode == 90001) {
                $('#reward dd').html(htmlerr.replace('?','出错了'));
            } else {
                $('#reward dd').html(htmlerr.replace('?',result.errmsg+''));
            }

        });
        hlsjs.wx();
    });
}

//扫码排名方法
function show_rank(data){
    var user=data.data;
    $.ajax({
        url: hlsjs.getRootUrl()+'/h5/userdata',
        jsonp: 'callback',
        dataType: 'jsonp',
        data:{mchid:user.mchId,userid:user.id}
    }).done(function(h5UserData){
        console.log(h5UserData);
        if (typeof h5UserData.rank != 'undefined' && h5UserData.rank != null) {
            $('#rank .city').html(h5UserData.city);
            $('#rank .number span').html(h5UserData.rank);
            $('#rank .scantime strong').html(h5UserData.scanNum);
        }
    }).fail(function(data){
        console.log('排名出错');
        console.log(data);
    });
}

//按配置加载模块
function loadModule() {
    $.ajax( {
        url:hlsjs.getRootUrl()+'/h5/mchdata/?t=' + new Date().getTime() + Math.random().toString(36).substr(2), 
        data:{},    
        type:'post',    
        cache:false,    
        dataType:'script',    
        success:function(d) { 
            if (typeof h5MchData == 'undefined') {
                alert('加载失败，请重试!');
                return;
            }
            if ($.trim(h5MchData.h5Config) == '') {
                $('.module').show();
                $('#subscribe .qrcode img').attr('src', h5MchData.qrCode);
                hlsjs.getCurrentUser(show_rank);
                return;
            }
            var config = $.parseJSON(h5MchData.h5Config);
            $.each(config, function(k, v) {
                if (k == 'banner' || k == 'logo') $('#' + k + ' img').attr('src', v);
                if(k=='title1'||k=='title2') $('#'+k).html(v);
                if (k == 'rule') {
                    var rulehtml = '';
                    $.each(v, function(i, e) {
                        rulehtml += '<p>' + e + '</p>';
                    });
                    $('#' + k + ' .rule-box').html(rulehtml);
                }
                if (k == 'subscribe') $('#' + k + ' .qrcode img').attr('src', h5MchData.qrCode);
                $('#' + k).show();
                // if(k=='rank'){
                //     hlsjs.getCurrentUser(show_rank);
                // }
                if(k=='reward'){
                    //抽奖
                    getReward();
                }
            });
        },
        error : function(d) {
        } 
    });
    
}

//摇一摇事件
function shakeScan() {
    if (window.DeviceMotionEvent) {
        window.addEventListener('devicemotion', deviceMotionHandler, false);
    }
    var SHAKE_THRESHOLD = 4000;
    var last_update = 0;
    var x, y, z, last_x = 0,
        last_y = 0,
        last_z = 0;
    //设备运动事件
    function deviceMotionHandler(eventData) {
        var acceleration = eventData.accelerationIncludingGravity;
        var curTime = new Date().getTime();
        if ((curTime - last_update) > 10) {
            var diffTime = curTime - last_update;
            last_update = curTime;
            x = acceleration.x;
            y = acceleration.y;
            z = acceleration.z;
            var speed = Math.abs(x + y + z - last_x - last_y - last_z) / diffTime * 10000;
            if (speed > SHAKE_THRESHOLD) {
                scanByJssdk();
            }
            last_x = x;
            last_y = y;
            last_z = z;
        }
    }
}

//jssdk扫码
window.scanStart=false;
function scanByJssdk() {
    if(window.scanStart) return;
    window.scanStart=true;
        wx.scanQRCode({
            needResult: 0,
            scanType: ["qrCode"],
            success: function(res) {
                setTimeout(function(){window.scanStart=false;},3000);
                console.log(res);
            }
        });
    console.log('摇一摇操作');
}


//先判断设备是否支持HTML5摇一摇功能
if (window.DeviceMotionEvent) {
    //获取移动速度，得到device移动时相对之前某个时间的差值比
    window.addEventListener('devicemotion', deviceMotionHandler, false);
} else {
    alert('您好，你目前所用的设备好像不支持重力感应哦！');
}

//设置临界值,这个值可根据自己的需求进行设定，默认就3000也差不多了
var shakeThreshold = 3000;
//设置最后更新时间，用于对比
var lastUpdate = 0;
//设置位置速率
var curShakeX = curShakeY = curShakeZ = lastShakeX = lastShakeY = lastShakeZ = 0;

function deviceMotionHandler(event) {
    //获得重力加速
    var acceleration = event.accelerationIncludingGravity;
    //获得当前时间戳
    var curTime = new Date().getTime();
    if ((curTime - lastUpdate) > 100) {
        //时间差
        var diffTime = curTime - lastUpdate;
        lastUpdate = curTime;
        //x轴加速度
        curShakeX = acceleration.x;
        //y轴加速度
        curShakeY = acceleration.y;
        //z轴加速度
        curShakeZ = acceleration.z;
        var speed = Math.abs(curShakeX + curShakeY + curShakeZ - lastShakeX - lastShakeY - lastShakeZ) / diffTime * 10000;
        if (speed > shakeThreshold) {
            //TODO 相关方法，比如：
            //播放音效
            shakeAudio.play();
            // //动作
            if(!window.scanStart){
                scanByJssdk();
            }
        }
        lastShakeX = curShakeX;
        lastShakeY = curShakeY;
        lastShakeZ = curShakeZ;
    }
}


//预加摇一摇声音
var shakeAudio = new Audio();
shakeAudio.src = 'sound/shake_sound.mp3';
var shake_options = {
    preload: 'auto'
}
for (var key in shake_options) {
    if (shake_options.hasOwnProperty(key) && (key in shakeAudio)) {
        shakeAudio[key] = shake_options[key];
    }
}

window.onload = function() {
    //加载进度条
    Pace.on('done', function() { //加载完成
        $('.wrapper').show();
    });
    //加载模块
    loadModule();
    $('body').fadeIn();
}