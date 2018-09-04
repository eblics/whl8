var goodsEdit = {
	init:function(){
		var _this=this;
        //var desval = desval;//$('#desval').val();
        var ue = UE.getEditor('description',{
            toolbars: [
                ['bold', 'italic', 'blockquote','fontsize', 'simpleupload', 'insertimage','justifyleft','justifyright', 'justifycenter', 'justifyjustify', 'forecolor','imagecenter']
            ],
            wordCount:true,
            initialFrameHeight:300,
            imageScaleEnabled:true,
            elementPathEnabled:false,
            maximumWords:500,
            wordCount:false,
            imageUrlPrefix: httpval
        });
        //console.log(desval);
        //ue.ready(function() {
            //设置编辑器的内容
            //ue.setContent(desval);
        //}); 
        //ajax请求图片数据
        var id = $('#id').val();
        var html = "";
        if(id){
            var data = {id:id};
            $.post('/mall/get_good_images',data,function(r){
                for (var obj in r.data[0]) {
                    $('.addImg').eq(obj).html('<img src="'+r.data[0][obj]+'">');
                }
            });
        }
        common.ajaxSelect('categoryId','/mall/catedata');
        common.uploadInit('uploadImg1','/mall/upload',undefined,function(d){
            $('#uploadImg1View').html('<img src="'+d+'">');
        });
        common.uploadInit('uploadImg2','/mall/upload',undefined,function(d){
            $('#uploadImg2View').html('<img src="'+d+'">');
        });
        common.uploadInit('uploadImg3','/mall/upload',undefined,function(d){
            $('#uploadImg3View').html('<img src="'+d+'">');
        });
        common.uploadInit('uploadImg4','/mall/upload',undefined,function(d){
            $('#uploadImg4View').html('<img src="'+d+'">');
        });
        common.uploadInit('uploadImg5','/mall/upload',undefined,function(d){
            $('#uploadImg5View').html('<img src="'+d+'">');
        });
        _this.action();
	},
    action:function(){
        var _this = this;
        $('.addImg').off().on('click',function(){
            $(this).next('.addImgUp').find('.choose').trigger('click');
        });
        $('.imgclass .addImg').mouseover(function(){
            window.thisvalue = null;
            if($(this).children('img').length>0){
                var thisvalue = {};
                var thisid = $(this).attr('id');
                var thisval = $(this).attr('val');
                var thisup = 'uploadImg'+thisval.toString();
                thisvalue = {thisid:thisid,thisval:thisval,thisup:thisup};
                window.thisvalue = thisvalue;
                var x = $(this).offset();
                $('.hover-panel').css({"top":x.top,"left":x.left,"width":"120px","height":"120px","line-height":"120px","border-radius":"2px"});
                $('.hover-panel').show();
            }
        });
        $('.hover-panel').mouseleave(function(){
            $('.hover-panel').hide();
        });
        $('#thisedit').off().on('click',function(){
            $('#'+window.thisvalue.thisid).next('.addImgUp').find('.choose').trigger('click');
        });
        $('#thisdel').off().on('click',function(){
            // var html = '<div class="addImgUp" style="display:none;"><div class="hls-upload textarea"><div class="img"></div><div class="choose noselect">选择</div><input type="hidden" value="" name="'+window.thisvalue.thisup+'"></div><input id="'+window.thisvalue.thisup+'" class="js-upload" type="file" edit-value=""></div>';
            $('#'+window.thisvalue.thisid).children('img').remove();
            // $('#'+window.thisvalue.thisid).next('.addImgUp').remove();
            // $('#'+window.thisvalue.thisid).after(html);
            // $('#'+window.thisvalue.thisid).next('.addImgUp').find('.choose').trigger('click');
            $(this).parent().hide();
        });
        _this.validate();
    },
    validate:function(){
        var _this=this;
        $("#btnSave").bind("click",function(){
            if(beforeSubmitAct()){
                _this.submit();
            }
        });

    },
    submit:function(){
        var data = {};
        var goodsName = $('#goodsName').val();
        var oPrice = $('#oPrice').val();
        var price = $('#price').val();
        var description = $('#description').val();
        $('form input,form textarea,form select,form script').each(function(e){
            var name = $(this).attr('name');
            var val = $(this).val();
            if($.trim(name) != null){
                data[name] = val;
            }
        });

        // --------------------------------------
        // Added by shizq - begin
        data.viralGoods = 0;
        if ($('#viralGoods').prop('checked')) {
            data.viralGoods = 1;
        }
        data.exchangeType = 0;
        // 乐券兑换已取消
        // if ($('#goodsTypeCard').prop('checked')) {
        //     data.exchangeType = 1;
        // }
        data.createOrder = 0;
        if ($('#createOrder').prop('checked')) {
            data.createOrder = 1;
        }
        // --------------------------------------

        var array = [];
        $('.addImg img').each(function(i){
            // var array = [];
            var url = $(this).attr('src');
            array.push(url);
        });
        data.arraydata = array;
        if(data.arraydata.length == 0){
            common.alert("请上传至少一张图片");
            return;
        }
        // console.log(data);
        // return;
        $.post('/mall/get_update/' + data.id,data,function(res){
            if(res.errcode == 0){
                common.alert('提交成功',function(e){
                    if(e == 1){
                        window.location.href = "/mall/goods";
                    }
                });
            }else{
                common.alert(res.errmsg,function(e){
                    if(e == 1){
                        window.location.href = "/mall/goods";
                    }
                });
            }
        });
    }
};
$(function(){
	goodsEdit.init();
});