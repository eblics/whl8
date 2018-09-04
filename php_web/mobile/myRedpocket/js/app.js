/**
 * Created by Vee on 2017/5/5.
 */
// $(".tab li").mouseenter(function () {
//     var $this = $(this),
//         index = $this.index();
//     $this.addClass("active").siblings("li").removeClass("active");
//
//     $(".products div").eq(index).addClass("selected").siblings("div").removeClass("selected");
// });


// $('.redPocket div').on('tap',function () {
//     var $this = $(this),
//         index = $this.index();
//     $this.addClass("active").siblings("div").removeClass("active");
//     // console.log(index);
//     // console.log($(".tap").eq(index));
//     console.log($(".tap div").eq(0));
//     $(".tap div").eq(index).removeClass("hidden").siblings("div").addClass("hidden");
// })


//TAB栏
$(function(){
    $('.redPocket div').on('tap',function () {
        var $this = $(this),
            index = $this.index();
        $this.addClass("active").siblings("div").removeClass("active");
        // console.log(index);
        // console.log($(".tap").eq(index));
        console.log($(".tap div").eq(0));
        $("div .tap").eq(index).removeClass("hidden").siblings("div").addClass("hidden");
    })
});