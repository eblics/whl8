function done(result){
    if(result.errcode==0){
        window.location.href=result.data.url;
    }
    else
        {
        $('#loading').hide();
        $('#page-err').show();
        $('.err-text').html(result.errmsg);
    }
}
function fail(result){
    console.log(result);
    //alert(JSON.stringify(result));
    $('#loading').hide();
    $('#page-err').show();
    $('.err-text').html('出错啦');
}
if(typeof window.mchGeoLocation!='undefined' && window.mchGeoLocation==1){
    $.ajax({
        url: hlsjs.getRootUrl() + 'activity/get_best_match',
        data:{code: hlsjs.getSegment(3),pos:{lng:0,lat:0}}
    }).done(done).fail(fail);
}else{
    if(hlsjs.ready != undefined) {
        hlsjs.ready(function(){
            hlsjs.getLocation(function(pos) {
                hlsjs.watchLocation(pos,hlsjs.getSegment(3),done,fail);
            },function(err){
                alert('允许获取地理位置，才能匹配所在地区的活动。');
                hlsjs.getLocation(function(pos){
                    hlsjs.watchLocation(pos,hlsjs.getSegment(3),done,fail);
                },function(pos){
                    hlsjs.watchLocation(pos,hlsjs.getSegment(3),done,fail);
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
