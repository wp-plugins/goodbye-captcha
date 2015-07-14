<?php
/*
 * Copyright (C) 2014 Mihai Chelaru
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

defined( 'ABSPATH' ) || exit;

function gdbcRewriteNoCacheHeaders($arrHeaders)
{
	$arrHeaders['Cache-Control'] = 'no-store, no-cache, must-revalidate, max-age=0';
	$arrHeaders['Content-Type']  = 'application/javascript; charset=utf-8';

	return $arrHeaders;
}

add_filter('nocache_headers', 'gdbcRewriteNoCacheHeaders', 1);

nocache_headers();


?>

(function($) {
	$.GdbcClient = function(el, options) {
		var gdbcClient = this, defaults = {};
		gdbcClient.settings = {};
		var init = function() {
			gdbcClient.settings = $.extend({}, defaults, options);
		};

		gdbcClient.requestTokens = function() {
			$('form input[name=' + Gdbc.formFieldName + ']').each(function(){
				requestTokenValue($(this));
			});
		};

		var requestTokenValue = function(elm) {
			var ajaxData = {};

			ajaxData[Gdbc.formFieldName] = '<?php echo GdbcTokenController::getInstance()->getAjaxNonce(); ?>';
			ajaxData['action']      = 'retrieveToken';
			ajaxData['requestTime'] = (new Date()).getTime();
			ajaxData['browserInfo'] = JSON.stringify(Gdbc.browserInfo);
			$.ajax({
				type : "post",
				cache: false,
				dataType : "json",
				url : Gdbc.ajaxUrl + '?t=' + ajaxData['requestTime'],
				data : ajaxData,
				success: function(response){
					$.each(response, function(prop, val){
						if(prop === 'token'){
							elm.val(val);return;
						}

						var value = '', arrValues = val.split('|');
						for(var i=0; i<arrValues.length; ++i) {
							if (Gdbc.browserInfo.hasOwnProperty(arrValues[i]))
								value += Gdbc.browserInfo[arrValues[i]];
						}
						$('<input>').prop({type:'hidden',name:prop,value:value}).appendTo(elm.closest('form'));
					});
				}
			});
		};
		init();
	}
})(jQuery);

jQuery(document).ready(function($){(new $.GdbcClient()).requestTokens();});
