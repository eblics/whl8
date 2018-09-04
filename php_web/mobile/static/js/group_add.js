var groupAdd={
	init:function(){
        if(top.location.hash!==''){
            top.common.refreshTitle('修改群组');
        }else{
            common.refreshTitle('创建群组');
        }
		this.event();
	},
	event:function(){
        this.randImg();
        this.uploadImg();
		this.groupName()
        this.btnNext();
        common.noOverScroll('body');
	},
    randImg:function(){
        var _this=this;
        $('#rand').on('tap',function(){
            var max=18;
            var min=1;
            var r = Math.random() * (max - min);
            var num = Math.round(r + min);
            num = Math.max(Math.min(num, max), min);
            $('#groupImg>p').html('<img src="/static/images/groupimg/'+num+'.png"/>');
            _this.imgOk=true;
            _this.check();
        });
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
                    url: "/group/upload",
                    data: {filestr: dataURL},
                    type: 'post',
                    dataType: 'html',
                }).done(function(d){
                    common.unloading();
                    this.imgOk=true;
                    $('#groupImg>p').html('<img src="'+d+'"/>');
                    if(_this.check()){
                        $('#btnNext').removeClass('weui_btn_disabled');
                    }
                    $('.img_cut').hide();
                }).fail(function(d){
                    alert(JSON.stringify(d));
                    common.unloading();
                    common.alert('保存失败，请重试');
                });
            }
        });
        $('#groupImg input').on('change',function(){
            $('.img_cut').show();
        });
        $('#cancelBtn').on('tap',function(){
            $('.img_cut').hide();
        });
    },
    groupName:function(){
        var _this=this;
        _this.nameOk=false;
		$('#groupName').on('focus',function(){
            var curVal=$(this).val();
            var defaultVal='填写群名称（10个字以内）';
            if(curVal==defaultVal){
                $(this).val('');
            }
        }).on('input',function(){
            var curVal=$(this).val();
            if($.trim(curVal)==''){
                _this.nameOk=false;
            }else{
                _this.nameOk=true;
            }
            _this.check();
        }).on('blur',function(){
            var curVal=$(this).val();
            var defaultVal='填写群名称（10个字以内）';
            if($.trim(curVal)==''){
                $(this).val(defaultVal);
                _this.nameOk=false;
            }else{
                _this.nameOk=true;
            }
            _this.check();
        });
	},
    check:function(){
        var _this=this;
        if($('#groupImg>p>img').length>0){
            _this.imgOk=true;
        }
        if($.trim($('#groupName').val())!=''){
            _this.nameOk=true;
        }
        if(_this.nameOk && _this.imgOk){
            $('#btnNext').removeClass('weui_btn_disabled');
            return;
        }
        if(!$('#btnNext').hasClass('weui_btn_disabled')){
            $('#btnNext').addClass('weui_btn_disabled');
        }
    },
    btnNext:function(){
		$('#btnNext').on('tap',function(){
            if($(this).hasClass('weui_btn_disabled')) return;
            var groupName=$.trim($('#groupName').val());
            var groupImg=$.trim($('#groupImg p img').attr('src'));
            if(groupName=='' || groupImg==''){
                common.alert('信息填写不完整');
                return;
            }
            var data={'groupName':groupName,'groupImg':groupImg};
            if(typeof currentGroup!='undefined' && currentGroup.id!=''){
                data.id=currentGroup.id;
            }
            common.loading();
            $.ajax({
                url: '/group/save',
                type: "POST",
                dataType: 'json',
                data: data,
                success: function(d) {
                    if(d.errcode!=0){
                        common.alert('保存失败，请重试');
                    }else{
                        if(top.location.hash!==''){
                            window.history.back(-1);
                        }else{
                            location.href='/group/pwd/'+d.data;
                        }
                    }
                    common.unloading();
                },
                error: function(d) {
                    common.unloading();
                    common.alert('请求失败，请重试');
                }
            });
        });
	},
    
};
$(function(){
	groupAdd.init();
});
