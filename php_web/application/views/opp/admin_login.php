<?php include 'common/header.php' ?>
<link type="text/css" rel="stylesheet" href="/static/css/login.css" />
</head>
<body><?php include 'common/menus.php';?>
<div class="main">
    <div class="login_main">
		<div class="login_one">账号登录</div>
		<div class="login_two input">
		<input type="text" id="account" placeholder="登录帐号" name="username" 
		valType="NICKNAME" msg="<font color=red>*</font>账号不能为空或不正确" />
		</div>
		<div class="login_check input">
			<div class="lc_left">
			<input type="text" id="verifica" maxlength="4" placeholder="图片验证码" 
			msg="<font color=red>*</font>请填写图片验证码" />
			</div>
			<div class="lc_right validpic" title="点击刷新验证码">
				<img id="verify_img" src="/login/verify_img" />
			</div>
			<span id="coderrmsg"></span>
		</div>
		<div class="login_three input">
			<input type="password" id="password" placeholder="密码" name="password" 
			valType="PASS" msg="<font color=red>*</font>密码格式错误" />
		</div>
		<div class="login_four">
			<input id="ison" type="checkbox" checked="checked" />
			&nbsp;&nbsp;<label for="ison">记住账户</label>&nbsp;&nbsp;
			<span id="errmsg"></span>
		</div>
		<div class="login_five" id="ishover">
			<input type="submit" value="登录"></div>
    </div>
    <div class="h30"></div>
</div>
<script type="text/javascript" src="/static/js/admin/login.js?v=<?=time()?>"></script>
<?php include 'common/footer.php';?>