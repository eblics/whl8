common.extend({
    load:function(){
        var _this=this;
        
        this.connect('get_addresses_list',{},function(result){
            var list=$('.content');
            result.data.forEach(function(data){
                list.append(_this.createItem(data));
            });
            
            list.find('.address>.info').on('tap',function(){
                _this.redirect('/app/mall/submit.html',{addressid:$(this).closest('.item').attr('addressid')});
            });
            list.find('.edit').on('tap',function(){
                var params={id:$(this).closest('.item').attr('addressid')};
                _this.redirect('/app/mall/edit_address.html',params);
            });
            list.find('.delete').on('tap',function(){
                if(confirm('确定要删除吗？')){
                    var item=$(this).closest('.item');
                    _this.connect('delete_address/'+$(this).closest('.item').attr('addressid'),{},function(result){
                        item.remove();
                    });
                }
            });
            list.find('.default').on('tap',function(){
                if($(this).hasClass('checked')){
                    $('.default').removeClass('checked');
                    _this.connect('default_address',{},function(){});
                }
                else{
                    $('.default').removeClass('checked');
                    $(this).addClass('checked');
                    _this.connect('default_address/'+$(this).closest('.item').attr('addressid'),{},function(){});
                }
            });
        });
        
        $('.add').on('touchend',function(){
            _this.redirect('/app/mall/edit_address.html');
        });
    },
    createItem:function(data){
        return '\
        <div class="item" addressid="' + data.id + '">\
            <div class="address">\
                <span class="info">\
                    <span class="basic">\
                        <span class="name">' + data.receiver + '</span>\
                        <span class="telephone">' + data.phoneNum + '</span>\
                    </span>\
                    <span class="text">' + data.area + data.address + '</span>\
                </span>\
                <span class="edit"></span>\
                <span class="delete"></span>\
            </div>\
            <div class="default ' + (data.isDefault==1?'checked':'') + '">\
                <span class="icon"></span>\
                <span class="info">【默认地址】</span>\
            </div>\
        </div>';
    }
});