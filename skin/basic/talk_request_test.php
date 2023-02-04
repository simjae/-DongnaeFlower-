<style>
.font_sp{font-family:'Spoqa Han Sans Neo'}
#noticePage {
	height:calc(100vh - env(safe-area-inset-top));
	height:calc(100vh - constant(safe-area-inset-top));
	background: #4d4d4d;
	border-radius:12px;
	overflow:hidden;
	float:left;
	position: relative;
    width: 100%;
	margin-top: env(safe-area-inset-top);
	margin-top: constant(safe-area-inset-top);
}
.talkProfile {
	width:50px;
	height:50px;
	background: url('/app/skin/basic/svg/talkProfileIcon.svg');
	float: left;
	margin: 8px 8px 8px 15px;
}
.triangle-isosceles {
	max-width:80%;
	width:fit-content;
	position:relative;
	padding:15px;
	margin:8px 0 8px;
	color:#000;
	background:#f4f4f4; /* default background for browsers without gradient support */
	border-radius:6px;
	word-break:break-all;
	word-wrap:break-word;
}
/*.first_isosceles:after {
	content:"";
	position:absolute;
	bottom:-15px; /* value = - border-top-width - border-bottom-width */
	left:8px; /* controls horizontal position */
	border-style:solid;
	display:block;
	width:0;
}*/
.dropzone .dz-preview.dz-image-preview
,.dropzone .dz-progress 
,.dropzone .dz-file-preview{
	display:none;
}
.talk_content_wrap{font-size: 1.3em;}
.talk_button{padding: 10px;border: 1px solid #ebebeb;color: #848484;border-radius: 10px;-webkit-appearance: none;font-size: 14px;}
.talk_input_bg{
	position: fixed;
	bottom: 0;
	background-color: #ffffff;
	width: 100%;
	height: calc(75px + constant(safe-area-inset-bottom));
	height: calc(75px + env(safe-area-inset-bottom));
}
.fc_pink {color:#e61e6e;}
</style>
<form name="requestForm" id="requestForm" action="request_proc.php" method="post" enctype="multipart/form-data">
	<div id="calendarForm" style="display:none">
		<?
			$times = mktime();
			//시작시간
			$startHour = 10;
			//종료시간
			$lastHour = 20;
			//주문가능 시간차
			$gapHour = 4;
			//카렌더 검색가중치
			$gapDay = 0;
			$todayDate = date("Y-m-d", $times+86400+(3600*$gapHour)); 
			$realHour = date("H", $times);
			$nowHour = $realHour + $gapHour;
			if ($nowHour < $startHour){
				$nowHour = $startHour;
			}
			else if($nowHour  >= $lastHour){
				$nowHour = $startHour;
				$todayDate = date("Y-m-d", $times+86400+(3600*24)); 
				$gapDay = 1;
			}
		?>
		<div style="width:100%;">
			<input type="text" name="receiveDateInput" maxLength="10" style="width:46%;float:left;margin-right:10px;padding: 10px;border: 1px solid #ebebeb;color: #848484;border-radius: 10px;-webkit-appearance: none;font-size: 14px;" class="talk_button receiveDate" placeholder="날짜(예 <?=date($todayDate)?>)" value="<?=date($todayDate)?>" readonly/>
			<select name="receiveTimeInput" class="talk_button receiveTime" style="width:46%;float:left;">
				<?for( $i=$startHour ; $i<=20 ; $i++ ){
					$time_str01 = sprintf('%02d',$i) . ':00';
				?>
					<option value="<?=$time_str01?>"  <? if($i == $nowHour){ echo "selected"; } ?>><?=$time_str01?></option>
				<?}?>
			</select>
		</div>
		
	</div>
	<div id="closeForm" style="display:none">
		<?
			//시작시간
			$closeStartHour = 7;
			//종료시간
			$closeLastHour = 20;
			//카렌더 검색가중치
			$closeGapDay = 0;
			$todayDate = date("Y-m-d", $times+(3600* $gapDay )); 
			$realHour = date("H", $times);
			$nowHour = $realHour + $closeGapDay ;
			if ($nowHour < $closeStartHour){
				$nowHour = $closeStartHour;
			}
			else if($nowHour  >= $closeLastHour){
				$nowHour = $closeStartHour;
				$todayDate = date("Y-m-d", $times+(3600*24)); 
				$gapDay = 1;
			}
		?>
		<div>
			<input type="text" name="closeDateInput" maxLength="10" style="width:46%;margin-right:10px; float:left;padding: 10px;border: 1px solid #ebebeb;color: #848484;border-radius: 10px;-webkit-appearance: none;font-size: 14px;" class="closeDate" placeholder="날짜(예 <?=date($todayDate)?>)" value="<?=date($todayDate)?>" readonly/>
			<select name="closeTimeInput" class="talk_button closeTime" style="width:46%;float:left;">
				<?for( $i=$closeStartHour ; $i<=$closeLastHour ; $i++ ){
					$time_str01 = sprintf('%02d',$i). ':00';
				?>
					<option value="<?=$time_str01?>" ><?=$time_str01?></option>
				<?}?>
			</select>
		</div>
	</div>	
	<div id="receiveTypeForm" style="display:none">
		<table cellpadding="0" cellspacing="0" border="0" class="receive_type_select" style="width:100%">
			<tr>
				<td style="width:50%;text-align: center;" onclick="stepProc(this);" selval="0" >
					<img src="skin/basic/svg/receiveType01.svg" style="height: 30px;margin:5px;vertical-align: middle;">
					<span>배송</span>
				</td>
				<td style="width:50%;text-align: center;" onclick="stepProc(this);" selval="1">
					<img src="skin/basic/svg/receiveType02.svg" style="width:30px;margin:5px;vertical-align: middle;">
					<span>픽업</span>
				</td>
			</tr>
		</table>
	</div>
	<div id="addrForm" style="display:none">
		<table cellpadding="0" cellspacing="0" border="0" style="width:97%">
			<tr>
				<td onclick="stepProc(this);" style="text-align: center;">
					<div class="talk_button">배송지 추가</div>
				</td>
			</tr>
		</table>
	</div>
	<div id="prodNumForm" style="display:none">
		<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
			<tr>
				<td colspan="2">
					<div class="order_quantity">
						(<span class="quantityTypeText"></span>)
						<input type="button" value="-" class="qty_button" onClick="quantityControlForm('minus',this);" />
						<input class="qty_input" type="number" name="prodNumInput" size="2" value="1" />
						<input type="button" value="+" class="qty_button" onClick="quantityControlForm('plus',this);" />개
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div id="purposeForm" style="display:none;">
		<table cellpadding="0" cellspacing="10" border="0" style="width:100%;">
			<tr>
				<td style="width:50%">
					
					<select name="purposeInput" class="talk_button purpose" style="width:100%;">
					<?
						$sql = "SELECT * FROM item_mst WHERE keyText='purpose' ORDER BY sortNum";
						//echo $sql;
						$result1=mysql_query($sql,get_db_conn());
						while($row1=mysql_fetch_object($result1)) {
					?>
							<option value="<?=$row1->seq?>"><?=$row1->valText?></option>
					<?	}
						mysql_free_result($result1);?>
					</select>
				</td>
				<td style="width:50%;">
					<select name="productTypeInput" class="talk_button productType" style="width:100%;">
					<?
						$sql = "SELECT * FROM item_mst WHERE keyText='productType' ORDER BY sortNum";
						//echo $sql;
						$result2=mysql_query($sql,get_db_conn());
						while($row2=mysql_fetch_object($result2)) {
					?>
							<option value="<?=$row2->seq?>"><?=$row2->valText?></option>
					<?	}
						mysql_free_result($result2);?>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div id="priceRangeForm" style="display:none">
		<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
			<tr>
				<td>
					<select name="priceRangeInput" class="talk_button priceRange" style="width:96%;">
					<?
						$sql = "SELECT * FROM item_mst WHERE keyText='priceRange' ORDER BY sortNum";
						//echo $sql;
						$result3=mysql_query($sql,get_db_conn());
						while($row3=mysql_fetch_object($result3)) {
							$selected = "";
							if ($row3->sortNum == 4) {
								$selected = "selected";
							}
					?>
							<option value="<?=$row3->seq?>" <?=$selected?>><?=$row3->valText?></option>
					<?	}
						mysql_free_result($result3);?>
					</select>
				</td>
			</tr>
		</table>
	</div>	
	<div id="styleForm" style="display:none">
		<table cellpadding="0" cellspacing="0" border="0" class="styleForm_select" style="width:96%">
			<tr>
				<td style="text-align: center;" onclick="stepProc(this);">
					<div class="talk_button" id="styleSelecter">
						<span>스타일 선택하기</span>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div id="commentForm" style="display:none">
		<textarea class="talk_button" name="commentInput" id="commentInput" placeholder="Ex) 가격이 좀 비싸도 오래가는 꽃으로 만들어주세요" style="border: 1px solid #ebebeb; height: 40px; line-height:1.6em;"></textarea>
	</div>
	<div id="confirmForm" style="display:none;">
		<table cellpadding="0" cellspacing="0" border="0"  style="width:100%;margin: 5px 0;">
			<tr>
				<td style="width:50%;text-align: center;">
					<a onclick="resultHide();" class="basic_button fullsize" style="width:90%;text-align:center;">수정하기</a>
				</td>
				<td style="width:50%;text-align: center;">
					<a onclick="CheckRequestForm();" class="basic_button fullsize" style="width:90%;text-align:center;background:#e61e6e;color:#ffffff;">주문하기</a>
				</td>
			</tr>
		</table>
	</div>
	<div id="procForm" style="display:none">
		<table cellpadding="0" cellspacing="0" border="0"  style="width:100%;margin: 5px 0;">
			<tr>
				<td style="text-align: center;">
					<div class="basic_button fullsize" style="width:90%;text-align:center;background:gray;color:#ffffff;">주문 처리중입니다</div>
				</td>
			</tr>
		</table>
	</div>
	<script>
		var step = 0;
		var modStep = -1;
		var styleSelect = false;
		var htmlArr = new Array();
		var htmlModArr = new Array();
		var htmlBtn = new Array();
		var stepPer = new Array();
		var weekArr = new Array("일요일","월요일","화요일","수요일","목요일","금요일","토요일");
		
		htmlArr[0] = "<?=$_ShopInfo->memname?>님, 안녕하세요?<br>주변 꽃집들에게<br>딱 맞는 제안을 받아보세요.<br>꽃이 언제까지 필요하신가요?";
		htmlArr[1] = "제안서는 언제까지 보내드릴까요?<br>꽃이 필요한 시점으로부터 3시간 전인,<br> ##suggestCloseDateTime##가<br>좋을 것 같아요.";
		htmlArr[2] = "어디로, 어느 분께 보내드릴까요?";
		htmlArr[3] = "안전하게 보내드릴게요.<br>꽃의 용도와 원하는 종류는 무엇인가요?";
		htmlArr[4] = "원하는 가격대를 선택해 주세요.";
		htmlArr[5] = "선택하신 정보로<br>스타일을 추천해 드릴게요!<br>원하시는 스타일이 없으면,<br>사진을 직접 올려주세요.";
		htmlArr[6] = "플로리스트에게 남길 메모가<br>있으시면 알려주세요!";
		htmlArr[7] = "수고하셧습니다! 마지막으로 <br>주문하신 내용을 확인해 주세요.";
		htmlModArr[0] = "날짜수정";
		htmlModArr[1] = "제안서 기간수정"
		htmlModArr[2] = "주소수정";
		htmlModArr[3] = "용도/종류수정";
		htmlModArr[4] = "가격대수정";
		htmlModArr[5] = "스타일수정";
		htmlModArr[6] = "메모수정";
		htmlBtn[0] = $("#calendarForm").html();
		htmlBtn[1] = $("#closeForm").html();
		htmlBtn[2] = $("#receiveTypeForm").html();
		htmlBtn[2] = $("#addrForm").html();
		htmlBtn[3] = $("#purposeForm").html();
		htmlBtn[4] = $("#priceRangeForm").html();
		htmlBtn[5] = $("#styleForm").html();
		htmlBtn[6] = $("#commentForm").html();
		htmlBtn[7] = $("#confirmForm").html();
		stepPer[0] = 12;
		stepPer[1] = 26;
		stepPer[2] = 38;
		stepPer[3] = 52;
		stepPer[4] = 64;
		stepPer[5] = 78;
		stepPer[6] = 90;
		stepPer[7] = 100;
		
		function sendMessage(editFlag){
			var messageStr = $( "input[name=inputMessage]").val();
			var requestStep = 0;
			var requestImage = "";
			if(modStep < 0){
				requestStep = step;
			}
			else{
				requestStep = modStep;
			}
			if( messageStr != ""){
				
				if(requestStep == 5 && !editFlag){
					for(var i = 1 ; i <= 3 ; i++){
						var styleImageSrc = $("#styleImage" + i).val();
						if(styleImageSrc != ""){
							requestImage += "<div style=\"float:left;width:80px;height:90px;margin-left:5px;margin-top:5px;background:#ffffff url('/data/style/" + styleImageSrc + "') no-repeat;background-size:cover;background-position:center\"></div>"
						}
					}
				}
				var html = "<div style=\"clear:both;\"></div>";
				if(!editFlag){
					html += "<div style=\"display: flex;justify-content: flex-end;\"class=\"edit\" onclick=\"editStep('"+ requestStep +"')\"><img style =\"width:25px;margin-right: 10px;\"  src=\"/app/skin/basic/svg/talk_edit.svg\">";
				}
				html += "<div class=\"triangle-isosceles right\">";
				html += messageStr;
				html += "<div style=\"overflow:hidden\">";
				html += requestImage;
				html += "</div>";
				html += "</div>";
				html += "</div>";
				$( "#talk_content_wrap" ).append(html);
				fnMove();
				receiveMessage(messageStr);
				$( "input[name=inputMessage]").val("")
			}
			if(!editFlag){
				if(modStep < 0){
					step++;
				}
				else{
					if(modStep == 0){
						modStep++;
					}
					else{
						modStep = -1;
					}
				}
			}
		}
		
		function editStep(requestStep){
			$("#procBtnTd").show();
			modStep = requestStep;
			$("input[name=inputMessage]").val(htmlModArr[requestStep]);
			sendMessage(true);
		}
		function receiveMessage(messageStr){
			var requestStep = 0;
			if( messageStr != "" || step == 6 ){
				var formData = $("form[name=requestForm]").serialize() ;
				$.ajax({
					type : 'post',
					url : '/api/talk_response.php',
					data : formData,
					dataType : 'json',
					error: function(xhr, status, error){
						var html = "<div class=\"triangle-isosceles left\">";
						html += "메시지를 판단하는데 실패했어요. 다시 입력해 주세요";
						html += "</div>";
						$( "#talk_content_wrap" ).append(html);		
					},
					success : function(json){
						if(json["result"] == "Y"){
							if(step == 6){
								$("#comment").val(messageStr);
							}
							if(modStep < 0){
								requestStep = step;
							}
							else{
								requestStep = modStep;
							}
							printMsg(requestStep);
						}
						else{
							var html = "<div class=\"triangle-isosceles left\">";
							html += "메시지를 판단하는데 실패했어요. 다시 입력해 주세요";
							html += "</div>";
							$( "#talk_content_wrap" ).append(html);					
						}
					}
				});
				fnMove();
				$( "input[name=inputMessage]").val("")
			}
		}
		function printMsg(requestStep){
			if(requestStep < 7){
				var requestMsg = htmlArr[requestStep];
				if(requestStep == 1){
					var suggestCloseDateTime = "";
					var receiveTimeArr = $("#receiveTime").val().split(":");
					var receiveTimeText = getHourText(parseInt(receiveTimeArr[0]) - 3);
					var receiveDate = $("#receiveDate").val();
					var receiveDateObj = new Date(receiveDate);
					var nowDate = new Date();
					var week = weekArr[receiveDateObj.getDay()];
					var year = receiveDateObj.getFullYear();
					var month = ("0" + (receiveDateObj.getMonth() + 1)).slice(-2);
					var day = ("0" + receiveDateObj.getDate()).slice(-2);
					var receiveDateStr = year + "년&nbsp;" + month + "월&nbsp;" + day + "일";
					suggestCloseDateTime = receiveDateStr + " " + week + " " + receiveTimeText;
					requestMsg = requestMsg.replace("##suggestCloseDateTime##",suggestCloseDateTime);
				}
				var html = "<div style=\"clear:both;\"></div>";
				html += "<div class=\"talkProfile\">";
				html += "</div>";
				html += "<div class=\"triangle-isosceles left\">";
				html += "<span class=\"comment\">";
				html += requestMsg;
				html += "</span>";
				html += "</div>";
		//		html += "<div class=\"btn_box left\">";
		//		html += htmlBtn[requestStep];
		//		html += "</div>";
				$("#talk_content_wrap" ).append(html);
				$(".comment").last().show(600);
				setTalkInput(requestStep);
				if(requestStep == 0){
					setReceiveCalendar();
				}
				else if(requestStep == 1){
					setCloseCalendar();
					$("input[name=closeDateInput]").val($("#receiveDate").val());
					var receiveTimeArr = $("#receiveTime").val().split(":");
					var closeTime = parseInt(receiveTimeArr[0]) - 3
					var receiveTimeText = closeTime>9?closeTime + ":00":"0"+closeTime + ":00";
					$("select[name=closeTimeInput]").val(receiveTimeText);
				}
			}
			else{
				setTalkInput(requestStep);
				resultShow();
				var receiveDateArr = $("#receiveDate").val().split("-");
				var receiveTimeArr = $("#receiveTime").val().split(":");
				var receiveTime = getHourText(parseInt(receiveTimeArr[0]));
				var closeDateArr = $("#closeDate").val().split("-");
				var closeTimeArr = $("#closeTime").val().split(":");
				var closeTime = getHourText(parseInt(closeTimeArr[0]));
				
				$(".receiveDateTimeConf").last().html(receiveDateArr[0] + "년 " + receiveDateArr[1] + "월 " + receiveDateArr[2] + "일 " + receiveTime);
				$(".closeDateTimeConf").last().html(closeDateArr[0] + "년 " + closeDateArr[1] + "월 " + closeDateArr[2] + "일 " + closeTime);
				$(".addrConf").last().html($("#addr1").val() + " " + $("#addr2").val() + "<br>" + $("#rcvName").val() + "&nbsp;" + $("#tel").val());

				$(".purposeConf").last().html($("#purposeText").val());
				$(".productTypeConf").last().html($("#productTypeText").val());
				$(".priceRangeConf").last().html($("#priceRangeText").val());
				$(".styleConf").last().html($("#styleText").val());
				var styleImage1 = "/data/style/" + $("#styleImage1").val();
				var styleImage2 = "/data/style/" + $("#styleImage2").val();
				var styleImage3 = "/data/style/" + $("#styleImage3").val();
				
				if($("#styleImage1").val()){
					$(".styleImageConf1").css("background","#ffffff url('" + styleImage1 + "') no-repeat")
				}
				if($("#styleImage2").val()){
					$(".styleImageConf2").css("background","#ffffff url('" + styleImage2 + "') no-repeat")
				}
				if($("#styleImage3").val()){
					$(".styleImageConf3").css("background","#ffffff url('" + styleImage3 + "') no-repeat")
				}
				$(".commentConf").last().html($("#comment").val());
			}
		}
		
		function setTalkInput(requestStep){
			$("#talk_input_td").html(htmlBtn[requestStep]);
		}
		function fnMove(){
			var scrollPosition = $("#talk_content_wrap").height();
			$('#talk_wrap').animate({scrollTop : scrollPosition}, 200);
		}
		function talkOn(){
			$('#requestFormContent').css("height","100vh");
			$('#talk_wrap').css("height","calc(100vh - 120px)");
			$('#gotop').slideUp(200);
			$('#top').slideUp(200);
			$('.bot_copy').slideUp(200);
			$('#talk_input_wrap').animate({bottom : "-=60"}, 200);
			
		}
		function talkOff(){
			$('#requestFormContent').css("height","calc(100vh - 60px)");
			$('#talk_wrap').css("height","calc(100vh - 240px)");
			$('#gotop').slideDown(200);
			$('#top').slideDown(200);
			$('.bot_copy').slideDown(200);
			$('#talk_input_wrap').animate({bottom : "+=60"}, 200);
			fnMove();
		}
		function stepProc(obj){
			var noticeFlg = $('#noticeFlg').val();
			if (noticeFlg == 0) {
				$('#requestFormContent').hide();
				$('.talk_input_bg').hide();
				$('#noticePage').show();
				noticeFlg++;
				$('#noticeFlg').val(noticeFlg);
			} else {
				var requestStep = 0;
				if(modStep < 0){
					requestStep = step;
				}
				else{
					if(modStep == 0){
						requestStep = modStep;
					}
					else{
						requestStep = modStep;
					}
				}
				if(requestStep == 0){
					var receiveDate = $(obj).parent().parent().parent().find("input[name=receiveDateInput]").val();
					var receiveTime = $(obj).parent().parent().parent().find("select[name=receiveTimeInput]").val();
					$("#receiveDate").val(receiveDate);
					$("#receiveTime").val(receiveTime);
					
					var receiveDateObj = new Date(receiveDate);
					var week = weekArr[receiveDateObj.getDay()];
					var year = receiveDateObj.getFullYear();
					var month = ("0" + (receiveDateObj.getMonth() + 1)).slice(-2);
					var day = ("0" + receiveDateObj.getDate()).slice(-2);
					var receiveDateStr = year + "년&nbsp;" + month + "월&nbsp;" + day + "일";
					var receiveTimeArr = $("#receiveTime").val().split(":");
					receiveTimeText = getHourText(parseInt(receiveTimeArr[0]));
					var limitTime = parseInt(receiveTimeArr[0]) - 3;
					var receiveCompText = receiveDate + " " + (limitTime > 9 ? limitTime : "0" + limitTime) + ":00:00";
					if('<?=date("Y-m-d H:i:m")?>' > receiveCompText){
						alert("수령일은 현재시간보다 3시간 이후부터 가능합니다.");
						return;
					}
					
					
					$("input[name=inputMessage]").val('"'+ receiveDateStr + " " + week + " " + receiveTimeText +'"');
				}
				else if(requestStep == 1){
					var closeDate = $(obj).parent().parent().parent().find("input[name=closeDateInput]").val();
					var closeTime = $(obj).parent().parent().parent().find("select[name=closeTimeInput]").val();
					$("#closeDate").val(closeDate);
					$("#closeTime").val(closeTime);
					var closeTimeArr = $("#closeTime").val().split(":");
					
					var receiveDate = $("#receiveDate").val();
					var receiveTime = $("#receiveTime").val();
					var receiveTimeArr = $("#receiveTime").val().split(":");
					var receiveCompText = receiveDate + " " + receiveTimeArr[0];
					var limitTime = (parseInt(closeTimeArr[0])+3)
					var closeCompText = closeDate + " " + (limitTime > 9 ? limitTime : "0" + limitTime);
					var closeNowCompText = closeDate + " " + (closeTimeArr[0] > 9 ? closeTimeArr[0] : "0" + closeTimeArr[0]) + ":00:00";
					if('<?=date("Y-m-d H:i:m")?>' > closeNowCompText){
						alert("제안서 접수기간은 현재시간 이후부터 가능합니다.");
						return;
					}
					if(receiveCompText < closeCompText){
						alert("제안서 접수기간은 수령일로부터 최대 3시간 전까지 가능합니다.");
						return;
					}
					var closeDateObj = new Date(closeDate);
					var week = weekArr[closeDateObj.getDay()];
					var year = closeDateObj.getFullYear();
					var month = ("0" + (closeDateObj.getMonth() + 1)).slice(-2);
					var day = ("0" + closeDateObj.getDate()).slice(-2);
					var closeDateStr = year + "년&nbsp;" + month + "월&nbsp;" + day + "일";
					
					var closeTime = getHourText(parseInt(closeTimeArr[0]));
					$("input[name=inputMessage]").val('"' + closeDateStr + " " + week + " " + closeTime + '"');
				}
				else if(requestStep == 2){
					ReceiverShow();
				}
				else if(requestStep == 3){
					var purpose = $(obj).parent().parent().parent().find("select[name=purposeInput]").val();
					var purposeText = $(obj).parent().parent().parent().find("select[name=purposeInput] option:selected" ).text();
					var productType = $(obj).parent().parent().parent().find("select[name=productTypeInput]").val();
					var productTypeText = $(obj).parent().parent().parent().find("select[name=productTypeInput] option:selected" ).text();
					$("#purpose").val(purpose);
					$("#purposeText").val(purposeText);
					$("#productType").val(productType);
					$("#productTypeText").val(productTypeText);
					for(var i = 1 ; i < 12 ; i++){
						$(".typeImage" + i).hide();
					}
					$(".typeImage" + (productType - 18)).show();
					$("input[name=inputMessage]").val('"' + purposeText + "/" + productTypeText + '"');
				}
				else if(requestStep == 4){
					var priceRange = $(obj).parent().parent().parent().find("select[name=priceRangeInput]").val();
					var priceRangeText = $(obj).parent().parent().parent().find("select[name=priceRangeInput] option:selected" ).text();
		//			var style = $(obj).parent().parent().parent().find("select[name=styleInput]").val();
		//			var styleText = $(obj).parent().parent().parent().find("select[name=styleInput] option:selected" ).text();
					$("#priceRange").val(priceRange);
					$("#priceRangeText").val(priceRangeText);
		//			$("#style").val(style);
		//			$("#styleText").val(styleText);
					$("input[name=inputMessage]").val('"' + priceRangeText + '"');
				}
				else if(requestStep == 5){
					if(!styleSelect) {
						styleShow();
						styleSelect = true;
					}
					else{
						if($("#style1").val() == ""){
							alert("최소 하나 이상의 스타일을 선택해 주세요")
						}
						else{
							styleHide();
							var styleText = "";
							for(var i = 1 ; i <= 3 ; i++){
								if($("#style" + i).val() == "")break;
								styleText += ((styleText!='')?'/':'') + $("#styleText" + i).val();
							};
							$("#styleText").val(styleText);
							$("input[name=inputMessage]").val('"' + styleText + '"');
							setProgress(stepPer[step]);
							sendMessage(false);
							styleSelect = false;
						}
					}
				}else if(requestStep == 6){
					var comment = $(obj).parent().parent().find("textarea[name=commentInput]").val();
					if(comment == ""){
						comment = "없음";
					}
					$("#comment").val(comment);
					$("input[name=inputMessage]").val('"' + comment + '"' );
				}
				if(requestStep != 2 && requestStep != 5){
					setProgress(stepPer[step]);
					sendMessage(false);
				}
			}
		}
		function setProgress(per){
			var ratioText = per + "%";
			$('#progress01').progress({
				percent: per,
				text: {
				  ratio: ratioText
				}
			});
		}
		function backProc(obj){
			var receiveBox = $(obj).parent().parent().parent().parent();
			$("input[name=inputMessage]").val("이전으로");
			$(receiveBox).hide(100,function(){sendMessage();});
		}
		function addAddr(name,tel1,tel2,post,addr1,addr2){
			setPoint(addr1);
			var html ="			<input type=\"hidden\" name=\"zip\" id=\"zip\" value=\"" + post + "\"/>";
			html += "			<input type=\"hidden\" name=\"addr1\" id=\"addr1\" value=\"" + addr1 + "\"  />";
			html += "			<input type=\"hidden\" name=\"addr2\" id=\"addr2\" value=\"" + addr2 + "\"  />";
			html += "			<input type=\"hidden\" name=\"tel\" id=\"tel\" value=\"" + tel1 + "\"  />";
			html += "			<input type=\"hidden\" name=\"rcvName\" id=\"rcvName\" value=\"" + name + "\"  />";
			$("form[name=requestForm]").append(html);
			$("input[name=inputMessage]").val('"' + addr1 + " " + addr2 + "<br>" + name + "&nbsp;" + tel1 + '"');
			setProgress(stepPer[step]);
			sendMessage(false);
		}
		
	//	receiveTypeSel(this,'0')
	//	function receiveTypeSel(obj,val){
	//		var btnObjs = $(obj).parent().find(".typeBtn");
	//		var inputObj = $("#receiveType").val(val);
	//		btnObjs.each(function(obj) {
	//			$(this).removeClass("select")
	//		})
	//		$(obj).addClass("select")
	//	}

		
		function selectStyleText(obj){
			var styleGroup = $(obj).parent();
			var selStyleCnt = styleGroup.find(".select").length
			
			for(var i = 1 ; i <= styleGroup.find(".select").length ; i++){
				$("#style" + i).val("");
				$("#styleText" + i).val("");
			};
			
			if($(obj).hasClass("select")){
				$(obj).removeClass("select")
			}
			else{
				if(selStyleCnt >= 3){
					alert("스타일은 최대 3개까지 선택 가능합니다");
				}
				else{
					$(obj).addClass("select");
				}
			}
			for(var i = 1 ; i <= styleGroup.find(".select").length ; i++){
				$("#style" + i).val(styleGroup.find(".select").eq(i-1).attr("val"));
				$("#styleText" + i).val($.trim(styleGroup.find(".select").eq(i-1).html()));
			};

		}
		
		function selectStyleImage(obj){
			var styleGroup = $(obj).parent().parent();
			var selStyleImageCnt = styleGroup.find(".selImage").length;
			var showFlag = $(obj).find(".p_prmsg").is(':visible');
			
			for(var i = 1 ; i <= styleGroup.find(".selImage").length ; i++){
				$("#styleImage" + i).val("");
			};
			
			if(showFlag){
				$(obj).find(".p_prmsg").hide();
				$(obj).removeClass("selImage");
			}
			else{
				if(selStyleImageCnt >= 3){
					alert("스타일 이미지는 최대 3개까지 선택 가능합니다");
				}
				else{
					$(obj).find(".p_prmsg").show();
					$(obj).addClass("selImage");
				}
			}
			
			for(var i = 1 ; i <= styleGroup.find(".selImage").length ; i++){
				$("#styleImage" + i).val($.trim(styleGroup.find(".selImage").eq(i-1).attr("value")));
			};
	//		$("#styleSelecter").html("<img src=\""+src+"\" style=\"height:50px;\">");
		}
		
		function setPoint(ao_addr) {
			var pointx = "";
			var pointy = "";
			naver.maps.Service.geocode({
					address: ao_addr
				}, function(status, response) {
					if (status !== naver.maps.Service.Status.OK) {
						return alert('Something wrong!');
					}

					var result = response.result, // 검색 결과의 컨테이너
						items = result.items; // 검색 결과의 배열
						pointx = items[0].point.x;
						pointy = items[0].point.y;
						$('#ao_addr_pointx').val(pointx);
						$('#ao_addr_pointy').val(pointy);
				});
		   
		}
	</script>
	<input type="hidden" name="type" />
	<input type="hidden" name="targetVender" value="" />
	<input type="hidden" name="orderType" value="2" />
	<INPUT type="hidden" name="receiveDate" id="receiveDate" />
	<INPUT type="hidden" name="receiveTime" id="receiveTime" />
	<INPUT type="hidden" name="closeDate" id="closeDate" />
	<INPUT type="hidden" name="closeTime" id="closeTime" />
	<input type="hidden" name="receiveType" id="receiveType" value="0"  />
	<input type="hidden" name="receiveTypeText" id="receiveTypeText"/>
	<input type="hidden" name="prodNum" id="prodNum" value="1"  />
	<INPUT type="hidden" name="purpose" id="purpose"/>
	<INPUT type="hidden" name="purposeText" id="purposeText"/>
	<INPUT type="hidden" name="productType" id="productType"/>
	<INPUT type="hidden" name="productTypeText" id="productTypeText"/>
	<INPUT type="hidden" name="priceRange" id="priceRange"/>
	<INPUT type="hidden" name="priceRangeText" id="priceRangeText"/>
	<INPUT type="hidden" name="style1" id="style1"/>
	<INPUT type="hidden" name="styleText1" id="styleText1"/>
	<INPUT type="hidden" name="styleImage1" id="styleImage1"/>
	<INPUT type="hidden" name="style2" id="style2"/>
	<INPUT type="hidden" name="styleText2" id="styleText2"/>
	<INPUT type="hidden" name="styleImage2" id="styleImage2"/>
	<INPUT type="hidden" name="style3" id="style3"/>
	<INPUT type="hidden" name="styleText3" id="styleText3"/>
	<INPUT type="hidden" name="styleText" id="styleText"/>
	<INPUT type="hidden" name="styleImage3" id="styleImage3"/>
	<INPUT type="hidden" name="comment" id="comment"/>
	<INPUT type="hidden" name="ao_addr_pointx" id="ao_addr_pointx"/>
	<INPUT type="hidden" name="ao_addr_pointy" id="ao_addr_pointy"/>
	
	<div id="noticePage">	
		<input type="hidden" id="noticeFlg" value="0">
		<div class="popCloseBtn" onclick="talkRequestClose()">×</div>
		<div id="titleWrap" class="titleWrap">
			<h2>스페셜 오더</h2>
		</div>
		<div class="noticeContent" style="margin-top:10%;margin-left:10%;width:80%;">
			<div class="noticeHeader" style="text-align:center;border-radius:25px 25px 0px 0px;width:100%;height:35px;background-color:white;padding-top:20px;">
				<font style="font-size:1.3rem;font-weight:500;color:black;">꼭 확인해 주세요!</font>
			</div>
			<div class="noticeBody" style="width:100%;height:295px;background-color:#fFE6E6;padding-top:10px;padding-bottom:10px;">
				<div style="clear:both;"></div>
				<table style="width:100%;">
					<tr>
						<td rowspan="3" style="width:25%;vertical-align:top;">
							<div class="talkProfile" style="float:left;"></div>
						</td>
						<td>
							<div class="triangle-isosceles first_isosceles left">
								<span class="comment" style="display: inline;font-size:1.0rem;">
									<font class="fc_pink" style="font-weight:500">스페셜 오더</font>는<br>
									꽃집에서 꽃을 제안드리는<br>
									일대일 맞춤형 주문입니다.<br>
								</span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="triangle-isosceles left">
								<span class="comment" style="display: inline;font-size:1.0rem;">
									시간을 <font style="font-weight:500">촉박하게 설정</font>하면,<br>
									충분한 제안을<br>
									받지 못하실 수 있어요 :(<br>
								</span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="triangle-isosceles left">
								<span class="comment" style="display: inline;font-size:1.0rem;">
									<font class="fc_pink" style="font-weight:500">하루이틀</font><font style="font-weight:500"> 정도 여유있게</font><br>
									주문하시면 더 많은 꽃집에게<br>
									다양한 제안을 받으실 수 있습니다!<br>
								</span>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="noticeBottom noticeConfirm" style="text-align:center;padding-top:20px;border-radius:0px 0px 25px 25px;width:100%;height:40px;background-color:#e61e6e;">
				<font style="color:white;font-size:1.3rem;font-weight:500">확인 후, 일정 선택</font>
			</div>
		</div>
	</div>
	
	<div id="requestFormContent">

		<!-- 주문결제 -->
		<input type="hidden" name="msg_type" value="1" />
		<input type="hidden" name="addorder_msg" value="" />
		<input type="hidden" name="sumprice" value="<?=$basketItems['sumprice']?>" />

		<div class="popCloseBtn" onclick="talkRequestClose()">×</div>
		<div id="titleWrap" class="titleWrap">
			<h2>스페셜 오더</h2>
		</div>
		<div class="ui small pink progress" id="progress01" style="background:none;margin:0px;">
			<div class="bar">
				<div class="progress"></div>
			</div>
		</div>
		<!-- 채팅창 START -->
		<div id="talk_wrap" class="talk_wrap" style="">
			<div id="talk_content_wrap" class="talk_content_wrap">
			</div>
		</div>
		<div style="widht:100vw;display:none;" id="sytle_wrap">
			<!-- 이미지 START -->
			<div id="style_wrap_content" class="style_wrap">
			
				<div style="clear:both;"></div>
				<div class="talkProfile">
				</div>
				<div class="triangle-isosceles left">
					스타일은 3개까지 고를 수 있어요.
				</div>
				<div style="clear:both;">
				<?
					$sql = "SELECT * FROM item_mst WHERE keyText='style' ";
					//echo $sql;
					$result4=mysql_query($sql,get_db_conn());
					while($row4=mysql_fetch_object($result4)) {
				?>
						
					<a class="styleTextBtn" val="<?=$row4->seq?>" style="margin:3px;" onclick="selectStyleText(this)">
					  <?=$row4->valText?>
					</a>
				<?	}
					mysql_free_result($result4);
				?>
				</div>
				<div style="clear:both;"></div>
				<div style="margin-top:11px;">
					<div class="talkProfile">
					</div>
					<div class="triangle-isosceles left">
						아래 사진 중에서<br>원하는 느낌을 3개까지 골라주세요!<br>플로리스트가 스타일을 참고할게요.<br>꽃은 별도로 제안드립니다.
					</div>
				</div>	
				<div style="clear:both;"></div>
				<!-- <span style="color:#E61E6E;">사진은 예시입니다. 스타일만 참고하시고 꽃은 별도로 제안드려요.</span> -->
				<div id="styleImageWrap" style="margin-top: 10px;">
					<div class="pr_type_list_table addImageBtn" style="float:left">
						<div class="dropzone typelist_attach" id="fileDropzone" style="background:#ffffff url('/app/skin/basic/svg/plus_photo.svg') no-repeat;background-size: 60% auto;background-position:center">
							<input name="file" type="file" id="file" accept="image/*" style="display:none;" />
						</div>
					</div>
					<?for($i = 1 ;$i < 38; $i++){?> 
						<div class="pr_type_list_table typeImage1" style="float:left">
							<div class="typelist_image_wrap" style="background:#ffffff url('/data/style/1<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>.jpg') no-repeat;background-size:cover;background-position:center" onclick="selectStyleImage(this)" value="1<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>.jpg">
								<div class="p_prmsg">
									<!-- <i class="check icon" style="margin-top:40px;"></i>
									<div>선택완료</div> -->
								</div>
							</div>
						</div>
					<?}?>
					<?for($i = 1 ;$i < 12; $i++){?> 
						<div class="pr_type_list_table typeImage2" style="float:left">
							<div class="typelist_image_wrap" style="background:#ffffff url('/data/style/2<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>.jpg') no-repeat;background-size:cover;background-position:center" onclick="selectStyleImage(this)" value="2<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>.jpg">
								<div class="p_prmsg">
									<i class="check icon" style="margin-top:40px;"></i>
									<div>선택완료</div>
								</div>
							</div>
						</div>
					<?}?>
					<?for($i = 1 ;$i < 21; $i++){?> 
						<div class="pr_type_list_table typeImage3" style="float:left">
							<div class="typelist_image_wrap" style="background:#ffffff url('/data/style/3<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>.jpg') no-repeat;background-size:cover;background-position:center" onclick="selectStyleImage(this)" value="2<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>.jpg">
								<div class="p_prmsg">
									<i class="check icon" style="margin-top:40px;"></i>
									<div>선택완료</div>
								</div>
							</div>
						</div>
					<?}?>
					<?for($i = 1 ;$i < 24; $i++){?> 
						<div class="pr_type_list_table typeImage5" style="float:left">
							<div class="typelist_image_wrap" style="background:#ffffff url('/data/style/5<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>.jpg') no-repeat;background-size:cover;background-position:center" onclick="selectStyleImage(this)" value="2<?=str_pad($i, 2, "0", STR_PAD_LEFT)?>.jpg">
								<div class="p_prmsg">
									<i class="check icon" style="margin-top:40px;"></i>
									<div>선택완료</div>
								</div>
							</div>
						</div>
					<?}?>
				</div>
			</div>
			<!-- 이미지 END -->
		</div>
		<div style="width:100vw;display:none;" id="result_wrap">
			<!-- 이미지 START -->
			<div id="result_wrap_content" class="result_wrap">
			
				<div style="clear:both;"></div>
				<div class="talkProfile">
				</div>
				<div class="triangle-isosceles left">
					수고하셨습니다!<br>마지막으로 주문하신 내용 확인해주세요.

				</div>
				
				<div id="resultForm">
					<div style="clear:both;"></div>
					<div class="resultWrapTitle">
						<div>주문서</div>
					</div>
					<div class="subTitle">
						<div class="subTitleIcon"></div>
						<div class="subTitleText">꽃집 주문서 마감시간</div>
					</div>
					<div class="resultContent">
						<div class="contentText">
							<span style="font-size: 1.2em;font-weight: 600;color: #282828;" class="closeDateTimeConf"></span>
						</div>
						<div>
							<div class="contentInfoIcon"></div>
							<div class="contentInfoText red">
								해당시간이 지나면 주문서가 사라집니다!
							</div>
						</div>
						<div style="overflow:hidden;margin-left:12px;">
							<div class="resultBanner productTypeConf"></div>
							<div class="resultBanner purposeConf"></div>
							<div class="resultBanner priceRangeConf"></div>
							<div class="resultBanner styleConf"></div>
						</div>
						<div style="overflow:hidden;margin-left:10px;">
							<div class="pr_type_list_table" style="float:left">
								<div class="typelist_image_wrap styleImageConf1" >
								</div>
							</div>
							<div class="pr_type_list_table" style="float:left">
								<div class="typelist_image_wrap styleImageConf2" >
								</div>
							</div>
							<div class="pr_type_list_table" style="float:left">
								<div class="typelist_image_wrap styleImageConf3" >
								</div>
							</div>
						</div>
					</div>
					<div class="subTitle">
						<div class="subTitleIcon"></div>
						<div class="subTitleText">배송정보 </div>
					</div>
					<div class="resultContent">
						<div class="contentText">
							<span class="receiveDateTimeConf"></span>
						</div>
						<div>
							<div class="contentInfoIcon"></div>
							<div class="contentInfoText red">
								시간에 맞춰, 원하는 장소로 준비해 드립니다!
							</div>
						</div>
						<div>
							<div class="contentInfoText black">
								<span class="addrConf"></span>
							</div>
						</div>
					</div>
					<div class="subTitle">
						<div class="subTitleIcon"></div>
						<div class="subTitleText">요청사항</div>
					</div>
					<div class="resultContent">
						<div class="contentText">
							<span class="commentConf"></span>
						</div>
					</div>
				</div>
			</div>
			<!-- 이미지 END -->
		</div>
		<!-- 채팅창 END -->
		<!-- 입력창 START -->
		<div class="talk_input_bg" style="position: fixed;bottom: 0;background-color: #ffffff;width: 100%;height: calc(75px + env(safe-area-inset-bottom));">
			<div class="talk_input_wrap" id="talk_input_wrap">
				<table class="talk_table">
					<tr>
						<td style="width:auto" id="talk_input_td">
						<!--
							<textarea name="inputMessage" id="inputMessage" placeholder="메세지를 입력해 주세요."></textarea>
						-->
						</td>
						<td style="width:40px" id="procBtnTd">
							<a onclick="stepProc(this);" class="basic_button grayBtn" id="procBtn" style="border-radius:5px;padding:3px;background:#e51e6e;text-align:center;">
								<img src="/app/skin/basic/svg/arrow.svg" style="width:30px">
							</a>
						</td>
					</tr>
				</table>
				<input type="hidden" name="inputMessage" id="inputMessage">
			</div>
		</div>
		<!-- 입력창 End -->
		<Script>
			$(document).ready(function() {
				$(".move_scroll").hide();
				resetTalk("","");
				
				var noticeFlg = $('#noticeFlg').val();
				if (noticeFlg == 0) {
					$('#noticePage').hide();
				}
				
				setNoticeConfirmClickEvent();
			});
			function setNoticeConfirmClickEvent() {
				$('.noticeConfirm').click(function() {
					$('#requestFormContent').show();
					$('.talk_input_bg').show();
					$('#noticePage').hide();
				});
			}
			function resetTalk(vidx,brand_name){
				$("#talk_content_wrap").html("");
				$("input[name=targetVender]").val(vidx);
				
				step = 0;
				$('#progress01').progress({
					percent: 0
				});
				if(vidx != ""){
					htmlArr[0] = "<b>" + brand_name + "</b>에서만<br>제안을 받아보세요!<br>꽃이 언제까지 필요하세요?";
				}
				else{
					htmlArr[0] = "<?=$_ShopInfo->memname?>님, 안녕하세요?<br>주변 꽃집들에게<br>딱 맞는 제안을 받아보세요.<br>꽃이 언제까지 필요하신가요?";

				}
				printMsg(0);
			}
			function quantityControlForm(mode, obj){
				if(mode != null || mode != 'undifined'){
					var quantituObj = $(obj).parent().find(".qty_input");
					if(mode == 'plus'){
						quantituObj.val(parseInt(quantituObj.val()) + 1);
					}

					if(mode == 'minus'){
						if(quantituObj.val() > 1){
						quantituObj.val(parseInt(quantituObj.val()) - 1);
						}else{
							alert("최소 구매가능한 수량은 1개 입니다.");
						}
					}
				}
			}
			function styleShow(){
				$("#sytle_wrap").show();
				$("#talk_wrap").hide();
				$("#result_wrap").hide();
				
			}
			function styleHide(){
				$("#sytle_wrap").hide();
				$("#talk_wrap").show();
				$("#result_wrap").hide();
			}
			function resultShow(){
				$("#procBtnTd").hide();
				$("#sytle_wrap").hide();
				$("#talk_wrap").hide();
				$("#result_wrap").show();
				
			}
			function resultHide(){
				$("#sytle_wrap").hide();
				$("#talk_wrap").show();
				$("#result_wrap").hide();
			}
			//주소록 팝업창
			function ReceiverCheck(){
				window.open("mydelivery.php","addrbygone","width=100,height=100,toolbar=no,menubar=no,scrollbars=yes,status=no");
			}
		</script>
		
		<script type="text/javascript">

			//달력 처리
			function setReceiveCalendar(){
				$(".receiveDate").last().datepicker({
					dateFormat: 'yy-mm-dd',
					prevText: '이전 달',
					nextText: '다음 달',
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNames: ['일','월','화','수','목','금','토'],
					dayNamesShort: ['일','월','화','수','목','금','토'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					showMonthAfterYear: true,
					changeMonth: true,
					changeYear: true,
					yearSuffix: '년',
					minDate: '+<?=$gapDay?>d',
					yearRange: "-100:+0"
				});
			}
			
			function setCloseCalendar(){
				var receiveDate = $("#receiveDate").val();
				$(".closeDate").last().datepicker({
					dateFormat: 'yy-mm-dd',
					prevText: '이전 달',
					nextText: '다음 달',
					monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
					dayNames: ['일','월','화','수','목','금','토'],
					dayNamesShort: ['일','월','화','수','목','금','토'],
					dayNamesMin: ['일','월','화','수','목','금','토'],
					showMonthAfterYear: true,
					changeMonth: true,
					changeYear: true,
					yearSuffix: '년',
					minDate: '+<?=$gapDay?>d',
					maxDate: receiveDate,
					yearRange: "-100:+0"
				});
			}
			//-->
		</script>
		
		
		<div id="delivery_popup" style="display: none; position: fixed; padding-top:calc(60px + env(safe-area-inset-top));padding-top:calc(60px + constant(safe-area-inset-top)); box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 999; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
			<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="ReceiverClose()">
				<div style="position:absolute;top:calc(3% + env(safe-area-inset-top));top:calc(3% + constant(safe-area-inset-top));right:3%;color:#fff;font-size:4em;font-weight:500;">×</div>
			</div>
			<div style="border-radius:8px;position: relative; width: 100%; height: 100%; background-color: rgb(255, 255, 255); z-index: 0; overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;">
				<iframe frameborder="0" id="delivery_content" src="about:blank" style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
			</div>
		</div>
		<script>

			function CheckRequestForm() {
				if($("input[name=receiveDate]").val().length==0) {
					alert("배송날짜 미지정");
					return;
				}
				if($("input[name=receiveTime]").val().length==0) {
					alert("배송시간 미지정");
					return;
				}
				if($("input[name=addr1]").val().length==0) {
					alert("주소 미지정");
					return;
				}
				if($("input[name=purpose]").val().length==0) {
					alert("용도 미지정");
					return;
				}
				if($("input[name=productType]").val().length==0) {
					alert("종류 미지정");
					return;
				}
				if($("input[name=priceRange]").val().length==0) {
					alert("가격대 미지정");
					return;
				}
				if($("input[name=style1]").val().length==0) {
					alert("스타일 미지정");
					return;
				}

				document.requestForm.type.value="insert";
				$("#talk_input_td").html($("#procForm").html());
				var formData = $("form[name=requestForm]").serialize() ;
				$.ajax({
					type : 'post',
					url : '/api/request_insert.php',
					data : formData,
					dataType : 'json',
					error: function(xhr, status, error){
						alert("데이터 통신중에 오류가 발생했습니다.");
						$("#talk_input_td").html($("#confirmForm").html());
					},
					success : function(json){
						if(json["result"] == "Y"){
							location.replace("/app/proposalList.php");
						}
						else if(json["result"] == "E"){
							alert("처리중에 오류가 발생했습니다. 다시 시도해주세요");
						$("#talk_input_td").html($("#confirmForm").html());
						}
					}
				});
			}

			//주소록 팝업창
			function ReceiverShow(){
				$("#delivery_popup").show();
				$("#delivery_content").attr("src","/app/mydeliveryModal.php");
			}
			function ReceiverClose(){
				$("#delivery_popup").hide();
				$("#delivery_content").attr("src","about:blank");
			}
			function setFilesName(){
				alert("sfn")
			}
			function submitForm(){
				alert("submitForm")
			}
			
			function getHourText(receiveTimeHour){
				var receiveTime;
				if(receiveTimeHour < 12 ){
					receiveTime = "오전 " +  receiveTimeHour + "시";
				}
				else if(receiveTimeHour == 12 ){
					receiveTime = "오후 12시";
				}
				else{
					receiveTime = "오후 " + (receiveTimeHour - 12) + "시";
				}
				return receiveTime;
			}
			Dropzone.autoDiscover = false;
			$("#fileDropzone").dropzone({
				url: '/api/style_upload_proc.php',
				maxFilesize: 5000,
				acceptedFiles: 'image/*',
				maxFiles: 3,
				addRemoveLinks: true,
				init: function() {
					var fileDropzone = this;
					
					// Append all the additional input data of your form here!
					this.on("sending", function(file, xhr, formData) {
						formData.append("token", $("input[name=token]").val());
					});
					// Append all the additional input data of your form here!
					this.on("success",  function(file, json) {
						json = $.parseJSON( json )
						var styleGroup = $("#styleImageWrap");
						var selStyleImageCnt = styleGroup.find(".selImage").length;
						if(selStyleImageCnt > 2){
							alert("스타일 이미지는 최대 3개까지 선택 가능합니다");
						}
						else{
							for(var i = 1 ; i <= styleGroup.find(".selImage").length ; i++){
								$("#styleImage" + i).val("");
							};
							var imageHtml = "<div class=\"pr_type_list_table\" style=\"float:left\">";
							imageHtml += "	<div class=\"typelist_image_wrap selImage\" style=\"background:#ffffff url('/data/style/" + json.file_name + "') no-repeat;background-size:cover;background-position:center\" onclick=\"selectStyleImage(this)\" value=\"" + json.file_name + "\">";
							imageHtml += "		<div class=\"p_prmsg\" style=\"display: block;\">";
							imageHtml += "			<i class=\"check icon\" style=\"margin-top:40px;\"></i>";
							imageHtml += "			<div>선택완료</div>";
							imageHtml += "		</div>";
							imageHtml += "	</div>";
							imageHtml += "</div>";
							var styleGroup = $("#styleImageWrap");
							var selStyleImageCnt = styleGroup.find(".selImage").length;
							$(".addImageBtn").after(imageHtml);
							for(var i = 1 ; i <= styleGroup.find(".selImage").length ; i++){
								$("#styleImage" + i).val($.trim(styleGroup.find(".selImage").eq(i-1).attr("value")));
							};
						}
					});

					this.on("maxfilesexceeded", function() {
						// Gets triggered when there was an error sending the files.
						// Maybe show form again, and notify user of error
						alert("스타일 이미지는 최대 3개까지 업로드 가능합니다");
					});
					
				}
			});
		</script>
	</div>
</form>