<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/shop_list.css">
    <script type="text/javascript" src="/static/js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/static/js/shop_list.js"></script>
    <title></title>
</head>
<body>
    <div class="head">
        <div class="title">
            <span class="menu"><span class="text">全部分类</span><span class="icon"></span></span>
            <!-- <span class="line"></span>
            <span class="menu">默认排序<span class="icon"></span></span> -->
        </div>
        <div class="menulist" style="display:none;"></div>
    </div>
    <div class="content" style="padding-top:51px;padding-bottom:48px;">
        <?php foreach ($shops as $shop):?>
        <div class="item" shopid="<?=$shop['id']?>">
            <div class="address">
                <span class="info">
                    <span class="basic">
                        <span class="name"><?=$shop['name']?></span>
                        <span class="telephone"><?=$shop['address']?></span>
                    </span>
                    <span class="text"><?=$shop['ownerName']?>(<?=$shop['ownerPhoneNum']?>)</span>
                </span>
                <?php if($shop['state']==1):?>
                <span class="revoke"></span>
                <?php elseif($shop['state']==0 || $shop['state']==3):?>
                <span class="edit"></span>
                <span class="delete"></span>
                <?php endif;?>
            </div>
            <div class="state">
                <span class="info">
                <?php 
                    switch ($shop['state']){
                        case 0:
                            echo '草稿';
                            break;
                        case 1:
                            echo '审批中';
                            break;
                        case 2:
                            echo '通过审批';
                            break;
                        case 3:
                            echo '驳回审批';
                            break;
                    }
                ?>
                </span>
                <span class="time"><?=$shop['createTime']?></span>
            </div>
        </div>
        <?php endforeach;?>
    </div>
    <div class="add">
        <span class="icon"></span>
        <span class="info">添加门店</span>
    </div>
</body>
</html>