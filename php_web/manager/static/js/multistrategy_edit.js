var multistrategyEdit = {
	init:function(){
		var _this=this;
        _this.valid();
        common.autoHeight();
	},
    valid:function(type){
        var _this=this;
        var num=$(".trlist").length;
        $(".trlist .op .del").hide();
        if(num>2){
            $(".trlist").eq(num-1).find('.del').show();
        }
        $("#btnSave").off('click').on("click",function(){
			if(beforeSubmitAct()){
                _this.submit();
			}
		});
        $("#btnAdd").off('click').on("click",function(){
			_this.addNew();
            common.autoHeight();
		});
        $('select[name=strategyType]').each(function(index,elem) {
            if(type=='add'){
                if(index!=num-1){
                    return true;
                }
            }
            if($(this).attr('edit-value')!=''){
                $(this).val($(this).attr('edit-value'));
            }
            if($(this).val()==0){
                common.ajaxSelect('strategyId_'+index,'/multistrategy/data_rp');
            }
            if($(this).val()==2){
                common.ajaxSelect('strategyId_'+index,'/multistrategy/data_cards');
            }
            if($(this).val()==3){
                common.ajaxSelect('strategyId_'+index,'/multistrategy/data_point');
            }
            $(this).off('change').on('change',function() {
                if($(this).val()==0)
                    common.ajaxSelect('strategyId_'+index,'/multistrategy/data_rp');
                if($(this).val()==2)
                    common.ajaxSelect('strategyId_'+index,'/multistrategy/data_cards');
                if($(this).val()==3)
                    common.ajaxSelect('strategyId_'+index,'/multistrategy/data_point');
            });
        });
        $(".trlist .del").off('click').on("click",function(){
			var num=$(".trlist .del").length;
            if(num>2){
                $(this).parent('td').parent('.trlist').remove();
            }else{
                common.alert('子策略不得少于2个');
            }
            var num=$(".trlist").length;
            $(".trlist .op .del").hide();
            if(num>2){
                $(".trlist").eq(num-1).find('.del').show();
            }
		});
    },
    addNew:function(){
        var length=$('.trlist').length;
        var addhtml=$('.trlist').eq(length-1).clone();
        $('.addnew').before(addhtml);
        $('.trlist').eq(length).find('input[name=sonid]').attr('id','sonid_'+length);
        $('.trlist').eq(length).find('select[name=strategyType]').attr('id','strategyType_'+length);
        $('.trlist').eq(length).find('select[name=strategyId]').attr('id','strategyId_'+length);
        $('.trlist').eq(length).find('input[name=weight]').attr('id','weight_'+length);
        //为带有valType属性的元素初始化提示信息并注册onblur事件
        $.each($("[valType]"),function(i, n) {
            if(i!=length+1) return true;
            $(n).poshytip({
                    className: 'tip-yellowsimple',
                    content: $(n).attr('msg'),
                    showOn: 'none',
                    alignTo: 'target',
                    alignX: 'right',
                    alignY: 'center',
                    offsetX: 5,
                    offsetY: 10
                });
            $(n).unbind('blur').bind('blur',validateBefore);
        });
        this.valid('add');
    },
    submit:function(){
        var data={};
        $('form input,form textarea,form select').each(function(i,n){
            var name=$(this).attr('name');
            var val=$(this).val();
            if($.trim(name)!=''){
                if(name=='strategyType'||name=='strategyId'||name=='weight'){
                    if(typeof data[name]=='undefined'){
                        data[name]=[];
                    }
                    data[name].push(val);
                }else{
                    data[name]=val;
                }
            }
        });
        $('form input[type=radio]').each(function(e){
            var name=$(this).attr('name');
            var val=$(this).val();
            if($.trim(name)!=''){
                if($(this).is(':checked')){
                    data[name]=val;
                }
            }
        });
        common.loading();
        $.ajax( {    
            url:'/multistrategy/save/', 
            data:data,
            type:'post',
            cache:false,
            dataType:'json',
            success:function(d) {
                common.unloading();
                if(d.errorCode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/multistrategy/lists';
                        }
                    });
                }else{
                    common.alert(d.errorMsg);
                }
            },
            error : function() {
                common.unloading();
                common.alert('请求失败');
            } 
        });
        
    }
};
$(function(){
	multistrategyEdit.init();
});