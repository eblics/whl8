<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no" />
    <link type="text/css" rel="stylesheet" href="/min/?f=static/css/weui.css,static/css/group_common.css,static/css/group.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/lifted.css"/>
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/iscroll-zoom.js,static/js/hammer.js,static/js/jquery.photoClip.js,static/js/group_common.js"></script>
    <script type="text/javascript" src="/static/js/lifted.js"></script>
    <title><?=$title?></title>
</head>
<body>
    <div class="lifted-content">
		<?php 
		if($commonStatus == 0 && $status == 1): 
		?>
		<p style="margin-top:30px;width:100%;text-align:center;color:red;font-size:14px;">您的解封申请已经提交！</p>
		<?php  
		elseif($commonStatus == 0 && $status == 0):
		?>
		<p style="margin-top:30px;width:100%;text-align:center;color:green;font-size:14px;">您的帐号正常，无需解封。</p>
		<!-- <input type="hidden" id="status" value="0">
        <div class="lifted-rea">申请理由</div>
		<div class="lifted-read">
			<textarea placeholder="请填写你的申诉理由 注意要填写你扫码的产品名称" id="lreason"></textarea>
		</div>
		<div class="lifted-name">姓名</div>
		<div class="lifted-named">
			<input type="text" name="lname" maxlength="30" id="lname" placeholder="你的名字" minlength="1" value="">
		</div>

		<div class="lifted-num">手机号码</div>
		<div class="lifted-numd">
			<input type="text" name="lphonenum" id="lphonenum" placeholder="你的手机号码 方便工作人员核对联系" value="" maxlength="11" minlength="11">
		</div>
		<div class="lifted-qr">上传图片</div>
		<div class="tip">请上传拍摄的清晰的商品包装内的二维码，作为审核依据</div>
		<div class="lifted-qrimg img" id="lifted-qrimg">
			上传图片<p id="view"><img src=""></p><input type="file" id="file" />
		</div>
		<div class="img_cut">
			<div id="clipArea"></div>
			<div id="clipBtn" class="weui_btn weui_btn_primary">保存</div>
			<div id="cancelBtn" class="weui_btn weui_btn_default">取消</div>
		</div>
		<div class="lsubmit lifted-sub">提交申请</div> -->
		<?php  
		else:
		?>
    	<input type="hidden" id="status" value="<?=$status?>">
        <div class="lifted-rea">申请理由</div>
		<div class="lifted-read">
			<textarea placeholder="请填写你的申诉理由 注意要填写你扫码的产品名称" id="lreason"><?=$data->lreason?></textarea>
		</div>
		<div class="lifted-name">姓名</div>
		<div class="lifted-named">
			<input type="text" name="lname" maxlength="30" id="lname" placeholder="你的名字" minlength="1" value="<?=$data->lname?>">
		</div>
		<div class="lifted-num">手机号码</div>
		<div class="lifted-numd">
			<input type="text" name="lphonenum" id="lphonenum" placeholder="你的手机号码 方便工作人员核对联系" value="<?=$data->lphonenum?>" maxlength="11" minlength="11">
		</div>
		<div class="lifted-qr">上传图片</div>
		<div class="tip">请上传拍摄的清晰的商品包装内的二维码，作为审核依据</div>
		<div class="lifted-qrimg img" id="lifted-qrimg">
			上传图片<p id="view"><img src="<?=$data->img?>"></p><input type="file" id="file" />
		</div>
		<div class="img_cut">
			<div id="clipArea"></div>
			<div id="clipBtn" class="weui_btn weui_btn_primary">保存</div>
			<div id="cancelBtn" class="weui_btn weui_btn_default">取消</div>
		</div>
		<div class="lsubmit lifted-sub">提交申请</div>
		<?php 
		endif; 
		?>
    </div>
</body>
</html>