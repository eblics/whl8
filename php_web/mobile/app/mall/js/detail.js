common.extend({
    load:function(){
        var _this=this;
        var goodid=this.getQueryString('id');
        if(goodid==null){
            this.error('没有这个商品');
            return;
        }
        
        this.connect('get_good/'+goodid,{},function(result){
            if(result.data==null){
                _this.error('没有这个商品');
                return;
            }
            
            var list=$('.swiper-container>.swiper-wrapper');
            if(result.data.images.length==0){
                list.append('<div class="swiper-slide" style="background-image:url(/app/mall/images/noimage.png)"></div>');
            }
            else{
                result.data.images.forEach(function(path){
                    list.append('<div class="swiper-slide" style="background-image:url(' + path + ')"></div>');
                });
            }
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',//分页器
                paginationClickable: true,//点击分页器圆点切换图片
                spaceBetween: 0,
                centeredSlides: true,
                autoplay: 6000,
                loop:true,
                autoplayDisableOnInteraction: false,
                effect:'fade',
                fade: {
                    crossFade: true
                }
            });
            
            $('.name').text(result.data.goodsName);
            $('.price>.point').text(result.data.price);
            if(result.data.description!=null && result.data.description!=''){
                $('.description>.text').html(result.data.description.replace(/\n/g,'<br/>'));
                $('.description').show();
            }
            
            if(result.data.isViral!=1){
                $('.buttons>.hollow').show().on('tap',function(event){
                    event.stopPropagation();
                    shopCart.add(goodid);
                    _this.redirect('/app/mall/trolley.html');
                });
            }
            $('.buttons>.solid').on('tap',function(event){
                event.stopPropagation();
                var data=[{id:goodid,amount:1}];
                orderList.set(data);
                _this.redirect('/app/mall/submit.html');
            });
        });
    }
});