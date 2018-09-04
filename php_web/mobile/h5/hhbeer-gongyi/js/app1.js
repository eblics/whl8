/**
 * Created by Vee on 2017/3/29.
 */
$(function () {
    //查看公益金
    $(".scene .hand").off().on("tap",function (event) {
        $(".iframe").removeClass("hidden");
        $(".scene").addClass("hidden");
    });

    $(".iframe.close_btn").off().on("tap",function (event) {
        $(".scene").removeClass("hidden");
        $(".iframe").addClass("hidden");
    })

    hlsjs.ready(function() {
        hlsjs.takeActivity(function(data) {
            if (data.errcode == 0){
                if (data.datatype == 0){
                    //奖品类型为：红包
                    //data.amount 中奖金额，单位分，实际使用元需要/100
                    $('#result').html("恭喜您中奖啦！")
                    $('#result2').html((Number(data.amount) / 100).toFixed(2) + '<p>元</p>');
                }
                if (data.datatype == 2){
                    //奖品类型为：乐券
                    //data.data.name 奖品名称
                    console.log(2);
                    $('#result').html("恭喜您中奖啦！")
                    $('#result2').html(data.data.name);
                }
                if (data.datatype == 3){
                    //奖品类型为：积分
                    //data.amount 中奖积分额度
                    console.log(3);
                    $('#result').html("恭喜您中奖啦！")
                    $('#result2').html(data.amount + '积分');
                }
                if (data.datatype == 100){
                    //奖品类型为：红包、乐券、积分的叠加类型
                    if(data.multiData.length>0){
                        for(var i=0;i<result.multiData.length;i++){
                            if(data.multiData[i].strategyType==0){
                                //奖品类型为：红包
                                $('#result').html("恭喜您中奖啦！")
                                $('#result2').html((Number(result.multiData[i].value) / 100).toFixed(2) + '<p>元</p>');
                            }
                            if(data.multiData[i].strategyType==2){
                                //奖品类型为：乐券
                                $('#result').html("恭喜您中奖啦！")
                                $('#result2').html(result.multiData[i].value);
                            }
                            if(data.multiData[i].strategyType==3){
                                //奖品类型为：积分
                                $('#result').html("恭喜您中奖啦！")
                                $('#result2').html(result.multiData[i].value + '积分');
                            }
                        }
                    }else{
                        //未中奖
                        $('#result').html("抱歉您未中奖")
                    }
                }
            }else if(data.errcode == 20){
                //未中奖
                $('#result').html("抱歉您未中奖")
            }else if(data.errcode == 2){
                //此码已被他人扫过
                $('#result').html("此码已被他人扫过")
            }else if(data.errcode == 3){
                //您已扫过此码
                $('#result').html("您已扫过此码")
            }else{
                //失败
                $('#result').html(data.errmsg)
            }
        });
    });

});

/*加载进度条*/
(function () {
    Pace.start({
        ajax: false,
        restartOnPushState: false,
        restartOnRequestAfter: false,
        document: false
    });
    Pace.on('done', function () { //加载完成
        //加载完成事件
        $('.scene').removeClass('hidden');
    });
})();
