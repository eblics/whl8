var shop_examine = {
    init:function(){
        $('#agree').click(function(){
            var id=$(this).attr('data-id');
            $.post("/shop/examine_agree/"+id,{},function(d){
                common.alert('已同意审批',function(){
                    location.href='/shop/examine';
                });
            });
        });
        $('#reject').click(function(){
            var id=$(this).attr('data-id');
            $.post("/shop/examine_reject/"+id,{},function(d){
                common.alert('已拒绝审批',function(){
                    location.href='/shop/examine';
                });
            });
        });
    }
};
$(function(){
    shop_examine.init();
});