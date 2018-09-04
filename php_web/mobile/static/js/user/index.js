$(function(){
    $('[href]').on('touchend',function(){
        location=$(this).attr('href');
    });
});