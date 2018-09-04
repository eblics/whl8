$(function() {
    //加载进度条
    Pace.on('done', function() { //加载完成
        $('.wrapper').show();
    });
    var winHeight=$(window).height();
    if(winHeight>1008){
        $('.wrapper').height(winHeight);
    }
    hlsjs.ready(function() {
        hlsjs.takeActivity(function(result) {
            var html='';
            var html0='恭喜发财<BR>?元';
            var html2='恭喜发财<BR>?';
            var html3='恭喜发财<BR>?积分';
            var htmlerr='?';
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
                        html=htmlerr.replace('?','运气不够好哦');
                        $('.result').html(html);
                    }
                }
                $('.result').html(html);
            } else if (result.errcode == 20) {
                $('.result').html(htmlerr.replace('?',result.errmsg+''));
            } else if (result.errcode == 2) {
                $('.result').html(htmlerr.replace('?','此码已被扫过'));
            }else if (result.errcode == 3) {
                $('.result').html(htmlerr.replace('?','此码已“失身”与您'));
            }else {
                $('.result').html(htmlerr.replace('?','出错了'));
            }

        });
    });
    
});
