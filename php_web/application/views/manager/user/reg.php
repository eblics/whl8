<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/register.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/register.js"></script>
<style>
/*分步样式开始*/
.reg_step{width:100%;height:80px;}
.reg_s{
	width:100%;
	margin-bottom:0;
}
.reg_s .rs{
	width:110px;
	height:8px;
	float:left;
	background:#EBEBEB;
}
.reg_s .rs .title{
	position: relative;
    top: -50px;
    width:110px;
    height:40px;
    line-height:40px;
    font-size:16px;
    text-align:center;
    color:#E3E3E3;
}


.circle{
	width:16px;
	height:16px;
	border:2px solid #EBEBEB;
	border-radius:10px;
	margin:-46px 0 0 30px;
	background: #fff;
}

.login_dealer input{
	width: 325px;
	height: 40px;
	padding-left: 5px;
	margin-left: 0;
	border: 1px solid #d7d7d7;
	line-height: 40px;
}

.rs_active{background: #3367D6 !important;}
.circle_active{background:#3367D6 !important;border:2px solid #3367D6 !important;}
.title_active{color:#3367D6 !important;}

.step{display:none;}
.step_active{display: block}
/*分布样式结束*/
</style>
</head>
<body class="login">
	<div class="dl">
		<div class="title"><div style="height:120px"></div></div>
		<div class="main">
			<ul class="left_main"></ul>
			  <ul class="content">
			  	<li class="li_1">
			  		<span class="l_title">用户注册</span>
			  		<span class="r_title">
			  			<a href="/user/login">登录</a>
			  		</span>
			  	</li>
			  	<!-- 分步注册进度条 -->
			  	<li class="li_2">
			  		<div class="reg_s">
			  			<div class="rs rs1 rs_active">
			  				<div class="title title_active">注册账号</div>
			  				<div class="circle circle1 circle_active"></div>
			  			</div>
			  			<div class="rs rs2">
			  				<div class="title">确认密码</div>
			  				<div class="circle circle2"></div>
			  			</div>
			  			<div class="rs rs3">
			  				<div class="title">完成</div>
			  				<div class="circle circle3"></div>
			  			</div>
			  		</div>
			  	</li>
			  	<!-- 分步注册 第一步 -->
			  	<div class="step step1 step_active" data-step="1">
					<li>
						<div class="login_two input">
							<input type="text" id="account" maxlength=11 placeholder="手机号" name="account"><span id="account_img"></span>
						</div>
					</li>
					<li> 
						<div class="login_check input">
							<div class="lc_left">
								<input type="text" maxlength=4 id="verifyca" name="verifyca" placeholder="图片验证码">
							</div>
							<span id="verifyca_img"></span>
							<div class="lc_right">
								<img  src="/user/create_img" onclick="this.src='create_img?'+Math.random();">
							</div>
						</div>
					</li>
					<li> 
						<div class="login_check input">
							<div class="lc_left">
								<input type="text" maxlength=6 id="verify" name="verify" placeholder="验证码">
							</div>
							<span id="verify_img"></span>
							<div class="lc_right">
								<input type="button" id="getCode" value="免费获取验证码" />
							</div>
						</div>
					</li>
				</div>
				<!-- 分步注册 第二步 -->
				<div class="step step2" data-step="2">
					<li>
						<div class="login_three input">
							<input type="password" id="password" placeholder="密码" name="password"><span id="password_img"></span>
						</div>
					</li>
					<li>
						<div class="login_three input">
							<input type="password" id="repassword" placeholder="重复密码" name="repassword"><span id="repassword_img"></span>
						</div>
					</li>
				</div>
				<!-- 分步注册 第三步 -->
				<div class="step step3" data-step="3">
					<li>
						<div class="login_dealer input">
							<input type="text" name="dealer" placeholder="经销商代码 没有请留空">
						</div>
					</li>
					<li class="login_special">
						<div class="login_service input">
							<label>
							<input name="agree" type="checkbox" checked="checked" value="" />
							<a href="/static/doc/termsofservice.pdf" target="_blank">
								同意<span style="color:#4a85e0">《欢乐扫平台服务条款》</span>
							</a></label>
						</div>
					</li>
				</div>
				<li class="li_6">
					<div class="login_five" id="ishover">
						<input id="submit_btn" disabled style="background-color: #ccc" type="button" value="下一步">
					</div>
				</li>
			</ul>
		</div>
	</div>
</body>
</html>
