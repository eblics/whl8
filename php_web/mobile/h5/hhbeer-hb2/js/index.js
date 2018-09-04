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
            var html0='$ ?元';
            var html2='恭喜发财<BR>?';
            var html3='恭喜发财<BR>?积分';
            var htmlerr='?';
            if (result.errcode == 0) {
                if (result.datatype == 0) {
                    html = html0.replace('?', (Number(result.amount) / 100).toFixed(2)+'');
                    html = html.replace('$', getGameStrByAmount(result.amount));
                }
                if (result.datatype == 2) {
                    html=html2.replace('?',result.data.name+'');
                }
                if (result.datatype == 3) {
                    html=html3.replace('?',result.amount+'');
                }
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
                $('.result').html(htmlerr.replace('?', result.errmsg));
            }

        });
    });

    var getGameStrByAmount = function(amount) {
        var gameStr, amount = amount + ''; // 数字转字符串类型
        switch (amount) {
            case "10":
                var gameStrs = [
                    '给摇车高手满上!', 
                    '喝一个漱漱口!<br/>', 
                    '左边喝一个!<br/>', 
                    '右边喝一个!<br/>', 
                    '对面喝一个!<br/>'
                ];
                gameStr = gameStrs[Math.floor(Math.random() * (gameStrs.length))];
                break;
            case "20":
                var gameStrs = [
                    '挖到重来!<br/>', 
                    '感情深，一口闷！', 
                    '不中大奖我不停！', 
                    '中奖啦？走一个！', 
                    '来个交杯!<br/>'
                ];
                gameStr = gameStrs[Math.floor(Math.random() * (gameStrs.length))];
                break;
            case "50":
                gameStr = '瓶子提上老吹到！';
                break;
            case "80":
                gameStr = '乱拳打死老师傅！';
                break;
            case "5800":
                gameStr = '红包炸弹！打个通关！';
                break;
            default:
                gameStr = '恭喜你，中得：<br/>';
        }
        return gameStr;
    }
    
    // setInterval(function() {
        // $('#gameStr').toggleClass('big');
    // }, 550);
});
