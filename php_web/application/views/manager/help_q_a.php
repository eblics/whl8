<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?=PRODUCT_NAME.' - '.SYSTEM_NAME?></title>
<link type="text/css" rel="stylesheet" href="/static/css/common.css" />
<script type="text/javascript" src="/static/js/jquery.js"></script>
<script type="text/javascript" src="/static/js/common.js"></script>
<link type="text/css" rel="stylesheet" href="/static/css/help.css?12" />
</head>
<body>
<?php include 'header.php';?>
<div class="main">
    <?php include 'lefter_help.php';?>
        <div class="rightmain">
            <div class="path">
                <span class="title fleft">Q&A</span>
            </div>
            <div class="h20"></div>
            <div class="content">
                <div class="paragraph impor">一、 标签：安全 微信支付</div>
                <div class="paragraph h5">问：我们的钱放在了微信支付平台，怎么保证我们的资金安全？ </div>
                <div class="paragraph">答：资金的整个流程如下：
                    <div class="space">
1.  给微信支付平台充值之前。微信支付平台是没有钱的。此时用户提现（企业给用户发放真实红包）会失败，也就是获取不到真实的红包。<br/>
2.  给微信支付平台充值，微信支付平台有了一定的资金。<br/>
3.  用户在手机端操作，提现后，用户可以获得企业发到他微信的微信红包。此时企业充到微信支付平台的钱，就从微信支付平台以红包的形式发到了用户的微信中。<br/>

这其中，我们确实需要企业填微信公众平台和微信支付平台的相关信息。这是用来调用微信的接口，实现公众号的各种功能的。如果实在不放心，可以试试观察支付平台中的余额，每次少充值一些钱，不够了就再充。<br/>

可以看到整个涉及到资金的过程中，资金并没有流入到欢乐扫平台中。而发放红包的各种信息，都可以在微信支付平台中查询到的。微信支付平台是专业的第三方支付平台，安全系数还是一流的。所以不必太多担心。
                    </div>

                
                </div>

                <div class="paragraph impor">二、 标签：消费者 微信信息</div>
                <div class="paragraph h5">问：扫码的消费者如果更新了他自己的微信信息，我们能获取到新的信息吗？</div>
                <div class="paragraph">答：当消费者关注了企业的公众号，扫描我们二维码时，我们就可以在欢乐扫平台中查看到该消费者的信息。之后，如果消费者的微信信息被自己修改过了。那么下次该消费者点击“我的账户”，系统将重新获取他的微信信息，来保证消费者信息的“新鲜度”。

                
                </div>

                <div class="paragraph impor">三、标签：地理位置</div>
                <div class="paragraph h5">问：地理位置报表中的位置是如何获取的？</div>
                <div class="paragraph">答：微信用户在微信公众号中，上报了地理位置，我们对其进行收集。

                
                </div>

                <div class="paragraph impor">四、标签： 地理位置</div>
                <div class="paragraph h5">问：地理位置能精确到什么范围？</div>
                <div class="paragraph">答：可以精确到100mx100m的范围。实际情况会受到用户使用的设备，电磁等一些因素的干扰导致准确度受影响。
                </div>

                <div class="paragraph impor">五、标签：消费者 提现</div>
                <div class="paragraph h5">问：为什么某些消费者会显示提现失败？</div>
                <div class="paragraph">答：有以下三种可能
                    <div class="space">
                   ①商户微信支付中余额不足。需要企业自行充值。<br/>
                ②用户红包金额小于提现金额。<br/>
                ③用户账户被锁定。这种情况需要自行解锁，其他红包也领不了。当解除锁定后，再次提现即可。

                    </div>

                
                </div>

                <div class="paragraph impor">六、标签：自动回复</div>
                <div class="paragraph h5">问：微信公众号已经授权给欢乐扫平台了，那我们如何使用自动回复功能？</div>
                <div class="paragraph">答：直接在微信公众平台上开启自动回复功能，并使用即可。此操作不会妨碍授权的继续使用，两者互不影响。
                    <div class="space">
                   
                    </div>

                
                </div>
            </div>
        </div>

</div>
<?php include 'footer.php';?>
</body>
</html>
