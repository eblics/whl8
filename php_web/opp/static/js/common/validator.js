/*
 * Poshy Tip jQuery plugin v1.0
 * http://vadikom.com/tools/poshy-tip-jquery-plugin-for-stylish-tooltips/
 * Copyright 2010, Vasil Dinkov, http://vadikom.com/
 */

(function($) {

	var tips = [],
		reBgImage = /^url\(["']?([^"'\)]*)["']?\);?$/i,
		rePNG = /\.png$/i,
		ie6 = $.support.msie && $.support.version == 6;

	// make sure the tips' position is updated on resize
	function handleWindowResize() {
		$.each(tips, function() {
			this.refresh(true);
		});
	}
	$(window).resize(handleWindowResize);

	$.Poshytip = function(elm, options) {
		this.$elm = $(elm);
		this.opts = $.extend({}, $.fn.poshytip.defaults, options);
		this.$tip = $(['<div class="',this.opts.className,'">',
				'<div class="tip-inner tip-bg-image"></div>',
				'<div class="tip-arrow tip-arrow-top tip-arrow-right tip-arrow-bottom tip-arrow-left"></div>',
			'</div>'].join(''));
		this.$arrow = this.$tip.find('div.tip-arrow');
		this.$inner = this.$tip.find('div.tip-inner');
		this.disabled = false;
		this.init();
	};

	$.Poshytip.prototype = {
		init: function() {
			tips.push(this);

			// save the original title and a reference to the Poshytip object
			this.$elm.data('title.poshytip', this.$elm.attr('title'))
				.data('poshytip', this);

			// hook element events
			switch (this.opts.showOn) {
				case 'hover':
					this.$elm.bind({
						'mouseenter.poshytip': $.proxy(this.mouseenter, this),
						'mouseleave.poshytip': $.proxy(this.mouseleave, this)
					});
					if (this.opts.alignTo == 'cursor')
						this.$elm.bind('mousemove.poshytip', $.proxy(this.mousemove, this));
					if (this.opts.allowTipHover)
						this.$tip.hover($.proxy(this.clearTimeouts, this), $.proxy(this.hide, this));
					break;
				case 'focus':
					this.$elm.bind({
						'focus.poshytip': $.proxy(this.show, this),
						'blur.poshytip': $.proxy(this.hide, this)
					});
					break;
			}
		},
		mouseenter: function(e) {
			if (this.disabled)
				return true;

			this.clearTimeouts();
			this.$elm.attr('title', '');
			this.showTimeout = setTimeout($.proxy(this.show, this), this.opts.showTimeout);
		},
		mouseleave: function() {
			if (this.disabled)
				return true;

			this.clearTimeouts();
			this.$elm.attr('title', this.$elm.data('title.poshytip'));
			this.hideTimeout = setTimeout($.proxy(this.hide, this), this.opts.hideTimeout);
		},
		mousemove: function(e) {
			if (this.disabled)
				return true;

			this.eventX = e.pageX;
			this.eventY = e.pageY;
			if (this.opts.followCursor && this.$tip.data('active')) {
				this.calcPos();
				this.$tip.css({left: this.pos.l, top: this.pos.t});
				if (this.pos.arrow)
					this.$arrow[0].className = 'tip-arrow tip-arrow-' + this.pos.arrow;
			}
		},
		show: function() {
			if (this.disabled || this.$tip.data('active'))
				return;

			this.reset();
			this.update();
			this.display();
		},
		hide: function() {
			if (this.disabled || !this.$tip.data('active'))
				return;

			this.display(true);
		},
		reset: function() {
			this.$tip.queue([]).detach().css('visibility', 'hidden').data('active', false);
			this.$inner.find('*').poshytip('hide');
			if (this.opts.fade)
				this.$tip.css('opacity', this.opacity);
			this.$arrow[0].className = 'tip-arrow tip-arrow-top tip-arrow-right tip-arrow-bottom tip-arrow-left';
		},
		update: function(content) {
			if (this.disabled)
				return;

			var async = content !== undefined;
			if (async) {
				if (!this.$tip.data('active'))
					return;
			} else {
				content = this.opts.content;
			}

			this.$inner.contents().detach();
			var self = this;
			this.$inner.append(
				typeof content == 'function' ?
					content.call(this.$elm[0], function(newContent) {
						self.update(newContent);
					}) :
					content == '[title]' ? this.$elm.data('title.poshytip') : content
			);
			
			this.refresh(async);
		},
		refresh: function(async) {
			if (this.disabled)
				return;

			if (async) {
				if (!this.$tip.data('active'))
					return;
				// save current position as we will need to animate
				var currPos = {left: this.$tip.css('left'), top: this.$tip.css('top')};
			}

			// reset position to avoid text wrapping, etc.
			this.$tip.css({left: 0, top: 0}).appendTo(document.body);

			// save default opacity
			if (this.opacity === undefined)
				this.opacity = this.$tip.css('opacity');

			// check for images - this code is here (i.e. executed each time we show the tip and not on init) due to some browser inconsistencies
			var bgImage = this.$tip.css('background-image').match(reBgImage),
				arrow = this.$arrow.css('background-image').match(reBgImage);

			if (bgImage) {
				var bgImagePNG = rePNG.test(bgImage[1]);
				// fallback to background-color/padding/border in IE6 if a PNG is used
				if (ie6 && bgImagePNG) {
					this.$tip.css('background-image', 'none');
					this.$inner.css({margin: 0, border: 0, padding: 0});
					bgImage = bgImagePNG = false;
				} else {
					this.$tip.prepend('<table border="0" cellpadding="0" cellspacing="0"><tr><td class="tip-top tip-bg-image" colspan="2"><span></span></td><td class="tip-right tip-bg-image" rowspan="2"><span></span></td></tr><tr><td class="tip-left tip-bg-image" rowspan="2"><span></span></td><td></td></tr><tr><td class="tip-bottom tip-bg-image" colspan="2"><span></span></td></tr></table>')
						.css({border: 0, padding: 0, 'background-image': 'none', 'background-color': 'transparent'})
						.find('.tip-bg-image').css('background-image', 'url("' + bgImage[1] +'")').end()
						.find('td').eq(3).append(this.$inner);
				}
				// disable fade effect in IE due to Alpha filter + translucent PNG issue
				if (bgImagePNG && !$.support.opacity)
					this.opts.fade = false;
			}
			// IE arrow fixes
			if (arrow && !$.support.opacity) {
				// disable arrow in IE6 if using a PNG
				if (ie6 && rePNG.test(arrow[1])) {
					arrow = false;
					this.$arrow.css('background-image', 'none');
				}
				// disable fade effect in IE due to Alpha filter + translucent PNG issue
				this.opts.fade = false;
			}

			var $table = this.$tip.find('table');
			if (ie6) {
				// fix min/max-width in IE6
				this.$tip[0].style.width = '';
				$table.width('auto').find('td').eq(3).width('auto');
				var tipW = this.$tip.width(),
					minW = parseInt(this.$tip.css('min-width')),
					maxW = parseInt(this.$tip.css('max-width'));
				if (!isNaN(minW) && tipW < minW)
					tipW = minW;
				else if (!isNaN(maxW) && tipW > maxW)
					tipW = maxW;
				this.$tip.add($table).width(tipW).eq(0).find('td').eq(3).width('100%');
			} else if ($table[0]) {
				// fix the table width if we are using a background image
				$table.width('auto').find('td').eq(3).width('auto').end().end().width(this.$tip.width()).find('td').eq(3).width('100%');
			}
			this.tipOuterW = this.$tip.outerWidth();
			this.tipOuterH = this.$tip.outerHeight();

			this.calcPos();

			// position and show the arrow image
			if (arrow && this.pos.arrow) {
				this.$arrow[0].className = 'tip-arrow tip-arrow-' + this.pos.arrow;
				this.$arrow.css('visibility', 'inherit');
			}

			if (async)
				this.$tip.css(currPos).animate({left: this.pos.l, top: this.pos.t}, 200);
			else
				// this.$tip.css({left: this.pos.l, top: this.pos.t});
                //自定义位置
                var thisElmTop=this.$elm.position().top;
                this.$tip.css({left: this.pos.l, top: thisElmTop});
                //end 自定义位置
		},
		display: function(hide) {
			var active = this.$tip.data('active');
			if (active && !hide || !active && hide)
				return;

			this.$tip.stop();
			if ((this.opts.slide && this.pos.arrow || this.opts.fade) && (hide && this.opts.hideAniDuration || !hide && this.opts.showAniDuration)) {
				var from = {}, to = {};
				// this.pos.arrow is only undefined when alignX == alignY == 'center' and we don't need to slide in that rare case
				if (this.opts.slide && this.pos.arrow) {
					var prop, arr;
					if (this.pos.arrow == 'bottom' || this.pos.arrow == 'top') {
						prop = 'top';
						arr = 'bottom';
					} else {
						prop = 'left';
						arr = 'right';
					}
					var val = parseInt(this.$tip.css(prop));
					from[prop] = val + (hide ? 0 : this.opts.slideOffset * (this.pos.arrow == arr ? -1 : 1));
					to[prop] = val + (hide ? this.opts.slideOffset * (this.pos.arrow == arr ? 1 : -1) : 0);
				}
				if (this.opts.fade) {
					from.opacity = hide ? this.$tip.css('opacity') : 0;
					to.opacity = hide ? 0 : this.opacity;
				}
				this.$tip.css(from).animate(to, this.opts[hide ? 'hideAniDuration' : 'showAniDuration']);
			}
			hide ? this.$tip.queue($.proxy(this.reset, this)) : this.$tip.css('visibility', 'inherit');
			this.$tip.data('active', !active);
		},
		disable: function() {
			this.reset();
			this.disabled = true;
		},
		enable: function() {
			this.disabled = false;
		},
		destroy: function() {
			this.reset();
			this.$tip.remove();
			this.$elm.unbind('poshytip').removeData('title.poshytip').removeData('poshytip');
			tips.splice($.inArray(this, tips), 1);
		},
		clearTimeouts: function() {
			if (this.showTimeout) {
				clearTimeout(this.showTimeout);
				this.showTimeout = 0;
			}
			if (this.hideTimeout) {
				clearTimeout(this.hideTimeout);
				this.hideTimeout = 0;
			}
		},
		calcPos: function() {
			var pos = {l: 0, t: 0, arrow: ''},
				$win = $(window),
				win = {
					l: $win.scrollLeft(),
					t: $win.scrollTop(),
					w: $win.width(),
					h: $win.height()
				}, xL, xC, xR, yT, yC, yB;
			if (this.opts.alignTo == 'cursor') {
				xL = xC = xR = this.eventX;
				yT = yC = yB = this.eventY;
			} else { // this.opts.alignTo == 'target'
				var elmOffset = this.$elm.offset(),
					elm = {
						l: elmOffset.left,
						t: elmOffset.top,
						w: this.$elm.outerWidth(),
						h: this.$elm.outerHeight()
					};
				xL = elm.l + (this.opts.alignX != 'inner-right' ? 0 : elm.w);	// left edge
				xC = xL + Math.floor(elm.w / 2);				// h center
				xR = xL + (this.opts.alignX != 'inner-left' ? elm.w : 0);	// right edge
				yT = elm.t + (this.opts.alignY != 'inner-bottom' ? 0 : elm.h);	// top edge
				yC = yT + Math.floor(elm.h / 2);				// v center
				yB = yT + (this.opts.alignY != 'inner-top' ? elm.h : 0);	// bottom edge
			}

			// keep in viewport and calc arrow position
			switch (this.opts.alignX) {
				case 'right':
				case 'inner-left':
					pos.l = xR + this.opts.offsetX;
					if (pos.l + this.tipOuterW > win.l + win.w)
						pos.l = win.l + win.w - this.tipOuterW;
					if (this.opts.alignX == 'right' || this.opts.alignY == 'center')
						pos.arrow = 'left';
					break;
				case 'center':
					pos.l = xC - Math.floor(this.tipOuterW / 2);
					if (pos.l + this.tipOuterW > win.l + win.w)
						pos.l = win.l + win.w - this.tipOuterW;
					else if (pos.l < win.l)
						pos.l = win.l;
					break;
				default: // 'left' || 'inner-right'
					pos.l = xL - this.tipOuterW - this.opts.offsetX;
					if (pos.l < win.l)
						pos.l = win.l;
					if (this.opts.alignX == 'left' || this.opts.alignY == 'center')
						pos.arrow = 'right';
			}
			switch (this.opts.alignY) {
				case 'bottom':
				case 'inner-top':
					pos.t = yB + this.opts.offsetY;
					// 'left' and 'right' need priority for 'target'
					if (!pos.arrow || this.opts.alignTo == 'cursor')
						pos.arrow = 'top';
					if (pos.t + this.tipOuterH > win.t + win.h) {
						pos.t = yT - this.tipOuterH - this.opts.offsetY;
						if (pos.arrow == 'top')
							pos.arrow = 'bottom';
					}
					break;
				case 'center':
					pos.t = yC - Math.floor(this.tipOuterH / 2);
					if (pos.t + this.tipOuterH > win.t + win.h)
						pos.t = win.t + win.h - this.tipOuterH;
					else if (pos.t < win.t)
						pos.t = win.t;
					break;
				default: // 'top' || 'inner-bottom'
					pos.t = yT - this.tipOuterH - this.opts.offsetY;
					// 'left' and 'right' need priority for 'target'
					if (!pos.arrow || this.opts.alignTo == 'cursor')
						pos.arrow = 'bottom';
					if (pos.t < win.t) {
						pos.t = yB + this.opts.offsetY;
						if (pos.arrow == 'bottom')
							pos.arrow = 'top';
					}
			}
			this.pos = pos;
		}
	};

	$.fn.poshytip = function(options){
		if (typeof options == 'string') {
			return this.each(function() {
				var poshytip = $(this).data('poshytip');
				if (poshytip && poshytip[options])
					poshytip[options]();
			});
		}

		var opts = $.extend({}, $.fn.poshytip.defaults, options);

		// generate CSS for this tip class if not already generated
		if (!$('#poshytip-css-' + opts.className)[0])
			$(['<style id="poshytip-css-',opts.className,'" type="text/css">',
				'div.',opts.className,'{visibility:hidden;position:absolute;top:0;left:0;}',
				'div.',opts.className,' table, div.',opts.className,' td{margin:0;font-family:inherit;font-size:inherit;font-weight:inherit;font-style:inherit;font-variant:inherit;}',
				'div.',opts.className,' td.tip-bg-image span{display:block;font:1px/1px sans-serif;height:',opts.bgImageFrameSize,'px;width:',opts.bgImageFrameSize,'px;overflow:hidden;}',
				'div.',opts.className,' td.tip-right{background-position:100% 0;}',
				'div.',opts.className,' td.tip-bottom{background-position:100% 100%;}',
				'div.',opts.className,' td.tip-left{background-position:0 100%;}',
				'div.',opts.className,' div.tip-inner{background-position:-',opts.bgImageFrameSize,'px -',opts.bgImageFrameSize,'px;}',
				'div.',opts.className,' div.tip-arrow{visibility:hidden;position:absolute;overflow:hidden;font:1px/1px sans-serif;}',
			'</style>'].join('')).appendTo('head');

		return this.each(function() {
			new $.Poshytip(this, opts);
		});
	}

	// default settings
	$.fn.poshytip.defaults = {
		content: 		'[title]',	// content to display ('[title]', 'string', element, function(updateCallback){...}, jQuery)
		className:		'tip-yellow',	// class for the tips
		bgImageFrameSize:	10,		// size in pixels for the background-image (if set in CSS) frame around the inner content of the tip
		showTimeout:		500,		// timeout before showing the tip (in milliseconds 1000 == 1 second)
		hideTimeout:		100,		// timeout before hiding the tip
		showOn:			'hover',	// handler for showing the tip ('hover', 'focus', 'none') - use 'none' to trigger it manually
		alignTo:		'cursor',	// align/position the tip relative to ('cursor', 'target')
		alignX:			'right',	// horizontal alignment for the tip relative to the mouse cursor or the target element
							// ('right', 'center', 'left', 'inner-left', 'inner-right') - 'inner-*' matter if alignTo:'target'
		alignY:			'top',		// vertical alignment for the tip relative to the mouse cursor or the target element
							// ('bottom', 'center', 'top', 'inner-bottom', 'inner-top') - 'inner-*' matter if alignTo:'target'
		offsetX:		-22,		// offset X pixels from the default position - doesn't matter if alignX:'center'
		offsetY:		18,		// offset Y pixels from the default position - doesn't matter if alignY:'center'
		allowTipHover:		true,		// allow hovering the tip without hiding it onmouseout of the target - matters only if showOn:'hover'
		followCursor:		false,		// if the tip should follow the cursor - matters only if showOn:'hover' and alignTo:'cursor'
		fade: 			true,		// use fade animation
		slide: 			true,		// use slide animation
		slideOffset: 		8,		// slide animation offset
		showAniDuration: 	300,		// show animation duration - set to 0 if you don't want show animation
		hideAniDuration: 	300		// hide animation duration - set to 0 if you don't want hide animation
	};

})(jQuery);



$(function(){
	//拦截form,在form提交前进行验证
    // $('form').bind('submit',beforeSubmitAct);
	
	//为带有valType属性的元素初始化提示信息并注册onblur事件
	$.each($("[valType]"),function(i, n) {
		$(n).poshytip({
				className: 'tip-yellowsimple',
				content: $(n).attr('msg'),
				showOn: 'none',
				alignTo: 'target',
				alignX: 'right',
				alignY: 'center',
				offsetX: 5,
				offsetY: 10
			});
		$(n).bind('blur',validateBefore);
	});
	
	//定义一个验证器
	$.Validator=function(para) {
		
	
	}

	$.Validator.ajaxValidate=function() {
		beforeSubmitAct();
	}
	
	//验证的方法
	$.Validator.match=function(para) {
		//定义默认的验证规则
		var defaultVal = {
			POSINT: "^[0-9]*[1-9][0-9]*$",
			NUMBER : "^\\d+$",
			TEL : "^0(10|2[0-5789]|\\d{3})-\\d{7,8}$",
			IP : "^((\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5]|[*])\\.){3}(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5]|[*])$",
			MOBILE : "^1(3[0-9]|4[57]|5[0-35-9]|7[06-8]|8[01235-9])\\d{8}$",
			MAIL : "^([a-zA-Z0-9]+[_|\\_|\\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\\_|\\.]?)*[a-zA-Z0-9]+\\.[a-zA-Z]{2,3}$",
			IDENTITY : "^(^[1-9]\\d{7}((0\\d)|(1[0-2]))(([0|1|2]\\d)|3[0-1])\\d{3}$)|(^[1-9]\\d{5}[1-9]\\d{3}((0\\d)|(1[0-2]))(([0|1|2]\\d)|3[0-1])((\\d{4})|\\d{3}[Xx])$)$",
			CHINESE : "^([\\u4E00-\\uFA29]|[\\uE7C7-\\uE7F3])*$",
			URL : "^http[s]?://[\\w\\.\\-]+$",
			UNAME:"",
			MANDM:"^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$|^1(3[0-9]|5[0-35-9]|8[0235-9])\\d{8}$",
            PRONAME:"^.{4,32}$",
            AMOUNT:"^([1-9][\\d]{0,7}|0)(\\.[\\d]{1,2})?$",
            PROBABILITY:"^((([1-9][\\d]{0,1}|0)(\\.[\\d]{1,3})?)|100)$",
            LECODE:"^([1-9][\\d]{0,6}|[1-4][\\d]{7}|50000000)$",
            NOTNULL:"^.{1,}$",
            PASS: "^[a-zA-Z0-9_]{6,18}$",
            WXSEND: "^([\\u4E00-\\uFA29]{1,10}|[\\uE7C7-\\uE7F3]{1,10}|[a-zA-Z0-9]{1,30})$",
            NICKNAME: "^([\\u4E00-\\uFA29]{2,20}|[\\uE7C7-\\uE7F3]{2,20}|[a-zA-Z0-9]{4,40})$",
            NAME: "^([\\u4E00-\\uFA29]{2,20}|[\\uE7C7-\\uE7F3]{2,20})$",
            NUM: "^[3-9]$|^1[0-9]{1}$|^20$"
		};
		var flag=false;
		if(para.rule=='OTHER') {//自定义的验证规则匹配
			flag=new RegExp(para.regString).test(para.data);
		}
		else {
			if(para.rule in defaultVal) {//默认的验证规则匹配
                var testData=para.data.replace(/\n/g,'');
                flag=new RegExp(defaultVal[para.rule]).test(testData);
			}
            //test
            // console.log(defaultVal[para.rule]);
            // console.log(para.data);
            // console.log(para.data.length);
            // console.log(new RegExp(defaultVal[para.rule]));
            // console.log(flag);
            //test
		}
		
		return flag;
	}

	
	
	//为jquery扩展一个doValidate方法，对所有带有valType的元素进行表单验证，可用于ajax提交前自动对表单进行验证
	$.extend({
		doValidate: function() {
			return $.Validator.ajaxValidate();
		}
	});

   });

//输入框焦点离开后对文本框的内容进行格式验证
function validateBefore() {
    if($(this).is(":hidden")) return true;
	//验证通过标识
	var flag=true;
	//获取验证类型
	var valType=$(this).attr('valType');
	//获取验证不通过时的提示信息
	var msg=$(this).attr('msg');
	//自定义的验证字符串
	var regString;
    var thisVal=$(this).val();
	if(valType=='OTHER') {//如果类型是自定义，则获取自定义的验证字符串
		regString=$(this).attr('regString');
		flag=thisVal!=''&&$.Validator.match({data:thisVal, rule:valType, regString:$(this).attr('regString')});
	}
	else {//如果类型不是自定义，则匹配默认的验证规则进行验证
		if(valType=='required') {//不能为空的判断
			if(thisVal=='') {
				flag=false;
			}
		}
		else {//已定义规则的判断
			flag=thisVal!=''&&$.Validator.match({data:thisVal,rule:valType});
		}
	}
	//先清除原来的tips
	$(this).poshytip('hide');
	//如果验证没有通过，显示tips
	if(!flag) {
			$(this).poshytip('show');
	}
	
}

//submit之前对所有表单进行验证
function beforeSubmitAct() {
    var flag=true;
    $.each($("[valType]"),function(i, n) {
        var thisVal=$(this).val();
        //清除可能已有的提示信息
        $(n).poshytip('hide');
        if(!$(this).is(":hidden")){
            if($(n).attr("valType")=='required') {//对不能为空的文本框进行验证
                if($(n).val()=='') {
                    //显示tips			
                    $(n).poshytip('show');
                    flag=false;
                }
            }else if($(n).attr("valType")=='OTHER') {//对自定义的文本框进行验证
                if(!(thisVal!=''&&$.Validator.match({data:thisVal, rule:$(this).attr('valType'), regString:$(this).attr('regString')}))) {
                    $(n).poshytip('show');
                    flag=false;
                }
            }else{//对使用已定义规则的文本框进行验证			
                if(!(thisVal!=''&&$.Validator.match({data:thisVal, rule:$(this).attr('valType')}))) {
                    $(n).poshytip('show');
                    flag=false;
                }
            }
        }
    });
    //自定义回到第一个位置
    setTimeout(function(){
        if($(".tip-yellowsimple").length>0){
            var toPostion=$(".tip-yellowsimple:first").offset().top;
            if(!flag) $("html,body").animate({scrollTop:toPostion},200);
        }
    },100);
    //end 自定义回到第一个位置
    return flag;
}


//下面是测试代码，不属于验证器的功能代码之内
//用原型的方式来模拟js的类
// function Validators() {

// }

// Validators.prototype.subByJs=function(e) {
// 	if($.doValidate()) {
// 		// alert('验证通过');
// 		//todo
// 		return true;
// 	}
// };
