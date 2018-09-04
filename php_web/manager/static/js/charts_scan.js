$(function(){
    $(window).off('resize');
    var screenHeight=window.screen.height;
    common.autoHeight();
    $('#container').height($('.rightmain').height()-365);
    //设置标识用来判断是否重置日历选择样式0 否 1 是
    localStorage.setItem("is_rili","1");
    map = new BMap.Map("container",{enableMapClick:false,minZoom:5});
    map.enableScrollWheelZoom();
    map.disableDoubleClickZoom();
    map.centerAndZoom(new BMap.Point(103.403765, 39.914850), 5);
    map.addControl(new BMap.NavigationControl({ type: BMAP_NAVIGATION_CONTROL_SMALL,anchor: BMAP_ANCHOR_TOP_RIGHT }));
    var cityCtl=new BMap.CityListControl({anchor: BMAP_ANCHOR_TOP_LEFT});
    cityCtl.onChangeBefore.push(function(){
        scan.flag=false;
    });
    cityCtl.onChangeAfter.push(function(){
        if(scan.flag==false)
            scan.refresh();
    });
    map.addControl(cityCtl);
    
    function FullScreenControl(){
        this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
        this.defaultOffset = new BMap.Size(10, 45);
    }
    FullScreenControl.prototype = new BMap.Control();
    FullScreenControl.prototype.initialize = function(map){
        var div = document.createElement("div");
        div.id='btnFullScreen';
        div.innerHTML='全屏查看';
        div.setAttribute('isfull',0);
        common.fullScressStatus(function(status){
            if(status=='no'){
                div.innerHTML='全屏查看';
                div.setAttribute('isfull',0);
                $('#container').height(600);
                common.autoHeight();
                $('#container').height($('.rightmain').height()-365);
            }else{
                $('#container').height(screenHeight);
            }
        });
        div.onclick = function(){
            var isfull=div.getAttribute('isfull');
            if(isfull==0){
                var element=document.getElementById("container");
                common.requestFullScreen(element);
                div.innerHTML='退出全屏';
                div.setAttribute('isfull',1);
            }else if(isfull==1){
                common.exitFullScreen();
                div.innerHTML='全屏查看';
                div.setAttribute('isfull',0);
            }
        };
        map.getContainer().appendChild(div);
        return div;
    };
    map.addControl(new FullScreenControl());
    
    scan.init();
});

var map;

var scan = {
    flag:false,
    init:function(){
        var _this=this;
        map.addEventListener("dragend", function () {
            _this.refresh();
        });
        
        map.addEventListener("zoomend", function () {
            _this.flag=true;
            _this.refresh();
        });
        
        //_this.dateRangeInit();
        //_this.selectDateInit();
        _this.filterMenuInit();
    },
    filterMenuInit:function(){
        var _this=this;
        $("#getSearch").click(function(){
            var param=rptbase.getValue();
            param.pro=$("#proCode").val();
            param.city=$("#cityCode").val();
            _this.filterConditions=param;
            _this.refresh();
        });
        $("#getSearch").triggerHandler('click');
        //监测省份选择下拉选中事件
        $("#proCode").change(function(){
            var proCode=$("#proCode").val();
            if(proCode==0){
                $("#cityCode").empty();
                $("#areaCode").empty();
                $("#cityCode").append("<option value='0'>全部</option>");
                $("#areaCode").append("<option value='0'>全部</option>");
            }else{
                //请求选中省份的市
                $("#cityCode").empty();
                $("#areaCode").empty();
                $("#cityCode").append("<option value='0'>全部</option>");
                $("#areaCode").append("<option value='0'>全部</option>");
                $.post('/charts/get_city_list',{proCode:proCode},function(data){
                    if(data==''){
                        $("#cityCode").hide();
                    }else{
                        $("#cityCode").show();
                    }
                    for(var i=0; i<data.length; i++){
                            var option = "";
                            option += "<option value='"+data[i]['code']+"'>"+data[i]['name']+"</option>";
                            $("#cityCode").append(option);
                     }
                },'json');
            }
        });
        //监测城市选择下拉选中事件
        $("#cityCode").change(function(){
            var cityCode=$("#cityCode").val();
            if(cityCode==0){
                $("#areaCode").empty();
                $("#areaCode").append("<option value='0'>全部</option>");
            }else{
                //请求选中省份的市
                $("#areaCode").empty();
                $("#areaCode").append("<option value='0'>全部</option>");
                $.post('/charts/get_dist_list',{cityCode:cityCode},function(data){
                    if(data==''){
                        $("#areaCode").hide();
                    }else{
                        $("#areaCode").show();
                    }
                    for(var i=0; i<data.length; i++){
                        var option = "";
                        option += "<option value='"+data[i]['code']+"'>"+data[i]['name']+"</option>";
                        $("#areaCode").append(option);
                    }
                },'json');
            }
        });
    },
    selectDateInit:function(){
        var _this=this;
        $('.datebtn').click(function(){
            //设置标识用来判断是否重置日历选择样式0 否 1 是
            localStorage.setItem("is_rili","0");
            $('.datebtn').removeClass('active');
            $(this).addClass('active');
            var type=$(this).attr('date');
            if(type=='yesterday'){
                _this.startDate=yesterday;
                _this.endDate=yesterday;
            }
            else if(type=='week'){
                _this.startDate=curweek;
                _this.endDate=today;
            }
            else if(type='month'){
                _this.startDate=curmonth;
                _this.endDate=today;
            }
            _this.refresh();
        });
        _this.startDate=threemonth;
        _this.endDate=today;
        _this.refresh();
    },
    dateRangeInit:function(){
        var _this=this;
        var dateRange = new pickerDateRange('scan_date_get', {
            isTodayValid : true,
            monthRangeMax : 3,
            defaultText : ' 至 ',
            autoSubmit : false,
            theme : 'ta',
            success : function(obj) {
                //设置标识用来判断是否重置日历选择样式0 否 1 是
                localStorage.setItem("is_rili","1");
                if(obj['startDate']==''){
                    ZENG.msgbox.show('请选择时间范围！', 1, 3000);
                }else{
                    if(obj['endDate']==''){
                        obj['endDate']=obj['startDate'];                    
                    }
                    
                    $(".datebtn").removeClass("active");
                    
                    _this.startDate=obj['startDate'];
                    _this.endDate=obj['endDate'];
                    _this.refresh();
                }
            }
        });
    },
    getPointByScale:function(lngScale,latScale){
        var r=6371393;
        var a=100;

        var latMaxScale=Math.PI*r/(2*a);
        var lat=latScale/latMaxScale*90;
        
        var lngMaxScale=Math.cos(lat*Math.PI/180)*(Math.PI*r/a);
        var lng=(lngScale/lngMaxScale*180).toFixed(6);
        lat=lat.toFixed(6);
        
        return new BMap.Point(lng,lat);
    },
    getScaleByPoint:function(lng,lat){
        var r=6371393;
        var a=100;

        var latMaxScale=Math.PI*r/(2*a);
        var latScale=Math.round(lat/90*latMaxScale);
        var latGeo=latScale/latMaxScale*90;
        
        var lngMaxScale=Math.cos(latGeo*Math.PI/180)*(Math.PI*r/a);
        var lngScale=Math.round(lng/180*lngMaxScale);
        
        return {lngScale:lngScale,latScale:latScale};
    },
    drawArea:function(data,times,max,min,isShowLabel){

        var offset=times-1;

        var leftBottom=this.getPointByScale(data.lngScale-0.5,data.latScale-0.5);
        var rightBottom=this.getPointByScale(data.lngScale+offset+0.5,data.latScale-0.5);
        var leftTop=this.getPointByScale(data.lngScale-0.5,data.latScale+offset+0.5);
        var rightTop=this.getPointByScale(data.lngScale+offset+0.5,data.latScale+offset+0.5);
        
        var point={lng:leftBottom.lng+(rightTop.lng-leftBottom.lng)/2,
                    lat:leftBottom.lat+(rightTop.lat-leftBottom.lat)/2};
        
        if(isShowLabel==true){
            var content=data.count.toString();
            var label = new BMap.Label(content,{position:point});
            label.setStyle({
                color : '#333',
                fontFamily:'Arial',
                fontSize:'10px',
                //borderWidth:0,
                border: '1px solid #900',
                borderRadius: '5px',
                opacity:1,
                backgroundColor:'transparent'
            });
            label.setOffset(new BMap.Size(-3.5*content.length,-8));
            
            map.addOverlay(label);
        }

        point.count=data.count;
        return point;
    },
    showDateText:function(){
        if(this.startDate==this.endDate){
            $('#now_time').html(this.startDate);
        }
        else{
            $('#now_time').html(this.startDate+'至'+this.endDate);
        }
    },
    isLoading:0,
    showLoading:function(){
        var _this=this;
        _this.isLoading=1;
        setTimeout(function(){
            if(_this.isLoading==1){
                ZENG.msgbox.show("努力加载中，请稍后...", 6);
                _this.isLoading=2;
            }
        },500);
    },
    hideLoading:function(){
        if(this.isLoading==2){
            ZENG.msgbox._hide();
        }else{
            this.isLoading=0;
        }
    },
    refresh:function(){
        var _this=this;
        _this.showLoading();
        
        map.clearOverlays();
        
        var zoom=map.getZoom();
        var level=18-zoom-3;
        if(level<0)
            level=0;
        var times=Math.pow(2,level);
        var bound = map.getBounds();
        var northWest=this.getScaleByPoint(bound.getSouthWest().lng,bound.getNorthEast().lat);
        var southEast=this.getScaleByPoint(bound.getNorthEast().lng,bound.getSouthWest().lat);
        var north = Math.floor(northWest.latScale/times)+1;
        var south = Math.floor(southEast.latScale/times)-1;
        var west = Math.floor(northWest.lngScale/times)-1;
        var east = Math.floor(southEast.lngScale/times)+1;
        
        //_this.showDateText();
        
        var params={
            north:north,south:south,west:west,east:east,level:level,times:times,
            westlng:bound.getSouthWest().lng,eastlng:bound.getNorthEast().lng
        };
        for(var name in _this.filterConditions){
            if(name!='level')
                params[name]=_this.filterConditions[name];
        }
        
        $.post(common.getRptRootUrl()+'charts/get_scan_area_data',params,function(result){

            var max=result.max;
            var min=Number.MAX_VALUE;
            var data=result.data;
            
            if(data.length==0){
                _this.hideLoading();
                return;
            }
            
            data=data.sort(function(a,b){
                return parseInt(b.count)-parseInt(a.count) || data.indexOf(b)-data.indexOf(a);
            });

            var points=[];
            var scalePoints=[];
            
            data.forEach(function(item,index){
                var isShowLabel=false;
                
                if(scalePoints.length<10){
                    var flag=false;
                    for(var i=0;i<scalePoints.length;i++){
                        if(Math.abs(item.latScale-scalePoints[i].latScale)<4 &&
                           Math.abs(item.lngScale-scalePoints[i].lngScale)<4){
                            flag=true;
                            break;
                        }
                    }
                    if(!flag){
                        scalePoints.push({lngScale:item.lngScale,latScale:item.latScale});
                        isShowLabel=true;
                    }
                }
                if(zoom>17){
                    isShowLabel=true;
                }
                item.lngScale*=times;
                item.latScale*=times;
                points.push(_this.drawArea(item,times,max,min,isShowLabel));
            });
            
            var heatmapOverlay = new BMapLib.HeatmapOverlay({
                radius:60,
                maxOpacity:0.7
            });
            map.addOverlay(heatmapOverlay);
            heatmapOverlay.setDataSet({data:points,max:max});
            
            _this.hideLoading();
        });
    }
};