var shop = {

    init: function() {
        $('.edit').click(function(){
            var shopid=$(this).closest('.item').attr('shopid');
            location.href='/shop/shop_detail/'+shopid;
        });
        $('.delete').click(function(){
            if(confirm('确定要删除吗？')){
                var shopid=$(this).closest('.item').attr('shopid');
                $.post('/shop/delete/'+shopid,{},function(d){
                    location='/shop/shop_list';
                });
            }
        });
        $('.revoke').click(function(){
            if(confirm('确定要撤回吗？')){
                var shopid=$(this).closest('.item').attr('shopid');
                $.post('/shop/revoke/'+shopid,{},function(d){
                    location='/shop/shop_list';
                });
            }
        });
        $('.add').on('touchend',function(){
            location.href='/shop/shop_detail';
        });
    },
};

$(function() {
    shop.init();
});