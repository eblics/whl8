<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/datatables/css/jquery.dataTables.min.css" />
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<link type="text/css" rel="stylesheet" href="/static/css/company.css?232" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/validator.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<script type="text/javascript" src="/static/js/person.js"></script>
<style>
.priceList>li{
    width:278px;
    height: 410px;
    border: 1px solid #e5e5e5;
    float: left;
    margin:0 30px 20px 5px;
    position:relative;
    text-align: center;
}
.priceList>.active{border:1px solid #5b8edd;}
.priceList>li i{
    position: absolute;
    bottom: 0;
    right: 0;
    width: 40px;
    height: 40px;
    overflow: hidden;
    text-indent: -99em;
    display: block;
    background-repeat: no-repeat;
    background-position: 0 0;
}
.priceList>li>.header{
    padding: 30px 15px;
    background: #f6f6f6;
    border-bottom:1px solid #e5e5e5;
}
.priceList>.active i{
    background-image: url(/static/images/gou.png);
}
.priceList>li p{
    line-height: 2;
    font-size: 20px;
    color: #333
}
.priceList>li .price{
    color: #fd9f00;
    font-size: 40px;
    font-weight: 800;
}
.priceList>li .batchNum{
    color: #5a8edd;
    font-size: 16px;
}
.priceList>.active>.batchNum{
    color: #5b8edd;
    font-size: 16px;
}
.content li{
    width: 50%;
    float: left;
    height: 60px;
    line-height: 60px;
    font-size: 16px;
}

.content li>.active{
    padding: 10px 20px;
    border-radius: 20px;
    background: #5b8edd;
    color: #fff;
}

.content .goBtn{
    padding: 8px 20px;
    border: 1px solid #5b8edd;
    border-radius: 30px;
    font-size: 16px;
    color: #5b8edd;
}

.bank_info{padding: 10px;border:1px solid #e5e5e5;line-height: 2}
.bank_info p{font-size: 16px;font-weight: blod}
h2{font-size: 16px;font-weight: 800;margin:20px 0;}
.h20{clear: both}

.payOnLine li{width: 25%;float: left;text-align: center;}
.pay_text{margin-top: 10px;}
.pay_text img{vertical-align:middle;width:30px;}
.pay_text span{vertical-align:middle;font-size:18px;}
#qrcode{
    width: 168px;
    height:168px;
    /*border:1px solid #42b035;*/
    margin: 0 auto;
    background-image: url(/static/images/scan_pay_bg.gif);
}
#qrcode img {
    display: block;
    width: 168px;
    height: 168px;
    border:1px solid #42b035;
}
/*降档透明*/
.no_select{
    opacity: 0.5;
}
.pay_way p{font-size:16px;line-height: 2}

.del_price{font-size: 18px;margin-top: -20px;color: #ccc;}

.login_check{width:369px;height:40px;margin-top:12px;margin-left:0;position:relative;line-height:40px}
.lc_left{float:left;width:215px;height:40px;border-left-width:2px;border-top-width:2px;border-bottom-width:2px;border-bottom-right:2px;padding-left:5px}
.lc_left input{overflow:hidden;border-style:none;height:40px;width:325px;border-width:2px;border:1px solid #d7d7d7;line-height:40px;background:0 0;margin-left:-5px;margin-top:-1px;padding-left:5px}
.lc_line{height:32px;float:left;line-height:32px;border-width:1px;margin-top:3px;border-color:#D7D7D7;border-style:solid}
.lc_right{position:absolute;left:220px;float:right;width:110px;letter-spacing:1px;height:40px;border:solid 1px;border-width:0;border-left-width:1px;border-left-color:#D7D7D7;line-height:34px;text-align:center;color:#6A94DE;font-size:16px}
.lc_right:hover{cursor:pointer;color:#5881CB}
.lc_right img{margin-top:2px}
#getCode {
    width: 110px;
    height: 40px;
    background: none;
    border: none;
    color: #4a85e0;
    cursor: pointer;
}

.confirm-form>.box {
    width: 375px;
    height: 250px;
}
.confirm-form>.box>dd>.condiv {
    height: 135px;
}
#amount,#total{
    font-size: 24px;
    color: #fd9f00;
    font-weight: 800;
}

.confirm-form>.box {
    box-shadow: 0 0 5px 2px #e0e0e0;
}
.taocan{
    position:absolute;left: 0;right:0;top: 162px;display:block;text-align: center;
}
.taocan>.title{
    border-radius: 20px;border: 1px solid #e5e5e5;padding: 6px 20px;background: #fff;font-size: 16px;
}
</style>
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_user.php';?>
    <div class="rightmain">
        <div class="path">
            <span class="title fleft">企业续费</span>
            <span style="color:red">
                <?php 
                    if($company->is_formal==0){
                        echo '（当前版本：试用版本&nbsp;到期日期：'.$company->expired.'）';
                    }

                    if($company->is_formal==1){
                        if($company->grade==0){
                            echo '（当前版本：基础版&nbsp;到期日期：'.$company->expired.'）';
                        }
                        if($company->grade==1){
                            echo '（当前版本：标准版&nbsp;到期日期：'.$company->expired.'）';
                        }
                        if($company->grade==2){
                            echo '（当前版本：高级版&nbsp;到期日期：'.$company->expired.'）';
                        }
                        if($company->grade==3){
                            echo '（当前版本：旗舰版&nbsp;到期日期：'.$company->expired.'）';
                        }
                    }

                ?>
            </span>
        </div>
        <div class="h20"></div>
        <h1 style="font-size: 20px;font-weight: 800;">购买企业VIP</h1>
        <div class="h40"></div>
        <!-- 价格选择框 -->
        <ul class="priceList">
            <li>
                <div class="header">
                    <p class="price"><span>10,000</span><small>元</small></p>
                    <p class="batchNum">（申请码≤10万）</p>
                    <div class="taocan">
                        <span class="title">基础版-<t>1</t></span>
                    </div>
                </div>
                <div class="content" style="padding-top: 30px;">
                    <p style="font-weight:800;font-size:14px;color:#5b8edd">扫码频率</p>
                    <ul>
                        <li>
                            <a class="active" href="javascript:;" data-grade="1" data-time="1" data-concurrencyNum="20" data-disable="<?=$company->grade>1?1:0?>" data-price="1">20次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="3" data-time="3" data-concurrencyNum="100" data-disable="<?=$company->grade>3?1:0?>" data-price="3">100次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="2" data-time="2" data-concurrencyNum="50" data-disable="<?=$company->grade>2?1:0?>" data-price="2">50次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="4" data-time="4" data-concurrencyNum="200" data-disable="<?=$company->grade>4?1:0?>" data-price="5">200次/秒</a>
                        </li>
                    </ul>
                    <p><a class="goBtn" href="javascript:;">确认选择</a></p>
                </div>
            </li>
            <li>
                <div class="header">
                    <p class="price"><span>15,000</span><small>元</small></p>
                    <p class="batchNum">（10万<申请码≤100万）</p>
                    <div class="taocan">
                        <span class="title">标准版-<t>1</t></span>
                    </div>
                </div>
                <div class="content" style="padding-top: 30px;">
                    <p style="font-weight:800;font-size:14px;color:#5b8edd">扫码频率</p>
                    <ul>
                        <li>
                            <a class="active" href="javascript:;" data-grade="5" data-time="1" data-concurrencyNum="20" data-disable="<?=$company->grade>5?1:0?>" data-price="1.5">20次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="7" data-time="3" data-concurrencyNum="100" data-disable="<?=$company->grade>7?1:0?>" data-price="3.5">100次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="6" data-time="2" data-concurrencyNum="50" data-disable="<?=$company->grade>6?1:0?>" data-price="2.5">50次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="8" data-time="4" data-concurrencyNum="200" data-disable="<?=$company->grade>8?1:0?>" data-price="5.5">200次/秒</a>
                        </li>
                    </ul>
                    <p><a class="goBtn" href="javascript:;">确认选择</a></p>
                </div>
            </li>
            <li>
                <div class="header">
                    <p class="price" data-disable="<?=$company->grade>0?1:0?>" data-price="20000"><span>20,000</span><small>元</small></p>
                    <p class="batchNum">（100万<申请码≤1000万）</p>
                    <div class="taocan">
                        <span class="title">高级版-<t>1</t></span>
                    </div>
                </div>
                <div class="content" style="padding-top: 30px;">
                    <p style="font-weight:800;font-size:14px;color:#5b8edd">扫码频率</p>
                    <ul>
                        <li>
                            <a class="active" href="javascript:;" data-grade="9" data-time="1" data-concurrencyNum="20" data-disable="<?=$company->grade>9?1:0?>" data-price="2">20次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="11" data-time="3" data-concurrencyNum="100" data-disable="<?=$company->grade>11?1:0?>" data-price="4">100次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="10" data-time="2" data-concurrencyNum="50" data-disable="<?=$company->grade>10?1:0?>" data-price="3">50次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="12" data-time="4" data-concurrencyNum="200" data-disable="<?=$company->grade>12?1:0?>" data-price="6">200次/秒</a>
                        </li>
                    </ul>
                    <p><a class="goBtn" href="javascript:;">确认选择</a></p>
                </div>
            </li>
            <li>
                <div class="header">
                    <p class="price"><span>25,000</span><small>元</small></p>
                    <p class="batchNum">（申请>1000万）</p>
                    <div class="taocan">
                        <span class="title">旗舰版-<t>1</t></span>
                    </div>
                </div>
                <div class="content" style="padding-top: 30px;">
                    <p style="font-weight:800;font-size:14px;color:#5b8edd">扫码频率</p>
                    <ul>
                        <li>
                            <a class="active" href="javascript:;" data-grade="13" data-time="1" data-concurrencyNum="20" data-disable="<?=$company->grade>13?1:0?>" data-price="2.5">20次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="15" data-time="3" data-concurrencyNum="100" data-disable="<?=$company->grade>15?1:0?>" data-price="3.5">100次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="14" data-time="2" data-concurrencyNum="50" data-disable="<?=$company->grade>14?1:0?>" data-price="4.5">50次/秒</a>
                        </li>
                        <li>
                            <a href="javascript:;" data-grade="16" data-time="4" data-concurrencyNum="200" data-disable="<?=$company->grade>16?1:0?>" data-price="6.5">200次/秒</a>
                        </li>
                    </ul>
                    <p><a class="goBtn" href="javascript:;">确认选择</a></p>
                </div>
            </li>
        </ul>
        <div class="h20"></div>
        <!-- 选择购买方式 -->
        <div class="pay_way">
            <h1 style="font-size: 20px;font-weight: 800;">选择支付方式</h1>
            <h2>一、易宝支付</h2>
            <p>本次需支付金额：<span id="total">0</span>元</p>
            <p style="display:none"><span id="orderNum"><?=$orderNum?></span></p>
            <input type="hidden" id="grade" value="<?=$company->grade?>">
            <div class="gopay">
                <a class="btn btn-blue" href="javascript:;">立即支付</a>
            </div>
           
        </div>
        <div class="h20"></div>
        <div class="bank_info">
            <p>说明：</p>
            <p>在线支付接入易宝支付，支持网银支付、微信支付和支付宝支付</p>
            <p>支付结果以实际到账通知为准，如有问题请及时联系我们！</p>
        </div>
        <div class="h20"></div>
     </div>
</div>


<div id="editForm" style="display:none">
    <div class="table-form">
        <div class="login_check input">
            <p>当前账号：<?=$userInfo->phoneNum?></p>
            <input type="hidden" id="account" value="<?=$userInfo->phoneNum?>">
            <div class="lc_left">
                <input type="text" maxlength="6" id="verify" placeholder="验证码">
            </div>
            <span id="verify_img"></span>
            <div class="lc_right">
                <input type="button" id="getCode" onclick="Init.getCode()" value="免费获取验证码">
            </div>
            <p style="color:red;clear: both;"><span>验证码通过之后将直接从余额扣除相关款项，请悉知！</span></p>
        </div>
    </div>
</div>

<?php include 'footer.php';?>

<!-- js部分 -->
<script>
var tc={1:1,2:2,3:3,4:5,5:1.5,6:2.5,7:3.5,8:5.5,9:2,10:3,11:4,12:6,13:2.5,14:3.5,15:4.5,16:6.5};
var Init={
    init:function(){
        var _this=this;
        $(".content li a").click(function(){
            var grade=$(this).attr('data-grade');
            var time=$(this).attr('data-time');
            var concurrencyNum=$(this).attr('data-concurrencyNum');
            var nowgrade=$("#grade").val();
            var isdisable=$(this).attr('data-disable');
            var price=$(this).attr('data-price') * 10000;
            //拒绝将档
            if(parseInt(nowgrade)>parseInt(grade)){
                common.alert('不能选择低于当前版本的套餐服务！');
                return false;
            }
            if(isdisable==0){
                $(".priceList li").removeClass('active');
                $(this).parents('.content').siblings().find('t').html(time);
                $(this).parent().siblings().children('a').removeClass('active');
                $(this).addClass('active');
                //计算价格
                $(this).parents('.content').siblings().find('.price span').html(common.splitFormoney(price,0));
                $("#total").html(common.splitFormoney(price,0));
            }
        })

        $(".goBtn").click(function(){
            var price=$(this).parents('.content').find('a.active').attr('data-price') * 10000;
            $("#total").html(common.splitFormoney(price,0));
            $(".priceList li").removeClass('active');
            $(this).parents('li').addClass('active');

        })

        //支付按钮发起扣款支付
        $(".gopay a").click(function(){
            var card=$(".priceList>li.active a.active");
            var price=card.attr('data-price');
            var concurrencynum=card.attr('data-concurrencynum');
            var grade=card.attr('data-grade');
            var nowgrade=$("#grade").val();
            var orderNum = $("#orderNum").html();
            if(card.length==0){
                common.alert('请<span style="color:red">确认选择</span>需要开通的服务！');
                return false;
            }

            if(parseInt(nowgrade)>parseInt(grade)){
                common.alert('不能选择低于当前版本的套餐服务！');
                return false;
            }
            
            var param={
                'amount':price * 10000,
                'concurrencynum':concurrencynum,
                'grade':grade,
                'orderNum':orderNum
            }

            _this.showResult(param);

            
            window.open("/cashier/sendYeepayOrder?amount="+param.amount+"&concurrencynum="+param.concurrencynum+"&grade="+param.grade+"&orderNum="+param.orderNum);
        })
    },
    showResult:function(param){
        var _this=this;
        _this.searchOrderStatus(param,function(res){
            if(res.status==true){
                common.alert(res.msg,function(r){
                    if(r=1){
                        location.reload();
                    }
                })
            }
            if(res.status==false){
                common.alert(res.msg,function(r){
                    if(r=1){
                        _this.showResult(param);
                    }
                })
            }
        })
    },
    //查询订单状态
    searchOrderStatus:function(param,callback){
        common.confirm('订单支付状态', function(r) {
            if (r == 1) {
                $.post('/cashier/searchOrder',param,function(res){
                    if(res.errcode==0&&res.errmsg['rb_PayStatus']=='SUCCESS'){
                        callback({status:true,msg:'订单支付成功~'});
                    }else{
                        if(res.errmsg['rb_PayStatus']=='INIT'){
                            callback({status:false,msg:'订单未支付~'});
                        }
                        if(res.errmsg['rb_PayStatus']=='CANCELED'){
                            callback({status:false,msg:'订单已取消~'});
                        }
                    }
                },'json')
            }
            if(r == 0){
                common.alert("窗口已关闭，不影响支付结果，在收银台完成支付后请刷新页面即可！");
            }
        },'已完成支付','关闭');
    },
    //检测表单是否为空
    isNull:function(obj){
        if (obj == null || obj == undefined || obj == '') { 
            return false;
        }else{
            return true;
        }
    }
}
$(function(){
    Init.init();
})
</script>
</body>
</html>