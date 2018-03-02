var bool = true;

$(document).ready(function(){
	$(".qr_btn").click(function(){
		if(!bool){return false;}
		bool = false;
		var u_name = $.trim($("#u_name").val());
		var u_mobile = $.trim($("#u_mobile").val());
		var u_address = $.trim($("#u_address").val());

		if(u_name==''||u_mobile==''||u_address==''){
			alert("信息填写不能为空");bool = true;return false;
		}
		var reg = /^0?1[3|4|5|7|8][0-9]\d{8}$/;
		if(!reg.test(u_mobile)){
			alert("请输入正确的手机号码");bool = true;return false;
		}


		$.ajax({
			type:"POST",
			url:"lib/updateinfo.php",
			
			data:{u_name:u_name,u_mobile:u_mobile,u_address:u_address},
			cache:false,
			dataType: "json",
			success:function(data){
				//alert(data.errMsg);
				if(data.errCode==0){
					var img = parseInt(data.res);
					//alert(img)
					// if(img==6){
					// 	$(".qr6").show();
					// }
					// else if(img==0){
					// 	$(".qr0").show();
					// }
					// else{
					// 	$(".qr").show();
					// }
					$(".qr"+img).show();
					
					$(".wechats").fadeIn("fast");
				}
				else if(data.errCode==1){
					$(".qr0").show();
					$(".wechats").fadeIn("fast");
				}
			},
			beforeSend:function(){},
			error:function(){alert("error");bool = true;},
			complete: function(){}
		});
	});

	$(".close_wechat").click(function(){
		$(".wechats").fadeOut("fast");
	});

	$(".event,.event_2").click(function(){
		$(".rules").fadeIn("fast");
	});

	$(".close_rule").click(function(){
		$(".rules").fadeOut("fast");
	});

});