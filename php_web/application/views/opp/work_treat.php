<?php include 'common/header.php'; ?>
<link rel="stylesheet" type="text/css" href="/static/css/treat.css">
<script type="text/javascript" src="/static/js/work/work_treat.js"></script>
</head>
<body> <?php include 'common/menus.php';?>
<div class="main">
<?php include 'work_left.php';?>
    <div class="rightmain">
        <div class="path"><span class="title fleft"><?=$title?></span></div>
        <div class="h20"></div>
        <div class="work_ask">
            <div class="wa">
                <div class="wak">工单类型：</div>
                <div class="wav"><?=$data->type?></div>
                <div class="wak">提交企业：</div>
                <div class="wav"><?=$data->name?></div>
            </div>
            <div class="watitle">
                <div class="watk">工单标题：</div>
                <div class="watv"><?=$data->title?></div>
            </div>
            <div class="wacontent">
                <div class="wack">工单内容:</div>
                <div class="wacv"><?=$data->content?></div>
            </div>
        </div>
        <div class="work_answer">
            <div class="work_banner"></div>
            <div class="work_tl">我继续提我的问题</div>
            <div class="work_tr">这是客服人员的回复</div>
            <div class="work_tl">客户在提问</div>
            <div class="work_tr">这是客服人员的回复</div>
            <div class="work_tl">继续来一个提问</div>
            <div class="work_tr">这是客服人员的回复</div>
            <div class="work_tl">啊啊啊，为什么还没人回复我的问题</div>
            <div class="work_tr">这是客服人员的回复</div>
            <div class="work_tl">请继续帮我跟踪吧！</div>
            <div class="work_tr">这是客服人员的回复</div>
            <div class="work_tl">现在再观察下，貌似可以解决</div>
        </div>
        <!-- <form type="validate"> -->
            <!-- <table class="table-form"> -->
                <!-- <tr>
                    <td class="name" width="150">工单类型：</td>
                    <td class="value" width="350">
                        <input type="text" class="input" id="type" name="type" value="<?=$data->type?>" readonly="readonly" disabled="disabled">
                    </td>
                    <td class="tip">企业提的工单需求类型</td>
                </tr>
                <tr>
                    <td class="name" width="150">企业名称：</td>
                    <td class="value" width="350">
                        <input type="text" class="input" name="name" id="name" value="<?=$data->name?>" disabled="disabled">
                    </td>
                    <td class="tip"></td>
                </tr>
                <tr>
                    <td class="name" width="150">工单标题：</td>
                    <td class="value" width="350">
                        <textarea class="textarea" readonly="readonly" disabled="disabled"><?=$data->title?></textarea>
                    </td>
                    <td class="tip"></td>
                </tr>
                <tr>
                    <td class="name" width="150">工单内容：</td>
                    <td class="value" width="350">
                        <textarea class="textarea" readonly="readonly" disabled="disabled"><?=$data->content?></textarea>
                    </td>
                    <td class="tip"></td>
                </tr>
                <tr>   
                    <td colspan="3"><div style="height:50px;width:100%;color:#F0F0F0;"></div></td>
                </tr> -->
            <!--     <tr>
                    <td class="name" width="150">客服选择：</td>
                    <td class="value" width="350">
                        <select id="codeVersion" class="select" name="codeVersion" edit-value="<?=$merchant->codeVersion?>">
                            <option value="">请选择......</option>
                            <?php foreach ($codes as $key => $value): ?>
                            <option <?=$merchant->codeVersion==$value->versionNum?'selected':'' ?> value="<?=$value->versionNum?>"><?=$value->versionNum?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="tip">0,不加密版本;1以上为加密版本</td>
                </tr> -->
<!--                 <tr>
                    <td></td>
                    <td>
                        <input type="hidden" id="mid" name="mid" value="<?=$id?>">
                        <input style="float:left;" id="cancle_sub" class="btn btn-gray" type="button" value="放弃预审核">
                        <input style="float:right;" id="sub" class="btn btn-blue" type="button" value="通过预审核">
                    </td>
                    <td>
                        
                    </td>
                </tr> -->
            <!-- </table>
        </form> -->
    </div>
</div>
<script type="text/javascript" src="/static/js/merchant/pre.js"></script>
<?php include 'common/footer.php';?>