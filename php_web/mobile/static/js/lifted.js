var lifted = {
	init:function(){
		if($('#status').val() == 1){
			$('#lreason').attr("readonly","readonly");
	    	$('#lreason').attr("disabled","disabled");
	    	$('#lname').attr("readonly","readonly");
	    	$('#lname').attr("disabled",true);
	    	$('#lphonenum').attr("readonly","readonly");
	    	$('#lphonenum').attr("disabled",true);
	    	$('.lsubmit').text('审核中 无需再次提交');
	    	$('.lsubmit').removeClass('lifted-sub');
	    	$('.lsubmit').css('background','#9D9D9D');
	    	$('.lifted-qrimg input').remove();
		}
		this.uploadImg();
		this.click();
	},
	uploadImg:function(){
        var _this=this;
        this.imgOk=false;
        $("#clipArea").photoClip({
            width: 200,
            height: 200,
            file: "#file",
            view: "#view",
            ok: "#clipBtn",
            loadStart: function() {
                // $(".photo-clip-rotateLayer").html("<img src='/static/images/loading.gif'/>");
                common.loading();
                console.log("照片读取中");
            },
            loadComplete: function() {
                common.unloading();
                console.log("照片读取完成");
            },
            clipFinish: function(dataURL) {
                common.loading();
                $.ajax({
                    url: "/service/upload",
                    data: {filestr: dataURL},
                    type: 'post',
                    dataType: 'html',
                }).done(function(d){
                    common.unloading();
                    this.imgOk=true;
                    $('#lifted-qrimg>p').html('<img src="'+d+'"/>');
                    // if(_this.check()){
                    //     $('#btnNext').removeClass('weui_btn_disabled');
                    // }
                    $('.img_cut').hide();
                }).fail(function(d){
                    alert(JSON.stringify(d));
                    common.unloading();
                    common.alert('保存失败，请重试');
                });
            }
        });
        $('#lifted-qrimg input').on('change',function(){
            $('.img_cut').show();
        });
        $('#cancelBtn').on('tap',function(){
            $('.img_cut').hide();
        });
    },
    click:function(){
        $('.s-sub').off().on('tap',function(){
            window.location.href = "/service/re_lifted?edit=ok";
            return;
        });
    	$('.lifted-sub').off().on('tap',function(){
	    	var _this = $(this);
            _this.blur();
	    	var lreason = $('#lreason').val();
	    	var lname = $('#lname').val();
	    	var lphonenum = $('#lphonenum').val();
	    	var num = /^1(3[0-9]|4[57]|5[0-35-9]|7[6-8]|8[01235-9])\d{8}$/;
	    	var img=$.trim($('#lifted-qrimg p img').attr('src'));
	    	if(lreason.length <3){
	    		alert('请提交一个适合的申诉理由');
	    		return;
	    	}
            if(lname.length <2){
                alert('请填写您的名字');
                return;
            }
	    	if(!num.test(lphonenum)){
	    		alert('手机号码有误 请重新输入');
	    		return;
	    	}
	    	if(img == ''){
	    		alert('请上传您的二维码图片');
	    		return;
	    	}
	    	_this.remove();
	    	_this.css('background','#818181');
	        $.post('/service/get_lifted',{lreason:lreason,lname:lname,lphonenum:lphonenum,img:img},function(response){
                    // $.post('/service/appeal',function(res){
                    //     if(res.errocode == 0){
                    //         window.location.href = "/service/lifted?234";
                    //         return;
                    //     }
                    // },'json');
	        	alert('提交成功');
                var html = '<div class="lifted-success">提交成功，请等待审核！</div>';
                $('.lifted-content').children().remove();
                $('.lifted-content').append(html);
	        	// $('#lreason').attr("readonly","readonly");
	        	// $('#lreason').attr("disabled","disabled");
	        	// $('#lname').attr("readonly","readonly");
	        	// $('#lname').attr("disabled",true);
	        	// $('#lphonenum').attr("readonly","readonly");
	        	// $('#lphonenum').attr("disabled",true);
	        });
	    });
    }
};
$(function() {
    lifted.init();
});