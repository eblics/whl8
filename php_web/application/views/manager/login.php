<?php include VIEWPATH . '/templates/header.php' ?>
<body class="login">
	<link type="text/css" rel="stylesheet" href="/static/css/login.css">
	<div class="dl dl_bac">
		<div class="title"><div style="height:120px"></div></div>
		<div class="main">
			<ul class="left_main"></ul>
			  <ul class="content">
			  	<li>
			  		<span class="l_title">用户登录</span>
			  		<span class="r_title">
			  			<a href="/user/reg">注册</a>
			  		</span>
			  	</li>
			  	<li style="height:25px;margin-top: -15px;"><span id="errmsg" class=""></span></li>
				<li>
					<div class="login_two input">
						<input type="text" id="account" maxlength=11 placeholder="手机号" name="username"><span id="account_img"></span>
					</div>
				</li>
				<li>
					<div class="login_three input">
						<input type="password" id="password" placeholder="密码" name="password"><span id="password_img"></span>
					</div>
				</li>
				<li> 
					<div class="login_check input">
						<div class="lc_left">
							<input type="text" maxlength=6 id="verify" placeholder="验证码">
						</div>
						<span id="verify_img"></span>
						<div class="lc_right">
							<input type="button" id="getCode" value="获取验证码" />
						</div>
					</div>
				</li>

				<!-- Added by shizq - begin -->
				<li class="merchants-selector" style="display: none;"> 
					<div class="input">
						<select id="mch_id" name="mchId" class="input-select">
							<option value="">--请选择要登录的商户--</option>
						</select>
					</div>
				</li>
				<!-- Added by shizq - end -->

				<li>
					<div class="login_four">
						<div class="lsix_left">
							<input id="ison" checked="true" type="checkbox">&nbsp;&nbsp;记住账号&nbsp;&nbsp;<span id="errmsg"></span>
						</div>
					</div>
					<div class="lsix_right"><a href="/user/forget">忘记密码?</a></div>
				</li>
				<li>
					<div class="login_five" id="ishover">
						<input id="submit_btn" type="button" value="登录">
					</div>
				</li>
			</ul>
		</div>
	</div>

	<script type="text/javascript" src="/static/js/login.js?v=1.0"></script>
</body>
</html>
