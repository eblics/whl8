(function($){
    $.fn.areaSelect = function() {
        var obj=$(this);
        obj.attr('readonly','readonly');
        var defaultVal={'code':obj.val(),'name':''};
        var getDefault=false;
        setTimeout(function(){
            $(window.areaSelectData).each(function(i,e){
                if(defaultVal.code=='') return false;
                if(getDefault) return false;
                if(defaultVal.code==e.code) {
                    defaultVal.name=e.name;
                    getDefault=true;
                    return false;
                }
                $(e.children).each(function(i2,e2){
                    if(getDefault) return false;
                    if(defaultVal.code==e2.code) {
                        defaultVal.name=e.name+'-'+e2.name;
                        getDefault=true;
                        return false;
                    }
                    $(e2.children).each(function(i3,e3){
                        if(getDefault) return false;
                        if(defaultVal.code==e3.code) {
                            defaultVal.name=e.name+'-'+e2.name+'-'+e3.name;
                            getDefault=true;
                            return false;
                        }
                    });
                });
            });
            obj.val(defaultVal.name).after('<input type="hidden" value="'+defaultVal.code+'" name="'+obj.attr('name')+'" />').removeAttr('name');
            obj.on('click',function(){
                window.currentAreaSelect=$(this);
                var css={
                    'top':$(this).position().top+$(this).height()+14,
                    'left':$(this).position().left,
                    'width':336,
                    'height':400
                };
                $('#areaSelectHtml').css(css);
                $('#areaSelectHtml,#areaSelectHtmlLayer').show();
            });
        },10);
    };
})(jQuery);
$(function(){
    $('body').append("<style>.area-select{cursor:pointer;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAWCAYAAAChWZ5EAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6M0Y3MjkwNjhCODY4MTFFNkJGOENENTUzQjE4ODYxNDUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6M0Y3MjkwNjlCODY4MTFFNkJGOENENTUzQjE4ODYxNDUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozRjcyOTA2NkI4NjgxMUU2QkY4Q0Q1NTNCMTg4NjE0NSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDozRjcyOTA2N0I4NjgxMUU2QkY4Q0Q1NTNCMTg4NjE0NSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PuSD3+YAAAFkSURBVHjaxFWBjcMgDHSrLsAKXSErtCN8RoARmhHSEZIRmhE6Q1foChkhb/7NC1k2bz5IfxJSggJ3du7g4L0HxIjjBt8IOGYwYpom66cQQog8C655pblj3CMjB3rvoDGQ/EY8T3y+5AIksmdLEUj4QV2OcCTCJwFXHCtb46gTrgF5R3txdEnAqojoqBN7yGMBD6GQF/ogJAFfE2Q+SeW0Q0Ms4Mzm3lQw5AIiFkWEZya1Vi+ZOXa5x+pXSQBQ/KQIjiSkxvHS9yGPoCSgdA7Eii6Vjs8xIPnCJ4/KPgP5guNRimfB8TOS36U1moCUjLcQT8nVJsfXCPgxjBDPM7nb1Tq+VkCKZ6/Ec6x1vIQDXUa/wSv/9k5Ekul6yXQcJ2OyZmovPw+082GwkFt+AU+G5ZpWHb9XQCmeJse3EKDF0+T4FgK0eJoc30pAiue1dMZbcYK/I13hzup48RzYtg3+E58CDACmLoAlxvq4lQAAAABJRU5ErkJggg==) no-repeat 98% 50%; background-size:auto 25%;}#areaSelectHtml{display:none;position:absolute;z-index:100;overflow:auto;border:1px solid #666;background:#fff;padding:5px;cursor:default;}#areaSelectHtmlLayer{display:none;position:fixed;top:0;right:0;bottom:0;left:0;z-index:99;}#areaSelectHtml dt,#areaSelectHtml dd{line-height:30px;}#areaSelectHtml dt{border-bottom:1px solid #ddd;border-radius:5px;}#areaSelectHtml>dl>dt:hover,#areaSelectHtml>dl>dd>dl>dt:hover,#areaSelectHtml>dl>dd>dl>dd:hover{background:#ddd;border-radius:5px;}#areaSelectHtml>dl>dt{padding-left:20px;}#areaSelectHtml>dl>dd>dl>dt{padding-left:40px;display:none;}#areaSelectHtml>dl>dd>dl>dd{padding-left:60px;display:none;}#areaSelectHtml .cbtn{height:26px;line-height:26px;padding:0 10px;margin:2px;background:#fff;cursor:pointer;border-radius:5px;float:right;}#areaSelectHtml>dl>dt:before,#areaSelectHtml>dl>dd>dl>dt:before,#areaSelectHtml>dl>dd>dl>dd:before{position:absolute;left:12px;width:20px;content:'＞';color:#999;}#areaSelectHtml>dl>dd>dl>dt:before{left:32px;}#areaSelectHtml>dl>dd>dl>dd:before{left:52px;}#areaSelectHtml>dl>dt.open:before,#areaSelectHtml>dl>dd>dl>dt.open:before{position:absolute;left:12px;width:20px;content:'∨';color:#999;}#areaSelectHtml>dl>dd>dl>dt.open:before{left:32px;}</style>");
    $.ajaxSettings.async = false;
    $.getJSON('/static/js/jquery.areaselect.json?v=' + new Date().getTime(),function(data){
        window.areaSelectData=[];
        window.areaSelectHtml='<div id="areaSelectHtml">';
        $(data).each(function(i,e){
            if(e.level==1){
                window.areaSelectData.push(e);
            }
        });
        $(window.areaSelectData).each(function(i,e){
            e.children=[];
            $(data).each(function(i2,e2){
                if(e2.level!=2) return true;
                if(e2.level==2 && e2.code.substr(0,2)==e.code.substr(0,2)){
                    e.children.push(e2);
                }
            });
        });
        $(window.areaSelectData).each(function(i,e){
            window.areaSelectHtml+='<dl><dt data="'+e.code+'" level=1>'+e.name+'</dt><dd>';
            $(e.children).each(function(i2,e2){
                window.areaSelectHtml+='<dl><dt data="'+e2.code+'" level=2>'+e2.name+'</dt>';
                e2.children=[];
                $(data).each(function(i3,e3){
                    if(e3.level!=3) return true;
                    if(e3.level==3 && e3.code.substr(0,4)==e2.code.substr(0,4)){
                        e2.children.push(e3);
                        window.areaSelectHtml+='<dd data="'+e3.code+'" level=3>'+e3.name+'</dd>';
                    }
                });
                window.areaSelectHtml+='</dl>';
            });
            window.areaSelectHtml+='</dd></dl>';
        });
        window.areaSelectHtml+='</div><div id="areaSelectHtmlLayer"></div>';
        $('body').append(window.areaSelectHtml);
        $('#areaSelectHtmlLayer').on('click',function(){
            $('#areaSelectHtml,#areaSelectHtmlLayer').hide();
        });
        $('#areaSelectHtml>dl>dt,#areaSelectHtml>dl>dd>dl>dt,#areaSelectHtml>dl>dd>dl>dd').on('mouseenter',function(){
            $(this).append('<span class="cbtn">选择</span>');
            $(this).find('.cbtn').on('click',function(){
                $('#areaSelectHtmlLayer').trigger('click');
                var thisLevel=$(this).parent().attr('level');
                var thisFullName='';
                if(thisLevel==1){
                    thisFullName=$(this).html('').parent().text();
                }
                if(thisLevel==2){
                    thisFullName=$(this).parent().parent().parent().siblings('dt').text()+'-'+$(this).html('').parent().text();
                }
                if(thisLevel==3){
                    thisFullName=$(this).parent().parent().parent().siblings('dt').text()+'-'+$(this).parent().siblings('dt').text()+'-'+$(this).html('').parent().text();
                }
                window.currentAreaSelect.val(thisFullName);
                window.currentAreaSelect.siblings('input[type=hidden]').val($(this).parent().attr('data'));
            });
        }).on('mouseleave',function(){
            $(this).find('.cbtn').off().remove();
        });
        $('#areaSelectHtml>dl>dt').on('click',function(){
            if($(this).hasClass('open')){
                $(this).removeClass('open');
                $(this).siblings('dd').find('dt,dd').removeClass('open').hide();
            }else{
                $(this).addClass('open');
                $(this).siblings('dd').find('dt').show();
            }
        });
        $('#areaSelectHtml>dl>dd>dl>dt').on('click',function(){
            if($(this).hasClass('open')){
                $(this).removeClass('open');
                $(this).siblings('dd').hide();
            }else{
                $(this).addClass('open');
                $(this).siblings('dd').show();
            }
        });
    });
    $.ajaxSettings.async = true;
});