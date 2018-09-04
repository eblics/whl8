<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/orders.css?880" />
<!-- <link rel="stylesheet" href="/static/msgbox/msgbox.css" /> -->
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/order.js"></script>
<script type="text/javascript" src="/static/js/pagging.js"></script>
<style type="text/css">
    .content .main-tip{
        width:90%;
        height:300px;
        background:#F9F9F9;
        border-radius:5px;
        border:1px solid #CECBCB;
        margin:50px auto 0;
    }
    .content .tip-title{
        font-size:20px;
        font-weight:bold;
        text-align:center;
        line-height:30px;
        width:100%;
        height:30px;
        margin-top:100px;
    }
    .content .tip-btn{
        width:120px;
        height:30px;
        border-radius:3px;
        background:#5A8EDD;
        text-align:center;
        line-height:30px;
        font-size:14px;
        color:white;
        margin:0 auto;
        margin-top:50px;
    }
    .content .tip-btn:hover{
        background:#5583CB;
        cursor:pointer;
    }
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">订单处理</span>
        </div>
        <!-- <div class="h30"></div> -->
        <?php if($sdata['isopen'] == false): ?>
        <div class="content">
            <div class="main-tip">
                <div class="tip-title">贵企业还未开通商城，该块功能暂时不能使用，请在开通商城之后再使用！</div>
                <div class="tip-btn">点击去开通商城</div>
            </div>
        </div>
        <?php else: ?>
        <div class="orders_body">
            <div class="orders" style="border:none">
                <div class="statusbar">
                    <div id="bar_page" class="bar_page">
                        <label>
                        每页显示
                            <select class="bar_select">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        条
                        </label>
                    </div>
                    <div id="bar_search">
                        <label>
                        搜索订单号：
                        <input class="" type="search" placeholder="请输入订单号进行搜索" aria-controls="batchTable">
                        </label>
                        <div id="onSearch" class="btn-search">查询</div>
                    </div>
                </div>
                <div class="tool tooltimedate">
                    <p class="ltitle">筛选时间</p>
                    <div class="rtitle">
                        <ul>
                            <li><a href="javascript:;" data-value="0" class="time active">全部</a></li>
                            <li><a href="javascript:;" data-value="1" class="time">今日</a></li>
                            <li><a href="javascript:;" data-value="2" class="time">昨天</a></li>
                            <li><a href="javascript:;" data-value="3" class="time">最近7天</a></li>
                            <li>
                                <span><a href="javascript:;" data-value="4" class="timea">自选日期</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="text" id="b_time" value="" class="Wdate timer" onfocus="WdatePicker({skin:'twoer'})"/>
                                -
                                    <input type="text" id="e_time" value="" class="Wdate timer" onfocus="WdatePicker({skin:'twoer'})">
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tool toolstatus">
                    <p class="ltitle">筛选状态</p>
                    <div class="rtitle cover">
                        <ul>
                            <li><a href="javascript:;" data-value="0" class="status">全部</a></li>
                            <!-- <li><a href="javascript:;" data-value="1" class="status">未付款</a></li> -->
                            <li><a href="javascript:;" data-value="2" class="status active">未发货</a></li>
                            <li><a href="javascript:;" data-value="3" class="status">已发货</a></li>
                            <li><a href="javascript:;" data-value="4" class="status">已收货</a></li>
                            <li><a href="javascript:;" data-value="5" class="status">已完成</a></li>
                            <li>
                                <div id="getSearch" class="btn btn-blue">查询</div>
                            </li>
                        </ul>
                    </div>
                    <div id="filter-fold" class="">
                        <label>
                        折叠状态
                        <select id="ff-select">
                            <option index="0" value="0">折叠</option>
                            <option index="1" value="1">展开</option>
                        </select>
                        </label>
                    </div>
                </div>
                <input type="hidden" id="status" value="<?=$btn_data['status']?>">
                <input type="hidden" id="timedate" value="<?=$btn_data['timedate']?>">
                
            <!-- 条件筛选结束 -->
            <div class="h30" style="clear:both"></div>
            <hr style="height:1px;border:none;border-top:1px dashed #ccc;margin:0px">
            <div class="h30" style="clear:both"></div>
            <!-- 表格开始 -->
            <!--
                时间 订单号 状态
                图片 名称 数量 

            -->
            <div class="total_form">
                <?php if(count($data) == 0):?>
                <div class="order_form nullform">
                    <div class="result">查询结果为空</div>
                </div>
                <?php elseif(count($data)>0):?>
                <?php if(isset($search)): ?>
                    <div class="order_form searchform">
                        <div class="result">↓↓↓↓以下是查询结果↓↓↓↓</div>
                        <div class="delform" onclick="alertLocal()">返回</div>
                    </div>
                <?php endif; ?>
                <?php foreach($data as $key => $value): ?>
                <div class="order_form">
                    <div class="order_num">
                        <div class="order1-fold">
                            <div class="fold-div short" title="点击切换折叠状态">点击展开</div>
                        </div>
                        <div class="order1_time">订单生成时间：<?=$value[0]['utime']?></div>
                        <div class="order1_order">订单号：<?=$value[0]['ordernum']?></div>
                        <?php 
                            if($value[0]['amount']!=null):
                         ?>
                        <div class="order1_amount">合计：<?=$value[0]['amount']?>积分</div>
                        <?php endif; ?>
                        <div class="order1_status">状态:&nbsp;&nbsp;<font size="2" color="green"><?php
                            switch ($value[0]['estatus'])
                            {
                                case 0:
                                    if($value[0]['paystatus'] == 0){
                                        echo "未付款";
                                    }
                                    if($value[0]['paystatus'] == 1){
                                        echo "未发货";
                                    }
                                    break;
                                case 1:
                                  echo "已发货";
                                  break;
                                case 2:
                                    if($value[0]['ostatus'] == 1){
                                        echo "已完成";
                                    }else{
                                        echo "已收货";
                                    }
                                  
                                  break;
                                default:
                                  echo "状态异常";
                            }

                        ?></font></div>
                    </div>
                    <?php foreach($value as $k=>$v):?>
                    <div class="order_good nhide">
                        <div class="good_img"><img src="<?=$v['path']?>"></div>
                        <div class="good_detail">
                            品名：<span class="good_1"><?=$v['gname']?></span><br/>
                            数量：<span class="good_2"><?=$v['gnumber']?></span><br/>
                            <?php if($v['gcname'] !=null): ?>
                                付款：<span class="good_3"><?=$v['gcname']?></span>
                            <?php else: ?>
                                付款：<span class="good_3"><?=$v['gprice']*$v['gnumber']?></span>积分
                            <?php endif; ?>
                        </div>
                        <div class="good_express">
                        <?php if (isset($v['address'])):?>
                            收件人：<span class="exp_1">
                            <?php
                                $str = explode("|",$v['address']);
                                echo $str[0]; ?>
                            </span><br/>
                            联系电话：<span class="exp_2">
                            <?php
                                $str = explode("|",$v['address']);
                                echo $str[1]; ?>
                            </span><br/>
                            收件地址：<span class="exp_3">
                            <?php
                                $str = explode("|",$v['address']);
                                echo $str[2].$str[3]; ?>
                            </span>
                        <?php else: ?>
                            虚拟商品
                        <?php endif; ?>
                        </div>
                        <div class="good_status"></div>
                    </div>
                    <div class="h30 nhide" style="clear:both;margin-top:1px;"></div>
                    <hr class="nhide" style="height:1px;border:none;border-top:1px dashed #ccc;margin:0px">
                    <div class="h30 nhide" style="clear:both;"></div>
                    <?php endforeach;?>
                    <div class="order_submit nhide">
                        <?php
                            if($value[0]['estatus']==1 || $value[0]['estatus']==2){
                                echo '<div class="order1_btn" value="" ordernum="">查看</div>';
                            }
                        ?>
                        <?php if (isset($v['address'])):?>
                            <div class="order_btn" os="<?=$value[0]['ostatus']?>" paystatus="<?=$value[0]['paystatus']?>" status="<?=$value[0]['estatus']?>" value="<?=$value[0]['oid']?>" ordernum="<?=$value[0]['ordernum']?>">
                        <?php else:?>
                            <div class="order_btn virual" os="<?=$value[0]['ostatus']?>" paystatus="<?=$value[0]['paystatus']?>" status="<?=$value[0]['estatus']?>" value="<?=$value[0]['oid']?>" ordernum="<?=$value[0]['ordernum']?>">
                        <?php endif;?>
                        
                        <?php
                            switch ($value[0]['estatus'])
                            {
                            case 0:
                                if($value[0]['paystatus'] == 0){
                                    echo "未发货";
                                }
                                if($value[0]['paystatus'] == 1){
                                    echo "去发货";
                                }
                                break;
                            case 1:
                                echo "确认收货";
                                break;
                            case 2:
                                echo "完成订单";
                                break;
                            default:
                                echo "状态异常";
                            }
                        ?></div>
                    </div>
                    <div class="h30 dynamic" style="clear:both"></div>
                </div>
                <?php endforeach;?>
            <?php endif;?>

            </div>
                <div class="h10" style="clear:both"></div>
                <div class="o_mes"></div>
                <div class="o_paging">
                    <div class="o_prev">上一页</div>
                    <!-- <div class="o_page">1</div>
                    <div class="o_page">2</div>
                    <div class="o_page">3</div> -->
                    <div class="o_next">下一页</div>
                </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'footer.php';?>
</body>
</html>
