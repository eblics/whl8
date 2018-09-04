var accumstrategyEdit = {
    init:function(){
        var _this=this;
        _this.valid();
        common.autoHeight();
    },
    valid:function(type){
        var _this=this;
        var num=$(".trlist").length;
        $(".trlist .op .del").hide();
        if(num>1){
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
                common.ajaxSelect('strategyId_'+index,'/accumstrategy/data_rp');
            }
            if($(this).val()==2){
                common.ajaxSelect('strategyId_'+index,'/accumstrategy/data_cards');
            }
            if($(this).val()==3){
                common.ajaxSelect('strategyId_'+index,'/accumstrategy/data_point');
            }
            $(this).off('change').on('change',function() {
                if($(this).val()==0)
                    common.ajaxSelect('strategyId_'+index,'/accumstrategy/data_rp');
                if($(this).val()==2)
                    common.ajaxSelect('strategyId_'+index,'/accumstrategy/data_cards');
                if($(this).val()==3)
                    common.ajaxSelect('strategyId_'+index,'/accumstrategy/data_point');
            });
        });
        $(".trlist .del").off('click').on("click",function(){
            var num=$(".trlist .del").length;
            if(num>1){
                $(this).parent('td').parent('.trlist').remove();
            }else{
                common.alert('子策略不得少于1个');
            }
            var num=$(".trlist").length;
            $(".trlist .op .del").hide();
            if(num>1){
                $(".trlist").eq(num-1).find('.del').show();
            }
        });
    },
    addNew:function(){
        var length=$('.trlist').length;
        var addhtml=$('.trlist').eq(length-1).clone();
        addhtml.find('input[name=start]').val(parseInt($('.trlist').eq(length-1).find('input[name=end]').val()) + 1);
        addhtml.find('input[name=end]').val(parseInt(addhtml.find('input[name=start]').val()) + 99);
        addhtml.find('input[name=chance]').val(1);
        $('.addnew').before(addhtml);
        $('.trlist').eq(length).find('input[name=sonid]').attr('id','sonid_'+length);
        $('.trlist').eq(length).find('select[name=strategyType]').attr('id','strategyType_'+length);
        $('.trlist').eq(length).find('select[name=strategyId]').attr('id','strategyId_'+length);
        $('.trlist').eq(length).find('input[name=chance]').attr('id','chance_'+length);
        $('.trlist').eq(length).find('input[name=start]').attr('id','start_'+length);
        $('.trlist').eq(length).find('input[name=end]').attr('id','end_'+length);
        $(".tip-yellowsimple").remove();
        //为带有valType属性的元素初始化提示信息并注册onblur事件
        $.each($("[valType]"),function(i, n) {
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
                if(name=='strategyType'||name=='strategyId'||name=='start'||name=='end'||name=='chance'){
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
        var checkVal=true;
        var valArr=[];
        $("input.ckval").each(function(){
            var thisVal=Number($(this).val());
            var thisVal2=Number($(this).siblings('input').val());
            var thisName=$(this).attr('name');
            if(thisName=='start' && thisVal>thisVal2){
                checkVal=false;
            }
            if(thisName=='end' && thisVal<thisVal2){
                checkVal=false;
            }
        });
        $("input[name=start]").each(function(i,e){
            valArr[i]={'start':Number($(e).val())};
            $("input[name=end]").each(function(i2,e2){
                if(i==i2){
                    valArr[i].end=Number($(e2).val());
                }
            });
        });
        for(var i=0;i<valArr.length;i++){
            for(var j=0;j<valArr.length;j++){
                if(i==j) continue;
                if(valArr[i].start>=valArr[j].start && valArr[i].start<=valArr[j].end){
                    checkVal=false;
                }
            }
        }
        if(! checkVal){
            common.alert('扫码次数范围填写有误');
            return;
        }
        common.loading();
        $.ajax({
            url:'/accumstrategy/save_bonus', 
            data:data,
            type:'post',
            cache:false,
            dataType:'json',
            success:function(d) {
                common.unloading();
                if(d.errcode==0){
                    common.alert('保存成功',function(d){
                        if(d==1){
                            location.href='/accumstrategy/lists';
                        }
                    });
                }else{
                    common.alert(d.errmsg);
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
    accumstrategyEdit.init();
});