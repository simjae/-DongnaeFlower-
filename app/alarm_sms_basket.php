<?
include_once dirname(__FILE__).'/alarm_sms_basket.php';
//재고 알람 SMS 요청 기능
?>
	<style>
		.setDiv {
			text-align: left;
		}
		
		.setDiv h1.tit{ padding:0 0 10px 0; font-size:15px; color:#333;}
		
		.mask {
			position:absolute;
			left:0;
			top:0;
			z-index:9999;
			display:none;
		}
		.window {
			border:1px solid #ddd;
			
			display: none;
		   background:#fbfbfb;
			
			z-index:99999;
			padding:20px;
			width:250px;
			
			position:fixed;
			left:50%!important; 
			margin-left:-145px;
		}
		.window table {border-collapse:collapse; background:#fff;}
		.window table th {text-align:center; border:1px solid #d4d4d4; font-weight:bold; background:#fff!important;}
		.window table td {padding:10px; border:1px solid #d4d4d4;}
		.window table td input {border:1px solid #ccc; height:19px; padding-left:4px; }
		.window .btn_box {text-align:center; margin-top: 10px;}
		.window .btn_box a {display:inline-block; border:1px solid #d4d4d4; margin:0 2px; padding:5px 10px; background:#fff;}
	</style>

<div class="setDiv">
	<div class="mask"></div>
	<div class="window">
		<form name="alarm_form" id="alarm_form" method="post">
		<input type="hidden" name="alarm_productcode" id="alarm_productcode" value="" />

		<h1 class="tit">품절상품 알림요청</h1>
		<table border="0" cellpadding="0" cellspacing="1" width="100%">
			<colgroup>
				<col width="40%"/>
				<col width="60%" />
			</colgroup>
			<tr>
				<th style="background:#f2f2f2;">요 청 자</th>
				<td><input type="text" name="alarm_id" id="alarm_id" maxlength="20" value="" /></td>
			</tr>
			<tr>
				<th style="background:#f2f2f2;">연락처(휴대폰)</th>
				<td><input type="text" name="alarm_mobile" id="alarm_mobile"  maxLength="13" value="<?=$mobile?>"  /></td>
			</tr>
		</table>
		<table border="0" cellpadding="0" cellspacing="1" width="100%">
			<colgroup>
				<col width="50%"/>
				<col width="50%" />
			</colgroup>
			<!--<tr>
				<td align='center'><a href="javascript:;" onclick="a_application();">신청</a></td>
				<td align='center'><a href="javascript:;" onclick="wrapWindowhide();">닫기</a></td>
			</tr>-->
		</table>
		
		<div class="btn_box">
		<a href="javascript:;" onclick="a_application();">신청</a>
		<a href="javascript:;" onclick="wrapWindowhide();">닫기</a>
		</div>
		</form>
	</div>
</div>
<iframe src="" name="hidden_form" id="hidden_form" width="1" height="1"  frameborder="0" marginwidth="0"  marginheight="0" scrolling="no" ></iframe>
<script type="text/javascript">
	function autoHypenPhone(str){
				str = str.replace(/[^0-9]/g, '');
				var tmp = '';
				if( str.length < 4){
					return str;
				}else if(str.length < 7){
					tmp += str.substr(0, 3);
					tmp += '-';
					tmp += str.substr(3);
					return tmp;
				}else if(str.length < 11){
					tmp += str.substr(0, 3);
					tmp += '-';
					tmp += str.substr(3, 3);
					tmp += '-';
					tmp += str.substr(6);
					return tmp;
				}else{              
					tmp += str.substr(0, 3);
					tmp += '-';
					tmp += str.substr(3, 4);
					tmp += '-';
					tmp += str.substr(7);
					return tmp;
				}
				return str;
			}

	var cellPhone = document.getElementById('alarm_mobile');
	cellPhone.onkeyup = function(event){
			event = event || window.event;
			var _val = this.value.trim();
			this.value = autoHypenPhone(_val) ;
	}

	function wrapWindowByMask(){
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
		$('.mask').css({'width':maskWidth,'height':maskHeight});
		$('.mask').fadeIn();
		var left = ( $(window).scrollLeft() + ( $(window).width() - $('.window').width()) / 2 );
		var top = ( $(window).scrollTop() + ( $(window).height() - $('.window').height()) / 2 );
		$('.window').css({'left':left,'top':top, 'position':'absolute'});
		$('.window').show();
	}

	function wrapWindowhide(){
		$('#alarm_id').val("");
		$('#alarm_mobile').val("");
		$('.mask, .window').hide();
		$('.window').hide();
	}


	function alarm_productcode_add(va){
		$('#alarm_productcode').val(va);
	}

	function a_application(){

		if(!$('#alarm_id').val()){
			alert("요청자를 입력해주세요.");
			$('#alarm_id').focus();
			return;
		}
		if(!$('#alarm_mobile').val()){
			alert("연락처를 입력해주세요.");
			$('#alarm_mobile').focus();
			return;
		}
	
		if (confirm("해당 연락처로 알림요청을 진행하시겠습니까?") == true){
			document.alarm_form.target = "hidden_form";
			document.alarm_form.action = "./alarm_sms_process.php";
			document.alarm_form.submit();
		}	
	}
 
	$(document).ready(function(){
		$('#showMask').click(function(e){
			e.preventDefault();
			wrapWindowByMask();
		});

		$('.window .close').click(function (e) {
			e.preventDefault();
			$('.mask, .window').hide();
			$('#alarm_id').val("");
			$('#alarm_mobile').val("");
		});
		$('.mask').click(function () {
			$(this).hide();
			$('.window').hide();
			$('#alarm_id').val("");
			$('#alarm_mobile').val("");
		});
	});
</script>
