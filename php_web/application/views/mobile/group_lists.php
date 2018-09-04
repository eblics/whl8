<?php
$myAdd=0;
$myJoin=0;
$recommendNum=0;
$myAddHtml='';
$myJoinHtml='';
$recommendHtml='';
$html='<dd data-id="%s">
		<div class="img"><img src="%s" /></div>
		<div class="txt">%s</div>
</dd>';
foreach($groups as $k=>$v){
	if($v->role==1){
		$myAdd++;
		$myAddHtml.=sprintf($html,$v->id,$v->groupImg,$v->groupName);
	}else{
		$myJoin++;
		$myJoinHtml.=sprintf($html,$v->id,$v->groupImg,$v->groupName);
	}
}
foreach($recommend as $k=>$v){
	$recommendNum++;
	$recommendHtml.=sprintf($html,$v->id,$v->groupImg,$v->groupName);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<meta name="format-detection" content="telephone=no, address=no">
	<link type="text/css" rel="stylesheet" href="/min/?f=static/css/weui.css,static/css/group_common.css,static/css/group.css" />
</head>
<body class="noselect">
	<div id="page_group_list" class="wraper noselect">
		<div class="weui_search_bar" id="search_bar">
			<div class="weui_search_outer">
				<div class="weui_search_inner">
					<i class="weui_icon_search"></i>
					<input type="text" class="weui_search_input" id="search_input" placeholder="搜索"/>
					<a href="javascript:" class="weui_icon_clear" id="search_clear"></a>
				</div>
				<label for="search_input" class="weui_search_text" id="search_text">
					<i class="weui_icon_search"></i>
					<span>搜索</span>
				</label>
			</div>
			<a href="javascript:" class="weui_search_cancel" id="search_cancel">取消</a>
		</div>
		<div class="weui_cells weui_cells_access search_show" id="search_show"></div>
		<?php if($recommendNum>0){?>
		<dl class="group_list">
			<dt>推荐的群（<?=$recommendNum?>）</dt>
			<?=$recommendHtml?>
		</dl>
		<?php }?>
		<dl class="group_list">
			<dt>我创建的群（<?=$myAdd?>）</dt>
			<?=$myAddHtml?>
		</dl>
		<dl class="group_list myjoin">
			<dt>我加入的群（<?=$myJoin?>）</dt>
			<?=$myJoinHtml?>
		</dl>
	</div>
	<ul id="nav" class="nav_bar">
		<li id="create_group">创建新群<em></em></li>
		<li id="join_group">口令加群</li>
	</ul>
</body>
</html>
<script type="text/javascript">
localStorage.setItem("groupProductName","<?=$groupProductName?>");
</script>
<script type="text/javascript" src="/min/?f=static/js/jquery-2.1.1.min.js,static/js/group_common.js,static/js/group_lists.js"></script>