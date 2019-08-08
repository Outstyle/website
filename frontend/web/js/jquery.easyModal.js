/**
* easyModal.js v1.3.2
* A minimal jQuery modal that works with your CSS.
* Author: Flavius Matis - http://flaviusmatis.github.com/
* URL: https://github.com/flaviusmatis/easyModal.js
*
* Copyright 2012, Flavius Matis
* Released under the MIT license.
* http://flaviusmatis.github.com/license.html
*/

/*jslint browser: true*/
/*global jQuery*/

!function(a,b){var c=function(a,b,c){var d;return function(){function h(){c||a.apply(f,g),d=null}var f=this,g=arguments;d?clearTimeout(d):c&&a.apply(f,g),d=setTimeout(h,b||100)}};jQuery.fn[b]=function(a){return a?this.bind("resize",c(a)):this.trigger(b)}}(jQuery,"smartModalResize"),function(a){"use strict";var b={init:function(b){var c={top:"auto",left:"auto",autoOpen:!1,overlayOpacity:.5,overlayColor:"#000",overlayClose:!0,overlayParent:"body",closeOnEscape:!0,closeButtonClass:".close",transitionIn:"",transitionOut:"",onOpen:!1,onClose:!1,zIndex:function(){return function(a){return a===-(1/0)?0:a+1}(Math.max.apply(Math,a.makeArray(a("*").map(function(){return a(this).css("z-index")}).filter(function(){return a.isNumeric(this)}).map(function(){return parseInt(this,10)}))))},updateZIndexOnOpen:!0,hasVariableWidth:!1};return b=a.extend(c,b),this.each(function(){var c=b,d=a('<div class="lean-overlay"></div>'),e=a(this);d.css({display:"none",position:"fixed","z-index":c.updateZIndexOnOpen?0:c.zIndex(),top:0,left:0,height:"100%",width:"100%",background:c.overlayColor,opacity:c.overlayOpacity,overflow:"auto"}).appendTo(c.overlayParent),e.css({display:"none",position:"fixed","z-index":c.updateZIndexOnOpen?0:c.zIndex()+1,left:parseInt(c.left,10)>-1?c.left+"px":"50%",top:parseInt(c.top,10)>-1?c.top+"px":"50%"}),e.bind("openModal",function(){var a=c.updateZIndexOnOpen?c.zIndex():parseInt(d.css("z-index"),10),b=a+1;""!==c.transitionIn&&""!==c.transitionOut&&e.removeClass(c.transitionOut).addClass(c.transitionIn),e.css({display:"block","margin-left":(parseInt(c.left,10)>-1?0:-(e.outerWidth()/2))+"px","margin-top":(parseInt(c.top,10)>-1?0:-(e.outerHeight()/2))+"px","z-index":b}),d.css({"z-index":a,display:"block"}),c.onOpen&&"function"==typeof c.onOpen&&c.onOpen(e[0])}),e.bind("closeModal",function(){""!==c.transitionIn&&""!==c.transitionOut?(e.removeClass(c.transitionIn).addClass(c.transitionOut),e.one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend",function(){e.css("display","none"),d.css("display","none")})):(e.css("display","none"),d.css("display","none")),c.onClose&&"function"==typeof c.onClose&&c.onClose(e[0])}),d.click(function(){c.overlayClose&&e.trigger("closeModal")}),a(document).keydown(function(a){c.closeOnEscape&&27===a.keyCode&&e.trigger("closeModal")}),a(window).smartModalResize(function(){c.hasVariableWidth&&e.css({"margin-left":(parseInt(c.left,10)>-1?0:-(e.outerWidth()/2))+"px","margin-top":(parseInt(c.top,10)>-1?0:-(e.outerHeight()/2))+"px"})}),e.on("click",c.closeButtonClass,function(a){e.trigger("closeModal"),a.preventDefault()}),c.autoOpen&&e.trigger("openModal")})}};a.fn.easyModal=function(c){return b[c]?b[c].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof c&&c?void a.error("Method "+c+" does not exist on jQuery.easyModal"):b.init.apply(this,arguments)}}(jQuery);