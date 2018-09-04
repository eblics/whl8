Date.prototype.format = function (fmt) { //author: meizz
    var o = {
        "M+": this.getMonth() + 1, //月份
        "d+": this.getDate(), //日
        "h+": this.getHours(), //小时
        "m+": this.getMinutes(), //分
        "s+": this.getSeconds(), //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds() //毫秒
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
};
if (typeof String.prototype.endsWith != 'function') {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
};

Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
};

//返回标准yyyy-MM-dd日期格式，测试已通过
Date.prototype.toDateString = function(){
	var year=this.getFullYear();
	var month=this.getMonth()+1;
	var day=this.getDate();
	return year+'-'+(month<10?'0'+month:month)+'-'+(day<10?'0'+day:day);
};

Date.parse = function (str) {
    var arr = str.split('-');
    return new Date(parseInt(arr[0]),parseInt(arr[1])-1,parseInt(arr[2]));
};

Date.prototype.getDateOfWeek=function(w, y) {
    var t=new Date(y,0,1,0,0,0);
    var day=t.getDay();
    //从周一开始
    day=(day==0)?6:day-1;
    var d = (w - 1) * 7-day+1;
    t.setDate(t.getDate()+d);
    return t;
}

//根据年、月、周、日返回时间范围
Date.prototype.getDateSpan=function(y,m,w,d) {
   // console.log(y);
   // console.log(m);
   // console.log(w);
   // console.log(d);
    var span={start:new Date(),end:new Date()};
    var t=new Date();
    y=(y==0)?t.getFullYear():y;
    if(w!=0){
        span.start=t.getDateOfWeek(w,y);
        span.end=span.start.addDays(6);
        return span;
    }
    m=(m==0)?1:m;
    if(d==0){
        span.start=new Date(y,parseInt(m)-1,1,0,0,0);
        span.end=new Date(y,parseInt(m),1,0,0,0);
        return span;
    }
    span.start=new Date(y,m,d);
    span.end=new Date(y,m,parseInt(d),0,0,0);
    return span;
}

function getWeeks(y,m){
    var weeks=[];
    var start=new Date(y,m,1);
    var end=new Date(y,m+1,0);
    var day=start.getDay();
    day=(day==0)?6:day-1;
    start.setDate(start.getDate()-day);

    day=end.getDay();
    day=(day==0)?6:day-1;
    end.setDate(end.getDate()-day+6);
    while(start<end){
        var t={};
        t.start=new Date(start);
        start.setDate(start.getDate()+6);
        t.end=new Date(start);
        weeks.push(t);
    }
    return weeks;
}

function getWeekNumber(d){
    console.log(d);
    d.setHours(0,0,0,0);
    d.setDate(d.getDate()+4-(d.getDay()||7));
    return Math.ceil((((d-new Date(d.getFullYear(),0,1))/8.64e7)+1)/7);
};

function getWeekNumberOfMonth(d){//根据日期计算周 add by cw
    // var weeks=getWeeks(d.getFullYear(),d.getMonth())
    // for(var i=0;i<weeks.length;i++){
    //     if(d>=weeks[i].start&&d<weeks[i].end)
    //         return i+1;
    // }
    // return -1;
    //拆分年月日
    var strs=d.toString().split("-");
    var year=strs[0];
    var month=strs[1];
    var day=strs[2];
    var day1 = new Date(year, month-1, day);
    var day2 = new Date(year, 0, 1);
    var firstweek = day2.getDay();//1月1日是星期几
    if(firstweek == 0) {
        firstweek = 6;
    }else {
        firstweek = firstweek - 1;
    }//转化为0表示星期一,6表示星期日
    firstweek = (7 - firstweek) % 7;//计算1月1日离第一周的天数
    var day3 = new Date(year, 0, 1+firstweek)
    var result = Math.round((day1.getTime() - day3.getTime())/(1000*60*60*24));
    result = Math.floor(result / 7)+1;//这个地方应该用floor返回最小次数然后+1
    if(result<10) result="0" +""+ result;
    if(result==00){//如果为0 这是上一年最后一周
        result = getWeekNumberOfMonth(year-1+'-12-31');
        year=year-1;
        return result;
    }else{
        return year+'-'+result;
    }  
}

function get_unix_time(dateStr){//将时间字符串转换成时间戳 格式2017-02-08 18:22:11
    var newstr = dateStr.replace(/-/g,'/'); 
    var date =  new Date(newstr); 
    var time_str = date.getTime().toString();
    return time_str.substr(0, 10);
}


String.prototype.trim=function()
{
    return this.replace(/(^\s*)|(\s*$)/g,'');
}
String.prototype.ltrim=function()
{
    return this.replace(/(^\s*)/g,'');
}
String.prototype.rtrim=function()
{
    return this.replace(/(\s*$)/g,'');
}
String.prototype.toSql=function(params){
	var sql=this;
    for(var i=0;i<params.length;i++){
    	var value=params[i];
    	if(typeof value=='string'){
    		value="'"+value.replace(/'/g,"''")+"'";
    	}
        sql=sql.replace('?',value);
    }
    return sql;
}
//process.env.TZ='Europe/London';
//process.env.TZ='Asia/Shanghai';

var ajaxResponseSuccess = function(data = null, errmsg = null, errcode = 0) {
    return {data: data, errmsg: errmsg, errcode: errcode};
};

var ajaxResponseFail = function(errmsg = '发生未知错误', errcode = 1) {
    return {data: null, errmsg: errmsg, errcode: errcode};
};

exports.getWeeks=getWeeks;
exports.getWeekNumber=getWeekNumber;
exports.getWeekNumberOfMonth=getWeekNumberOfMonth;
exports.get_unix_time=get_unix_time;
exports.ajaxResponseSuccess = ajaxResponseSuccess;
exports.ajaxResponseFail = ajaxResponseFail;
