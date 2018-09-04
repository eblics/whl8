var settingGuard = {
	init:function(){
        this.loadData();
        this.btnEvent();
        this.validator();
	},
    loadData:function(index){
        if(typeof index=='undefined'){
            dataFreq();
            return;
        }
        switch(index){
            case 0:
                dataFreq();
            break;
        }
        //扫码频率数据
        function dataFreq(){
            $.post("/setting/data_scan_freq",{},function(d){
                if(typeof d.errcode!='undefined'){
                    if(d.errcode!=0){
                        $('#freq .tip').html(d.errmsg);
                    }else{
                        var  data= d.data;
                        $('#freq input[name=times]').val(data.times);
                        $('#freq select[name=unit]').val(data.unit);
                    }
                }
            },'json');
        }
    },
    btnEvent:function(){
        var _this=this;
        // $('.tab li:eq(0)').on('click',function(){
        //    $(this).addClass('current').siblings('li').removeClass('current');
        //    $('.tab_con').eq($(this).index()).show().siblings('.tab_con').hide();
        // });
        $('.tab li:eq(1)').on('click',function(){
           window.location.href='/setting/warning';
        });
        $('.tab li:eq(2)').on('click',function(){
           window.location.href='/setting/user_scan';
        });
        $('.btnsave').on('click',function(){
            var tabindex=$('.tab li.current').index();
            _this.saveData(tabindex);
        });
    },
    validator:function(){
        var _this=this;
        $('#freq input[name=times]').on('keydown keyup paste blur focus',function(){
            var val=$(this).val();
            if(isNaN(val) || val.indexOf('.')!=-1 || val.indexOf(' ')!=-1){
                $(this).val('');
            }
        });
    },
	saveData:function(index){
		if(typeof index=='undefined'){
            saveFreq();
            return;
        }
        switch(index){
            case 0:
                saveFreq();
            break;
        }
        //扫码频率数据
        function saveFreq(){
            var data={};
            $('#freq input,#freq textarea,#freq select').each(function(e){
                var name=$(this).attr('name');
                var val=$(this).val();
                if($.trim(name)!=''){
                    data[name]=val;
                }
            });
            if(data.times=='' || data.unit==''){
                common.alert('数据填写不完整');
                return;
            }
            $.post("/setting/save_scan_freq",data,function(d){
                if(typeof d.errcode!='undefined'){
                    if(d.errcode!=0){
                        common.alert(d.errmsg);
                    }else{
                        $('#freq .tip').html('');
                        $('#freq input[name=id]').val(d.data);
                        common.alert('保存成功');
                    }
                }
            },'json');
        }
	}
};
$(function(){
	settingGuard.init();
});