<!DOCTYPE html>
<html>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="chrome=1,IE=edge" />
	<title>光明红包行动</title>
	<style>
		html {
			height:100%;
		}
		body {
			background-color:#BA1914;
			margin:0;
			height:100%;
		}
		#icon {
			background-size: 100%;
		}
	</style>
	<!-- copy these lines to your document head: -->

	<meta name="viewport" content="user-scalable=no, width=640" />
	<!-- <script type="text/javascript" src="Main/zepto.min.js"></script> -->
	<script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
	<script src="/static/lib/layer/layer.js"></script>
    <script src="/static/js/loader.js?v=1.0"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/util.js"></script>
    <script type="text/javascript" src="/static/js/hlsjs.js"></script>
	<!-- end copy -->
  </head>
  <body>
  	<script type="text/javascript">
    	var currentUser = <?=$currentUser?>;
    	Main={};
    	
    	if (typeof currentUser.username === 'undefined') {
    		Main.profile = null;
    	} else {
    		Main.profile={
	    		icon: currentUser.headimgurl,
	    		nickname: currentUser.nickname,
	    		username: currentUser.username,
	    		storename: currentUser.storename,
	    		storeaddress: currentUser.storeaddress,
	    		province: currentUser.province,
	    		city: currentUser.city,
	    		dealername: currentUser.dealername,
	    	};
    	}
    </script>
    
	<!-- copy these lines to your document: -->

	<div id="main_container" style="margin:auto;position:relative;width:640px;height:100%;overflow:hidden;">
		<script type="text/javascript" charset="utf-8" src="/h5/gm-hb/Main/main_generated_script.js?v=<?=time()?>"></script>
	</div>
	<input type="hidden" name="amount" value="<?=bcdiv($normalAmount,100,2)?>">

	<script type="text/javascript" src="/h5/gm-hb/Main/Main.js?v=<?=time()?>"></script>
  </body>
</html>
