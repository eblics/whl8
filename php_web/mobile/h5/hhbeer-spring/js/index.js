hlsjs.ready(function() {
    hlsjs.takeActivity(function(result) {

        var text058 = [
            "少小离家老大回，<br>乡音无改鬓毛衰。",
            "露从今夜白，<br>月是故乡明。",
            "白日放歌须纵酒，<br>青春作伴好还乡。",
            "故乡今夜思千里，<br>霜鬓明朝又一年。",
            "谓从丹霄落，<br>乃是故乡亲。",
            "回家过年，<br>是春节的终点站。",
            "过年了，<br>你妈喊你回家吃饭。",
            "团圆，<br>是春节最大的收获。",
            "每个人都是梦想家,<br>当梦走了，你只剩想家了。"
        ]
        var text018 = [
            "独在异乡为异客，<br>每逢佳节倍思亲。",
            "春风又绿江南岸，<br>明月何时照我还？",
            "海上生明月，<br>天涯共此时。",
            "仍怜故乡水，<br>万里送行舟。",
            "此夜曲中闻折柳，<br>何人不起故园情。",
            "每一个过年不回家的人<br>都有他的故事。",
            "再美的风景，<br>也不及你回家的那条路。",
            "离家的路有千万条，<br>回家的路只有一条。",
            "家乡的山，家乡的水，<br>家乡的酒，家乡的人。",
            "回家的路，<br>永远不会走错。",
            "这一生，<br>我们都走在回家的路上。",
            "春运路上太艰难，<br>就为吃上妈妈包的饺子。"
        ]

        function getText(array) {
            if(array instanceof Array){
                return array[Math.floor(Math.random() * array.length)]
            }
        }

        var html = '';
        var html0='恭喜你，中得 ?元';
        var htmlerr = '?';
        if (result.errcode == 0) {
            if (result.datatype == 0) {
                if (result.amount == 58) {
                    $(".blessing").html(getText(text058));
                }else if (result.amount == 18) {
                    $(".blessing").html(getText(text018));
                } else {
                    $(".blessing").html(getText(text018));
                }
                html = html0.replace('?', (Number(result.amount) / 100).toFixed(2) + '');
            } else if (result.datatype == 2) {
                html = '恭喜你！中得 ' + result.data.name;
                $(".blessing").html(getText(text018));
            } else {
                $(".blessing").html(getText(text018));
                alert('此H5不支持该奖品类型，请到个人中心查看奖品');
            }
            $('.result').html(html);
        } else if (result.errcode == 20) {
            $(".blessing").html(getText(text018));
            $('.result').html(htmlerr.replace('?',result.errmsg+''));
        } else if (result.errcode == 2) {
            $(".blessing").html(getText(text018));
            $('.result').html(htmlerr.replace('?','此码已被扫过'));
        }else if (result.errcode == 3) {
            $(".blessing").html(getText(text018));
            $('.result').html(htmlerr.replace('?','此码已“失身”与您'));
        }else {
            $(".blessing").html(getText(text018));
            $('.result').html(htmlerr.replace('?', result.errmsg));
        }
    });
});