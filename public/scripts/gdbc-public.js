jQuery(document).ready(function($) {"use strict"; 
var w=window,d=document,e=0,f=0;e|=w.ActiveXObject?1:0;e|=w.opera?2:0;e|=w.chrome?4:0;
e|='getBoxObjectFor' in d || 'mozInnerScreenX' in w?8:0;e|=('WebKitCSSMatrix' in w||'WebKitPoint' in w||'webkitStorageInfo' in w||'webkitURL' in w)?16:0;
e|=(e&16&&({}.toString).toString().indexOf("\n")===-1)?32:0;f|='sandbox' in d.createElement('iframe')?1:0;f|='WebSocket' in w?2:0;
f|=w.Worker?4:0;f|=w.applicationCache?8:0;f|=w.history && history.pushState?16:0;f|=d.documentElement.webkitRequestFullScreen?32:0;f|='FileReader' in w?64:0;

    var ua = navigator.userAgent.toLowerCase();

    var regex = /compatible; ([\w.+]+)[ \/]([\w.+]*)|([\w .+]+)[: \/]([\w.+]+)|([\w.+]+)/g;
    var match = regex.exec(ua);

    var browser = { screenWidth:screen.width, screenHeight:screen.height, engine:e, features:f};

    while (match !== null) {
        var prop = {};

        if (match[1]) {
          prop.type = match[1];
          prop.version = match[2];
        }
        else if (match[3]) {
          prop.type = match[3];
          prop.version = match[4];
        }
        else {
          prop.type = match[5];
        }

        prop.type = $.trim(prop.type).replace(".","").replace(" ","_"); 
        var value = prop.version ? prop.version : true;

        if (browser[prop.type]) {
            if (!$.isArray(browser[prop.type]))
                browser[prop.type] = new Array(browser[prop.type]);

            browser[prop.type].push(value);
        }    
        else browser[prop.type] = value;

        match = regex.exec(ua);
    }
	
    var cookieValue = $.cookie(Gdbc.slug + '-' + Gdbc.formFieldName);
	cookieValue ? reguestTokenValue(cookieValue) : '';
	
    $('form input[name=' + Gdbc.formFieldName + ']').each(function(){var elm = $(this);
        return elm.val() ? reguestTokenValue(elm) : '';
    });
    
	$.each(document.cookie.split(/; */), function()  {
	  var splitCookie = this.split('=');
	  if (typeof splitCookie[0] === 'undefined' || 0 !== splitCookie[0].indexOf(Gdbc.shortCode + '-'))
		  return;
	  $.removeCookie(splitCookie[0], { path: '/' });

	});	
	
    function reguestTokenValue(elm)
    {
        var ajaxData = {}, isJqueryObj = elm instanceof jQuery, date = new Date(); 
        ajaxData[Gdbc.formFieldName] = isJqueryObj ? elm.val() : elm;
        ajaxData['action']      = 'retrieveToken';
        ajaxData['browserInfo'] =  JSON.stringify(browser); 
        $.ajax({
                type : "post",
                cache: false,
                dataType : "json",
                url : Gdbc.ajaxUrl,
                data : ajaxData,
                success: function(response){ date.setMinutes(date.getMinutes() + 15);
                        $.each(response, function(prop, val){
                            if(prop === 'token'){
                               isJqueryObj ? elm.val(val) : $.cookie(Gdbc.formFieldName, val, { expires: date, path: '/' });
                                return;
                            }

                            var value = '', arrValues = val.split('|');
                            for(var i=0; i<arrValues.length;++i)
                                if(browser.hasOwnProperty(arrValues[i])) value += browser[arrValues[i]];
                            
                            isJqueryObj ? $('<input>').attr({type:'hidden',name:prop,value:value}).appendTo(elm.closest('form')): $.cookie(Gdbc.shortCode + '-' + prop, value, { expires: date, path: '/' });							
                        });

                }
        });		        
    };

});
/*! jquery.cookie v1.4.1 | MIT */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?a(require("jquery")):a(jQuery)}(function(a){function b(a){return h.raw?a:encodeURIComponent(a)}function c(a){return h.raw?a:decodeURIComponent(a)}function d(a){return b(h.json?JSON.stringify(a):String(a))}function e(a){0===a.indexOf('"')&&(a=a.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\"));try{return a=decodeURIComponent(a.replace(g," ")),h.json?JSON.parse(a):a}catch(b){}}function f(b,c){var d=h.raw?b:e(b);return a.isFunction(c)?c(d):d}var g=/\+/g,h=a.cookie=function(e,g,i){if(void 0!==g&&!a.isFunction(g)){if(i=a.extend({},h.defaults,i),"number"==typeof i.expires){var j=i.expires,k=i.expires=new Date;k.setTime(+k+864e5*j)}return document.cookie=[b(e),"=",d(g),i.expires?"; expires="+i.expires.toUTCString():"",i.path?"; path="+i.path:"",i.domain?"; domain="+i.domain:"",i.secure?"; secure":""].join("")}for(var l=e?void 0:{},m=document.cookie?document.cookie.split("; "):[],n=0,o=m.length;o>n;n++){var p=m[n].split("="),q=c(p.shift()),r=p.join("=");if(e&&e===q){l=f(r,g);break}e||void 0===(r=f(r))||(l[q]=r)}return l};h.defaults={},a.removeCookie=function(b,c){return void 0===a.cookie(b)?!1:(a.cookie(b,"",a.extend({},c,{expires:-1})),!a.cookie(b))}});





