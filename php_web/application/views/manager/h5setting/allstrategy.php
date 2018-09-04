<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <link type="text/css" rel="stylesheet" href="/static/css/common.css" />
    <style type="text/css">
        html,body{overflow:hidden;}
        .iframe{position:absolute;width:640px;top:0;left:0;bottom:0;overflow:auto;}
        .panel{position:absolute;left:640px;top:0;right:0;bottom:0;padding:20px;background:#eee;}
        .panel .moduleset dt{height:40px; line-height:40px; padding:0 10px;border-radius: 5px;color: #555;font-size: 15px;font-weight: 700; 
                            background: -moz-linear-gradient(top, #ccc 0%, #999 100%);
                            background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ccc), color-stop(100%,#999));
                            background: -webkit-linear-gradient(top, #ccc 0%,#999 100%);
                            background: -o-linear-gradient(top, #ccc 0%,#999 100%);
                            background: -ms-linear-gradient(top, #ccc 0%,#999 100%);
                            background: linear-gradient(to bottom, #ccc 0%,#999 100%);}
        .panel .moduleset dd{overflow:hidden; padding:10px;}
        .panel .moduleset dd label{display:block; border:1px solid #ccc; border-radius:3px;padding:5px 15px; margin:5px; height:26px; line-height:26px; font-size:13px;color:#666;background:#fff;}
        .panel .moduleset dd label input{ vertical-align:middle;margin-bottom:5px;}
        .panel .moduleset dd label:hover{border-color:#999;}
        .panel .btn-div{overflow:hidden;padding:0 15px;}
        /*animation*/ 
        /*晃动*/
        .shake {animation: shake 2s ease-in-out infinite;-webkit-animation: shake 2s ease-in-out infinite;-webkit-backface-visibility: hidden;}
        @-webkit-keyframes shake {0%,50%,65%,100% {-webkit-transform: translateX(0);}52.5%,57.5%,62.5% {-webkit-transform: translateX(-5px);}55%,60% {-webkit-transform: translateX(5px);}}
        /*main*/
        .wrapper {position:absolute;top:0;right:0;bottom:0;left:0;width:640px;background: #fef8e8;overflow: auto; font-family:'microsoft yahei';}
        .wrapper>.module{ position: relative; clear: both; display:none;}
        .wrapper>.module-list{ clear: both; padding: 0 18px; overflow:hidden;}
        .wrapper>.module-list>dt{height: 40px; line-height: 40px; color: #d74f43; border-bottom: 1px solid #d74f43; padding-top:10px; }
        .wrapper>.module-list>dt>strong{font-size: 24px; float: left;}
        .wrapper>.module-list>dt>span{font-size: 18px; float: right;}
        .wrapper>.module-list>dt>em{ float: right; display:inline-block; width:50px; height:50px;margin-top:-10px;background: url(<?=$this->config->item('mobile_url')?>h5/allstrategy/images/shake.png) no-repeat center;}
        .wrapper>.module-list>dd{text-align: center; padding:10px 0; overflow: hidden;}
        .wrapper>.module-list>dd>.li{width: 180px; height: 250px; background: #d74f43; border-radius: 5px; float:left; margin:7px;}
        .wrapper>.module-list>dd>.li>.li-in{width: 166px;height: 236px; border: 1px dashed #fc3; margin:7px; border-radius: 5px; color: #fff;}
        .wrapper>.module-list>dd>.li>.li-in>.tip{height: 70px; line-height: 70px; font-size: 24px; overflow: hidden;letter-spacing: 2px;}
        .wrapper>.module-list>dd>.li>.li-in>.name{height: 100px; line-height: 100px; font-size: 30px; overflow: hidden;}
        .wrapper>.module-list>dd>.li>.li-in>.type{height: 76px; line-height: 76px; font-size: 18px; overflow: hidden;letter-spacing: 2px;}
        .wrapper>.module-list>dd>.li.cash>.li-in>.name{ font-size: 50px;}
        .wrapper>.module-list>dd>.li.cash>.li-in>.name>em{ font-size: 26px;}
        .wrapper>.module-list>dd>.li.point>.li-in>.name{ font-size: 45px;}
        .wrapper>.module-list>dd>.rank-box{width: 430px; height: 30px; line-height: 30px; margin: 40px auto; font-size: 24px; color: #d74f43; padding:15px 40px; border: 1px solid #d74f43; border-radius: 30px; }
        .wrapper>.module-list>dd>.rank-box>.city{float: left; font-size: 24px;}
        .wrapper>.module-list>dd>.rank-box>.number{display: inline-block; font-size: 24px; font-weight: 700;}
        .wrapper>.module-list>dd>.rank-box>.scantime{float: right; font-size: 20px;}
        .wrapper>.module-list>dd>.rank-box>.scantime>strong{ font-weight: 700;}
        .wrapper>.module-list>dd>.rule-box{width:90%;line-height:26px; margin:20px auto; font-size:20px; color: #333; text-align: left;}
        .wrapper>.module-list>dd>.rule-box>p{font-size:20px;line-height:26px;padding:10px 0; }
        .wrapper>.module>.bg-img{width:100%; height:100px;background:url(<?=$this->config->item('mobile_url')?>h5/allstrategy/images/bg-bottom.png) no-repeat top center;}
        .wrapper>.module>.bg-color{background:#d74f43; overflow: hidden;}
        .wrapper>.module>.bg-color>.subscribe{ width: 480px; height: 140px; margin:20px auto; background: #fff; padding:15px 40px;border:6px solid #c33; border-radius:15px;}
        .wrapper>.module>.bg-color>.subscribe>.qrcode{ width:140px; height: 140px; float: left;}
        .wrapper>.module>.bg-color>.subscribe>.qrcode>img{ width:140px; height: 140px;}
        .wrapper>.module>.bg-color>.subscribe>.txt{ width:300px; height: 140px; float:right; text-align: center; color: #d74f43;}
        .wrapper>.module>.bg-color>.subscribe>.txt>h2{ font-size:24px; height: 40px;line-height: 40px;}
        .wrapper>.module>.bg-color>.subscribe>.txt>h1{ font-size:28px;font-weight: 700; height: 60px;line-height:60px; letter-spacing: 5px;}
        .wrapper>.module>.bg-color>.subscribe>.txt>h3{ font-size:18px; height: 40px;line-height: 40px;}
        #banner{height:320px;}
        #banner>img{width: 100%;height: 320px;}
        #logo{width: 140px; height: 140px; border-radius:140px; margin: -70px auto -20px auto; overflow: hidden; border:1px solid #fcc; background: #fff; text-align: center;}
        #logo>img{position:absolute;left:50%;top:50%; margin-left: -50%; margin-top: -50%;width:100%;height:100%;} 
        #title1{height:60px; line-height:60px; font-size:36px; color:#c96; text-align: center; margin-top:20px; }
        #title2{height:30px; line-height:30px; margin-bottom: 30px; font-size:24px; color:#c93; text-align: center; }
        .moduleHover{ position:absolute;left:0;top:0;width:0;height:0;background:#000;opacity:0.5;z-index:9990;} 
        .editbox{width:100%;position:absolute;top:0;left:0;background:rgba(0,0,0,.6);display:none;}
        .editbox>.upbox{width:80px;height:80px;position:absolute;top:50%;left:50%;margin:-40px 0 0 -40px;}
        .editbox>.inputbox,.editbox>.inputbox>input{width:100%;height:100%;position:absolute;top:0;left:0; bottom:0;right:0;border:none;text-align:center;}
        .edittxt{width:100%;position:absolute;top:0;left:0;background:rgba(0,0,0,.6);display:none;}
        .edittxt>.inputbox,.edittxt>.inputbox>input{width:100%;height:100%;position:absolute;top:0;left:0; bottom:0;right:0;border:none;text-align:center;}
        .editpart{width:100%;position:absolute;top:0;left:0;background:rgba(0,0,0,.6);display:none;}
        .editpart>.inputbox{width:100%;padding:90px 0 0 0;position:absolute;top:0;left:0; bottom:0;right:0;}
        .editpart>.inputbox>input{width:80%;height:26px;margin:5px auto;display:block;} 
        .hls-upload .img{display:none;}
        .hls-upload .choose{margin-left:0;}
    </style>
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/common.js"></script>
    <script type="text/javascript">
    $(function(){
        var config='<?=$config?>';
        window.h5config={};
        if(config=='null' || config==''){
            $('.module').show();
            $('.check').attr('checked','checked');
            $.each($('.check'),function(i,e){
                var id=($(this).attr('id')).replace('ck-','');
                window.h5config[id]='';
            });
        }else{
            window.h5config=$.parseJSON(config);
            $.each(window.h5config,function(k,v){
                if(k=='banner'||k=='logo') $('#'+k+'>img').attr('src',v);
                if(k=='title1'||k=='title2') $('#'+k).html(v);
                if(k=='rule'){
                    var rulehtml='';
                    $.each(v,function(i,e){
                        rulehtml+='<p>'+e+'</p>';
                    });
                    $('#'+k+' .rule-box').html(rulehtml);
                }
                $('#ck-'+k).attr('checked','checked');
                $('#'+k).show();
            });
        }
        $('.check').on('click',function(){
            var id=($(this).attr('id')).replace('ck-','');
            if($(this).is(':checked')){
                $('#'+id).show();
                window.h5config[id]='';
            }else{
                $('#'+id).hide();
                delete window.h5config[id];
            }
        });
        $('.moduleset label').on('mouseenter',function(){
            if(!$(this).find('.check').is(':checked')){
                return;
            }
            var id=($(this).children('.check').attr('id')).replace('ck-','');
            var opt={
                'top':$('#'+id).position().top+$('.wrapper').scrollTop(),
                'left':$('#'+id).offset().left,
                'width':$('#'+id).width(),
                'height':$('#'+id).height(),
                'z-index':$('#'+id).css('z-index')+1
            };
            if(id=='logo') opt.top+=-70;
            if(id=='title1') opt.top+=20;
            if(id=='reward' || id=='rank' || id=='rule') opt.width+=40;
            $('.wrapper').append('<div class="moduleHover"></div>');
            $('.moduleHover').css(opt);
        }).on('mouseleave',function(){
            $('.moduleHover').remove();
        });
        $('.module').on('mouseenter',function(){
            var id=$(this).attr('id');
            var opt={
                'top':$('#'+id).position().top+$('.wrapper').scrollTop(),
                'left':$('#'+id).offset().left,
                'width':$('#'+id).width(),
                'height':$('#'+id).height(),
                'z-index':$('#'+id).css('z-index'),
                'display':'block'
            };
            if(id=='logo') opt.top+=-70;
            if(id=='title1') opt.top+=20;
            if(id=='rule') opt.width+=40;
            $('#edit-'+id).css(opt);
        });
        $('.editbox').on('mouseleave',function(){
            $(this).hide();
        });
        $('.edittxt').on('mouseenter',function(){
            var id=($(this).attr('id')).replace('edit-','');
            $(this).find('input').val($('#'+id).text());
            $(this).show();
            $(this).siblings('.edittxt').hide();
        }).on('mouseleave',function(){
            var id=($(this).attr('id')).replace('edit-','');
            $('#'+id).html($(this).find('input').val());
            $('#edit-'+id).hide(); 
        });
        $('#edit-rule').on('mouseenter',function(){
            var inputhtml='';
            $.each($('#rule .rule-box p'),function(i,e){
                inputhtml+='<input type="text" value="'+$(e).text()+'"  maxlength="26" />';
            });
            $('#edit-rule .inputbox').html(inputhtml);
        }).on('mouseleave',function(){
            var phtml='';
            $.each($('#edit-rule .inputbox input'),function(i,e){
                phtml+='<p>'+$(e).val()+'</p>';
            });
            $('#rule .rule-box').html(phtml);
            $('#edit-rule').hide(); 
        });
        common.uploadInit('dataBanner','/card/upload',undefined,function(d){
            $('#banner>img').attr('src',d);
        });
        common.uploadInit('dataLogo','/card/upload',undefined,function(d){
            $('#logo>img').attr('src',d);
        });
        $('#dataTitle1').on('blur',function(){
            $(this).parents('.edittxt').hide();
        });
        $('#dataTitle2').on('blur',function(){
            $(this).parents('.edittxt').hide();
        });
        $('#btnSave').on('click',function(){
            $.each(window.h5config,function(k,v){
                if(k=='banner'||k=='logo') window.h5config[k]=$('#'+k+'>img').attr('src');
                if(k=='title1'||k=='title2') window.h5config[k]=$('#'+k).text();
                if(k=='rule'){
                    window.h5config[k]=[];
                    $.each($('#'+k+' .rule-box p'),function(i,e){
                        window.h5config[k].push($(this).text());
                    });
                } 
            });
            $.post('/activity/h5setting_save/allstrategy',{'config':JSON.stringify(window.h5config)},function(d){
                if(d.errcode==0){
                    common.alert('保存成功');
                }else{
                    common.alert(d.errmsg);
                }
            },'json');
            console.log(window.h5config);
        });
    });
    </script>
</head>
<body>
    <div class="iframe">
        <!--h5部分-->
        <div class="wrapper">
            <div class="module" id="banner"><img src="<?=$this->config->item('mobile_url')?>h5/allstrategy/images/banner.jpg"/></div>
            <div class="editbox" id="edit-banner">
                <div class="upbox">
                    <input class="js-upload" width-callback="1" type="file" edit-value="" id="dataBanner" name="dataBanner"/>
                </div>
            </div>
            <div class="module" id="logo"><img src="<?=$this->config->item('mobile_url')?>h5/allstrategy/images/logo.png"/></div>
            <div class="editbox" id="edit-logo">
                <div class="upbox">
                    <input class="js-upload" type="file" edit-value="" id="dataLogo" name="dataLogo"/>
                </div>
            </div>
            <div class="module" id="title1">爱创科技</div>
            <div class="edittxt" id="edit-title1">
                <div class="inputbox">
                    <input class="input" type="text" id="dataTitle1" maxlength='16' name="dataTitle1"/>
                </div>
            </div>
            <div class="module" id="title2">开盖扫码　　码上有奖</div>
            <div class="edittxt" id="edit-title2">
                <div class="inputbox">
                    <input class="input" type="text" id="dataTitle2" maxlength='24' name="dataTitle2"/>
                </div>
            </div>
            <dl class="module module-list" id="reward">
                <dt><strong>获得奖励</strong><em class="shake"></em><span>摇一摇再次扫码</span></dt>
                <dd>
                    <div class="li cash">
                        <div class="li-in">
                            <div class="tip">中奖啦</div>
                            <div class="name">6<em>元</em></div>
                            <div class="type">现金红包</div>
                        </div>
                    </div>
                    <div class="li card">
                        <div class="li-in">
                            <div class="tip">中奖啦</div>
                            <div class="name">再来一瓶</em></div>
                            <div class="type">乐券</div>
                        </div>
                    </div>
                    <div class="li point">
                        <div class="li-in">
                            <div class="tip">中奖啦</div>
                            <div class="name">500</div>
                            <div class="type">积分</div>
                        </div>
                    </div>
                </dd>
            </dl>
            <!--<dl class="module module-list" id="rank">
                <dt><strong>我的排名</strong><em class="shake"></em><span>摇一摇再次扫码</span></dt>
                <dd>
                    <div class="rank-box">
                        <div class="city">全国</div>
                        <div class="number">第548名</div>
                        <div class="scantime">扫码<strong>6630</strong>次</div>
                    </div>
                </dd>
            </dl>-->
            <dl class="module module-list" id="rule">
                <dt><strong>活动说明</strong><em class="shake"></em><span>摇一摇再次扫码</span></dt>
                <dd>
                    <div class="rule-box">
                        <p>1、活动规则1活动规则1活动规则1活动规</p>
                        <p>2、活动规则1活动规则1活动规则1活动规则1</p>
                        <p>3、活动规则1活动规则1活动规则1活</p>
                        <p>4、活动规则1活动规则1活动规则1活动</p>
                        <p>5、活动规则1活动规则1活动规则1活动规则1活动规则</p>
                    </div>
                </dd>
            </dl>
            <div class="editpart" id="edit-rule">
                <div class="inputbox">
                </div>
            </div>
            <div class="module" id="subscribe">
                <div class="bg-img"></div>
                <div class="bg-color">
                    <div class="subscribe">
                        <div class="qrcode"><img src="/activity/h5setting_getqrcode"/></div>
                        <div class="txt"><h2>关注我们的微信公众号</h2><h1>即可领取奖品</h1><h3>（长按二维码即可关注）</h3></div>
                    </div>
                </div>
            </div>
        </div>
        <!--h5部分 end-->
    </div>
    <div class="panel">
        <dl class="moduleset">
            <dt>扫码H5应用模块配置</dt>
            <dd>
                <label for="ck-banner"><input type="checkbox" class="check" id="ck-banner"> 横幅图片</label>
                <label for="ck-logo"><input type="checkbox" class="check" id="ck-logo"> LOGO图片</label>
                <label for="ck-title1"><input type="checkbox" class="check" id="ck-title1"> 标题1文字</label>
                <label for="ck-title2"><input type="checkbox" class="check" id="ck-title2"> 标题2文字</label>
                <label for="ck-reward"><input type="checkbox" class="check" id="ck-reward"> 奖励模块</label>
                <!--<label for="ck-rank"><input type="checkbox" class="check" id="ck-rank"> 扫码排名模块</label>-->
                <label for="ck-rule"><input type="checkbox" class="check" id="ck-rule"> 活动说明模块</label>
                <label for="ck-subscribe"><input type="checkbox" class="check" id="ck-subscribe"> 关注公众号模块</label>
            </dd>
        </dl>
        <div class="btn-div">
            <span id="btnSave" class="btn btn-blue noselect">保存配置</span>
        </div>
    </div>
</body>
</html>