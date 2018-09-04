/* global common */
var wxmenu = {
    menuData:[[],[]],
	init:function(){
        this.loadData();
        this.btnEvent();
	},
    urlReg:"^((https|http|ftp|rtsp|mms)?:\/\/)[^\\s]+",
    loadData:function(){
        var _this=this;
        $.post("/wechat/get_menu_c",{},function(d){
            if(typeof d.errcode!='undefined'){
                if(d.errcode==41001){
                    $('#wxmenu .tab_con').eq(0).find('.list').html('<font color=orange>公众号token无效</font>');
                }else if(d.errcode==42001){
                    $('#wxmenu .tab_con').eq(0).find('.list').html('公众号token已过期');
                }else if(d.errcode==46003){
                    $('#wxmenu .tab_con').eq(0).find('.list').html('暂无菜单数据');
                }else{
                    $('#wxmenu .tab_con').eq(0).find('.list').html('<font color=orange>'+d.errmsg+'</font>');
                }
                return;
            }
			var  data= d.menu.button;
            _this.menuData[0]=data;
            $('#wxmenu .tab_con').eq(0).find('.list').html(_this.createHtml(data));
			_this.bind(0);
		},'json');
        $.post("/wechat/get_menu_w",{},function(d){
            if(typeof d.errcode!='undefined'){
                if(d.errcode==41001){
                    $('#wxmenu .tab_con').eq(1).find('.list').html('<font color=orange>供应链公众号token无效</font>');
                }else if(d.errcode==42001){
                    $('#wxmenu .tab_con').eq(1).find('.list').html('公众号token已过期');
                }else if(d.errcode==46003){
                    $('#wxmenu .tab_con').eq(1).find('.list').html('暂无菜单数据');
                }else{
                    $('#wxmenu .tab_con').eq(1).find('.list').html('<font color=orange>'+d.errmsg+'</font>');
                }
                return;
            }
			var  data= d.menu.button;
            _this.menuData[1]=data;
            $('#wxmenu .tab_con').eq(1).find('.list').html(_this.createHtml(data));
			_this.bind(1);
		},'json');
    },
    btnEvent:function(){
        var _this=this;
        $('.tab li').on('click',function(){
           $(this).addClass('current').siblings('li').removeClass('current');
           $('.tab_con').eq($(this).index()).show().siblings('.tab_con').hide(); 
        });
        $('#wxmenu .tab_con .add .btn').on('click',function(){
           var index=$(this).parent('.add').parent('.tab_con').index();
           index--;
           if(_this.menuData[index].length>=3){
                common.alert('只能添加3个一级菜单');
                return;
            }
            $('#editForm label').eq(1).show();
            common.formConfirm('添加一级菜单','editForm',function(r){
                if(r==1){
                    var type=$('.confirm-form #editType').val();
                    var key=$('.confirm-form #editKey').val();
                    var name=$('.confirm-form #editName').val();
                    var url=$('.confirm-form #editUrl').val();
                    if($.trim(name)==''){
                        common.alert('名称不能为空');
                        return;
                    }
                    if(type=='view'){
                        if($.trim(url)==''){
                            common.alert('链接不能为空');
                            return;
                        }
                        if(! new RegExp(_this.urlReg).test(url)){
                            common.alert('链接格式不正确');
                            return;
                        }
                    }
                    var data={'name':name,'url':url,'type':type,'sub_button':[]};
                    if(type!='view'){
                        data={'name':name,'key':key,'type':type,'sub_button':[]};
                    }
                    _this.menuData[index].push(data);
                    $('#wxmenu .tab_con').eq(index).find('.list').html(_this.createHtml(_this.menuData[index]));
                    _this.bind(index);
                }
            });
            $('.confirm-form #editUrl').parent('label').show();
            $('.confirm-form #editType').on('change',function () {
                if($(this).val()!='view'){
                    if ($(this).val() === 'hls_app') {
                        $('.confirm-form #editUrl').parent('label').show();
                        $('.confirm-form #app_selector').parent('label').show();
                        _this.loadHlsApps();
                    } else {
                        $('.confirm-form #editUrl').parent('label').hide();
                        $('.confirm-form #editKey').val($(this).val());
                        $('.confirm-form #app_selector').parent('label').hide();
                    }
                }else{
                    $('.confirm-form #editUrl').parent('label').show();
                    $('.confirm-form #app_selector').parent('label').hide();
                }
            });
            _this.shortcut();
        });

        $('.hover-panel li').off().on('click',function(){
            var tabindex=$('.tab li.current').index();
            var index=$(this).index();
            var findex=$(this).parent('ul').attr('findex');
            var sindex=$(this).parent('ul').attr('sindex');
            if(index==0){//修改
                _this.updateBtn(tabindex,findex,sindex); 
            }else{//删除
                _this.deleteBtn(tabindex,findex,sindex);
            }
        });
        $('#wxmenu .tab_con .save .btn').on('click',_this.saveData);
        
    },
    addSub:function(){
        var tabindex=$('.tab li.current').index();
        var findex=$(this).parent('dl').attr('data-index');
        if(wxmenu.menuData[tabindex][findex].sub_button.length>=5){
            common.alert('二级菜单不能超过5个');
            return;
        }
        $('#editForm label').eq(1).show();
        setTimeout(function(){
            $('.confirm-form #editName').attr('maxlength',7);
            $('.confirm-form #editName').siblings('font').text('不超过7个字');
        },100);
        common.formConfirm('添加二级菜单','editForm',function(r){
            if(r==1){
                var type=$('.confirm-form #editType').val();
                var key=$('.confirm-form #editKey').val();
                var name=$('.confirm-form #editName').val();
                var url=$('.confirm-form #editUrl').val();
                if($.trim(name)==''){
                    common.alert('名称不能为空');
                    return;
                }
                if(type=='view' && ! new RegExp(wxmenu.urlReg).test(url)){
                    common.alert('链接格式不正确');
                    return;
                }
                var data={'name':name,'url':url,'type':type,'sub_button':[]};
                if(type!='view'){
                    data={'name':name,'key':key,'type':type,'sub_button':[]};
                }
                wxmenu.menuData[tabindex][findex].sub_button.push(data);
                $('#wxmenu .tab_con').eq(tabindex).find('.list').html(wxmenu.createHtml(wxmenu.menuData[tabindex]));
                wxmenu.bind(tabindex);
            }
        });
        $('.confirm-form #editUrl').parent('label').show();
        $('.confirm-form #editType').on('change',function () {
            if($(this).val()!='view'){
                if ($(this).val() === 'hls_app') {
                    $('.confirm-form #editUrl').parent('label').show();
                    $('.confirm-form #app_selector').parent('label').show();
                    wxmenu.loadHlsApps();
                } else {
                    $('.confirm-form #editUrl').parent('label').hide();
                    $('.confirm-form #editKey').val($(this).val());
                    $('.confirm-form #app_selector').parent('label').hide();
                }
            }else{
                $('.confirm-form #editUrl').parent('label').show();
                $('.confirm-form #app_selector').parent('label').hide();
            }
        });
        wxmenu.shortcut();
    },
    updateBtn:function(tabindex,findex,sindex){
        var _this=this;
        var oldType,oldKey,oldName,oldUrl;
        if(sindex==-1){
            oldType=_this.menuData[tabindex][findex].type;
            oldKey=_this.menuData[tabindex][findex].key;
            oldName=_this.menuData[tabindex][findex].name;
            oldUrl=_this.menuData[tabindex][findex].url?_this.menuData[tabindex][findex].url:'';
        }else{
            oldType=_this.menuData[tabindex][findex].sub_button[sindex].type;
            oldKey=_this.menuData[tabindex][findex].sub_button[sindex].key;
            oldName=_this.menuData[tabindex][findex].sub_button[sindex].name;
            oldUrl=_this.menuData[tabindex][findex].sub_button[sindex].url;
        }
        if(_this.menuData[tabindex][findex].sub_button.length>0 && sindex==-1){
            $('#editForm #editType').parent('label').hide();
            $('#editForm #editUrl').parent('label').hide();
        }else{
            $('#editForm #editType').parent('label').show();
        }
        setTimeout(function(){
            $('.confirm-form #editName').attr('maxlength',7);
            $('.confirm-form #editName').siblings('font').text('不超过7个字');
            if(sindex==-1){
                $('.confirm-form #editName').attr('maxlength',5);
                $('.confirm-form #editName').siblings('font').text('不超过5个字');
            }
            if(typeof oldKey=='undefined') oldKey='';
            if(oldKey.indexOf('_waiter')!=-1){
                oldType=oldKey;
            }
            if(oldKey.indexOf('_salesman')!=-1){
                oldType=oldKey;
            }
            $('.confirm-form #editType').val(oldType);
            $('.confirm-form #editKey').val(oldKey);
            $('.confirm-form #editName').val(oldName);
            $('.confirm-form #editUrl').val(oldUrl);
            if(oldType=='view'){
                $('.confirm-form #editUrl').parent('label').show();
            }
            if(typeof oldType=='undefined'){
                $('.confirm-form #editType').parent('label').hide();
            }
        },100);
        common.formConfirm('修改菜单','editForm',function(r){
            if(r!=1){
                return;
            }
            var type=$('.confirm-form #editType').val();
            var key=$('.confirm-form #editKey').val();
            var name=$('.confirm-form #editName').val();
            var url=$('.confirm-form #editUrl').val();
            if($.trim(name)==''){
                common.alert('名称不能为空');
                return;
            }
            if(sindex==-1){
                if(_this.menuData[tabindex][findex].sub_button.length==0){
                    if(type=='view' && $.trim(url)==''){
                        common.alert('链接不能为空');
                        return;
                    }
                    if(type=='view' && ! new RegExp(_this.urlReg).test(url)){
                        common.alert('链接格式不正确');
                        return;
                    }
                }
                _this.menuData[tabindex][findex].type=type;
                _this.menuData[tabindex][findex].name=name;
                if(type=='view'){
                    _this.menuData[tabindex][findex].url=url;
                }else{
                    _this.menuData[tabindex][findex].key=key;
                }
                $('#wxmenu .tab_con').eq(tabindex).find('.list').find('dl').eq(findex).find('dt').html(name);
            }else{
                if(type=='view'){
                    if(! new RegExp(_this.urlReg).test(url)){
                        common.alert('链接格式不正确');
                        return;
                    }
                }
                _this.menuData[tabindex][findex].sub_button[sindex].type=type;
                _this.menuData[tabindex][findex].sub_button[sindex].name=name;
                if(type=='view'){
                    _this.menuData[tabindex][findex].sub_button[sindex].url=url;
                }else{
                    _this.menuData[tabindex][findex].sub_button[sindex].key=key;
                }
                
                $('#wxmenu .tab_con').eq(tabindex).find('.list').find('dl').eq(findex).find('dd[data-index='+sindex+']').html(name);
            }
                
        });
        if(oldType=='view'){
            $('.confirm-form #editUrl').parent('label').show();
        }
        $('.confirm-form #editType').on('change',function () {
            if($(this).val()!='view'){
                $('.confirm-form #editUrl').parent('label').hide();
                $('.confirm-form #editKey').val($(this).val());
            }else{
                $('.confirm-form #editUrl').parent('label').show();
            }
        });
        this.shortcut();
    },
    deleteBtn:function(tabindex,findex,sindex){
        var _this=this;
        common.confirm('确定删除？',function(r){
            if(r==1){
                if(sindex==-1){
                    if(_this.menuData[tabindex][findex].sub_button.length>0){
                        common.alert('此菜单存在二级菜单，不能删除！');
                        return;
                    }else{
                        _this.menuData[tabindex].splice(findex,1);
                        // $('#wxmenu .tab_con').eq(tabindex).find('.list').find('dl').eq(findex).remove();
                    }
                }else{
                    _this.menuData[tabindex][findex].sub_button.splice(sindex,1);
                    // $('#wxmenu .tab_con').eq(tabindex).find('.list').find('dl').eq(findex).find('dd[data-index='+sindex+']').remove();
                    // $('#wxmenu .tab_con').eq(tabindex).find('.list').html(_this.createHtml(_this.menuData[tabindex]));
                    _this.bind(tabindex);
                }
                $('#wxmenu .tab_con').eq(tabindex).find('.list').html(_this.createHtml(_this.menuData[tabindex]));
                _this.bind(tabindex);
            }
        });
    },
    createHtml:function(data){
        var html='';
        for(var i=0;i<data.length;i++){
            html+='<dl data-index="'+i+'"><dt data-index="-1">'+data[i].name+'</dt>';
            html+='<dd class="arrow"><i class="iconfont">&#xe604;</i></dd>';
            if(data[i].sub_button.length>0){
                var sdata=data[i].sub_button;
                for(var j=0;j<sdata.length;j++){
                    html+='<dd data-index="'+j+'">'+sdata[j].name+'</dd>';
                }
            }
            if(data[i].sub_button.length<=4){
                html+='<dd class="addsub">+</dd>';
            }
            html+='</dl>';
        }
        if(html=='') html='暂无菜单';
        return html;
    },
	bind:function(index){
        $('#wxmenu .tab_con').eq(index).find('.list').find('dt,dd').off().on('mouseenter',function(){
            var _this=$(this);
            if(_this.hasClass('arrow') || _this.hasClass('addsub')) return;
            var pos={
                left:_this.position().left,
                top:_this.position().top,
                height:_this.height()+20,
                width:_this.width()+50
            };
            $('.hover-panel').css({
                'left':pos.left,'top':pos.top,'width':pos.width,'height':pos.height,'display':'block'
            }).attr({
                'findex':_this.parent('dl').attr('data-index'),'sindex':_this.attr('data-index')
            });
        }).on('mouseleave',function(){
            window.hoverT=setTimeout(function(){
                $('.hover-panel').hide();
            },100);
        });
        $('#wxmenu .tab_con').eq(index).find('.list').find('.addsub').off().on('click',this.addSub);
        $('.hover-panel').off().on('mouseenter',function(){
            clearTimeout(window.hoverT);
        }).on('mouseleave',function(){
            $(this).hide();
        });
    },
	saveData:function(){
		var tabindex=$('.tab li.current').index();
        var url=tabindex==0?"/wechat/update_menu_c":"/wechat/update_menu_w";
        common.loading();
        var data=wxmenu.menuData[tabindex];
        for(var i=0;i<data.length;i++){
            if(data[i].sub_button.length>0){
                delete data[i].type;
                delete data[i].url;
                delete data[i].key;
                for(var j=0;j<data[i].sub_button.length;j++){
                    if(data[i].sub_button[j].type!='view'){
                        delete data[i].sub_button[j].url;
                    }
                }
            }else{
                if(typeof data[i].type=='undefined'){
                    data[i].type='view';
                }
                if(data[i].type=='view' && typeof data[i].url=='undefined'){
                    common.alert('一级菜单“'+data[i].name+'”缺少链接');
                    common.unloading();
                    return;
                }
                if(data[i].type!='view'){
                    delete data[i].url;
                    if(data[i].type.indexOf('_waiter')!=-1){
                        data[i].type=data[i].type.replace('_waiter_sys','');
                        data[i].type=data[i].type.replace('_waiter_sq','');
                    }
                    if(data[i].type.indexOf('_salesman')!=-1){
                        data[i].type=data[i].type.replace('_salesman','');
                        data[i].type=data[i].type.replace('_salesman_sys','');
                        data[i].type=data[i].type.replace('_salesman_sq','');
                    }
                }else{
                    delete data[i].key;
                }
            }
        }
        if(data.length==0){
            url=tabindex==0?"/wechat/delete_menu_c":"/wechat/delete_menu_w";
        }
        var dataStr=JSON.stringify(data);
        dataStr='{"button":'+dataStr+'}';
        $.post(url,{'data':dataStr},function(d){
            common.unloading();
            if(typeof d.errcode!='undefined'){
                if(d.errcode==0){
                    common.alert('保存成功！');
                }else if(d.errcode==41001){
                    common.alert('公众号token无效');
                }else if(d.errcode==40055){
                    common.alert('链接格式有误');
                }else if(d.errcode==40020){
                    common.alert('是不是哪个一级菜单没填写链接地址？');
                }else{
                    common.alert(d.errmsg);
                }
                return;
            }
			common.alert(d);
		},'json');
	},
    shortcut:function(){
        var html='<div class="shortcut">消费者快捷操作：<span id="myBoard" style="display:none">个人中心</span><span id="myRedpack" style="display:none">我的红包</span><span id="myCard" style="display:none">我的乐券</span><span id="myCardIn">乐券转入</span><span id="myGroup">好友圈</span><span id="scanByWx">扫一扫</span></div>';
        var tabindex=$('.tab li.current').index();
        if(tabindex==1){
            html='<div class="shortcut">服务员快捷操作：<span id="waiterScan">扫码</span><span id="waiterTransferIn">转入</span><span id="waiterAccount">账户</span><BR>';
            html+='业务员快捷操作：<span id="salesmanScan">核销</span><span id="salesmanTransferIn">转入</span><span id="salesmanAccount">账户</span></div>';
        }
        if($('.confirm-form .shortcut').length==0){
            $('.confirm-form .table-form').prepend(html);
        }
        $('.confirm-form #scanByWx').off().on('click',function(){
            $('.confirm-form #editType,.confirm-form #editKey').val('scancode_push');
            $('.confirm-form #editName').val($(this).text());
            $('.confirm-form #editUrl').parent('label').hide();
        });
        if(typeof window.mchId!='undefined' && $.trim(window.mchId)!=''){
            $('.confirm-form #myRedpack').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://m.lsa0.cn/user/red_packet?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #myCard').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://m.lsa0.cn/user/cards?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #myGroup').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://m.lsa0.cn/group/lists/'+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #myCardIn').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://m.lsa0.cn/transfer/start_transfer/'+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #myBoard').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://m.lsa0.cn/user?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #waiterScan').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://shop.lsa0.cn/scan/waiter?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #waiterTransferIn').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://shop.lsa0.cn/transfer/waiter?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #waiterAccount').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://shop.lsa0.cn/account/waiter?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #salesmanScan').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://shop.lsa0.cn/settle?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #salesmanTransferIn').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://shop.lsa0.cn/transfer/salesman?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
            $('.confirm-form #salesmanAccount').off().on('click',function(){
                $('.confirm-form #editType,.confirm-form #editKey').val('view');
                $('.confirm-form #editName').val($(this).text());
                var thisUrl='http://shop.lsa0.cn/account/salesman?mch_id='+$.trim(window.mchId);
                $('.confirm-form #editUrl').val(thisUrl).parent('label').show();
            }).show();
        }
    },

    /**
     * 获取企业所有可用的应用(Added by shizq)
     *
     */
    loadHlsApps: function() {
        $.get('/myapp/get', {}, function(resp) {
            var appInsts = resp.data;
            var option;
            $('.confirm-form #app_selector').empty();
            for (var i = 0; i < appInsts.length; i++) {
                option = '<option value="' + appInsts[i].path + '">' + appInsts[i].name + '</option>';
                $('.confirm-form #app_selector').append(option);
            }
        }).error(function(err) {
            common.alert('无法连接服务器！');
        });
    }
};
$(function(){
	wxmenu.init();
});