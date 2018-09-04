/*
update at 2017-04-14 by cw
 */
var Init={
    init:function(){
        var _this=this;
        // 时间选择
        _this.timePicker();
        // 提交查询
        _this.formSubmit(_this.getParam());
        //查询按钮请求数据
        $("#getSearch").click(function(){
            _this.formSubmit(_this.getParam());
        })
        // checkbox选择
        var is_daily=$('#is_daily');
        is_daily.on('click',function(){
            if($(this).is(':checked')){
               $(this).val(1);
            }else{
                $(this).val(0);
            }
        });
        if(is_daily.val()==1){
            is_daily.prop("checked",true);
        }
        // 监听中国地图点击事件
        //地图点击事件监听
        myChart.on('click', function (params){
            console.log(params.name)
            //屏蔽台湾省和南沙诸岛的数据
            if(params.name=='台湾省'||params.name==''){
                myChart.dispatchAction({
                    type: 'mapSelect',
                    name: localStorage.getItem("Pro_name")
                });
                alert('该地区暂无数据！');
            }else if (params.name == '重庆市') {
                var mapType = [];
                for (var city in cityMap) {
                    mapType.push(city);
                    var geoJsonName = cityMap[params.name];
                }
                var param = _this.getParam();
                // 显示城市地图数据
                param.cityName = params.name;
                param.cityCode = geoJsonName;
                // 获取城市数据
                _this.getAreaMapData(param);
                // 更改最后一次的选中
                localStorage.setItem("City_name",param.cityName);
            } else {
                //判断选中状态如果未选择将其高亮
                var selected = params.data['selected'];
                if(selected==false){
                    myChart.dispatchAction({
                        type: 'mapSelect',
                        name: localStorage.getItem("Pro_name")
                    });
                }
                var param = _this.getParam();
                // 显示城市地图数据
                param.proName = params.name;
                param.proCode = params.data.code;
                // 获取城市数据
                _this.getCityMapData(param);
                // 更改最后一次的选中
                localStorage.setItem("Pro_name",params.name);
                localStorage.setItem("Pro_code",params.data.code);
            }
        });
        myChart2.on('click', function (params){
            var mapType = [];
            for (var city in cityMap) {
                mapType.push(city);
                var geoJsonName = cityMap[params.name];
            }
            var param = _this.getParam();
            // 显示城市地图数据
            param.cityName = params.name;
            param.cityCode = geoJsonName;
            // 获取城市数据
            _this.getAreaMapData(param);
            // 更改最后一次的选中
            localStorage.setItem("City_name",param.cityName);
        });
        //表格点击事件
        $('#sf_data tbody').on( 'click', 'tr', function () {
            var param = _this.getParam();
            var table = $('#sf_data').DataTable();
            if($(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
            var selected_data = table.row( this ).data();
            param.proName=selected_data.name;
            param.proCode=selected_data.proCode;
            //设置上次选中的
            localStorage.setItem("last_selected",selected_data.proCode);
            //刷新左边数据
            // table.ajax.reload(null,false);
            _this.getCityTableData({id:selected_data.proCode,param:param});
        });
    },
    formSubmit:function(param){
        common.loading();
        var _this=this;
        //首次加载子地图默认浙江省，否则读取本地存储数据
        var proName=localStorage.getItem("Pro_name");
        var proCode=localStorage.getItem("Pro_code");
        var cityName=localStorage.getItem("City_name");
        var last_selected=localStorage.getItem("last_selected");
        if(proName=='' || proName==undefined || proName==null){
            param.proName="江苏省";
        }else{
            param.proName=proName;
        }
        if(proCode=='' || proCode==undefined || proCode==null){
            param.proCode="320000";
        }else{
            param.proCode=proCode;
        }
        if(cityName=='' || cityName==undefined || cityName==null){
            param.cityName="无锡市";
        }else{
            param.cityName=cityName;
        }
        localStorage.setItem("Pro_name",param.proName);
        localStorage.setItem("Pro_code",param.proCode);
        localStorage.setItem("City_name",param.cityName);
        // 获取数据汇总数据
        _this.getSumData(param);
        // 获取卡片数据
        _this.getCardData(param);
        // 获取中国地图
        _this.getChinaMapData(param);
        // 获取城市数据
        _this.getCityMapData(param);
        // 获取中国表格数据
        _this.getChinaTableData({param:param});
        // 获取城市表格数据
        _this.getCityTableData({id:last_selected,param:param});
        //下载表格数据
        _this.getDown(common.getRptRootUrl()+'charts/down_area_date',param);
        
    },
    getSumData:function(param){
        var _this=this;
        if(sourceSwitch==1&&param['endTime']<nowDate){
            var data = JSON.parse(localStorage.getItem(_this.localKey(param,'sumData')));
            if(data!=='' && data!==undefined && data!==null){
                _this.fillInSumData(data);
                return false;
            }
        }
        //获取数据汇总的数据
        $.post(common.getRptRootUrl()+'charts/get_sum_of_data',{param:param},function(data){
            _this.fillInSumData(data);
            localStorage.setItem(_this.localKey(param,'sumData'),JSON.stringify(data));
        });
    },
    getCardData:function(param){
        var _this=this;
        if(sourceSwitch==1&&param['endTime']<nowDate){
            var data = JSON.parse(localStorage.getItem(_this.localKey(param,'cardData')));
            if(data!=='' && data!==undefined && data!==null){
                _this.fillInCardData(data);
                return false;
            }
        }
        //获取卡片汇总的数据
        $.post(common.getRptRootUrl()+'charts/get_total_data',{param:param},function(data){
            _this.fillInCardData(data);
            localStorage.setItem(_this.localKey(param,'cardData'),JSON.stringify(data));
        });
    },
    getChinaMapData:function(param){
        var _this=this;
        if(sourceSwitch==1&&param['endTime']<nowDate){
            var data = JSON.parse(localStorage.getItem(_this.localKey(param,'chinaMapData')));
            if(data!=='' && data!==undefined && data!==null){
                _this.fillInChinaMapData(data);
                return false;
            }
        }
        //获取省份地图的数据
        myChart.showLoading();
        $.post(common.getRptRootUrl()+'charts/get_pro_map_data',{param:param},function(data){
            myChart.hideLoading();
            _this.fillInChinaMapData(data);
            localStorage.setItem(_this.localKey(param,'chinaMapData'),JSON.stringify(data));
        });
    },
    getCityMapData:function(param){
        var _this=this;
        if(sourceSwitch==1&&param['endTime']<nowDate){
            var data = JSON.parse(localStorage.getItem(_this.localKey(param,'cityMapData')));
            if(data!=='' && data!==undefined && data!==null){
                _this.fillInCityMapData(param.proName,data);
                return false;
            }
        }
        //获取城市地图的数据
        myChart2.showLoading();
        $.post(common.getRptRootUrl()+'charts/get_city_map_data',{id:param.proCode,param:param},function(data){
            datas = [];
            data.city.forEach(function(c) {
                data.data.forEach(function(d) {
                    if(c.name==d.name){
                        c.value=d.scanNum;
                    }
                });
                datas.push({name:c.name,value:c.value});
            });
            myChart2.hideLoading();
            _this.fillInCityMapData(param.proName,datas);
            localStorage.setItem(_this.localKey(param,'cityMapData'),JSON.stringify(datas));
        });
    },
    getAreaMapData:function(param){
        console.log(param);
        var _this=this;
        if(sourceSwitch==1&&param['endTime']<nowDate){
            var data = JSON.parse(localStorage.getItem(_this.localKey(param,'areaMapData')));
            if(data!=='' && data!==undefined && data!==null){
                _this.fillInAreaMapData(param.cityCode,data);
                return false;
            }
        }
        //获取区域地图的数据
        myChart3.showLoading();
        $.post(common.getRptRootUrl()+'charts/get_area_map_data',{id:param.cityName,param:param},function(data){
            datas = [];
            data.city.forEach(function(c) {
                data.data.forEach(function(d) {
                    if(c.name==d.name){
                        c.value=d.scanNum;
                    }
                });
                datas.push({name:c.name,value:c.value});
            });
            myChart3.hideLoading();
            _this.fillInAreaMapData(param.cityCode,datas);
            localStorage.setItem(_this.localKey(param,'areaMapData'),JSON.stringify(datas));
        });
    },
    getChinaTableData:function(params){
        var _this=this;
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,
            "processing": false,
            "info":     false,
            "stateSave": true,
            "searching":false,
            "bDestroy": true,
            "pageLength": 10,
            "dom":'tip',
            "ajax":{
                url:common.getRptRootUrl()+dataurl,
                type:'POST',
                data:params!=undefined?params:{},
            },
            "columns": [
                        {"data":"name","class":"center"},
                        {"data":"scanNum","class":"center"},
                        {"data":"userId","class":"center"},
                        {"data":"redNum","class":"center"},
                        {"data":"pointAmount","class":"center"}
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
                var api = this.api();
                var e=api.rows({page:'current'}).data();
                if(e.length<'10'&&e.length!='0'){
                    document.getElementById("sf_data_wrapper").style="min-height:750px";
                }else{
                    document.getElementById("sf_data_wrapper").style="";
                }
                //遍历表格让其高亮
                for($i=0;$i<e.length;$i++){
                    this.$(':contains("'+localStorage.getItem("Pro_name")+'")').addClass('selected');
                }
            }
        };
        this.table=$("#sf_data").dataTable(config);
    },
    getCityTableData:function(params){
        var _this=this;
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,
            "processing": false,
            "info":     false,
            "stateSave": true,
            "searching":false,
            "bDestroy": true,
            "dom":'tip',
            "ajax":{
                url:common.getRptRootUrl()+dataurl2,
                type:'POST',
                data:params!=undefined?params:{},
            },
            "columns": [
                        {"data":"name","class":"center"},
                        {"data":"scanNum","class":"center"},
                        {"data":"userId","class":"center"},
                        {"data":"redNum","class":"center"},
                        {"data":"pointAmount","class":"center"},
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.unloading();
                common.autoHeight();
            }
        };

        this.table=$("#sq_data").dataTable(config);
    },
    // 获取请求参数
    getParam:function(){
        var startTime=$("#start_time").val();
        var endTime=$("#end_time").val();
        var productid=$("#productid").val();
        var batchid=$("#batchid").val();
        var is_daily = $("#is_daily").val() || 0;
        var tabIdentity=$('.tab_current').attr('data-identtity');
        var param={startTime:startTime,endTime:endTime,productid:productid,batchid:batchid,is_daily:is_daily,tab:tabIdentity}
        return param;
    },
    getDown:function(url,param){
        var _this=this;
        $("#getDown").click(function(){
            //下载直接赋予值
            var is_daily = $("#is_daily").val() || 0;
            param.is_daily=is_daily;
            rptbase.postDowndata(url,param);
        })
    },
    // 初始化时间选择
    timePicker:function(){
        var md=new Date();
        function pickedFunc(){  
            var Y=$dp.cal.getP('y');
            var M=$dp.cal.getP('M');  
            var D=$dp.cal.getP('d');  
            M=parseInt(M,10)-1;  
            D=parseInt(D,10)+30;
            var d = new Date()  
            d.setFullYear(Y,M,D)
            var nowDate=new Date();　  
            if(nowDate<=d){
                md=nowDate;  
            }else{
                 var month=d.getMonth()+1;
                md=d.getFullYear()+"-"+month+"-"+d.getDate();
            }  
        }  
        //第一个清空的时候的操作  
        function clearedFunc(){ 
            md=new Date();  
        }  
        //给第二个输入框定义规则  
        function endTimeRule(ele){  
            WdatePicker({el:ele,minDate:'#F{$dp.$D(\'start_time\')}',maxDate:md})  
        }
        $('#start_time').focus(function(){
            WdatePicker({
                maxDate:'#F{$dp.$D(\'end_time\')||\'new Date()\'}',
                minDate:'#F{$dp.$D(\'end_time\',{d:-31})}',
                onpicked:pickedFunc,
                oncleared:clearedFunc
            })
        });
        $('#end_time').focus(function(){
            endTimeRule(this);
        });
    },
    // 根据参数返回唯一本地存储标识 param 条件参数 level是类型
    localKey:function(param,level){
        var localKey = '';
        for(var i in param){
            localKey += "_"+param[i];
        } 
        localKey = '@@charts_region_'+level+'_'+ENV+'_'+mchId+localKey;
        return localKey;
    },
    getProEname:function(name){
        var nameArr={'北京市':"beijing", '天津市':"tianjin", '河北省':"hebei", '山西省':"shanxi", '内蒙古自治区':"neimenggu", '辽宁省':"liaoning", '吉林省':"jilin", '黑龙江省':"heilongjiang", '上海市':"shanghai", '江苏省':"jiangsu", '浙江省':"zhejiang", '安徽省':"anhui", '福建省':"fujian", '江西省':"jiangxi", '山东省':"shandong", '河南省':"henan", '湖北省':"hubei", '湖南省':"hunan", '广东省':"guangdong", '广西壮族自治区':"guangxi", '海南省':"hainan", '重庆市':"chongqing", '四川省':"sichuan", '贵州省':"guizhou", '云南省':"yunnan", '西藏自治区':"xizang", '陕西省':"shanxi1", '甘肃省':"gansu", '青海省':"qinghai", '宁夏回族自治区':"ningxia", '新疆维吾尔自治区':"xinjiang", '台湾省':"taiwan", '香港特别行政区':"xianggang", '澳门特别行政区':"aomen"};
        return nameArr[name];
    },
    // 填充数据汇总
    fillInSumData:function(data){
        $("#getDown").show();
        $("#get_daily_down").show();
        $("#city").html(data['city']);
        $("#red_city").html(data['red_city']);
        $("#scan_all").html(data['scan_all']);
        $("#scan_num").html(data['scan_num']);
        $("#gps_per").html(data['per']+'%');
    },
    // 填充卡片
    fillInCardData:function(data){
        $(".red_total").html(common.splitFormoney(data.total.redNum,2));
        $(".point_total").html(data.total.pointAmount);
        $(".scan_total").html(data.total.scanNum);
        $(".red_total_none").html(common.splitFormoney(data.total_none.redNum,2));
        $(".point_total_none").html(data.total_none.pointAmount);
        $(".scan_total_none").html(data.total_none.scanNum);
    },
    // 填充中国地图
    fillInChinaMapData:function(data){
        $.get('/static/echarts/json/china.json', function (geoJson) {
            myChart.hideLoading();
            echarts.registerMap('china', geoJson);
            myChart.setOption(option = {
                title: {
                      show:false
                  },
                  tooltip: {
                      trigger: 'item',
                      formatter: '{b}<br/>扫码次数：{c}'
                  },
                  legend: {
                      show:false
                  },
                  visualMap: {
                      show:false,
                      min: 0,
                      max: 8000,
                      text:['高','低'],
                      calculable: true,
                      color: ['#0080ff','#03a6ff','#6ab2fa','#a3daff','#F5F5F5']
                  },
                  toolbox: {
                      show: false,
                  },
                  series: [
                      {
                          name: '地域省份数据分布',
                          type: 'map',
                          mapType: 'china',
                          selectedMode:'single',
                          roam: false,
                          showLegendSymbol:false,
                          label: {
                              normal: {
                                  show: false
                              },
                              emphasis: {
                                  show: true
                              }
                          },
                          itemStyle:{
                              normal:{
                                  areaColor: '#F5F5F5',
                                  borderColor:'#333',
                                  borderWidth:0.1
                              },
                              emphasis: {                 
                                  areaColor: '#24a497',
                                  borderWidth:0.5,
                                  borderColor:'#fff',
                                  label: {
                                      show: true,
                                      textStyle: {
                                          color: '#333'
                                      }
                                  }
                              }
                          },
                          data:data,
                          nameMap: { '北京': '北京市', '天津': '天津市', '河北': '河北省', '山西': '山西省', '内蒙古': '内蒙古自治区', '辽宁': '辽宁省', '吉林': '吉林省', '黑龙江': '黑龙江省', '上海': '上海市', '江苏': '江苏省', '浙江': '浙江省', '安徽': '安徽省', '福建': '福建省', '江西': '江西省', '山东': '山东省', '河南': '河南省', '湖北': '湖北省', '湖南': '湖南省', '广东': '广东省', '广西': '广西壮族自治区', '海南': '海南省', '重庆': '重庆市', '四川': '四川省', '贵州': '贵州省', '云南': '云南省', '西藏': '西藏自治区', '陕西': '陕西省', '甘肃': '甘肃省', '青海': '青海省', '宁夏': '宁夏回族自治区', '新疆': '新疆维吾尔自治区', '台湾': '台湾省', '香港': '香港特别行政区', '澳门': '澳门特别行政区'}
                      }
                  ]
            }); 
            //高亮
            myChart.dispatchAction({
                type: 'mapSelect',
                name: localStorage.getItem("Pro_name")
            }); 
        });
    },
    // 填充城市地图
    fillInCityMapData:function(jsonMap,data){
        var _this = this;
        $('#main2').show();
        $('#main3').hide();
        $.get('/static/echarts/json/'+_this.getProEname(jsonMap)+'.json', function (geoJson) {
            echarts.registerMap(jsonMap, geoJson);
            myChart2.setOption(option = {
                title: {
                    show:false
                },
                legend: {
                    show:false
                },
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}<br/>扫码次数：{c}'
                },
                toolbox: {
                    show: false,
                },
                visualMap: {
                    show:false,
                      min: 0,
                      max: 8000,
                      text:['高','低'],
                      calculable: true,
                      color: ['#0080ff','#03a6ff','#6ab2fa','#a3daff','#F5F5F5']
                },
                series: [
                    {
                        name: '',
                        type: 'map',
                        mapType: jsonMap, // 自定义扩展图表类型
                        showLegendSymbol:true,
                        label: {
                            normal: {
                                show: true,
                                textStyle:{
                                    color:'#4b57a6',
                                    fontSize:'8'
                                }
                            },
                            emphasis: {
                                show: true,
                                textStyle:{
                                    color:'#333',
                                    fontSize:'8'
                                }
                            }
                        },
                        itemStyle:{
                              normal:{
                                  areaColor: 'red',
                                  borderColor:'#333',
                                  borderWidth:0.1,
                                  color:'#ff1e00'
                              },
                              emphasis: {                 
                                  areaColor: '#24a497',
                                  borderWidth:0.5,
                                  borderColor:'#fff',
                                  label: {
                                      show: true,
                                      textStyle: {
                                          color: '#333'
                                      }
                                  }
                              }
                          },
                        data:data
                    }
                ]
            });
          //高亮
            myChart2.dispatchAction({
                type: 'mapSelect',
                name: localStorage.getItem("City_name")
            });
        });
    },
    fillInAreaMapData:function(jsonMap,data){
        $.get('/static/echarts/json/city/'+jsonMap+'.json', function (geoJson) {
            //隐藏2
            $('#main2').hide();
            $('#main3').show();
            echarts.registerMap(jsonMap, geoJson);
            myChart3.setOption(option = {
                title: {
                    show:false
                },
                legend: {
                    show:false
                },
                tooltip: {
                    trigger: 'item',
                    formatter: '{b}<br/>扫码次数：{c}'
                },
                toolbox: {
                    show: false,
                },
                visualMap: {
                    show:false,
                      min: 0,
                      max: 8000,
                      text:['高','低'],
                      calculable: true,
                      color: ['#0080ff','#03a6ff','#6ab2fa','#a3daff','#F5F5F5']
                },
                series: [
                    {
                        name: '',
                        type: 'map',
                        mapType: jsonMap, // 自定义扩展图表类型
                        showLegendSymbol:true,
                        label: {
                            normal: {
                                show: true,
                                textStyle:{
                                    color:'#4b57a6',
                                    fontSize:'8'
                                }
                            },
                            emphasis: {
                                show: true
                            }
                        },
                        itemStyle:{
                              normal:{
                                  areaColor: '#F5F5F5',
                                  borderColor:'#333',
                                  borderWidth:0.1,
                                  color:'#ff1e00'
                              },
                              emphasis: {                 
                                  areaColor: '#24a497',
                                  borderWidth:0.5,
                                  borderColor:'#fff',
                                  label: {
                                      show: true,
                                      textStyle: {
                                          color: '#333'
                                      }
                                  }
                              }
                          },
                        data:data
                    }
                ]
            });
        });
    }
}
$(function(){
    Init.init();
});