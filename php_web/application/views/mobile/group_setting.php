<?php
$memberHtml='';
$html='<div class="member"><img src="%s"/><span>%s</span></div>';
foreach($data->allMembers as $k=>$v){
	$memberHtml.=sprintf($html,$v->headImage,$v->nickName);
}
?>
<!DOCTYPE html>
<html id="page_setting">
<head>
	<title></title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<meta name="format-detection" content="telephone=no, address=no">
	<link type="text/css" rel="stylesheet" href="/min/?f=static/css/weui.css,static/css/group_common.css,static/css/group.css" />
</head>
<body>
	<div id="page_group_setting" class="wraper noselect group_setting">
		<dl class="setting_list">
			<dt><strong>群名称</strong><span id="groupName"><em><?=$data->groupName?></em>></span></dt>
			<?php if($data->currentUser->id==$data->currentMaster->userId):?>
			<dt class="bt"><strong>群口令</strong><span id="password"><em><?=mb_strlen($data->password,'UTF8')>13?mb_substr($data->password,0,12,'UTF8').'..':$data->password?></em>></span></dt>
			<?php endif;?>
			<dt class="bt"><strong>我的昵称</strong><span id="nickName"><em><?=$data->currentMember->nickName?></em>></span></dt>
			<dt class="bt"><strong>我的头像</strong><span id="headImage"><em><?=$data->currentMember->headImage!=''?'修改':'未设置'?></em>></span></dt>
		</dl>
		<dl class="setting_list noselect">
			<dt class="bb"><strong>群成员（<?=$data->memberNum?>）</strong></dt>
			<dd class="member_box">
				<?=$memberHtml?>
			</dd>
		</dl>
		<div class="btn"><span id="btnQuit" class="weui_btn weui_btn_warn"><?=$data->currentMember->role==1?'解散本群':'退出本群'?></span></div>
	</div>
</body>
</html>
<script type="text/javascript">
var currentGroup={
	'id':'<?=$data->id?>',
	'masterId':'<?=$data->currentMaster->userId?>',
	'mchId':'<?=$data->mchId?>'
};
var currentUser={
	'id':'<?=$data->currentUser->id?>'
};
</script>
<script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/group_common.js"></script>
<script type="text/javascript" src="/static/js/group_setting.js"></script>