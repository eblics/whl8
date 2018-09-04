/* global common */
var redpacketLists = {
    init:function(){
        var _this=this;
        _this.createTable();
    },
    delTr:function(){
        $('#redpacketTable tbody td .del').off('click').on("click",function(){
            var _this=$(this);
            common.confirm('确定删除吗？',function(r){
                if(r==1){
                    common.loading();
                    var id=_this.attr('data-id');
                    var dataType=parseInt(_this.attr('data-type'));
                    var url='/redpacket/del';
                    if(dataType==1){
                        url='/redpacket/delsub';
                    }
                    $.post(url,{'id':id},function(d){
                        common.unloading();
                        if(d.errorCode==0){
                            _this.parent('td').parent('tr').addClass('selected');
                            var table=$('#redpacketTable').DataTable();
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
    createTable:function(){
        var _this=this;
        $('#redpacketTable').on('xhr.dt', function ( e, settings, json, xhr ) {
            window.dataList=json.data;
        }).DataTable({
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "paging":   true,
            "ordering": false,
            "order":[[0,'desc']],
            "info":     true,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "ajax": {
                "url":"/redpacket/data"
            },
            "columns": [
                {
                    "data":null,"class":"center",
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '';
                        }else{
                            return data.id;
                        }
                    }
                },
                {
                    "data":null,
                    "render": function (data,type,row) {
                        if(data.parentId){
                            return '<div style="color:#999;padding-left:40px">'+'(ID:'+data.id+') '+data.name+'</div>';
                        }else{
                            return data.name;
                        }
                        
                    }
                },
                {
                    "data":"rpType","class":"center",
                    "render":function(data,type,row){
                        var val='';
                        if(data==0) val='普通';
                        if(data==1) val='裂变';
                        if(row.parentId){
                            return '<div style="color:#999;">'+val+'</div>';
                        }else{
                            return val;
                        }
                    }
                },

                {
                    "data":null,
                    "render":function(data,type,row){
                        var val='';
                        if(data.amtType==0) val='固定 '+(data.amount/100).toFixed(2);
                        if(data.amtType==1) val='随机 '+(data.minAmount/100).toFixed(2)+' ~ '+(data.maxAmount/100).toFixed(2);
                        if(data.amtType==2) val='自定义红包金额比例';
                        if(row.parentId){
                            return '<div style="color:#999;">'+val+'</div>';
                        }else{
                            return val;
                        }
                    }
                },
                {
                    "data":"probability","class":"center",
                    "render":function(data,type,row){
                        var val=parseFloat((data*100).toPrecision(12)).toString();
                        if(row.levelType!=1){
                            val+='%';
                        }else{
                            var probabilityArr=[];
                            for(var i=0;i<window.dataList.length;i++){
                                if(typeof window.dataList[i].parentId!='undefined'){
                                    if(window.dataList[i].parentId==row.id){
                                        if(window.dataList[i].remainNum>0){
                                            probabilityArr.push(window.dataList[i].probability);
                                        }
                                    }
                                }
                            }
                            var lastPro=1;
                            var xpro=1;
                            for(var i=0;i<probabilityArr.length;i++){
                                xpro*=parseFloat((1-probabilityArr[i]).toPrecision(12));
                            }
                            lastPro-=parseFloat(xpro.toPrecision(12));
                            var lastVal='综合（'+(lastPro*100).toFixed(3)+'%）';
                            // console.log(lastVal);
                            val=lastVal;
                        }
                        if(row.parentId){
                            return '<div style="color:#999;">'+val+'</div>';
                        }else{
                            return val;
                        }
                    }
                },
                {
                    "data":null,"class":"center",
                    "render":function(data,type,row){
                        var val=(data.totalAmount/100).toFixed(2)+' / <font color=red>'+(data.remainAmount/100).toFixed(2)+'</font>';
                        if(data.limitType!=1){
                            val='';
                        }
                        if(data.parentId){
                            return '<div style="color:#999;">'+val+'</div>';
                        }else{
                            return val;
                        }
                    }
                },
                {
                    "data":"limitType","class":"center",
                    "render":function(data,type,row){
                        var val='';
                        if(data==0) val='数量 →';
                        if(data==1) val='← 金额';
                        if(row.parentId){
                            return '<div style="color:#999;">'+val+'</div>';
                        }else{
                            return val;
                        }
                    }
                },
                {
                    "data":null,"class":"center",
                    "render":function(data,type,row){
                        var val=data.totalNum+' / <font color=red>'+data.remainNum+'</font>';
                        if(data.limitType!=0){
                            val='';
                        }
                        if(row.levelType==1){
                            var totalNum=0;
                            var remainNum=0;
                            for(var i=0;i<window.dataList.length;i++){
                                if(typeof window.dataList[i].parentId!='undefined'){
                                    if(window.dataList[i].parentId==row.id){
                                        totalNum+=Number(window.dataList[i].totalNum);
                                        remainNum+=Number(window.dataList[i].remainNum);
                                    }
                                }
                            }
                            val='总计（'+totalNum+' / <font color=red>'+remainNum+'</font>）';
                        }
                        if(data.parentId){
                            return '<div style="color:#999;">'+val+'</div>';
                        }else{
                            return val;
                        }
                    }
                },
                {
                    "data":null,
                    "class":"right noselect nowrap",
                    "render": function (data,type,row) {
                        var add='';
                        var edit='<a class="btn-text noselect blue" href="/redpacket/edit/'+data.id+'">修改</a> &nbsp;&nbsp; ';
                        var del='<span class="btn-text noselect del gray" data-id="'+data.id+'">删除</span>';
                        if(data.levelType==1){
                            add='<a class="btn-text noselect blue" href="/redpacket/addsub/'+data.id+'">添加分级红包</a> &nbsp;&nbsp; ';
                        }
                        if(data.parentId){
                            edit='<a class="btn-text noselect blue" href="/redpacket/editsub/'+data.parentId+'/'+data.id+'">修改</a> &nbsp;&nbsp; ';
                            del='<span class="btn-text noselect del gray" data-type="'+(typeof data.parentId!='undefined'?'1':'0')+'" data-id="'+data.id+'">删除</span>';
                        }
                        return add+edit+del;
                    }
                }
            ],
            "initComplete": function () {
                _this.delTr();
                common.autoHeight();
            },
            "drawCallback":function(){
                _this.delTr();
				common.autoHeight();
            }
        });

    }
};
$(function(){
    redpacketLists.init();
});
