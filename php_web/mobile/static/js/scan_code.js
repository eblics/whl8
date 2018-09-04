function resetSize(){
    var winWidth=$(window).width();
    var adHeight=winWidth*5/6;
    $('.adarea').height(adHeight);
    $('.animation').css('bottom',adHeight);
}

function randGif(){
    var num=parseInt(5*Math.random());
    $('.animation').css('background-image','url(/static/images/loading/'+num+'.gif)');
    setTimeout(function() {
        $('.animation .animation-scan').fadeOut();
    },3500);
}

function RandomNumBoth(Min,Max){
      var Range = Max - Min;
      var Rand = Math.random();
      var num = Min + Math.round(Rand * Range);
      return num;
}

function done(result){
    if(result.errcode==0){
        ajaxOk=true;
        toUrl=result.data.url;
    }else{
        $('.animation .error').fadeIn().children('.txt').html(result.errmsg);
    }
}

function fail(result){
    console.log(result);
    $('.animation .error').fadeIn().children('.txt').html('出错啦');
}

document.body.addEventListener('touchmove', function (event) {
        event.preventDefault();
}, false);
resetSize();
randGif();
var adOk=false;
var ajaxOk=false;
var toUrl='';
var randTime=RandomNumBoth(5000,8000);
console.log(randTime);
setTimeout(function(){
    adOk=true;
},randTime);
if(typeof window.mchGeoLocation!='undefined' && window.mchGeoLocation==1){
    $.ajax({
        url: hlsjs.getRootUrl() + 'activity/get_best_match',
        data:{code: window.lecode, pos:{lng:0,lat:0}}
    }).done(done).fail(fail);
}else{
    if(hlsjs.ready != undefined) {
        hlsjs.ready(function(){
            hlsjs.getLocation(function(pos) {
                hlsjs.watchLocation(pos, window.lecode, done,fail);
            },function(err){
                alert('允许获取地理位置，才能匹配所在地区的活动。');
                hlsjs.getLocation(function(pos){
                    hlsjs.watchLocation(pos, window.lecode, done,fail);
                },function(pos){
                    hlsjs.watchLocation(pos, window.lecode, done,fail);
                });
            });
        });
    }else{
        $(document).ready(function() {
            alert('doc.ready');
            console.log('ready');
            hlsjs.getLocation(function(pos) {
                hlsjs.watchLocation(pos,done,fail);
            });
        });
    }
}
setInterval(function(){
    if(adOk&&ajaxOk){
        window.location.href=toUrl;
    }
},1000);