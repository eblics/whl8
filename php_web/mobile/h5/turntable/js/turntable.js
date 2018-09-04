(function($) {
var supportedCSS,styles=document.getElementsByTagName("head")[0].style,toCheck="transformProperty WebkitTransform OTransform msTransform MozTransform".split(" ");
for (var a=0;a<toCheck.length;a++) if (styles[toCheck[a]] !== undefined) supportedCSS = toCheck[a];
var IE = eval('"v"=="\v"');

jQuery.fn.extend({
    rotate:function(parameters)
    {
        if (this.length===0||typeof parameters=="undefined") return;
        if (typeof parameters=="number") parameters={angle:parameters};
        var returned=[];
        for (var i=0,i0=this.length;i<i0;i++)
            {
                var element=this.get(i);	
                if (!element.Wilq32 || !element.Wilq32.PhotoEffect) {

                    var paramClone = $.extend(true, {}, parameters); 
                    var newRotObject = new Wilq32.PhotoEffect(element,paramClone)._rootObj;

                    returned.push($(newRotObject));
                }
                else {
                    element.Wilq32.PhotoEffect._handleRotation(parameters);
                }
            }
            return returned;
    },
    getRotateAngle: function(){
        var ret = [];
        for (var i=0,i0=this.length;i<i0;i++)
            {
                var element=this.get(i);	
                if (element.Wilq32 && element.Wilq32.PhotoEffect) {
                    ret[i] = element.Wilq32.PhotoEffect._angle;
                }
            }
            return ret;
    },
    stopRotate: function(){
        for (var i=0,i0=this.length;i<i0;i++)
            {
                var element=this.get(i);	
                if (element.Wilq32 && element.Wilq32.PhotoEffect) {
                    clearTimeout(element.Wilq32.PhotoEffect._timer);
                }
            }
    }
});

Wilq32=window.Wilq32||{};
Wilq32.PhotoEffect=(function(){

	if (supportedCSS) {
		return function(img,parameters){
			img.Wilq32 = {
				PhotoEffect: this
			};
            
            this._img = this._rootObj = this._eventObj = img;
            this._handleRotation(parameters);
		}
	} else {
		return function(img,parameters) {
            this._img = img;

			this._rootObj=document.createElement('span');
			this._rootObj.style.display="inline-block";
			this._rootObj.Wilq32 = 
				{
					PhotoEffect: this
				};
			img.parentNode.insertBefore(this._rootObj,img);
			
			if (img.complete) {
				this._Loader(parameters);
			} else {
				var self=this;
				jQuery(this._img).bind("load", function()
				{
					self._Loader(parameters);
				});
			}
		}
	}
})();

Wilq32.PhotoEffect.prototype={
    _setupParameters : function (parameters){
		this._parameters = this._parameters || {};
        if (typeof this._angle !== "number") this._angle = 0 ;
        if (typeof parameters.angle==="number") this._angle = parameters.angle;
        this._parameters.animateTo = (typeof parameters.animateTo==="number") ? (parameters.animateTo) : (this._angle); 

        this._parameters.step = parameters.step || this._parameters.step || null;
		this._parameters.easing = parameters.easing || this._parameters.easing || function (x, t, b, c, d) { return -c * ((t=t/d-1)*t*t*t - 1) + b; }
		this._parameters.duration = parameters.duration || this._parameters.duration || 1000;
        this._parameters.callback = parameters.callback || this._parameters.callback || function(){};
        if (parameters.bind && parameters.bind != this._parameters.bind) this._BindEvents(parameters.bind); 
	},
	_handleRotation : function(parameters){
          this._setupParameters(parameters);
          if (this._angle==this._parameters.animateTo) {
              this._rotate(this._angle);
          }
          else { 
              this._animateStart();          
          }
	},

	_BindEvents:function(events){
		if (events && this._eventObj) 
		{
            if (this._parameters.bind){
                var oldEvents = this._parameters.bind;
                for (var a in oldEvents) if (oldEvents.hasOwnProperty(a)) 
                        jQuery(this._eventObj).unbind(a,oldEvents[a]);
            }

            this._parameters.bind = events;
			for (var a in events) if (events.hasOwnProperty(a)) 
					jQuery(this._eventObj).bind(a,events[a]);
		}
	},

	_Loader:(function()
	{
		if (IE)
		return function(parameters)
		{
			var width=this._img.width;
			var height=this._img.height;
			this._img.parentNode.removeChild(this._img);
							
			this._vimage = this.createVMLNode('image');
			this._vimage.src=this._img.src;
			this._vimage.style.height=height+"px";
			this._vimage.style.width=width+"px";
			this._vimage.style.position="absolute"; // FIXES IE PROBLEM - its only rendered if its on absolute position!
			this._vimage.style.top = "0px";
			this._vimage.style.left = "0px";

			/* Group minifying a small 1px precision problem when rotating object */
			this._container =  this.createVMLNode('group');
			this._container.style.width=width;
			this._container.style.height=height;
			this._container.style.position="absolute";
			this._container.setAttribute('coordsize',width-1+','+(height-1)); // This -1, -1 trying to fix ugly problem with small displacement on IE
			this._container.appendChild(this._vimage);
			
			this._rootObj.appendChild(this._container);
			this._rootObj.style.position="relative"; // FIXES IE PROBLEM
			this._rootObj.style.width=width+"px";
			this._rootObj.style.height=height+"px";
			this._rootObj.setAttribute('id',this._img.getAttribute('id'));
			this._rootObj.className=this._img.className;			
		    this._eventObj = this._rootObj;	
		    this._handleRotation(parameters);	
		}
		else
		return function (parameters)
		{
			this._rootObj.setAttribute('id',this._img.getAttribute('id'));
			this._rootObj.className=this._img.className;
			
			this._width=this._img.width;
			this._height=this._img.height;
			this._widthHalf=this._width/2; // used for optimisation
			this._heightHalf=this._height/2;// used for optimisation
			
			var _widthMax=Math.sqrt((this._height)*(this._height) + (this._width) * (this._width));

			this._widthAdd = _widthMax - this._width;
			this._heightAdd = _widthMax - this._height;	// widthMax because maxWidth=maxHeight
			this._widthAddHalf=this._widthAdd/2; // used for optimisation
			this._heightAddHalf=this._heightAdd/2;// used for optimisation
			
			this._img.parentNode.removeChild(this._img);	
			
			this._aspectW = ((parseInt(this._img.style.width,10)) || this._width)/this._img.width;
			this._aspectH = ((parseInt(this._img.style.height,10)) || this._height)/this._img.height;
			
			this._canvas=document.createElement('canvas');
			this._canvas.setAttribute('width',this._width);
			this._canvas.style.position="relative";
			this._canvas.style.left = -this._widthAddHalf + "px";
			this._canvas.style.top = -this._heightAddHalf + "px";
			this._canvas.Wilq32 = this._rootObj.Wilq32;
			
			this._rootObj.appendChild(this._canvas);
			this._rootObj.style.width=this._width+"px";
			this._rootObj.style.height=this._height+"px";
            this._eventObj = this._canvas;
			
			this._cnv=this._canvas.getContext('2d');
            this._handleRotation(parameters);
		}
	})(),

	_animateStart:function()
	{	
		if (this._timer) {
			clearTimeout(this._timer);
		}
		this._animateStartTime = +new Date;
		this._animateStartAngle = this._angle;
		this._animate();
	},
    _animate:function()
    {
         var actualTime = +new Date;
         var checkEnd = actualTime - this._animateStartTime > this._parameters.duration;

         // TODO: Bug for animatedGif for static rotation ? (to test)
         if (checkEnd && !this._parameters.animatedGif) 
         {
             clearTimeout(this._timer);
         }
         else 
         {
             if (this._canvas||this._vimage||this._img) {
                 var angle = this._parameters.easing(0, actualTime - this._animateStartTime, this._animateStartAngle, this._parameters.animateTo - this._animateStartAngle, this._parameters.duration);
                 this._rotate((~~(angle*10))/10);
             }
             if (this._parameters.step) {
                this._parameters.step(this._angle);
             }
             var self = this;
             this._timer = setTimeout(function()
                     {
                     self._animate.call(self);
                     }, 10);
         }

         // To fix Bug that prevents using recursive function in callback I moved this function to back
         if (this._parameters.callback && checkEnd){
             this._angle = this._parameters.animateTo;
             this._rotate(this._angle);
             this._parameters.callback.call(this._rootObj);
         }
     },

	_rotate : (function()
	{
		var rad = Math.PI/180;
		if (IE)
		return function(angle)
		{
            this._angle = angle;
			this._container.style.rotation=(angle%360)+"deg";
		}
		else if (supportedCSS)
		return function(angle){
            this._angle = angle;
			this._img.style[supportedCSS]="rotate("+(angle%360)+"deg)";
		}
		else 
		return function(angle)
		{
            this._angle = angle;
			angle=(angle%360)* rad;
			// clear canvas	
			this._canvas.width = this._width+this._widthAdd;
			this._canvas.height = this._height+this._heightAdd;
						
			// REMEMBER: all drawings are read from backwards.. so first function is translate, then rotate, then translate, translate..
			this._cnv.translate(this._widthAddHalf,this._heightAddHalf);	// at least center image on screen
			this._cnv.translate(this._widthHalf,this._heightHalf);			// we move image back to its orginal 
			this._cnv.rotate(angle);										// rotate image
			this._cnv.translate(-this._widthHalf,-this._heightHalf);		// move image to its center, so we can rotate around its center
			this._cnv.scale(this._aspectW,this._aspectH); // SCALE - if needed ;)
			this._cnv.drawImage(this._img, 0, 0);							// First - we draw image
		}

	})()
}

if (IE)
{
Wilq32.PhotoEffect.prototype.createVMLNode=(function(){
document.createStyleSheet().addRule(".rvml", "behavior:url(#default#VML)");
		try {
			!document.namespaces.rvml && document.namespaces.add("rvml", "urn:schemas-microsoft-com:vml");
			return function (tagName) {
				return document.createElement('<rvml:' + tagName + ' class="rvml">');
			};
		} catch (e) {
			return function (tagName) {
				return document.createElement('<' + tagName + ' xmlns="urn:schemas-microsoft.com:vml" class="rvml">');
			};
		}		
})();
}
})(jQuery);

// -------------------------------------------------------
// hls h5 utils
(function() {

	var hls = window.hls || {};

	hls.h5util = {};

	function showLoading() {
		var loading = '<img class="hls-loading" src="/app/turntable/images/loading.gif" />';
		$('body').append(loading);
	}
	hls.h5util.showLoading = showLoading;

	function closeLoading() {
		$('body .hls-loading').remove();
	}
	hls.h5util.closeLoading = closeLoading;

	hls.h5api = {};
	var errmsg = '无法连接服务器';

	hls.h5api.Turntable = {};

	function loadAreas(successCallback, faildCallback) {
		$.get('/ranking/areas', {}, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function(err) {
			faildCallback.call(window, errmsg);
		});
	}
	hls.h5api.Turntable.loadAreas = loadAreas;

	// -------------------------------------------------------------------------
	// 初始化大转盘项
	function loadTableItem(successCallback, faildCallback) {
		$.get('/activity/init_table_item', {}, function(resp) {
			if (! resp.errcode) {
				successCallback.call(window, resp.data);
			} else {
				faildCallback.call(window, resp.errmsg);
			}
		}).error(function(err) {
			faildCallback.call(window, errmsg);
		});
	}
	hls.h5api.Turntable.loadTableItem = loadTableItem;
	
	window.hls = hls;

})();

function drawRouletteWheel() {    
  var canvas = document.getElementById("wheelcanvas");    
  if (canvas.getContext) {
	  //根据奖品个数计算圆周角度
	  var arc = Math.PI / (turnplate.restaraunts.length/2);
	  var ctx = canvas.getContext("2d");
	  //在给定矩形内清空一个矩形
	  ctx.clearRect(0,0,422,422);
	  //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式  
	  ctx.strokeStyle = "#FFBE04";
	  //font 属性设置或返回画布上文本内容的当前字体属性
	  ctx.font = '20px Microsoft YaHei';      
	  for(var i = 0; i < turnplate.restaraunts.length; i++) {       
		  var angle = turnplate.startAngle + i * arc;
		  ctx.fillStyle = turnplate.colors[i];
		  ctx.beginPath();
		  //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
		  ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);    
		  ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
		  ctx.stroke();  
		  ctx.fill();
		  //锁画布(为了保存之前的画布状态)
		  ctx.save();   
		  
		  //----绘制奖品开始----
		  ctx.fillStyle = "#E5302F";
		  var text = turnplate.restaraunts[i];
		  var line_height = 17;
		  //translate方法重新映射画布上的 (0,0) 位置
		  ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 
		  	211 + Math.sin(angle + arc / 2) * turnplate.textRadius);
		  
		  //rotate方法旋转当前的绘图
		  ctx.rotate(angle + arc / 2 + Math.PI / 2);
		  
		  /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
		  if(text.indexOf("M")>0){//流量包
			  var texts = text.split("M");
			  for(var j = 0; j<texts.length; j++){
				  ctx.font = j == 0?'bold 22px Microsoft YaHei':'20px Microsoft YaHei';
				  if(j == 0){
					  ctx.fillText(texts[j]+"M", -ctx.measureText(texts[j]+"M").width / 2, j * line_height);
				  }else{
					  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
				  }
			  }
		  }else if(text.indexOf("M") == -1 && text.length>6){//奖品名称长度超过一定范围 
			  text = text.substring(0,6)+"||"+text.substring(6);
			  var texts = text.split("||");
			  for(var j = 0; j<texts.length; j++){
				  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
			  }
		  }else{
			  //在画布上绘制填色的文本。文本的默认颜色是黑色
			  //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
			  ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
		  }
		  
		  //添加对应图标
		  if (text.indexOf("红") > 0) {
			  var img = document.getElementById('img01');
			  ctx.drawImage(img, -(img.width / 2), 10);  
		  } 
		  else if (text.indexOf("卡") >= 0) {
			  var img = document.getElementById('img03');
			  ctx.drawImage(img, -(img.width / 2), 10);
		  }
		  else if (text.indexOf("谢谢") >= 0) {
			  var img = document.getElementById('img02');
			  ctx.drawImage(img, -(img.width / 2), 10);
		  } else {
		  	var img = document.getElementById('img03');
			ctx.drawImage(img, -(img.width / 2), 10);
		  }
		  ctx.restore();
	  }     
  } 
}

$(document).ready(function() {

	var search = location.search, 
		prizeStr,
		alt_txt = '';
	var mch_id, wx_openid, 
		currentAreaCode = 0, 
		areaCityStr, 
		inst_id = search.split('=')[2];

	window.turnplate = {
		restaraunts: [],
		colors: [
			"#FCCA00", 
			"#FBDB00", 
			"#FCCA00", 
			"#FBDB00",
			"#FCCA00", 
			"#FBDB00"
		],	
		outsideRadius: 192,
		textRadius: 155,
		insideRadius: 68,
		startAngle: 0,
		bRotate: true
	};

	//旋转转盘 item:奖品位置; txt：提示语;
	var rotateFn = function (item, txt){
		var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
		if (angles < 270){
			angles = 270 - angles; 
		} else {
			angles = 360 - angles + 270;
		}
		$('#wheelcanvas').stopRotate();
		$('#wheelcanvas').rotate({
			angle: 0,
			animateTo: angles + (360 * 5),
			duration: 8000,
			callback: function () {
				if (txt.indexOf('谢谢') == -1) {
					$('#prize_name').text(prizeStr);
					$('.layer-blank').show();
					prizeStr = '';
				} else {
					alert(txt);
				}
			}
		});
	};

	var start = function() {
		hls.h5util.showLoading();
		hlsjs.takeActivity(function(resp) {
			hls.h5util.closeLoading();
			var item = window.bonus_item.length;
			if (! resp.errcode) {
				$('#wx_qrcode').prop('src', resp.wx_qrcode_url); // '/h5/get_qrcode/' + encodeURIComponent(resp.wx_qrcode_url.split('/0/')[1])
				// 设置返回的结果和转盘项几号对应
				for (var i = 0; i < window.bonus_item.length - 1; i++) {
					if (resp.sId == window.bonus_item[i].strategyId) {
						item = i + 1; // i从0开始，因此要+1
					}
				}
				if (resp.datatype == 0) {
					prizeStr = '红包' + resp.amount + '分';
				} 
				if (resp.datatype == 2) {
					prizeStr = resp.name;
				}
				if (resp.datatype == 3) {
					prizeStr = resp.amount + '积分';
				}
				rotateFn(item, turnplate.restaraunts[item - 1]);
			} else if (resp.errcode == 3) {
				rotateFn(item, turnplate.restaraunts[item - 1]);
				alt_txt = resp.errmsg;
			} else if (resp.errcode == 20) {
				rotateFn(item, turnplate.restaraunts[item - 1]);
				alt_txt = resp.errmsg;
			} else {
				alt_txt = resp.alt_text;
				if (typeof resp.alt_text == 'undefined') {
					alt_txt = '';
				}
				alert(resp.errmsg + "！\n" + alt_txt);
			}
			mch_id = resp.mchId;
		});
	};

	hlsjs.ready(function() {
		hls.h5util.showLoading();
		hls.h5api.Turntable.loadTableItem(function(data) {
			hls.h5util.closeLoading();
			if (data.items.length > 5) {
				data.items = data.items.slice(0, 5);
			}
			data.items.push({"name": '谢谢参与'});
			turnplate.restaraunts = [];
			window.bonus_item = data.items;
			data.items.forEach(function(item) {
				if (item.name.length > 6) {
					item.name = item.name.substr(0, 6);
				}
				turnplate.restaraunts.push(item.name);
			});
			mch_id = data.merchant.id;
			drawRouletteWheel();
			turnplate.bRotate = false;
		}, function(errmsg) {
			hls.h5util.closeLoading();
			alert(errmsg);
		});

		$('.pointer').click(function () {
			if (turnplate.bRotate) return;
			turnplate.bRotate = !turnplate.bRotate;
			// 转动转盘
			start();
			
		});
	});

	// -----------------------------------------------------------------
	// 抽奖规则按钮点击处理
	$('#btn_rule').on('touchend', function() {
		$('.layer-rule').show();
	});

	// -----------------------------------------------------------------
	// 我的奖品按钮点击处理
	$('#btn_prize').on('touchend', function() {
		// $('.layer-prize').show();
		// location.replace('/card/account/' + mch_id);
	});

	// -----------------------------------------------------------------
	// 关闭弹出层按钮点击处理
	$('.btn-layer-close').on('touchend', function() {
		$('.layer').hide();
	});

	window.alert = function(msg) {
		var element  = '';
			element += '<div class="hls-alert">';
			element +=   '<h3>消息</h3>';
			element +=   '<p>' + msg + '</p>';
			element += '</div>';
		$('.hls-alert').remove();
		$('body').append(element);
		if (msg.length > 20) {
			setTimeout(function() {
				$('.hls-alert').fadeOut(); 
			}, 10000);
		} else {
			setTimeout(function() {
				$('.hls-alert').fadeOut(); 
			}, 2000);
		}
		
	};
});

$('.banner .turnplate').height($('.banner .turnplate').width() / 320 * 528);
$('.layer').height($('.layer').width() * 528 / 320);
$('.btn-rule').css('bottom', '3%');
$('.btn-prize').css('top', '30%');