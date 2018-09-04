$(function(){
    rptbase.init();
});
var rptbase = {
    init:function(){
            var _this=this;
            // _this.showNotice();
            var month=$(".month li>.active").attr('data-value');
            $('#daylist').hide();
            _this.get_week(month);
            _this.year();
            _this.month();
            _this.select();
            _this.getProducts();
            $("#getDown").hide();
            $("#get_daily_down").hide();
            $("#userscan_detail_down").hide();  
            //检测商品的下拉事件
            $("#productid").change(function(){
                var productid=$("#productid").val();
                $("#batchid").empty();
                $("#batchid").append("<option value='0'>全部</option>");
                _this.getBatchs(productid);
            });
    },
    year:function(){
        var _this=this;
        $(".year li>a").click(function(){
            $(".year li>a").removeClass("active");
            $(this).addClass("active");
            $(".month li>a").removeClass("active");
            $(".week li>a").removeClass("active");
            $(".month li:first a").addClass("active");
            $(".week li:first a").addClass("active");
            if(window.location.pathname=='/charts/userrank'){
                $("#tool").hide();
                $("#weeklist").hide();
            }
            $("#getDown").hide();
            $("#get_daily_down").hide();
            $("#userscan_detail_down").hide();
            var month=$(".month li:first a").attr('data-value');
            _this.get_week(month);
        });
    },
    month:function(){
        var _this=this;
        $(".month li>a").click(function(){
             $(".month li>a").removeClass("active");
             $(".week li>a").removeClass("active");
             $(".week li:first a").addClass("active");
             $(this).addClass("active");
             var month=$(this).attr('data-value');
             if(month==0){
                $("#tool").hide();
             }else{
                $("#tool").show();
                $("#weeklist").show();
                $('#daylist').hide();
                $('#daylist li>a').removeClass('active');
                $('#daylist li>a').eq(0).addClass('active');
             }
            $("#getDown").hide();
            $("#get_daily_down").hide();
            $("#userscan_detail_down").hide();
            _this.get_week(month);
        });
    },
    select:function(){
        $(".select").change(function(){
            $("#getDown").hide();
            $("#get_daily_down").hide();
            $("#userscan_detail_down").hide();
        })
    },
    get_week:function(month){
        var _this=this;
    	var year=$(".year li>.active").attr('data-value');
        //给月份添加上时间区间
        $(".month li>.active").attr('data-time',year+'-'+month+'-01_'+_this.getLastDay(year,month));
         //获取周信息
         if(month=='0'){
         	$("#weeklist").hide();
         	return false;
         }else{
         	$("#weeklist").show();
         }
         $.post('/charts/get_weekinfo',{'year':year,'month':month},function(data){
            $('.week').empty();
         	var row;
         	var num;
         	$('.week').append('<li><a href="javascript:;" data-value="0" class="active">全部</a></li>');
         	for($i=0;$i<data.length;$i++){
         		row = '<li><a href="javascript:;" data-value="'+data[$i][2]+'" data-time="'+data[$i][3]+'_'+data[$i][4]+'">第'+data[$i][2]+'周<small>('+data[$i][0]+'-'+data[$i][1]+')</small></a></li>';
         		$('.week').append(row);
         	}
            //给月份添加上周区间
            $(".month li>.active").attr('data-week',data[0][2]+'_'+data[data.length-1][2]);
     		$(".week li>a").click(function(){
                var dataTime = $(this).attr("data-time");
                $('#daylist').show();
                var week=$(this).attr('data-value') || 0;
                $(".month li>.active").attr('data-time',dataTime);
                if(week!=0){
                    $("#tool").hide();
                }else{
                    $("#tool").show();
                    $('#daylist').hide();
                    $('#daylist li>a').removeClass('active');
                    $('#daylist li>a').eq(0).addClass('active');
                    $(".month li>.active").attr('data-time',year+'-'+month+'-01_'+_this.getLastDay(year,month));
                }
                $(".week li>a").removeClass("active");
                $(this).addClass("active");
                
                $("#getDown").hide();
                $("#get_daily_down").hide();
                $("#userscan_detail_down").hide();
                
                if(dataTime != undefined || dataTime != null){
                    _this.get_day(dataTime);  
                }
		 	});
         });
        // _this.get_day();
    },
    get_day:function(dataTime){
        var _this = this;
        var dt = dataTime.toString();
        $.post('/charts/get_every_day',{dt:dt},function(res){
            $('.day').empty();
            $('.day').append('<li><a href="javascript:;" data-value="0" class="active">全部</a></li>');
            for (var i = 0; i < res.length; i++) {
                row = '<li><a href="javascript:;" data-value="'+res[i]+'" data-time="'+res[i]+'">'+res[i]+'</a></li>';
                $('.day').append(row);
            }
            _this.get_day_value();
        });
    },
    get_day_value:function(){
        $(".day li>a").click(function(){
            var day =$(this).attr('data-value') || 0;
            if(day!=0){
                $("#tool").hide();
            }else{
                $("#tool").show();
            }
            $(".day li>a").removeClass("active");
            $(this).addClass("active");
            $("#getDown").hide();
            $("#get_daily_down").hide();
            $("#userscan_detail_down").hide();
        });
    },
    getValue:function(){
        var _this = this;
        var year=$(".year li>.active").attr('data-value');
        var month=$(".month li>.active").attr('data-value');
        var week=$(".week li>.active").attr('data-value') || 0;
        var day=$(".day li>.active").attr('data-value') || 0;
        var productid=$("#productid").val();
        var batchid=$("#batchid").val();
        var dataTime=$(".month li>.active").attr('data-time');
        var tabIdentity=$('.tab_current').attr('data-identtity');
        // var dataWeek=$(".month li>.active").attr('data-week');
        //请求数据
        var param={year:year,month:month,week:week,day:day,productid:productid,batchid:batchid,tab:tabIdentity}
        if(param.month!=0&&param.week==0){
            var level=$(".label-menu .selected").attr('data-level') || 'day';
            param.startTime=_this.splitTime(dataTime)[0];
            param.endTime=_this.splitTime(dataTime)[1];
            param.level=level;
        }
        if(param.week!=0){
            var weektime=$(".week .active").attr('data-time');
            param.startTime=_this.splitTime(dataTime)[0];
            param.endTime=_this.splitTime(dataTime)[1];
            param.weektime=weektime;
        }
        if(param.month==0){
            $("#getDown").hide();
            $("#get_daily_down").hide();
            $("#userscan_detail_down").hide();
        }
        //只给区域分布和时段分析提供天数据
        if(window.location.pathname=='/charts/region'||window.location.pathname=='/charts/period'){
            var day = $(".day li>.active").attr('data-value') || 0;
            param.day=day;
        }
        //给用户扫码统计赋予详细扫码下载按钮
        if(window.location.pathname=='/charts/userscan'){
            var is_detail = $("#is_detail").val() || 0;
            param.is_detail=is_detail;
        }
        //给区域报表赋予日下载按钮
        if(window.location.pathname=='/charts/region'){
            var is_daily = $("#is_daily").val() || 0;
            param.is_daily=is_daily;
        }
        if(window.location.pathname=='/charts/policy'){
            //默认给第一个活动
            var aid=localStorage.getItem("Policy_aid") || 0;
            var categoryid=$("#categoryid").val();
            console.log(aid);
            if(aid==0){
                var activityid=$("#activityid").val();
                param.activityid=activityid;
                localStorage.setItem("Policy_aid",activityid);
               
            }else{
                param.activityid=aid;
            }
            
            
            param.categoryid=categoryid;
        }
        return param;
    },
    getDown:function(url,param){
        var _this=this;
        $("#getDown").click(function(){
            var down_data = $("#down_data").val();
            if (down_data === '') {
                return;
            }
            console.log(param);
            _this.postDowndata(url, {param:param,data:down_data});
        })
    },
    getProducts:function(){
        var _this=this;
        var url = '/charts/get_products';
        if (typeof window.chartsType !== 'undefined') {
            url += window.chartsType;
        }
        $.post(url,function(data){
            $("#productid").empty();
            $("#productid").append("<option value='0'>全部</option>");
            for(var i=0; i<data.length; i++){
                var option = "";
                option += "<option value='"+data[i]['id']+"'>"+data[i]['name']+"</option>";
                $("#productid").append(option);
             }
             var productid=$("#productid").val();
            _this.getBatchs(productid);
        },'json')
    },
    getBatchs:function(productid){
        $.post('/charts/get_batchs',{productid:productid},function(data){
            for(var i=0; i<data.length; i++){
                var option = "";
                option += "<option value='"+data[i]['id']+"'>"+data[i]['batchNo']+"</option>";
                $("#batchid").append(option);
             }
        },'json')
    },
    //返回月份最后一天
    getLastDay:function(year,month){
        return year +'-'+ month +'-'+ (new Date(year,month,0)).getDate();
    },
    splitTime:function(arr){
        return arr.split("_");
    },
    StringToArray:function(data){
        var strs= new Array();
        strs=data.split(",");
        for (i=0;i<strs.length ;i++ ) 
        { 
            strs[i]+"<br/>";
        }
        return strs;
    },
    //post下载文件（原理模拟form表单post提交）
    postDowndata:function(url,params,target){
        var tempForm = document.createElement("form");        
        tempForm.action = url;        
        tempForm.method = "post";
        tempForm.target = target;
        tempForm.style.display = "none"; 
        for (var x in params) {        
            var opt = document.createElement("textarea");        
            opt.name = x;        
            opt.value = params[x];      
            tempForm.appendChild(opt);        
        }        
        document.body.appendChild(tempForm);  
        tempForm.submit();
        //console.log(tempForm);
        return tempForm;    
    },
    showNotice: function() {
        common.transDialog(function(callback) {
            var html = '';
            html += '<div style="font-size: 24px;text-align: center;margin: 20px;">温馨提醒：</h1>';
            html += '<div style="font-size:16px;line-height:2;text-align:left;margin-top:10px;">';
            html += '尊敬的客户您好：<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp2017-05-01至今的报表数据暂不准确，数据正在生成中！给您带来不便，我们深感抱歉！';
            html += '</div>';
            html += '<div style="float:right;font-size:16px;line-height:2;">欢乐扫运营团队<br>2017-05-02</div>';
            // html += '<div id="readNotice" class="btn btn-blue">确认</div>';
            callback(html);
        });

    }
};