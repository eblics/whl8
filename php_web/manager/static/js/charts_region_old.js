$(function(){
    Init.init();
});
var Init = {
    init:function(){
            var _this=this;
            _this.formSubmit(_this.getParam());
            _this.search(); 
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

            $('#start_time').focus(function() {
            WdatePicker({
                    lang:'zh-cn',
                    dateFmt: 'yyyy-MM-dd',
                    onpicked:function(){
                      $dp.$('end_time').focus();
                    }
                });
            });

            $('#end_time').focus(function() {
                WdatePicker({
                    lang:'zh-cn',
                    dateFmt: 'yyyy-MM-dd',
                    maxDate: '#F{$dp.$D(\'start_time\', {d:+90})}',
                    minDate: '#F{$dp.$D(\'start_time\')}',
                });
            });
    },
    // 获取请求参数
    getParam:function(){
        var startTime=$("#start_time").val();
        var endTime=$("#end_time").val();
        var productid=$("#productid").val();
        var batchid=$("#batchid").val();
        var is_daily = $("#is_daily").val() || 0;
        var param={startTime:startTime,endTime:endTime,productid:productid,batchid:batchid,is_daily:is_daily}
        return param;
    },
    search:function(){
        var _this=this;
        //查询按钮请求数据
        $("#getSearch").click(function(){
            _this.formSubmit(_this.getParam());
        })
        //表格点击事件
        $('#sf_data tbody').on( 'click', 'tr', function () {
            var table = $('#sf_data').DataTable();
            if($(this).hasClass('selected') ) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
            var selected_data = table.row( this ).data();
            _this.creat_Localdata({name:selected_data['name']});//先创建本地存储
            //设置上次选中的
            localStorage.setItem("last_selected",selected_data['name']);
            //刷新左边数据
            table.ajax.reload(null,false);
            _this.createTable2({city:selected_data['name'],param:_this.getParam()});
        });
        //地图点击事件监听
        myChart.on('click', function (params){
            //屏蔽台湾省和南沙诸岛的数据
            if(params.name=='台湾省'||params.name==''){
                myChart.dispatchAction({
                    type: 'mapSelect',
                    name: localStorage.getItem("Pro_name")
                });
                alert('该地区暂无数据！');
            }else{
                //判断选中状态如果未选择将其高亮
                var selected = params.data['selected'];
                if(selected==false){
                    myChart.dispatchAction({
                        type: 'mapSelect',
                        name: localStorage.getItem("Pro_name")
                    });
                }
                _this.creat_Localdata(params);//先创建本地存储
                var Pro_name=localStorage.getItem("Pro_name");
                var Pro_Json_Map=localStorage.getItem("Pro_Json_Map");
                if(Pro_name=='' || Pro_name==undefined || Pro_name==null){
                    Pro_name="浙江省";
                    Pro_Json_Map="zhejiang";
                }
                _this.creatSF_count(Pro_name,Pro_Json_Map,_this.getParam());
            }
        });
        myChart2.on('click', function (params){
            localStorage.setItem("City_name",params.name);
            var mapType = [];
            for (var city in cityMap) {
                mapType.push(city);
                var geoJsonName = cityMap[params.name];
            }
            if(geoJsonName!=undefined){
                //创建区级地图数据
                _this.creatCity_count(params.name,geoJsonName,_this.getParam());
            }else{
                console.log('●_● 木有数据啦！');
            }
        });
    },
    formSubmit:function(param){
        var _this=this;
        common.loading();
        //获取数据汇总的数据
        $.post(common.getRptRootUrl()+'charts/get_sum_of_data',{param:param},function(data){
            common.unloading();
            $("#getDown").show();
            $("#get_daily_down").show();
            $("#city").html(data['city']);
            $("#red_city").html(data['red_city']);
            $("#scan_all").html(data['scan_all']);
            $("#scan_num").html(data['scan_num']);
            $("#gps_per").html(data['per']+'%');

        });
        // 获取卡片总和
        $.post(common.getRptRootUrl()+'charts/get_total_data',{param:param},function(resp){
            $(".red_total").html(common.splitFormoney(resp.total.redNum,2));
            $(".point_total").html(resp.total.pointAmount);
            $(".scan_total").html(resp.total.scanNum);
            $(".red_total_none").html(common.splitFormoney(resp.total_none.redNum,2));
            $(".point_total_none").html(resp.total_none.pointAmount);
            $(".scan_total_none").html(resp.total_none.scanNum);
        });

         //首次加载子地图默认浙江省，否则读取本地存储数据
        var Pro_name=localStorage.getItem("Pro_name");
        var Pro_Json_Map=localStorage.getItem("Pro_Json_Map");
        if(Pro_name=='' || Pro_name==undefined || Pro_name==null){
            Pro_name="浙江省";
            Pro_Json_Map="zhejiang";
        }
        _this.creatChina_Map(param);
        //加载子地图
        _this.creatSF_count(Pro_name,Pro_Json_Map,param);
        //创建表格数据
        _this.createTable({param:param});
        //默认右边表格是浙江省
        _this.createTable2({city:Pro_name,param:param});
        //下载表格
        _this.getDown(common.getRptRootUrl()+'charts/down_area_date',param);
    },
    //创建地图数据
    creatChina_Map:function(param){
        myChart.showLoading();
        $.post(common.getRptRootUrl()+'charts/get_pro_map_data',{param:param},function(data){
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
                                  name: '扫码记录统计',
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
                                          nameMap: {
                                              '北京': '北京市',
                                              '天津': '天津市',
                                              '河北': '河北省',
                                              '山西': '山西省',
                                              '内蒙古': '内蒙古自治区',
                                              '辽宁': '辽宁省',
                                              '吉林': '吉林省',
                                              '黑龙江': '黑龙江省',
                                              '上海': '上海市',
                                              '江苏': '江苏省',
                                              '浙江': '浙江省',
                                              '安徽': '安徽省',
                                              '福建': '福建省',
                                              '江西': '江西省',
                                              '山东': '山东省',
                                              '河南': '河南省',
                                              '湖北': '湖北省',
                                              '湖南': '湖南省',
                                              '广东': '广东省',
                                              '广西': '广西壮族自治区',
                                              '海南': '海南省',
                                              '重庆': '重庆市',
                                              '四川': '四川省',
                                              '贵州': '贵州省',
                                              '云南': '云南省',
                                              '西藏': '西藏自治区',
                                              '陕西': '陕西省',
                                              '甘肃': '甘肃省',
                                              '青海': '青海省',
                                              '宁夏': '宁夏回族自治区',
                                              '新疆': '新疆维吾尔自治区',
                                              '台湾': '台湾省',
                                              '香港': '香港特别行政区',
                                              '澳门': '澳门特别行政区',
                                          }
                              }
                          ]
                    });
                var Pro_name=localStorage.getItem("Pro_name");
                var Pro_Json_Map=localStorage.getItem("Pro_Json_Map");
                if(Pro_name=='' || Pro_name==undefined || Pro_name==null){
                    Pro_name="浙江省";
                    Pro_Json_Map="zhejiang";
                    myChart.dispatchAction({
                        type: 'mapSelect',
                        name: '浙江省'
                    });
                }else{
                    //高亮
                    myChart.dispatchAction({
                        type: 'mapSelect',
                        name: localStorage.getItem("Pro_name")
                    });
                }  
            });
        }); 
    },
    //默认子地图首次加载的省份，这里是通过浏览器H5本地保存的，没有则默认是浙江省
    creatSF_count:function(SF,jsonMap,param){
        myChart2.showLoading();
        $.post(common.getRptRootUrl()+'charts/get_city_map_data', {id:SF,param:param}, function(subdata) {
            myChart2.hideLoading();
            $('#main2').show();
            $('#main3').hide();
            var newdata=subdata;
            $.get('/static/echarts/json/'+jsonMap+'.json', function (geoJson) {
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
                            data:newdata
                        }
                    ]
                });
              //高亮
                myChart2.dispatchAction({
                    type: 'mapSelect',
                    name: localStorage.getItem("City_name")
                });
            });
      }).error(function() {
          console.log('网络错误！');
      });
    },
    //创建区级地图
    creatCity_count:function(CS,jsonMap,param){
        myChart3.showLoading();
        $.post(common.getRptRootUrl()+'charts/get_area_map_data', {id:CS,param:param}, function(subdata) {
            myChart3.hideLoading();
            var newdata=subdata;
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
                            data:newdata
                        }
                    ]
                });
            });
      }).error(function() {
          console.log('网络错误！');
      });
    },
    getDown:function(url,param){
        var _this=this;
        $("#getDown").click(function(){
            //下载直接赋予值
            var is_daily = $("#is_daily").val() || 0;
            param.is_daily=is_daily;
            rptbase.postDowndata(url,param);
            // console.log(param);
        })
    },
    createTable:function(params){
        var _this=this;
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     false,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "pageLength": 10,
            "dom":'tip',
            "ajax":{
                url:common.getRptRootUrl()+dataurl,//请求数据地址
                type:'POST',//请求方式
                data:params!=undefined?params:{},//携带参数
            },
            "columns": [
                        {"data":"name","class":"center"},
                        {"data":"scanNum","class":"center"},
                        {"data":"userId","class":"center"},
                        // {
                        //   "data":null,"class":"center",
                        //   "render": function (data,type,row) {
                        //       return _this.splitUserId(data.userId);
                        //   }
                        // },
                        {"data":"redNum","class":"center"},
                        {"data":"pointAmount","class":"center"}
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
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
    createTable2:function(params){
        var _this=this;
        var config={
            "language": {"url": "/static/datatables/js/dataTables.language.js"},
            "ordering": false,//关闭排序
            "processing": false,//加载中
            "info":     false,
            "stateSave": true,//保存状态，分页开启（H5本地存储，不支持低版本IE）
            "searching":false,
            "bDestroy": true,
            "dom":'tip',
            "ajax":{
                url:common.getRptRootUrl()+dataurl2,//请求数据地址
                type:'POST',//请求方式
                data:params!=undefined?params:{},//携带参数
            },
            "columns": [
                        {"data":"name","class":"center"},
                        {"data":"scanNum","class":"center"},
                        {"data":"userId","class":"center"},
                        // {
                        //   "data":null,"class":"center",
                        //   "render": function (data,type,row) {
                        //       return _this.splitUserId(data.userId);
                        //   }
                        // },
                        {"data":"redNum","class":"center"},
                        {"data":"pointAmount","class":"center"},
                      ],
            "initComplete": function () {
                common.autoHeight();
            },
            "drawCallback":function(){
                common.autoHeight();
            }
        };

        this.table=$("#sq_data").dataTable(config);
    },
    //时间转换
    dateToString:function(date){
        var year=date.getFullYear();
        var month=date.getMonth()+1;
        var day=date.getDate();
        var str=year+'-'+(month>9?month:'0'+month)+'-'+(day>9?day:'0'+day);
        return str;
    },
    // 去重
    // splitUserId:function(string){
    //     var strs= [];
    //     strs=string.split("、");
    //     var res = [];
    //     var json = {};
    //     for(var i = 0; i < strs.length; i++){
    //       if(!json[strs[i]]){
    //         res.push(strs[i]);
    //         json[strs[i]] = 1;
    //       }
    //      }
    //     return res.length;
    // },
    //创建本地存储
    creat_Localdata:function(param){
        localStorage.setItem("Pro_name",param.name);
        switch(param.name)
        {
            case '北京市':
                localStorage.setItem("Pro_Json_Map","beijing");
              break;
            case '天津市':
                localStorage.setItem("Pro_Json_Map","tianjin");
              break;
            case '河北省':
                localStorage.setItem("Pro_Json_Map","hebei");
              break;
            case '山西省':
                localStorage.setItem("Pro_Json_Map","shanxi");
              break;
            case '内蒙古自治区':
                localStorage.setItem("Pro_Json_Map","neimenggu");
              break;
            case '辽宁省':
                localStorage.setItem("Pro_Json_Map","liaoning");
              break;
            case '吉林省':
                localStorage.setItem("Pro_Json_Map","jilin");
              break;
            case '黑龙江省':
                localStorage.setItem("Pro_Json_Map","heilongjiang");
              break;
            case '上海市':
                localStorage.setItem("Pro_Json_Map","shanghai");
              break;
            case '江苏省':
                localStorage.setItem("Pro_Json_Map","jiangsu");
              break;
            case '浙江省':
                localStorage.setItem("Pro_Json_Map","zhejiang");
              break;
            case '安徽省':
                localStorage.setItem("Pro_Json_Map","anhui");
              break;
            case '福建省':
                localStorage.setItem("Pro_Json_Map","fujian");
              break;
            case '江西省':
                localStorage.setItem("Pro_Json_Map","jiangxi");
              break;
            case '山东省':
                localStorage.setItem("Pro_Json_Map","shandong");
              break;
            case '河南省':
                localStorage.setItem("Pro_Json_Map","henan");
              break;
            case '湖北省':
                localStorage.setItem("Pro_Json_Map","hubei");
              break;
            case '湖南省':
                localStorage.setItem("Pro_Json_Map","hunan");
              break;
            case '广东省':
                localStorage.setItem("Pro_Json_Map","guangdong");
              break;
            case '广西壮族自治区':
                localStorage.setItem("Pro_Json_Map","guangxi");
              break;
            case '海南省':
                localStorage.setItem("Pro_Json_Map","hainan");
              break;
            case '重庆市':
                localStorage.setItem("Pro_Json_Map","chongqing");
              break;
            case '四川省':
                localStorage.setItem("Pro_Json_Map","sichuan");
              break;
            case '贵州省':
                localStorage.setItem("Pro_Json_Map","guizhou");
              break;
            case '云南省':
                localStorage.setItem("Pro_Json_Map","yunnan");
              break;
            case '西藏自治区':
                localStorage.setItem("Pro_Json_Map","xizang");
              break;
            case '陕西省':
                localStorage.setItem("Pro_Json_Map","shanxi1");
              break;
            case '甘肃省':
                localStorage.setItem("Pro_Json_Map","gansu");
              break;
            case '青海省':
                localStorage.setItem("Pro_Json_Map","qinghai");
              break;
            case '宁夏回族自治区':
                localStorage.setItem("Pro_Json_Map","ningxia");
              break;
            case '新疆维吾尔自治区':
                localStorage.setItem("Pro_Json_Map","xinjiang");
              break;
            case '台湾省':
                localStorage.setItem("Pro_Json_Map","taiwan");
              break;
            case '香港特别行政区':
                localStorage.setItem("Pro_Json_Map","xianggang");
              break;
            case '澳门特别行政区':
                localStorage.setItem("Pro_Json_Map","aomen");
              break;
            default:
                localStorage.setItem("Pro_Json_Map","zhejiang");
        }
    }
};