<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="/static/libs/jquery-ui-1.12.1/jquery-ui.min.css" />
<link type="text/css" rel="stylesheet" href="/static/libs/jquery-ui-multiselect-widget/jquery.multiselect.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_base.css" />
<link type="text/css" rel="stylesheet" href="/static/css/charts_policy.css" />
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="/static/datatables/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/static/libs/jquery-ui-1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/static/libs/jquery-ui-multiselect-widget/src/jquery.multiselect.min.js"></script>
<script type="text/javascript" src="/static/echarts/echarts.js"></script>
</head>
<body>
    <?php include 'header.php';?>
    <div class="main">
        <?php include 'lefter_charts.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">活动评估</span><span class="movetitle">限时免费</span>
            </div>
            <div class="h30"><!-- 空行 --></div>
            <div class="container">

                <!-- 筛选条件 -->
                <div class="rptmain" style="border:none">

                    <div class="tool">

                        <!-- 活动筛选框 -->
                        <p class="ltitle">活　　动</p>
                        <div class="rtitle">
                            <ul>
                                <select id="activity_id" class="select select-activity" multiple="multiple" name="activityId">
                                    <?php foreach ($data['activity'] as $activity):?>
                                        <?php if (isset($activity->parentId)) { ?>
                                            <option value="<?=$activity->id?>" style="color:#000;">
                                                 (ID:<?=$activity->id?>)<?=$activity->name?>
                                            </option>
                                        <?php } else { ?>
                                            <option value="<?=$activity->id?>" disabled="disabled" style="color:#000;font-weight:bold">
                                                <?=$activity->name?>
                                            </option>
                                        <?php } ?>
                                    <?php endforeach;?>
                                </select>
                            </ul>
                        </div>

                        <!-- 乐码批次筛选框 -->
                        <p class="ltitle">扫码时间</p>
                        <div class="rtitle">
                            <ul id="datepicker_container" class="datepicker-container">
                                <input class="input Wdate" id="start_time" name="startTime" />
                                 - 
                                <input class="input Wdate" id="end_time" name="endTime" />
                            </ul>
                        </div>

                        <div id="btn_search" class="btn btn-blue">查询</div>

                        <!-- 产品分类筛选框 -->
                        <!-- <p class="ltitle">产品分类</p>
                        <div class="rtitle">
                            <ul>
                                <select id="category_id" class="select" name="categoryId" style="width:128px;">
                                    <option value="0">全部</option>
                                    <?php // foreach ($data['category'] as $category):?>
                                        <option value="<?php // echo $category['id'];?>" style="color:#000;">
                                            <?php 
                                                // $string='';
                                                // for($i = 0; $i < $category['level'] - 1; $i++) {
                                                //   $string = $string . "　";
                                                // }
                                                // echo $string;
                                            ?>
                                            <?php // echo $category['name'];?>
                                        </option>
                                    <?php // endforeach;?>
                                  </select>
                              </ul>
                        </div> -->

                        <!-- 产品筛选框 -->
                        <!-- <p class="ltitle">产品</p>
                        <div class="rtitle">
                            <ul>
                                <select class="select select2" id="product_id" name="productId" style="width:128px;">
                                    <option value="0">全部</option>
                                </select>
                            </ul>
                        </div> -->

                        <!-- 乐码批次筛选框 -->
                        <!-- <p class="ltitle">乐码批次</p>
                        <div class="rtitle">
                            <ul>
                                <select class="select select2" id="batch_id" name="batchId" style="width:128px;">
                                    <option value="0">全部</option>
                                </select>
                            </ul>
                        </div> -->

                    </div>

                    <!-- <div class="tool"></div>  -->

                </div> <!-- 筛选条件 end -->

                <div class="line"></div>
                <ul class="data-total-container">
                    <li class="scan-num">
                        <p><img src="/static/images/scan-num-policy.png" /><span id="scan_num">-</span></p>
                        扫码量
                    </li>
                    <li class="user-num">
                        <p><img src="/static/images/user-policy.png" /><span id="user_num">-</span></p>
                        扫码人数
                    </li>
                    <li class="redpacket-amount">
                        <p><img src="/static/images/amount-policy.png" /><span id="redpacket_amount">-</span></p>
                        红包发放金额
                    </li>
                    <li class="withdraw-amount">
                        <p><img src="/static/images/amount-policy.png" /><span id="redpacket_num">-</span></p>
                        红包发放个数
                    </li>
                </ul>

                <div class="rptmain">
                    <div class="rptcontent">
                        <div class="head">
                            <p class="ltitle">活动评估</p>
                        </div>
                        <div class="content">
                            <div id="main" style="width:100%;height:450px;line-height:450px;text-align:center;font-size:100px;color:#ddd">暂无数据</div>
                            <div class="h50" style="clear:both"></div>
                            <hr style="height:1px;border:none;border-top:1px dashed #ccc;margin:0px">
                            <div class="h30" style="clear:both"></div>
                            <div id="activity_logs_container" class="activity-logs-container" style="padding:20px 25px;text-align:center;margin-left:-75px;"></div>
                            <!-- 策略详情弹窗层 -->
                            <div id="policy_detail"></div>
                            <div id="content"></div> 
                            <!-- 策略详情弹窗层 -->
                        </div>
                    </div>
                </div> <!-- rptmain end -->

            </div> <!-- container end -->
        </div> <!-- rightmain -->
    </div> <!-- main -->
    <input type="hidden" name="mch_id" id="mch_id" value="<?=$mch_id?>">
    <?php include 'footer.php';?>

    <script type="text/javascript" src="/static/js/common.js"></script>
    <!-- <script type="text/javascript" src="/static/js/charts_base.js"></script> -->
    <script type="text/javascript" src="/static/js/charts_policy.js"></script>
</body>
</html>