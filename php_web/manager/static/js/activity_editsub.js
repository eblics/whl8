var activityEditSub = {
    init: function() {
        var _this = this;
        common.ajaxSelectSub('categoryId','/product/catedata','productId','/product/prodata');
        common.ajaxSelect('webAppId', '/activity/h5data', function(d) {
            window.listWebAppId = d;
            var val=$('#webAppId').attr('edit-value');
            $.each(window.listWebAppId.data,function(){
                if(Number(this.config)==1 && Number(this.id)==Number(val)){
                    $('#webappConfig').show();
                }
            });
        });
        common.ajaxSelect('batchId', '/batch/dataenable');
        // common.ajaxSelect('areaCode', '/activity/areadata');
        // common.ajaxSelect('saletoagc', '/activity/areadata');
        $('#areaCode').areaSelect();
        $('#saletoagc').areaSelect();
        $.ajax({
            url: '/activity/tag_data',
            data: {},
            type: 'post',
            cache: false,
            dataType: 'json',
            success: function(d) {
                if (typeof d != 'object') d = $.parseJSON(d);
                var html='';
                var curTag=$('#tagId').val();
                var curTagArr=curTag.split(',');
                $(d).each(function(index, elem) {
                    var thisOrderNo = elem.orderNo;
                    var css='';
                    if(curTagArr.indexOf(elem.tagId)>-1) css='active';
                    html+='<li class="'+css+'" data-id="'+elem.tagId+'">'+elem.name+'</li>';
                });
                $('#tagList').html(html);
                $('#tagList li').on('click', function() {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                    } else {
                        $(this).addClass('active');
                    }
                });
            },
            error: function() {}
        });
        _this.valid();
        common.autoHeight();
    },
    valid: function() {
        var _this = this;
        $("#btnSave").on("click", function() {
            if (beforeSubmitAct()) {
                _this.submit();
            }
        });
        $('#batchCheck').on('click', function() {
            if ($(this).is(':checked')) {
                $('.batch-tr').show();
            } else {
                $('.batch-tr').hide();
            }
            common.autoHeight();
        });
        $('#timeCheck').on('click', function() {
            if ($(this).is(':checked')) {
                $('.time-tr').show();
            } else {
                $('.time-tr').hide();
            }
            common.autoHeight();
        });
        $('#areaCheck').on('click', function() {
            if ($(this).is(':checked')) {
                $('.area-tr').show();
            } else {
                $('.area-tr').hide();
            }
            common.autoHeight();
        });
        $('#prodInOrderCheck').on('click', function() {
            if ($(this).is(':checked')) {
                $('.prodInOrder-tr').show();
            } else {
                $('.prodInOrder-tr').hide();
            }
            common.autoHeight();
        });
        $('#outOrderCheck').on('click', function() {
            if ($(this).is(':checked')) {
                $('.outOrder-tr').show();
            } else {
                $('.outOrder-tr').hide();
            }
            common.autoHeight();
        });
        $('#saletoagcCheck').on('click', function() {
            if ($(this).is(':checked')) {
                $('.saletoagc-tr').show();
            } else {
                $('.saletoagc-tr').hide();
            }
            common.autoHeight();
        });
        $('#expireCheck').on('click', function() {
            if ($(this).is(':checked')) {
                $('.expire-tr').show();
            } else {
                $('.expire-tr').hide();
            }
            common.autoHeight();
        });
        $("#widthProduct").on('click',function(){
            if($(this).is(':checked')){
                $('#onlyCategory').prop('checked',false);
                $('.product-ca').show();
                $('.product-list').show();
            }else{
                $('.product-ca').hide();
                $('.product-list').hide();
            }
            common.autoHeight();
        });
        $('#onlyCategory').on('click',function(){
            if($(this).is(':checked')){
                $('.product-list').hide();
                // $('#productId').prop("selected", false);
                $('#productId').val();
            }else{
                $('.product-list').show();
            }
            common.autoHeight();
        });
        $('#tagCheck').on('click', function() {
            if ($(this).is(':checked')) {
                $('.tag-tr').show();
            } else {
                $('.tag-tr').hide();
            }
            common.autoHeight();
        });
        $('#isForEvil').on('click', function() {
            if ($(this).is(':checked')) {
                $('.forevil-list').show();
            } else {
                $('.forevil-list').hide();
            }
            common.autoHeight();
        });
        var activityTypeVal = $('#activityType').attr('edit-value');
        if (activityTypeVal != '') {
            $('#activityType').val(activityTypeVal);
        }
        if ($('#role').attr('edit-value') != '') {
            $('#role').val($('#role').attr('edit-value'));
        }
        if (activityTypeVal == 0) {
            common.ajaxSelect('detailId', '/redpacket/datano');
        } else if (activityTypeVal == 2) {
            common.ajaxSelect('detailId', '/redpacket/data_cards');
        } else if (activityTypeVal == 3) {
            common.ajaxSelect('detailId', '/mixstrategy/data/fa');
        } else if (activityTypeVal == 4) {
            common.ajaxSelect('detailId', '/point/datano');
        } else if (activityTypeVal == 5) {
            common.ajaxSelect('detailId', '/multistrategy/data/fa');
        }else if (activityTypeVal == 6) {
            common.ajaxSelect('detailId', '/accumstrategy/data/fa');
        }
        $('#activityType').on('change', function() {
            var val = $(this).val();
            if (val == 0) {
                common.ajaxSelect('detailId', '/redpacket/datano');
            } else if (val == 2) {
                common.ajaxSelect('detailId', '/redpacket/data_cards');
            } else if (val == 3) {
                common.ajaxSelect('detailId', '/mixstrategy/data/fa');
            } else if (val == 4) {
                common.ajaxSelect('detailId', '/point/datano');
            } else if (val == 5) {
                common.ajaxSelect('detailId', '/multistrategy/data/fa');
            } else if (val == 6) {
                common.ajaxSelect('detailId', '/accumstrategy/data/fa');
            }
        });
        $('#webAppId').on('change',function(){
            var val=$(this).val();
            $.each(window.listWebAppId.data,function(){
                if(this.config==1 && this.id==val){
                    $('#webappConfig').show();
                    return false;
                }else{
                    $('#webappConfig').hide();
                }
            });
        });
        $('#webappConfig').on('click', function() {
            var val = $('#webAppId').val();
            if (typeof window.listWebAppId == 'undefined') {
                return;
            }
            if (typeof window.listWebAppId.data == 'undefined') {
                return;
            }
            var data = window.listWebAppId.data;
            var config = false;
            $(data).each(function(index, el) {
                if (el.id == val) {
                    if (el.config == 1) {
                        config = true;
                        return false;
                    }
                }
            });
            if (config) {
                _this.appConfig(val);
            } else {
                common.alert('此H5无需配置');
            }
        });
        _this.ttsInData = [];
        _this.ttsOutData = [];
        $.ajax({
            url: '/activity/tts_in_produce_data',
            data: {},
            type: 'post',
            cache: false,
            dataType: 'json',
            success: function(d) {
                if (typeof d != 'object') d = $.parseJSON(d);
                _this.ttsInData = d;
                $(d).each(function(index, elem) {
                    var thisOrderNo = elem.orderNo;
                    var thisId = elem.id;
                    var inputVal = $('#prodInOrderId').val();
                    if (thisId == inputVal) {
                        $('#prodInOrderIdInput').val(thisOrderNo);
                    }
                });
            },
            error: function() {}
        });
        $.ajax({
            url: '/activity/tts_out_order_data',
            data: {},
            type: 'post',
            cache: false,
            dataType: 'json',
            success: function(d) {
                if (typeof d != 'object') d = $.parseJSON(d);
                _this.ttsOutData = d;
                $(d).each(function(index, elem) {
                    var thisOrderNo = elem.orderNo;
                    var thisId = elem.id;
                    var inputVal = $('#outOrderId').val();
                    if (thisId == inputVal) {
                        $('#outOrderIdInput').val(thisOrderNo);
                    }
                });
            },
            error: function() {}
        });
        $('#prodInOrderIdInput').off().on('keyup paste', function(e) {
            if (e.keyCode != 13 && e.keyCode != 38 && e.keyCode != 40) _this.focus('prodInOrderIdInput', _this.ttsInData);
            if (e.keyCode == 13) {
                $('#listHtml li.current').click();
                $('#listHtml').remove();
            }
        }).on('blur', function() {
            var __this = $(this);
            setTimeout(function() {
                $('#listHtml').remove();
            }, 300);
            var inputVal = $(this).val();
            var inputId = $(this).parent().find('input:hidden').val();
            __this.css('background', '#FFA07A');
            $(_this.ttsInData).each(function(index, elem) {
                var thisOrderNo = elem.orderNo;
                var thisId = elem.id;
                if (thisOrderNo == inputVal && thisId == inputId) {
                    __this.css('background', '#eee');
                }
            });
        }).on('keydown', function(e) {
            var curIndex = $('#listHtml li.current').index();
            var allLenth = $('#listHtml li').length;
            if (e.keyCode == 38) { //moveup
                if (curIndex > 0) {
                    $('#listHtml li').eq(curIndex - 1).addClass('current').siblings('li').removeClass('current');
                }
            }
            if (e.keyCode == 40) { //movedown
                if (curIndex < allLenth - 1) {
                    $('#listHtml li').eq(curIndex + 1).addClass('current').siblings('li').removeClass('current');
                }
            }
        });
        $('#outOrderIdInput').off().on('keyup paste', function(e) {
            if (e.keyCode != 13 && e.keyCode != 38 && e.keyCode != 40) _this.focus('outOrderIdInput', _this.ttsOutData);
            if (e.keyCode == 13) {
                $('#listHtml li.current').click();
                $('#listHtml').remove();
            }
        }).on('blur', function() {
            var __this = $(this);
            setTimeout(function() {
                $('#listHtml').remove();
            }, 300);
            var inputVal = $(this).val();
            var inputId = $(this).parent().find('input:hidden').val();
            __this.css('background', '#FFA07A');
            $(_this.ttsOutData).each(function(index, elem) {
                var thisOrderNo = elem.orderNo;
                var thisId = elem.id;
                if (thisOrderNo == inputVal && thisId == inputId) {
                    __this.css('background', '#eee');
                }
            });
        }).on('keydown', function(e) {
            var curIndex = $('#listHtml li.current').index();
            var allLenth = $('#listHtml li').length;
            if (e.keyCode == 38) { //moveup
                if (curIndex > 0) {
                    $('#listHtml li').eq(curIndex - 1).addClass('current').siblings('li').removeClass('current');
                }
            }
            if (e.keyCode == 40) { //movedown
                if (curIndex < allLenth - 1) {
                    $('#listHtml li').eq(curIndex + 1).addClass('current').siblings('li').removeClass('current');
                }
            }
        });
        $('#prodInOrderIdInput,#outOrderIdInput').off('click').on('click', function(e) {
            $(this).css('background', 'none');
        });
    },
    focus: function(domid, data) {
        var listHtml = '<ul id="listHtml"></ul>';
        $('#listHtml').remove();
        $('body').append(listHtml);
        var width = $('#' + domid).width();
        var height = $('#' + domid).height();
        var left = $('#' + domid).position().left;
        var top = $('#' + domid).position().top;
        $('#listHtml').css({
            'left': left,
            'top': top + height + 10,
            'width': width + 20
        });
        var inputVal = $.trim($('#' + domid).val());
        var liHtml = '';
        $(data).each(function(index, elem) {
            var thisOrderNo = elem.orderNo;
            var thisId = elem.id;
            if (thisOrderNo.indexOf(inputVal) != -1) {
                var regExp = new RegExp(inputVal, 'gi');
                thisOrderNo = thisOrderNo.replace(regExp, '<font color=red>' + inputVal + '</font>');
                liHtml += '<li data-id="' + thisId + '">' + thisOrderNo + '</li>';
            }
        });
        if (liHtml == '') liHtml += '<li data-id="-1">没有匹配到数据</li>';
        $('#listHtml').html(liHtml);
        $('#listHtml li:first').addClass('current');
        $('#listHtml li').off().on('click', function() {
            var thisData = $(this).attr('data-id');
            if (thisData != -1) {
                $('#' + domid).val($(this).text());
                $('#' + domid).parent().find('input:hidden').val($(this).attr('data-id'));
                $('#' + domid).css('background', '#eee');
            }
        }).on('mouseenter', function() {
            $(this).addClass('current').siblings('li').removeClass('current');
        });
    },
    submit: function() {
        var _this = this;
        var data = {};
        var tagId=[];
        $('#tagList li.active').each(function() {
            tagId.push($.trim($(this).attr('data-id')));
        });
        var tagstr=tagId.join(',');
        $('#tagId').val(tagstr);
        $('form input,form textarea,form select').each(function(e) {
            var name = $(this).attr('name');
            var val = $(this).val();
            if ($.trim(name) != '') {
                data[name] = val;
            }
        });
        $('form input[type=radio]').each(function(e) {
            var name = $(this).attr('name');
            var val = $(this).val();
            if ($.trim(name) != '') {
                if ($(this).is(':checked')) {
                    data[name] = val;
                }
            }
        });
        //选择check
        if($('#detailId').val()=='' || $('#detailId').val()==null){
            common.alert('请选择策略内容');
            return false;
        }
        if($('#webAppId').val()=='' || $('#webAppId').val()==null){
            common.alert('请选择H5应用');
            return false;
        }
        
        if ($('#areaCheck').is(':checked')) {
            if($('#areaCode').val()=='' || $('#areaCode').val()==null){
                common.alert('请正确指定活动地区');
                return false;
            }
        }
        if ($('#batchCheck').is(':checked')) {
            if($('#batchId').val()=='' || $('#batchId').val()==null){
                common.alert('请正确选择乐码批次');
                return false;
            }
        }
        if ($('#prodInOrderCheck').is(':checked')) {
            if($('#prodInOrderId').val()=='' || $('#prodInOrderId').val()==null){
                common.alert('请正确关联生产入库单');
                return false;
            }
        }
        if ($('#outOrderCheck').is(':checked')) {
            if($('#outOrderId').val()=='' || $('#outOrderId').val()==null){
                common.alert('请正确关联出库单');
                return false;
            }
        }
        if ($('#saletoagcCheck').is(':checked')) {
            if($('input[name=saletoagc]').val()=='' || $('input[name=saletoagc]').val()==null){
                common.alert('请正确关联销售区域');
                return false;
            }
        }
        if ($('#expireCheck').is(':checked')) {
            if($('#expireTime').val()=='' || $('#expireTime').val()==null){
                common.alert('请正确关联商品过期策略');
                return false;
            }
        }
        if ($('#widthProduct').is(':checked')) {
            if($('#categoryId').val()=='' || $('#categoryId').val()==null){
                common.alert('请正确关联产品');
                return false;
            }
            if (! $('#onlyCategory').is(':checked')) {
                if($('#productId').val()=='' || $('#productId').val()==null){
                    common.alert('请正确关联产品');
                    return false;
                }
            }
        }
        if ($('#tagCheck').is(':checked')) {
            if($('#tagId').val()=='' || $('#tagId').val()==null){
                common.alert('请正确选择用户标签');
                return false;
            }
        }
        if ($('#isForEvil').is(':checked')) {
        	var chk_value =[]; 
        	$('input[name="forEvil"]:checked').each(function(){ 
        		chk_value.push($(this).val()); 
        	}); 
        	if(chk_value.length==0){
        		 common.alert('请正确选择恶意用户级别');
                 return false;
        	}else{
        		data.forEvil = chk_value.join(',');
        	}
        	//alert(data.forEvil);
        }else{
        	data.forEvil = '';
        }
        //选择check end
        var binding = 0;
        // if(!$('#timeCheck').is(':checked')){
        //     data['startTime']='';
        //     data['endTime']='';
        // }else{
        //     binding+=1;
        // }
        binding += 1; //时间
        if (!$('#areaCheck').is(':checked')) {
            data['areaCode'] = '';
        } else {
            binding += 2;
        }
        if (!$('#batchCheck').is(':checked')) {
            data['batchId'] = '';
        } else {
            binding += 4;
        }
        if (!$('#prodInOrderCheck').is(':checked')) {
            data['prodInOrderId'] = '';
        } else {
            binding += 8;
            var inVal = $('#prodInOrderIdInput').val();
            var inId = $('#prodInOrderId').val();
            var inValid = false;
            $(_this.ttsInData).each(function(index, elem) {
                var thisOrderNo = elem.orderNo;
                var thisId = elem.id;
                if (thisOrderNo == inVal && thisId == inId) {
                    inValid = true;
                }
            });
            if (!inValid) {
                common.alert('入库单数据有误');
                return false;
            }
        }
        
        if (!$('#outOrderCheck').is(':checked')) {
            data['outOrderId'] = '';
        } else {
            binding += 16;
            var outVal = $('#outOrderIdInput').val();
            var outId = $('#outOrderId').val();
            var outValid = false;
            $(_this.ttsOutData).each(function(index, elem) {
                var thisOrderNo = elem.orderNo;
                var thisId = elem.id;
                if (thisOrderNo == outVal && thisId == outId) {
                    outValid = true;
                }
            });
            if (!outValid) {
                common.alert('出库单数据有误');
                return false;
            }
        }
        if (!$('#saletoagcCheck').is(':checked')) {
            data['saletoagc'] = '';
        } else {
            binding += 32;
        }
        if (!$('#expireCheck').is(':checked')) {
            data['expireTime'] = '';
        } else {
            binding += 64;
        }
        if($('#onlyCategory').is(':checked')){
            data['productId']='';
        }
        if(!$('#widthProduct').is(':checked')){
            data['productId']='';
            data['categoryId']='';
        }else{
            binding += 128;
        }
        data.binding = binding;
        common.loading();
        $.ajax({
            url: '/activity/savesub',
            data: data,
            type: 'post',
            cache: false,
            dataType: 'json',
            success: function(d) {
                common.unloading();
                if (d.errorCode == 0) {
                    common.alert('保存成功', function(d) {
                        if (d == 1) {
                            location.href = '/activity/lists';
                        }
                    });
                } else {
                    common.alert(d.errorMsg);
                }
            },
            error: function() {
                common.unloading();
                common.alert('请求失败');
            }
        });

    },
    appConfig: function(appId) {
        //alert('配置界面 ');
        var h5name='allstrategy';
        $('.h5setting iframe').attr('src','/activity/h5setting/'+h5name)
        $('.h5setting').show(); 
        $('.h5setting .close').on('click',function(){
            $('.h5setting').hide(); 
        }); 
    }
};
$(function() {
    activityEditSub.init();
});