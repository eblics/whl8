<!DOCTYPE html>
<html>
<?php 
    $openid = $data->openid;
 ?>
<head>
    <meta charset="utf-8">
    <title><?=PRODUCT_NAME. ' - '.SYSTEM_NAME?></title>
    <link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link type="text/css" rel="stylesheet" href="/static/css/user_info.css" />
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
    <script type="text/javascript" src="/static/js/validator.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript" src="/static/js/reporting_show_user_info.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_charts.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">用户信息</span>
                <!-- <span onclick="javascript:window.location.href='/charts/userscan'" class="btn_back noselect fright">返回</span> -->
            </div>
            <div class="h20"></div>
            <div class="user_info">
            <!-- top begin -->
            <div class="info_top">
            	<?php 
                    if(!empty($data->headimgurl)){ ?>
                        <img class="headimg" src="<?php echo $data->headimgurl?>">
                <?php 
                    } else{ ?>
                        <img class="headimg" src="/static/images/zanwu.png">
                <?php 
                    } ?>
                <div class="nickname">
                    <div class="send_nickname"><?php echo (empty($data->nickName) ? '用户' : $data->nickName);?></div>
                </div>
                <p class="area_sex">
                <img src="/static/images/dingwei.png">
                <?php echo (empty($data->city) ? '未知地区' : $data->country.'-'.$data->province.'-'.$data->city);?>
                &nbsp;&nbsp;
                <?php 
                    if(!empty($data->sex)){
                       echo ($data->sex == '1' ? '<img src="/static/images/nan.png"> 男' : '<img style="vertical-align:middle" src="/static/images/nv.png"> 女');
                    }else{
                       echo '<img src="/static/images/nan.png"> 未知';
                    }
                ?>
                </p>
                <?php if($data->subscribe == 1): ?>
                <div class="send_btn">发送微信消息提醒</div>
                <?php else: ?>
                <div class="send_btn_no">不能发送消息</div>
                <?php endif; ?>
                <p> </p>
            </div>
            <!-- top end -->
            <!-- basic info begin -->
            <div class="user_content">
            <p class="title">基本信息</p>
            <div class="infolist">
            <table>
	            <tr>
	            	<td class="p_left">手机号码：</td>
	            	<td class="p_right">
	            	<?php echo (empty($data->mobile) ? '未填写' : $data->mobile);?>
	            	</td>
	            </tr>
	            <tr>
	            	<td class="p_left">电子邮箱：</td>
	            	<td class="p_right">
	            	<?php echo (empty($data->email) ? '未填写' : $data->email);?>
	            	</td>
	            </tr>
	            <tr>
	            	<td class="p_left">Q Q号码：</td>
	            	<td class="p_right">
	            	<?php echo (empty($data->qq) ? '未填写' : $data->qq);?>
	            	</td>
	            </tr>
	            <tr>
	            	<td class="p_left">生&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日：</td>
	            	<td class="p_right">
	            	<?php echo (empty($data->birthday)||$data->birthday=='0000-00-00 00:00:00' ? '未填写' : date('Y年m月d日',strtotime($data->birthday)));?>
	            	</td>
	            </tr>
            </table>
            </div>
			</div>
            <!-- <div class="mes_title msame"><div class="mt1">标&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;题：</div><div class="mt2"><input type="text" value="您好，你有一条客服消息提醒"></div><div class="mt3 mtip">建议默认，不超过40个字符</div></div>
            <div class="mes_form msame"><div class="mf1">提醒来源：</div><div class="mf2"><input type="text" value="客服"></div><div class="mf3 mtip">建议默认，不超过20个字</div></div>
            <div class="mes_content msame"><div class="mc1">提醒详情：</div><div class="mc2"><textarea></textarea></div><div class="mc3 mtip">建议不超过100字</div></div>
            <div class="mes_remark msame"><div class="mr1">备注内容：</div><div class="mr2"><textarea></textarea></div><div class="mr3 mtip">建议不超过100字</div></div>
            <input type="hidden" id="youopenid" openid="<?=$openid?>">
            <div class="mess_btn">点击发送</div> -->
            <!-- basic info end -->
            <!-- business begin -->
            <div class="user_content">
            <p class="title">业务信息</p>
            <div class="infolist">
            <table>
	            <tr>
	            	<td class="p_left">红包余额：</td>
	            	<td class="p_right">
	            	（普通红包：<?php 
                        if(empty($red_pt->amount)){
                        	echo '0.00';
                        }else{
                        	echo number_format(($red_pt->amount)/100,2);
                        }
                        ?> 元&nbsp;&nbsp;裂变红包：
                        <?php 
                        if(empty($red_lb->amount)){
                        	echo '0.00';
                        }else{
                        	echo number_format(($red_lb->amount)/100,2);
                        }
                        ?> 元）
	            	</td>
	            </tr>
	            <tr>
	            	<td class="p_left" valign="top">乐&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;券：</td>
	            	<td class="p_right" style="word-wrap: break-word; ">
	            	<ul>
	            	<?php 
	            	if(empty($card_info)){?>
	            		<li>未获得任何乐券</li>
	            	
	            	<?php }else{?>
	            	
	            	
	            	<?php foreach ($card_info as $item):?>

				        <li><?php echo $item->title;?>×<?php echo $item->Anum;?>&nbsp;&nbsp;&nbsp;</li>
				
				    <?php endforeach;?>
				    <?php }?>
	            	</ul>
	            	</td>
	            </tr>
                <tr>
                    <td class="p_left">积&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;分：</td>
                    <td class="p_right">
                    总获取积分：
                    <span style="color: #36c">
                    <?php 
                        if (! isset($user_point_total_get)) {
                            echo '0';
                        } else {
                            echo $user_point_total_get;
                        }
                    ?> （<a style="color: #36c" id="btn_show_point_get_log" href="javascript:;">查看获取记录</a>） 
                    </span>
                    | &nbsp;已使用积分：
                    <span style="color: #36c">
                    <?php 
                        if (! isset($used_amount)) {
                            echo '0';
                        } else {
                            echo $used_amount;
                        }
                    ?> （<a style="color: #36c" id="btn_show_point_trans_log" href="javascript:;">查看使用记录</a>） 
                    </span>
                    | &nbsp;未使用积分：
                    <?php 
                        echo $user_point_total_get - $used_amount;
                    ?>
                    </td>
                </tr>

                <script type="text/javascript">
                function showPopup(dataList) {
                    var content = '';
                        content += '<div class="box">';
                        content += '    <table id="point_trans_log_table">';
                        content += '        <thead><tr>';
                        content += '            <th>时间</th><th>积分数量</th><th>兑换类型</th>';
                        content += '        </tr></thead>';
                        content += '    </table>';
                        content += '<div>';

                    common.transDialog(function(callback){
                        callback(content);
                    });

                    var table = $('#point_trans_log_table').DataTable({
                        "info": false,
                        "paging": true,
                        "searching": false,
                        "ordering":  false,
                        "language": {"url": "/static/datatables/js/dataTables.language.js"},
                        "columns": [
                            {"class": "center", "width": "33%", "name": "时间", "data": function(row, type, set, meta) {
                                return row.actionTime;
                            }},
                            {"class": "center", "width": "33%", "name": "积分数量", "data": function(row, type, set, meta) {
                                return row.amount;
                            }},
                            {"class": "center", "width": "33%", "name": "兑换类型", "data": function(row, type, set, meta) {
                                var extra = '';
                                if (row.wxStatus === '2') {
                                    extra = '(失败，积分已退回账户)'
                                }
                                if (row.wxStatus === '0' || row.wxStatus === '3') {
                                    extra = '(处理中)';
                                }
                                return row.title + extra;
                            }},
                        ]
                    });
                    table.clear();
                    table.rows.add(dataList);
                    table.draw();


                }

                // 点击查看用户积分使用记录
                $('#btn_show_point_trans_log').click(function() {
                    common.loading();
                    $.get('/point/fetch_point_used_logs', {user_id: <?=$data->id?>}, function(resp) {
                        common.unloading();
                        if (resp.errcode === 0) {
                            showPopup(resp.data);
                        } else {
                            common.alert(resp.errmsg + '！');
                        }
                    }).fail(function() {
                        common.unloading();
                        common.alert('无法连接服务器！');
                    });
                    
                });

                // 点击查看用户积分获取记录
                $('#btn_show_point_get_log').click(function() {
                    common.loading();
                    $.get('/point/fetch_point_get_logs', {user_id: <?=$data->id?>}, function(resp) {
                        common.unloading();
                        if (resp.errcode === 0) {
                            showPopup(resp.data);
                        } else {
                            common.alert(resp.errmsg + '！');
                        }
                    }).fail(function() {
                        common.unloading();
                        common.alert('无法连接服务器！');
                    });
                    
                });
                </script>
	            <tr>
	            	<td class="p_left">关注时间：</td>
	            	<td class="p_right">
	            	<?php echo (empty($data->subscribe_time) ? '未知' : date('Y-m-d H:i:s',$data->subscribe_time));?>
	            	</td>
	            </tr>
            </table>
            </div>
			</div>
            <!-- business end -->
            <!-- other begin -->
            <div class="user_content">
            <p class="title">其他信息</p>
            <div class="infolist">
            <table>
	            <tr>
	            	<td class="p_left">来自红码：</td>
	            	<td class="p_right">
	            	<?php 
                        if(!empty($data->fromHLS)){
                        	echo ($data->fromHLS == '1' ? '是' : '否');
                        }else{
                        	echo '未知';
                        }
                     ?>
	            	</td>
	            </tr>
	            <tr>
	            	<td class="p_left">语&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;言：</td>
	            	<td class="p_right">
	            	<?php 
                        if(!empty($data->language)){
                        	echo ($data->language == "zh_CN" ? '中文' : '外语');
                        }else{
                        	echo '未知';
                        }
                    ?>
	            	</td>
	            </tr>
            </table>
            </div>
			</div>
            <!-- other end -->
			</div>
            <input type="hidden" id="youopenid" openid="<?=$openid?>">
        </div>
    </div>
    <?php include 'footer.php';?>
</body>

</html>
