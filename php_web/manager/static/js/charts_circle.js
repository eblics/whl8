// add by cw 2016-11-23
;(function($,window,document,undefined){
var plugin=function(ele, opt){
			this.parent=ele;
			this.defaults= {percent:80,w:500,text_1:"文本1",text_2:"文本2",color_1:"#ffffff",color_2:"red",color_3:"#ccc",oneCircle:"false"};	
			//初始化参数
			this.PARAMS=$.extend({},this.defaults,opt) ;
			this.drawCricle();
}
//定义方法
plugin.prototype={
	  drawCricle:function(){
			var drawOne=this.PARAMS.oneCircle;
			var r=this.PARAMS.w/2;
			var r1=this.PARAMS.w/2-20;
			var x1=this.PARAMS.w/2;
			var y1=this.PARAMS.w/2;
			var canvas=this.parent[0];
			var tip=this.PARAMS.percent;
			var text_1=this.PARAMS.text_1;
			var text_2=this.PARAMS.text_2;
			var color_1=this.PARAMS.color_1;
			var color_2=this.PARAMS.color_2;
			var color_3=this.PARAMS.color_3;
			var is_stop
			var angle="";
			var init=0;
			var preM=0;
			var initM=0;  //因为是半圆  所以初始角度是Math.PI; 
			var s=2*Math.PI/180;
			var bottomC=Math.PI;
			var allCount=180;
			var allCountP=1.8;
			canvas.width=this.PARAMS.w;
			canvas.height=this.PARAMS.w-100;
			var poinits=new Array();
			if(drawOne=="ture"){
				angle=tip*2*Math.PI/100;
				 bottomC=2*Math.PI;
				 allCount=0;
				 allCountP=3.6;
			}else{
				angle=tip*Math.PI/100+Math.PI;
				init=180;
				preM=Math.PI;
				initM=Math.PI;
				s=2*Math.PI/180;
			}
			var ctx=canvas.getContext("2d");
			var radius=this.PARAMS.w/2-2;

			clearInterval(T1);

			var T1;
			function drawScreen(){
				ctx.lineWidth="25";
				ctx.lineCap="round";
				ctx.clearRect(0,0,500,500);

				var s=2*Math.PI/180;
				var start=1*Math.PI;
				if(initM<angle){
					initM+=s;
				}else{
					initM=angle;
					clearInterval(T1);
				}
				ctx.beginPath();
				ctx.strokeStyle="#ebf4f7";
				ctx.arc(x1,y1,80,0,bottomC,true);
				ctx.stroke();
				ctx.closePath();

				ctx.beginPath();
				var gradient=ctx.createLinearGradient(0,0,170,0);
				gradient.addColorStop("0.1",color_1);
				gradient.addColorStop("1.0",color_2);
				ctx.strokeStyle=gradient;
				ctx.arc(x1,y1,80,start,initM,false);
				ctx.stroke();
				// 定义字体大小
				ctx.font="16px Arial";
				// 定义文字(中间文字 text_1)
				ctx.fillStyle="#666";
				var centerText=text_1;
				ctx.fillText(centerText,x1-20,y1-30);
				// 画竖线
				var hr="l";
				ctx.fillStyle=color_3;
				ctx.font="normal normal 100 14px 'Arial'";
				ctx.fillText(hr,x1,y1-95);
				// 画50%
				var bfb="50%";
				ctx.fillStyle="#333";
				ctx.font="normal normal 100 16px 'Arial'";
				ctx.fillText(bfb,x1-ctx.measureText(centerText).width/2,y1-110);

				// 定义文字(中间文字 text_2)
				ctx.fillStyle="#333";
				var centerText=text_2;
				ctx.font="normal normal 800 24px 'Microsoft Yahei'";
				// 获取文本长度 计算位置
				ctx.fillText(centerText,x1-ctx.measureText(centerText).width/2,y1+5);
				ctx.save();
				// 翻转
				ctx.translate(x1,y1);
				ctx.rotate((initM+Math.PI/2)*0.96);
				// 百分比计算
				ctx.font="normal normal 100 14px 'Arial'";
				ctx.fillStyle="#ffffff";

				var percent=tip;
				var text= percent+"%";
				ctx.fillText(text,-ctx.measureText(text).width/2+13,-74);
				ctx.restore();
			}	
		  	T1=setInterval(drawScreen,3) 
	  }
 }
// 检测浏览器是否支持canvas
// function canvasSupport(){
// 	return Modernizr.canvas;
// }	
$.fn.circle=function(options){
	var plugina=new plugin(this,options);
}	
})(jQuery,window,document);