<!DOCTYPE html>
<html lang="zh-cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>一键查询用户封禁状态和扫码信息</title>
    <!-- Bootstrap -->
    <!-- 新 Bootstrap 核心 CSS 文件 -->
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
	<!-- 可选的Bootstrap主题文件（一般不用引入） -->
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap-theme.min.css">
  <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
  <link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
    body,html{width:1200px;margin: 0 auto;}
    </style>
  </head>
  <body>
  <br>
<form role="form">
  <div class="form-group">
    <label for="userId">输入需要查询的用户userId</label>
    <input id="userId" class="form-control"></input>
  </div>

  <button type="button" class="btn btn-primary btn-block submit">确定查询</button>
</form>
<br><br>
<!-- 用户的基本信息 -->
<div class="panel panel-default">
  <div class="panel-heading">用户基本信息</div>
  <div class="panel-body userinfo"></div>
</div>
<!-- 用户扫码记录 -->
<div class="panel panel-default">
  <div class="panel-heading">用户扫码记录</div>
  <div class="panel-body scanlist">
      <table id="userscan_data" class="table">
        <thead>
            <tr>
                <th>扫码ID</th>
                <th>商户（mchId）</th>
                <th>用户id</th>
                <th>openId</th>
                <th>扫码时间</th>
            </tr>
        </thead>
      </table>
  </div>
</div>


<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script>
var Init={
	init:function(){
		var _this=this;
		$('.submit').click(function(){
			var userId=$('#userId').val();
			if(userId==''){
				alert('userid不能为空！');
				return false;
			}
			_this.submit(userId);
		})
	},
	submit:function(userId){
		var _this=this;
    common.loading();
    $.post('/tools/get_userscan_info',{userId:userId},function(res){
        $('.userinfo').empty();
        var html;
        html='   <p class="text-center">';
        html+='    <img style="width:80px;border-radius:50%;margin-right:0 auto;" src="'+res.headimgurl+'">';
        html+='  </p>';
        html+='  <p>mchId：'+res.mchId+'</p>';
        html+='  <p>ID：'+res.userId+'</p>';
        html+='  <p>昵称：'+res.nickName+'</p>';
        html+='  <p>OPENID：'+res.openid+'</p>';
        html+='  <p>地区：'+res.province+'-'+res.city+'-'+res.country+'</p>';
        html+='  <p>状态：'+(res.commonStatus==0?'正常':res.commonStatus==1?'<b style="color:red">被封禁</b>':'无')+'</p>';
        html+='  <p>理由：'+res.logDesc+'</p>';
        html+='  <p>发生Url：'+res.logUrl+'</p>';
        $('.userinfo').html(html);

        _this.createTable(res.scanList);
    },'json')
	},
  createTable:function(data){
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     true,
            "stateSave": false,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "lengthChange": false,
            "serverSide":false,//开启服务器分页
            "deferRender": true,
            "data": data,
            "columns": [
                {"data":"scanId","class":"center"},
                {"data":"mchId","class":"center"},
                {"data":"userId","class":"center"},
                {"data":"openid","class":"center"},
                {"data":"scanTime","class":"center"},
                
            ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
            },
            "preDrawCallback": function() {
                common.loading();
            }
        };
        
        this.table=$('#userscan_data').dataTable(config); 
    }
}
$(function(){
	Init.init();
})
</script>
</body>
</html>