/*
* @Author: jdi-juma
* @Date:   2017-12-09 18:36:01
* @Last Modified by:   jdi-juma
* @Last Modified time: 2017-12-09 22:14:53
*/
//check islogin
var check_logout_url = $("meta[name=check_logout_url]").attr('content');
var check_login_url = $("meta[name=check_login_url]").attr('content');
var auth_page 		= $("meta[name=auth_page]").attr('content');


var token 			= $("meta[name=_token]").attr('content');
var ipaddress 		= $("meta[name=ipaddress]").attr('content');
var logout_url 		= $("meta[name=logout_url]").attr('content');
var auth_check 		= $("meta[name=auth_check]").attr('content');
var current_url 	= $("meta[name=current_url]").attr('content');

setTimeout(function(){
	if(ipaddress == '')
	{
		ipaddress = $('.ip1').val()+'-'+$('.ip2').val();
	}
},500);


function init_cas() 
{
	if(auth_check == 1)
	{
		/*$.post(check_logout_url, 
		{
			_token 			: token,
			ipaddress		: ipaddress
		}, 
		function(response){
			if(response == 1)
			{
				// force logout
				$.get(logout_url, function(response){
					return false;
				});
			}
			return false;
		});*/
	}
	else
	{
		$.post(check_login_url, 
			{
				_token 			: token,
				ipaddress		: ipaddress
			}, 
			function(response){
				if(response == 1)
				{
					window.location.href = auth_page;
				}
				else
				{
					$.get(logout_url, function(response){
						return false;
					});
				}
			});
	}
}

setTimeout(function(){
	init_cas();
}, 1000);

