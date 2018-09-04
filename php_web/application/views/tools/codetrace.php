<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>企业辅助工具 - 码轨迹追踪</title>
    <!-- Bootstrap -->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.css">
    <!-- 可选的Bootstrap主题文件（一般不用引入） -->
    <link rel="stylesheet" href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
    body,html{width:90%;margin: 0 auto;font-size:12px;}
    ul li{list-style: none;}
    .track-rcol{width: 100%; border: 1px solid #eee;}
    .track-list{margin: 20px; padding-left: 5px; position: relative;}
    .track-list li{position: relative; padding: 9px 0 0 25px; line-height: 18px; border-left: 1px solid #d9d9d9; color: #999;}
    .track-list li .node-icon{
      position: absolute;
      left: -5px;
      top: 40%;
      width: 8px;
      height: 8px;
      display: inline-block;
      font-size: 10px;
      line-heigth: 16px;
      text-align: center;
      text-decoration: none;
      color:#999;
    }
    .track-list li .time{margin-right: 20px; position: relative; top: 4px; display: inline-block; vertical-align: middle;}
    .track-list li .txt{max-width: 600px; position: relative; top: 4px; display: inline-block; vertical-align: middle;}
    #result{-webkit-padding-start: 0;}
    </style>
  </head>
  <body>
  <br>
<form role="form">
  <div class="form-group">
    <label for="codes">选择查询类型</label><br>
    <input type="radio" name="codesType" checked="checked" value="1" onclick="Init.to_change();"/> 码轨迹追踪 
    <input type="radio" name="codesType" value="2" onclick="Init.to_change();"/> 用户扫码(openid)轨迹
    <input type="radio" name="codesType" value="3" onclick="Init.to_change();"/> 用户扫码(useId)轨迹
  </div>
  <div class="form-group">
    <label for="term">输入需要查询的条件（码请输入码文本，用户请输入openid/useId）</label>
    <textarea id="term" class="form-control" rows="1"></textarea>
  </div>


  <button type="button" class="btn btn-primary btn-block submit">确定查询</button>

  <br>

<div class="track-rcol">
<div class="track-list">
  <ul id="result"></ul>
</div>
</div>



</form>
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://www.lsa0.cn/static/js/jquery.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://apps.bdimg.com/libs/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>
var Init={
    init:function(){
        var _this=this;
        _this.to_change();
        $('.submit').click(function(){
            var term=$('#term').val();
            if(term==''){
                alert('code或openid不能为空！');
                return false;
            }
            _this.submit(term);
        });
    },
    submit:function(term){
        var value  = $('input[name="codesType"]:checked').val();
        $("#result").html('<div style="text-align:center">查询中...</div>');
        $.post('/tools/get_trace',{term:term,type:value},function(res){
              if(res.errcode==-1){
                  $("#result").html('<div style="text-align:center">'+res.errmsg+'</div>');
                  return false;
              }
              var row;
              $("#result").empty();
              if(res.length==0){
                  $("#result").html('<div style="text-align:center">无相关数据</div>');
                  return false;
              }
              res.forEach(function(data) {
                  var row='';
                    if(typeof data.errmsg!='undefined'){
                        row+= ''+data.errmsg+"<br>";
                    }else{
                      row+='<li>';
                      row+='  <i class="node-icon">●</i>';
                      row+='  <span class="time">'+data.theDate+'</span>';
                      if(value==1){
                          row+='  <span class="txt">'+data.nickname+'（'+data.userId+') '+data.logDesc+'</span>';
                      }
                      if(value==2||value==3){
                          row+='  <span class="txt">'+data.nickname+'（'+data.userId+') 扫了乐码 <span style=color:red>'+data.code+'</span> （'+data.logDesc+'）</span>';
                      }
                      
                      row+='</li>';
                    }
                    $("#result").append(row);
                })
        },'json')
    },
    to_change:function(){
      var value  = $('input[name="codesType"]:checked').val();
      if(value==1){
        $("#term").attr('placeholder','请输入要查询的码文本');
      }
      if(value==2){
        $("#term").attr('placeholder','请输入要查询的用户openid');
      }
      if(value==3){
        $("#term").attr('placeholder','请输入要查询的用户userId');
      }
    }
}
$(function(){
    Init.init();
})
</script>
</body>
</html>