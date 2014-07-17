/*
 * 	Ex Colorbox Form 0.1.3 - jQuery plugin
 *	written by Cyokodog	
 *
 *	Copyright (c) 2011 Cyokodog (http://d.hatena.ne.jp/cyokodog/)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
jQuery(function($){
	$.ex = $.ex || {};

	$.ex.colorboxForm = function (idx,targets,option) {
		var o = this, c = o.config = $.extend({},$.ex.colorboxForm.defaults,option);
		var s = $.ex.colorboxForm.status;

		c.targets = targets;
		c.target = targets.eq(idx);
		c.iframe = true;
		c.speed = c.speed || $.colorbox.settings.speed;
		c._fastIframeSupport = (typeof $.colorbox.settings.fastIframe != 'undefined');
		if(c._fastIframeSupport) c.fastIframe = false;

		var htmlScroll;
		$.extend(c,{
			onOpen : function(){
				htmlScroll = $('html').css('overflow-y');
				$('html').css('overflow-y','hidden');
				!option.onOpen || option.onOpen.call(o,o);
			},
			onLoad : function(){
				if (!s.isCloseIconTop && c.closeIconTopPosition && parseFloat($('#cboxClose').css('bottom')) == 0){
					s.isCloseIconTop = true;
					$('#cboxClose').css({top:0,bottom:'auto'});
				}
				$('#cboxClose').wrapInner('<div class="closeHandle" style="height:100%"/>');
				c.closeHandle = $('div.closeHandle');
				if (c.onClickCloseIcon) {
					c.closeHandle.bind('click',function(){
						return c.onClickCloseIcon.call(o,o);
					});
				}
				!option.onLoad || option.onLoad.call(o,o);
			},
			onComplete : function(){
				var loadedContent = $('#cboxLoadedContent');
				if (s.isCloseIconTop) {
					loadedContent.css({
						'margin-top':parseInt(loadedContent.css('margin-bottom')) + c.closeIconTopMargin,
						'margin-bottom':0
					});
				}
				var loadedHandle = function(){
					var target = $(this);
					var contents = c.contents = o.contnets = target.contents();
					if( c.adjustContentsBackgroundColor &&
						$('#cboxLoadedContent').css('background-color') != 'transparent' &&
						contents.find('html').css('background-color') == 'transparent'){
							contents.find('html').css('background-color',c.adjustContentsBackgroundColor);
					}
					if (c.fitContentsHeight) {
						var contentsBody = contents.find('body');
						var contentsBodyMargin = parseInt(contentsBody.css('margin-top')) + parseInt(contentsBody.css('margin-bottom'));
						var adjustCboxHeight = $('#cboxWrapper').height() - $('#cboxLoadedContent').height();
						var h = contentsBody.height() + contentsBodyMargin + adjustCboxHeight + c.closeIconTopMargin + c.contentsMargin;
						var wh = $(window).height();
						h = h > wh ? wh : h;
						$.colorbox.resize({height:h});
					}
					if (s.isCloseIconTop) {
						setTimeout(function(){
							loadedContent.height(loadedContent.height() - c.closeIconTopMargin);
						},0);
					};
					contents.find(c.closeButtonClass).click(function(){
						if (c.closeButtonSyncCloseIcon) o.close();
						else {
							$.colorbox.close();
						}
					});
					!option.onComplete || option.onComplete.call(o,o);
				}
				c.iframeObject = $('#colorbox iframe').load(loadedHandle);
				!c._fastIframeSupport || loadedHandle.apply(c.iframeObject[0]);
			},
			onClosed : function(){
				$('html').css('overflow-y',htmlScroll);
				!option.onClosed || option.onClosed.call(o,o);
			}
		});

		if (c.target[0].tagName == 'A'){
			c.show || c.target.colorbox(c);
		}
		else
		if (c.target[0].tagName == 'FORM'){
			c._form = c.target;
			c.show || c._form.submit(function(){
				o.show();
				return false;
			});
		}
		else
		if (c.target[0].tagName == 'INPUT'){
			c._form = $(c.target[0].form);
			c.show || c.target.click(function(){
				o.show();
				return false;
			});
		}
		if (c.show) {
			o.show();
		}
	}

	$.extend($.ex.colorboxForm.prototype,{
		getTarget : function(){
			var o = this, c = o.config;
			return c.target;
		},
		getContents : function(){
			var o = this, c = o.config;
			return c.contents;
		},
		getCloseIcon : function(){
			var o = this, c = o.config;
			return c.closeHandle;
		},
		getFrame : function(){
			var o = this, c = o.config;
			return c.iframeObject;
		},
		show : function(){
			var o = this, c = o.config;
			if (!c._form){
				c.target[0].click();
			}
			else{
				var url = c._form.attr('action');
				if (c._form.attr('method') == 'post') {
					url = url + '?' + c._form.serialize();
				}
				$('<a href="' + url + '">-</a>').prependTo('body').colorbox(c).click().remove();
			}
			return o;
		},
		close : function(){
			var o = this, c = o.config;
			if (c.close) {
				if (o.getCloseIcon()[0].click) {
					o.getCloseIcon()[0].click();
				}
				else {
					o.getCloseIcon().trigger('click');
				}
			}
			else {
				$.colorbox.close();
			}

		}
	});

	$.ex.colorboxForm.status = {
		isCloseIconTop : false
	}

	$.ex.colorboxForm.defaults = {
		fitContentsHeight:true,
		adjustContentsBackgroundColor : '#fff',
		contentsMargin:32,
		closeIconTopPosition: true,
		closeIconTopMargin: 8,
		closeButtonClass : '.colorbox-close',
		closeButtonSyncCloseIcon : true,
		close : '[X]',
		overlayClose : false,
		escKey : false,
		width : "750px",
		height : "80%",
		show : false
	}
	$.fn.exColorboxForm = function(option){
		var targets = this;
		return targets.each(function(idx){
			var target = targets.eq(idx);
			var api = target.data('ex-colorbox-form');
			if (!api){
				target.data('ex-colorbox-form',api = new $.ex.colorboxForm(idx,targets,option||{}));
			}
			else
			if (option.show) {
				api.show();
			}
		});
	}
});

