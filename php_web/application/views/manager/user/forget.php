<?php include VIEWPATH . '/templates/header.php' ?>
<body>
<link type="text/css" rel="stylesheet" href="/static/css/forget.css" />
<?php include VIEWPATH .'header.php';?>
<div class="main">
	<div class="h50"></div>
    <div class="forget-main">
		<div class="fmain-s1 same" style="display:none;">
			<div class="ftip">1.需要找回密码的帐号</div>
			<hr>
			<table class="form-table">
				<tr>
					<td class="name">手机号</td>
					<td class="value"><input type="text" id="phoneNum" maxlength="11" name="phoneNum"></td>
					<td class="tip">请填写您需要找回密码的手机号</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td><td class="sbt"><button class="step1">下一步</button></td></td>
				</tr>
			</table>
		</div>
		<div class="fmain-res same" style="display:none;">
			<div class="ftip">2.填写验证码</div>
			<hr>
			<table class="form-table">
				<tr>
					<td class="name">图片验证码</td>
					<td class="value valid" width="300">
						<input class="input" type="text" maxlength="4">
					</td>
					<td class="tip tipvalid">
						<img  src="/user/create_img" onclick="this.src='create_img?'+Math.random();">
					</td>
				</tr>
				<tr>
					<td class="name">短信验证码</td>
					<td class="value mesvalid"><input type="text" maxlength="6"></td>
					<td class="tip tipmesvalid"><button class="thisbutton">点击获取验证码</button></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="sbt"><button class="next2">下一步</button></td>
				</tr>
			</table>
		</div>
		<div class="fmain-suc same" style="display:none;">
			<div class="ftip">3.填写新密码</div>
			<hr>
			<table class="form-table">
				<tr>
					<td class="name">新密码</td>
					<td class="value"><input type="password" name="password" id="password" maxlength="18" ></td>
					<td class="tip">只能输入6-18个字母、数字、下划线。</td>
				</tr>
				<tr>
					<td class="name">确认密码</td>
					<td class="value"><input type="password" name="repassword" id="repassword" maxlength="18"></td>
					<td class="tip">输入确认密码</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="sbt"><button class="save">保存</button></td>
				</tr>
			</table>
		</div>
    </div>
    <div class="h30"></div>
</div>
<?php include VIEWPATH .'footer.php';?>
<script type="text/javascript" src="/static/js/user/forget.js"></script>
</body>
</html>
