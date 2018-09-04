<?php
function echoHtml($lists){
	$listHtml='';
	$html='<dd data-id="%s" class="status_%s">
		<div class="txt">
			<h3>彩头：<span>%s</span></h3>
			<h3>额度：<span>%s</span></h3>
		</div>
		<div class="status">%s</div>
	</dd>';
	foreach($lists as $k=>$v){
		$pktype='';
		$timestr='';
		if($v->pkType==0) $pktype='红包';
		if($v->pkType==1) $pktype='积分';
		if($v->pkType==2) $pktype='乐券';
		if($v->endTime>=time()){
			$time=$v->endTime-time();
			if($time<3600){
				$timestr=bcdiv($time,60,0) . '分钟后结束';
			}else if($time>=3600 && $time<3600*24){
				$timestr=ceil($time/3600) . '小时后结束';
			}else if($time>=3600*24){
				$timestr=ceil($time/(3600*24)) . '天后结束';
			}
		}else if($v->status==2){
			$timestr='已结束';
		}else if($v->status==1){
			$timestr='结算中';
		}else{
			$timestr='0分钟后结束';
		}
		$listHtml.=sprintf($html,$v->id,$v->status,$pktype,$v->pkAmount,$timestr);
	}
	return $listHtml;
}
$listHtml=echoHtml($lists);
$myListHtml=echoHtml($mylists);
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<meta name="format-detection" content="telephone=no, address=no">
	<link type="text/css" rel="stylesheet" href="/min/?f=static/css/weui.css,static/css/font/iconfont.css,static/css/group_common.css,static/css/group_scanpk.css<?='&t='.time()?>" />
</head>
<body class="noselect">
	<div class="wraper noselect app_scanpk_list">
		<h2 class="title">扫码PK <span id="btnRule">( 规则说明 )</span></h2>
		<div class="rule" id="rule">扫码PK游戏的规则：统计所有参与PK的玩家，在规定PK时间段内的扫码量作为排名，扫码量最高者（并列第一平分）赢得所有人的彩头。若当局所有参与者扫码量都为0，则PK发起者赢得所有人的彩头。</div>
		<dl class="list" id="notMyList">
			<dt>PK活动</dt>
			<?=$listHtml?>
		</dl>
		<?php if($myListHtml!=''){ ?>
		<dl class="list mypk" id="myList">
			<dt>我的PK</dt>
			<?=$myListHtml?>
		</dl>
		<?php } ?>
		<div class="btn"><span id="btnAddPK" class="weui_btn weui_btn_primary">发起PK</span></div>
	</div>
	<div class="wraper noselect app_scanpk_add">
		<h2 class="title">发起扫码PK</h2>
		<input type="hidden" id="groupId" value="<?=$groupId?>"/>
		<dl class="list">
			<dt>彩头：</dt>
			<dd id="input_data_type_area">
				<label for="input_data_type_0"><input type="radio" checked="checked" id="input_data_type_0" name="input_data_type" value='0' /> 红包</lebal>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label for="input_data_type_1"><input type="radio" id="input_data_type_1" name="input_data_type" value='1' /> 积分</lebal>
				<!--<label for="input_data_type_2"><input type="radio" id="input_data_type_2" name="input_data_type" class="input"/> 红包</lebal>-->
			</dd>
			<dt>额度：</dt>
			<dd><input type="text" id="input_data_amount" class="input"/> <span id="unit">元</span></dd>
			<dt>时长：</dt>
			<dd id="input_data_end">
				<span data="0.2">12分钟</span><span data=1>1小时</span><span data=2>2小时</span><span data=4>4小时</span><span data=8>8小时</span>
				<span data=24 class="cur">1天</span><span data=48>2天</span><span data=168>7天</span><span data=720>30天</span>
			</dd>
		</dl>
		<div class="btn"><span id="btnPub" class="weui_btn weui_btn_primary">保存</span>
		<span id="btnToList" class="weui_btn weui_btn_default">取消</span></div>
	</div>
	<div class="wraper noselect app_scanpk_detail">
		<h2 class="title">扫码PK详情</h2>
		<dl class="list">
			<dt>彩头：</dt>
			<dd id="data_type"></dd>
			<dt>额度：</dt>
			<dd id="data_amount"></dd>
			<dt>PK发起人：</dt>
			<dd id="data_master"></dd>
			<dt>开始时间：</dt>
			<dd id="data_start"></dd>
			<dt>结束时间：</dt>
			<dd id="data_end"></dd>
		</dl>
		<fieldset>
			<legend>PK成员 (<span id="data_num"></span>)</legend>
			<div id="data_user"></div>
		</fieldset>
		<div class="btn"><span id="btnJoin" data="" class="weui_btn weui_btn_primary">加入PK</span>
		<span id="btnBackList" class="weui_btn weui_btn_default">返回</span></div>
		<div class="icon_status"></div>
	</div>
</body>
</html>
<script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/group_common.js"></script>
<script type="text/javascript" src="/static/js/group_scanpk_lists.js?<?=time()?>"></script>