<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>企业辅助工具 - 欢乐扫</title>
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
    body,html{width:90%;margin: 0 auto;}
    </style>
  </head>
  <body>
  <br>
<form role="form">
  <div class="form-group">
    <label for="codes">选择查询类型</label>
    <input type="radio" id="codesType_0" name="codesType" checked="checked" value="1" /> 扫码信息查询 
    <input type="radio" id="codesType_1" name="codesType" value="2" /> 码批次查询
  </div>
  <div class="form-group">
    <label for="codes">输入需要查询的code,多个用英文逗号隔开</label>
    <textarea id="codes" class="form-control" rows="3"></textarea>
  </div>


  <button type="button" class="btn btn-primary btn-block submit">确定查询</button>

  <br>

  <div id="result"></div>
</form>
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://www.lsa0.cn/static/js/jquery.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://apps.bdimg.com/libs/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>
var Init={
	init:function(){
		var _this=this;
		$('.submit').click(function(){
			var codes=$('#codes').val();
			if(codes==''){
				alert('code不能为空！');
				return false;
			}
			_this.submit(codes);
		});
    <?php if(isset($_GET['stype'])){?>
    _this.viewFromUrl();
    <?php }?>
	},
  viewFromUrl:function(){
    var stype='<?=$_GET["stype"]?>';
    var code='<?=$_GET["code"]?>';
    $('#codesType_'+stype).click();
    $('#codes').val(code);
    $('.submit').click();
	},
	submit:function(codes){
		var _this=this;
    var stype=$('input[name=codesType]:checked').val();
    if(stype==1){
      $.post('/tools/get_code_info',{codes:codes},function(res){
          var row;
          $("#result").empty();
          res.forEach(function(data) {
                row='码文本： '+ data.code+'<br>';
                if(typeof data.errmsg!='undefined'){
                    row+= ''+data.errmsg+"<br>";
                }else{
                    row+= '所属企业：' +data.mname+'(ID：'+data.mid+')'+'<br>';
                    row+= '所属活动：' +data.aname+'(ID：'+data.aid+')'+'<br>';
                    row+= '结束日期：' +data.aendTime+'<br>';
                    row+= '微信昵称：' +data.nickName+'<br>';
                    row+= 'OPENID：' +data.openid+'<br>';
                    row+= '扫码状态：' +(data.over==1?"完成":"<span style='color:red'>未完成</span>")+'<br>';
                    row+= '扫码时间：' +data.scanTime+'<br>';
                    row+= '扫码地址：' +data.scanAddress+'<br>';
                    row+= '中奖金额：' +data.amount+'<br>';
                }
                row+= '==========================================<br>';
                $("#result").append(row);
            })
      },'json')
    }
		if(stype==2){
      $.post('/tools/get_code_noscan',{codes:codes},function(res){
          var row;
          $("#result").empty();
          res.forEach(function(data) {
            row= '码文本：'+data.code+"<br>";
            if(typeof data.errmsg!='undefined'){
                row+= ''+data.errmsg+"<br>";
            }else{
                row+= '所属企业：'+data.mchName+'(ID：'+data.mchId+')'+"<br>";
                row+= '所属批次：'+data.batchNo+'(ID：'+data.batchId+')'+"<br>";
                row+= '批次状态：'+(data.batchState==0?'申请':'')+(data.batchState==1?'激活':'')+(data.batchState==2?'停用':'')+"<br>";
            }
            row+= '==========================================<br>';
                $("#result").append(row);
            })
      },'json')
    }
	}
}
$(function(){
	Init.init();
})
</script>
</body>
</html>