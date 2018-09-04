//格式化字符串
String.prototype.format = function() {
    var formatted = this;
    for (var i = 0; i < arguments.length; i++) {
        var regexp = new RegExp('\\{'+i+'\\}', 'gi');
        formatted = formatted.replace(regexp, arguments[i]);
    }
    return formatted;
};
var common={
        init:function(){
            var _this=this;
            _this.uploadPlugin();
            _this.autoHeight();
            $(window).resize(_this.autoHeight);
        },
        autoHeight:function(){
            $('.leftbar,.rightmain').css('min-height','auto');
            var winH=$(window).height();
            var left=$('.leftbar');
            var right=$('.rightmain');
            if(left.length>0 && right.length>0){
                var leftH=left.height()-30;
                var rightH=right.height();
                var headH=$('.head').height();
                var footH=$('.foot').height();
                var maxH=winH-headH-footH-40-80;
                if(maxH>rightH && maxH>leftH){
                    $('.rightmain').css('min-height',maxH);
                    $('.leftbar').css('min-height',maxH+30);
                }else{
                    if(leftH>=rightH){
                        $('.rightmain').css('min-height',leftH);
                    }else{
                        $('.leftbar').css('min-height',rightH+30);
                    }
                }
            }
        },
        loading:function(){
            var num=$('.loading').length;
            if(num==0){
                var html='<div class="loading"><div class="layer"></div><div class="gif"></div></div>';
                $('body').append(html);
            }
            $('.loading').show();
        },
        unloading:function(){
            $('.loading').hide();
        },
        alert:function(txt,callback){
            $('.alert').remove();
            var html='<div class="alert" style="display:none"><div class="layer"></div><dl class="box"><dt>提示</dt><dd><div class="condiv">'+txt+'</div><div class="btndiv"><span class="btn btn-blue">确定</span></div></dd></dl></div>';
            $('body').append(html);
            $('.alert').fadeIn();
            $('.alert .btn').click(function(){
                $('.alert').remove();
                if(typeof callback!='undefined'){
                    callback('1');
                }
            });
            $('.alert').show();
            $('input').blur();
        },
        confirm:function(txt,confirmCallback){
            $('.confirm').remove();
            var html='<div class="confirm" style="display:none"><div class="layer"></div><dl class="box"><dt>提示</dt><dd><div class="condiv">'+txt+'</div><div class="btndiv"><span class="btn btn-blue" data="1">确定</span> <span class="btn btn-gray" data="0">取消</span></div></dd></dl></div>';
            $('body').append(html);
            $('.confirm').fadeIn();
            $('.confirm .btn').click(function(){
                var num=Number($(this).attr('data'));
                confirmCallback(num);
                $('.confirm').remove();
            });
            $('.confirm').show();
        },
        refuseConfirm: function(title, name1, value1, name2, name3, txt, text, text2, confirmCallback) {
            $('.refuseConfirm').remove();
            var html = '<div class="refuseConfirm" style="display:none"><div class="layer"></div><dl class="box"><dt></dt><dd><div class="condiv"><div class="s1">'+name1+'：<span class="span1"><input type="text" id="textid" value="'+value1+'"></span></div><div class="s2">'+name2+'：<span class="span2"><textarea cols="2" rows="2">'+text+'</textarea></span></div><div class="s3">'+name3+'：<span class="span3"><textarea cols="2" rows="2">'+text2+'</textarea></span></div></div><div class="btndiv"><span class="btn btn-blue" data="3">拉黑</span> <span class="btn btn-blue" data="1">驳回</span> <span class="btn btn-gray" data="0">取消</span></div></dd></dl></div>';
            $('body').append(html);
            if(txt == 1){
                $('.refuseConfirm dl dt').text(title);
            }else{
                $('textarea').val(txt);
                $('.refuseConfirm dl dt').text(title);
                $('.refuseConfirm .btn-blue').hide();
                $('.refuseConfirm .btn-gray').text('关闭');
                $('.refuseConfirm textarea').attr("disabled","disabled");
            }
            $('.refuseConfirm').fadeIn();
            $('.refuseConfirm .btn').click(function() {
                var num = Number($(this).attr('data'));
                var textarea = $('textarea').eq(0).val();
                var mark = $('textarea').eq(1).val();
                confirmCallback(num,textarea,mark);
                $('.refuseConfirm').remove();
            });
            $('.refuseConfirm').show();
        },
        refuseSec: function(title, name1, value1, name2, name3, txt, text, text2, confirmCallback) {
            $('.refuseSec').remove();
            var html = '<div class="refuseSec" style="display:none"><div class="layer"></div><dl class="box"><dt></dt><dd><div class="condiv"><div class="s1">'+name1+'：<span class="span1"><input type="text" id="textid" value="'+value1+'"></span><span></span></div><div class="s2">'+name2+'：<span class="span2"><textarea cols="2" rows="2">'+text+'</textarea></span><span class="btn btn-orange btn-10" data="1">驳回</span></div><div class="s3">'+name3+'：<span class="span3"><textarea cols="2" rows="2">'+text2+'</textarea></span><span class="btn btn-red btn-10" data="3">拉黑</span></div></div><div class="btndiv">  <span class="btn btn-gray" data="0">取消</span></div></dd></dl></div>';
            $('body').append(html);
            if(txt == 1){
                $('.refuseSec dl dt').text(title);
            }else{
                $('textarea').val(txt);
                $('.refuseSec dl dt').text(title);
                $('.refuseSec .btn-blue').hide();
                $('.refuseSec .btn-gray').text('关闭');
                $('.refuseSec textarea').attr("disabled","disabled");
            }
            $('.refuseSec').fadeIn();
            $('.refuseSec .btn').click(function() {
                var num = Number($(this).attr('data'));
                var textarea = $('textarea').eq(0).val();
                var mark = $('textarea').eq(1).val();
                confirmCallback(num,textarea,mark);
                $('.refuseSec').remove();
            });
            $('.refuseSec').show();
        },
        refuseConfirmBro: function(title, name1, value1, name2, txt, text, confirmCallback) {
            $('.refuseConfirmBro').remove();
            var html = '<div class="refuseConfirmBro" style="display:none"><div class="layer"></div><dl class="box"><dt></dt><dd><div class="condiv"><div class="s1">'+name1+'：<span class="span1"><input type="text" id="textid" value="'+value1+'"></span></div><div class="s2">'+name2+'：<span class="span2"><textarea cols="2" rows="2">'+text+'</textarea></span></div></div><div class="btndiv"><span class="btn btn-blue" data="1">保存</span> <span class="btn btn-gray" data="0">取消</span></div></dd></dl></div>';
            $('body').append(html);
            if(txt == 1){
                $('.refuseConfirmBro dl dt').text(title);
            }else{
                $('textarea').val(txt);
                $('.refuseConfirmBro dl dt').text(title);
                $('.refuseConfirmBro .btn-blue').hide();
                $('.refuseConfirmBro .btn-gray').text('关闭');
                $('.refuseConfirmBro textarea').attr("disabled","disabled");
            }
            $('.refuseConfirmBro').fadeIn();
            $('.refuseConfirmBro .btn').click(function() {
                var num = Number($(this).attr('data'));
                var textarea = $('textarea').val();
                confirmCallback(num,textarea);
                $('.refuseConfirmBro').remove();
            });
            $('.refuseConfirmBro').show();
        },
        selectConfirm:function(txt,confirmCallback){
            $('.sconfirm').remove();
            var html='<div class="sconfirm" style="display:none"><div class="layer"></div><dl class="box"><dt>提示</dt><dd><div class="clodiv">×</div><div class="condiv">'+txt+'</div><div class="btndiv"><span class="btn btn-blue" data="1">重置消费者</span> <span class="btn btn-gray" data="0">重置供应链</span></div></dd></dl></div>';
            $('body').append(html);
            $('.sconfirm').fadeIn();
            $('.sconfirm .btn').click(function(){
                var num=Number($(this).attr('data'));
                confirmCallback(num);
                $('.sconfirm').remove();
            });
            $('.clodiv').click(function(){
                $('.sconfirm').remove();
            });
            $('.sconfirm').show();
        },
        formConfirm:function(title,domid,confirmCallback){
            $('.confirm-form').remove();
            var content=$('#'+domid).html();
            var html='<div class="confirm-form" style="display:none"><div class="layer"></div><dl class="box"><dt>'+title+'</dt><dd><div class="condiv">'+content+'</div><div class="btndiv"><span class="btn btn-blue" data="1">确定</span> <span class="btn btn-gray" data="0">取消</span></div></dd></dl></div>';
            $('body').append(html);
            $('.confirm-form').fadeIn();
            $('.confirm-form .btn').click(function(){
                if( $('.confirm-form #checkReason').val().length > 0){
                    var num=Number($(this).attr('data'));
                    confirmCallback(num);
                    $('.confirm-form').remove();
                } 
                if($(this).hasClass('btn-gray')){
                    var num=Number($(this).attr('data'));
                    confirmCallback(num);
                    $('.confirm-form').remove();
                }
            });
            $('.confirm-form').show();
        },
        ajaxSelect:function(domid,url){
            $.post(url,{},function(d){
                var data=d.data;
                var html='';
                $(data).each(function(index,el) {
                    var nbsp='';
                    if(/webkit/.test(navigator.userAgent.toLowerCase())){
                        for(var i=1;i<el.level;i++){
                            nbsp+='　　　';
                        }
                    }
                    html+='<option value="'+el.id+'" class="txti'+el.level+'">'+nbsp+el.name+'</option>';
                });
                $('#'+domid).html(html);
                var editVal=$('#'+domid).attr('edit-value');
                if(editVal){
                    $('#'+domid).val(editVal);
                }else{
                    $('#'+domid+' option:first').prop("selected","selected"); 
                }
            },'json');
        },
        ajaxSelectSub:function(domid,url,subDomid,subUrl){
            $('#'+domid+',#'+subDomid).attr('disabled','disabled');
            $.post(url,{},function(d){
                $.post(subUrl,{},function(v){
                    //父数据
                    window['select_data_'+domid]=d.data;
                    var html='';
                    $(d.data).each(function(index,el) {
                        var nbsp='';
                        if(/webkit/.test(navigator.userAgent.toLowerCase())){
                            for(var i=1;i<el.level;i++){
                                nbsp+='　　　';
                            }
                        }
                        html+='<option value="'+el.id+'" class="txti'+el.level+'">'+nbsp+el.name+'</option>';
                    });
                    $('#'+domid).html(html);
                    var editVal=$('#'+domid).attr('edit-value');
                    if(editVal){
                        $('#'+domid).val(editVal);
                    }else{
                        $('#'+domid+' option:first').prop("selected","selected"); 
                    }
                    setTimeout(function(){
                        $('#'+domid).trigger('change');
                    },10);
                    //子数据
                    window['select_data_'+subDomid]=v.data;
                    $('#'+domid).on('change',function(){
                        var selId=$('#'+domid).val();
                        var newData=[];
                        $(window['select_data_'+subDomid]).each(function(index,el){
                            if(el.categoryId==selId){
                                newData.push(el);
                            }
                        });
                        var html='';
                        $(newData).each(function(index,el) {
                            html+='<option value="'+el.id+'" class="txti'+el.level+'">'+el.name+'</option>';
                        });
                        $('#'+subDomid).html(html);
                        var editVal=$('#'+subDomid).attr('edit-value');
                        if(editVal){
                            $('#'+subDomid).val(editVal);
                        }else{
                            $('#'+subDomid+' option:first').prop("selected","selected"); 
                        }
                    });
                    $('#'+domid+',#'+subDomid).removeAttr('disabled');
                },'json');
            },'json');
        },
        uploadInit:function(fileId,upUrl,isatt){
            $('#'+fileId).on('change',function(){
                var _this=$(this);
                var feild=$(this);
                if(typeof isatt!='undefined'){
                    function callbackAtt(d){
                        var x=$.trim(d);
                        if(x!=''){
                            if(x=='toolarge'){
                                common.alert('上传附件大小超出了允许范围！');
                                return;
                            }
                            if(x=='exterror'){
                                common.alert('上传文件格式不正确！');
                                return;
                            }
                            var img='<a class="att" href="'+d+'" target=_blank><img src="/static/images/att.jpg" width=100% height=100% /></a>';
                            _this.siblings('.hls-upload').children('.img').html(img);
                            _this.siblings('.hls-upload').children('input[type=hidden]').val(d);
                        }else{
                            common.alert('失败,请重试！');
                        }
                    }
                    common.ajaxUpload(feild,upUrl,callbackAtt);
                }else{
                    function callback(d){
                        var x=$.trim(d);
                        if(x!=''){ 
                            if(x=='toolarge'){
                                common.alert('上传图片大小超出了允许范围！');
                                return;
                            }
                            if(x=='exterror'){
                                common.alert('上传文件格式不正确！');
                                return;
                            }
                            var img='<img src="'+d+'" width=100% height=100% />';
                            _this.siblings('.hls-upload').children('.img').html(img);
                            _this.siblings('.hls-upload').children('input[type=hidden]').val(d);
                        }else{
                            common.alert('失败,请重试！');
                        }
                    }
                    common.ajaxUpload(feild,upUrl,callback);
                }
            });
        },
        uploadPlugin:function(){
            $('.js-upload,.js-upload-att').each(function(){
                var name=$(this).attr('name');
                var editVal=$(this).attr('edit-value');
                var img='';
                if(editVal) img='<img src="'+editVal+'" width=100% height=100% />';
                if($(this).hasClass('js-upload-att')){
                    if(editVal){
                        img='<a class="att" href="'+editVal+'" target=_blank><img src="/static/images/att.jpg" width=100% height=100% /></a>';
                    }else{
                        img='<div class="att"></div>';
                    }
                }
                var html='<div class="hls-upload textarea"><div class="img">'+img+'</div><div class="choose noselect">选择</div><input type="hidden" name="'+name+'" value="'+(editVal?editVal:"")+'" /></div>';
                $(html).insertBefore($(this));
                $(this).removeAttr('name');
            });
            $('.hls-upload .choose').off().on('click',function(){
                $(this).parent('.hls-upload').siblings('.js-upload').trigger('click');
                $(this).parent('.hls-upload').siblings('.js-upload-att').trigger('click');
            });
        },
        ajaxUpload:function(feild,upUrl,callback){
            common.loading();
            var fd = new FormData();
            var fdIf = new FormData();
            var file=feild.get(0).files[0];
            var name=file.name;
            var ext = name.substr(name.lastIndexOf(".")+1);
            var size=file.size/1024;
            fd.append("userfile", 1);
            fd.append("userfile", file);
            fd.append("fileSize", size);
            fd.append("fileExt", ext);
            fdIf.append("userfile", 1);
            fdIf.append("userfile", 'if');
            fdIf.append("fileSize", size);
            fdIf.append("fileExt", ext);
            function dopost(data){
                $.ajax({
                    url: upUrl,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data:data,
                    success: function(d){
                        feild.val('');
                        common.unloading();
                        if(d=='ifok'){
                            dopost(fd);
                        }else{
                            callback(d);
                        }
                    },
                    error : function() {
                        common.unloading();
                        common.alert('请求失败');
                    }
                });
            }
            dopost(fdIf);
            
        },
    transDialog: function(callback) {
        $('.transDialog').remove();
        $('body').css('overflow','hidden');
        var html = '<div class="transDialog" style="display:none"><div class="layer"></div><div class="con"></div><div class="close"></div></div>';
        $('body').append(html);
        $('.transDialog').fadeIn('fast');
        $('.transDialog .layer,.transDialog .close').click(function() {
            $('.transDialog').fadeOut(function(){
                $('.transDialog').remove();
                $('body').css('overflow','visible');
            });
        });
        callback(function(d){
            $('.transDialog .con').html(d);
        });
    }
};
$(function(){
    common.init();
});