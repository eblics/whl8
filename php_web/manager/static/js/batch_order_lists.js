$(function(){
    order.init();
});
var order = {
    init:function(){
        this.createTable();
    },
    createTable:function(){
        var _this=this;
        var showTooltipRender=function(data,type,row){
            return '<span class="showtip">'+data+'</span>';
        };
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": true,//加载中
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "serverSide":true,//开启服务器分页
            "ajax":{
                url:'/batch/order_list_data/in',//请求数据地址
                type:'POST',//请求方式
            },
            "columns": [
                {"data":"id","class":"center","render":showTooltipRender},
                {"data":"orderNo","class":"center","render":showTooltipRender},
                {"data":"productCode","class":"center"},
                {"data":"productName","class":"center"},
                {"data":"orderType","class":"center",
                    "render":function(data,type,row){
                        var val='';
                        if(data=='produce') val='生产入库';
                        if(data=='in') val='普通入库';
                        if(data=='out') val='出库';
                        return val;
                    }
                },
                {"data":"orderTime","class":"center"},
                {"data":"putTime","class":"center"},
                {"data":null,"class":"center nowrap",
                    "render":function(data,type,row){
                        var html='<span class="btn-text noselect blue get_scan_num" data-id="'+data.id+'" ">查看</span>';
                        return html;
                    }
                },
                {"data":"processStatus","class":"center nowrap",
                    "render":function(data,type,row){
                        var val='';
                        if(data==1) val='<font color=green>完成</font>';
                        if(data==0) val='<font color=red>处理中</font>';
                        return val;
                    }
                },
                {"data":null,"class":"center state nowrap",
                    "render":function(data,type,row){
                        var stateBtn='';
                        if(row.processStatus==1){
                            stateBtn=//'<a class="btn-text noselect blue" href="/batch/order_code_download/'+row.id+'">码下载</a>&nbsp;&nbsp;'+
                            '<span class="btn-text noselect blue download" data-id="'+row.id+'">码下载</span>&nbsp;&nbsp;'+
                            '<a class="btn-text noselect blue" href="/batch/order_errmsg_download/'+row.id+'">错误信息下载</a>&nbsp;&nbsp;'+
                            '<span class="btn-text noselect del gray" data-id="'+row.id+'">删除</span>';
                        }
                        return stateBtn;
                    }
                }
            ],
            "initComplete": function(){
                _this.downloadOrder();
                _this.deleteOrder();
                _this.showTooltip();
                _this.scanAmount();
                common.autoHeight();
            },
            "drawCallback":function(){
                _this.downloadOrder();
                _this.deleteOrder();
                _this.showTooltip();
                _this.scanAmount();
                common.autoHeight();
            }
        };

        this.table=$('#orderTable').dataTable(config);
    },

    /**
     * 码下载
     * 
     */
    downloadOrder:function(){
        var _this=this;
        $('#orderTable tbody td .download').off('click').on("click",function(){
            var id=$(this).attr('data-id');
            $.post('/batch/order_code_download_log/'+id,function(d){

            },'json');
            $.get('/batch/fetch_token', {}, function(resp) {
                if (! resp.errcode) {
                    _this.postMessage(apiurl+'app/token',{appid:appid,appsecret:appsecret},function(d){
                        location.href=apiurl+'order/get/'+id+'?token='+d.token;
                    });
                } else {
                    common.alert(resp.errmsg);
                }
            }, 'json').error(function(err) {
                common.alert('网络错误');
            });
        });
    },
    scanAmount:function(){
        $('#orderTable tbody td .get_scan_num').off('click').on("click",function(){
            var obj=$(this);
            var id=obj.attr('data-id');
            obj.html('<img src="/static/images/loading-mini.gif" />');
            $.post('/batch/order_scan_data/'+id,{},function(d){
                obj.html('<font color=red>'+d.scaned+'</font>/<font color=green>'+(d.sum-d.scaned)+'</font>');
            },'json');
        });
        $('#orderTable .get_scan_num_all').off('click').on("click",function(){
            clearInterval(window.getScanT);
            window.getScanNums=$('#orderTable .get_scan_num').length;
            window.getScanCount=0;
            window.getScanT=setInterval(function(){
                if(window.getScanCount>=window.getScanNums-1) clearInterval(window.getScanT);
                $('#orderTable .get_scan_num').eq(window.getScanCount).triggerHandler('click');
                window.getScanCount++;
            },600);
        });
    },
    deleteOrder:function(){
        $('#orderTable tbody td .del').off('click').on("click",function(){
            var _this=$(this);
            common.confirm('确定删除吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    $.post('/batch/order_delete/'+id+'/in',function(d){
                        common.unloading();
                        if(d.errorCode==0){
                            _this.parent('td').parent('tr').addClass('selected');
                            var table=$('#orderTable').DataTable();
                            table.row('.selected').remove().draw(false);
                            common.autoHeight();
                        }else{
                            common.alert(d.errorMsg);
                        }
                    },'json');
                }
            });
        });
    },
    postMessage:function(url,data,success){
        $.ajax({
            url:url,
            data:JSON.stringify(data),
            contentType:'application/json',
            type:'POST',
            cache:false,
            dataType:'json',
            xhrFields: {
                withCredentials: false
            },
            success:function(d) {
                success(d);
            },
            error:function(e) {
                common.unloading();
                common.alert('请求失败');
            }
        });
    },
    showTooltip:function(){
        var tipObj;
        var closeTooltip=function(){
            tipObj=setTimeout(function(){
                $('#showTooltip').hide().children().remove();
            },500);
        };
        var openTooltip=function(object){
            clearTimeout(tipObj);
            var offset=object.offset();
            $('#showTooltip').css('top',offset.top - 20);
            $('#showTooltip').css("left",offset.left + object.width() + 10);
            $('#showTooltip').show();
        };
        $('#orderTable .showtip').off().on('mouseover',function(event){
            var id = $(this).closest('tr').children(':first').text();
            var orderNo = $(this).closest('tr').children(':eq(1)').text();
            var div ='<div class="out"></div><div class="in"></div>'+
                '<div class="showtitle" >ID:'+id+' 的信息</div>'+
                '<div class="showcontent">订单编号: &nbsp'+orderNo+'</div>'+
                '<div><a class="btnInner btn-blue noselect" onclick="order.showDetail('+id+')" >单据详情</a></div>';
            $('#showTooltip').html(div);
            openTooltip($(this));
        }).on('mouseout',function(event){
            closeTooltip();
        });
        
        $('#showTooltip').on('mouseenter',function(){
            clearTimeout(tipObj);
        }).on('mouseleave',function(){
            closeTooltip();
        });
    },
    showDetail:function(id){
        $('.info').remove();
        var html='<div id="detail" class="info" style="display:none"><div class="layer"></div><dl class="box"><dt>入库单详情</dt><dd><div class="condiv">';
        $.post('/batch/order_detail/'+id,function(data){
            var s ='<table class="table-form" style="width:100%">';
            s+= '<tr><td class="name" width="20%">订单编号：</td>';
            s+= '<td class="value" width="30%"><span>';
            s+= data.orderNo;
            s+= '</span></td> <td class="name" width="20%">产品编码：</td><td class="value" width="30%"><span>';
            s+= data.productCode;
            s+= '</span></td></tr>';
            s+= '<tr><td class="name">产品名称：</td><td class="value">';
            s+= data.productName;
            s+= '</td><td class="name">入库类型：</td><td class="value">';
            switch(data.orderType){
                case 'produce':
                    s+= "生产入库";
                    break;
                case 'in':
                    s+= "普通入库";
                    break;
            }
            s+= '</td></tr>';
            if(data.orderType=='produce'){
                s+= '<tr><td class="name">生产工厂编码：</td><td class="value">';
                s+= data.factoryCode;
                s+= '</td><td class="name">生产工厂名称：</td><td class="value">';
                s+= data.factoryName;
                s+= '</td></tr>';
                s+= '<tr><td class="name">生产时间：</td><td class="value">';
                s+= data.produceTime;
                s+= '</td><td class="name">入库时间：</td><td class="value">';
                s+= data.orderTime;
                s+= '</td></tr>';
                s+= '<tr><td class="name">保质期：</td><td class="value">';
                s+= data.shelfLifeStr==null?'无':data.shelfLifeStr;
                s+= '</td><td class="name">过期时间：</td><td class="value">';
                s+= data.expireTime;
                s+= '</td></tr>';
                s+= '<tr><td class="name">上传时间：</td><td class="value">';
                s+= data.putTime;
                s+= '</td>';
                s+= '<td class="name">状态：</td><td class="value">';
                switch(data.processStatus){
                    case '1':
                        s+= "<font color=green>完成</font>";
                        break;
                    case '0':
                        s+= "<font color=red>处理中</font>";
                        break;
                }
            }
            else{
                s+= '<tr><td class="name">入库时间：</td><td class="value">';
                s+= data.orderTime;
                s+= '</td><td class="name">上传时间：</td><td class="value">';
                s+= data.putTime;
                s+= '</td></tr>';
                s+= '<tr><td class="name">状态：</td><td class="value">';
                switch(data.processStatus){
                    case '1':
                        s+= "<font color=green>完成</font>";
                        break;
                    case '0':
                        s+= "<font color=red>处理中</font>";
                        break;
                }
                s+= '</td></tr>';
            }
            s+= '</td></tr></table>';
            html= html +s+'</div><div class="btndiv"><span class="btn btn-blue">关闭</span></div></dd></dl></div>';
            $('body').append(html);
            $('.info').fadeIn();
            $('.info .btn').click(function(){
                $('.info').remove();
            });
            $('.info').show();
            //鼠标可以拖拽 弹出的可视窗口
            $('.info dl dt').mousedown(function(ev){
                var startX = ev.pageX;
                var startY = ev.pageY;
                $(document).mousemove(function(ev){
                    
                    var offset = new Object();
                    var disX = ev.pageX -startX;
                    var disY = ev.pageY -startY;
                    startX = ev.pageX;
                    startY = ev.pageY;
                    offset.left = $('.info dl').offset().left + disX;
                    offset.top = $('.info dl').offset().top + disY;
                    $(".info dl").offset(offset);
                });
                $(document).mouseup(function(){
                    $(document).off();
                });
            });
        },'json');
    }
};