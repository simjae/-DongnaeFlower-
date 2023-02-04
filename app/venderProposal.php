<? include_once('header.php'); ?>

<style>
	.vender_info_input{padding:20px 13px;box-sizing:border-box;font-size:0.9em;}
	.vender_info_input th{padding:5px 0px;border-bottom:1px solid #eee;text-align:left;font-weight:normal;}
	.vender_info_input td{padding:5px 0px;border-bottom:1px solid #eee;}
	.vender_info_input .input{height: 40px;line-height: 40px;padding-left: 0.9em;border: 1px solid #ebebeb;box-sizing: border-box;background: #ffffff;color: #848484;width: 100%;font-size: 14px;letter-spacing: -1px;border-radius:2px;}
	.vender_info_input .button{padding:0px 10px;height:40px;vertical-align:top;}
	.vender_info_input .select{height:40px;vertical-align:top;}
</style>

<div id="content">
	<div class="h_area2">
		<h2>입점문의</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back(-1)" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<FORM name="proposalFrom">
	<table cellpadding="0" cellspacing="0" width="100%" class="vender_info_input">
		<colgroup>
			<col width="30%" />
			<col width="" />
		</colgroup>
		<?
			$sql="SELECT * FROM `tblVenderProposalType` ";
			$result=mysql_query($sql,get_db_conn());
			$num=mysql_num_rows($result);

			if($num > 0){
		?>
		<tr>
			<th><font color="#F02800">＊</font>문의내용</th>
			<td>
				<?
					while($row=mysql_fetch_object($result)){
						$sel=($sel_i == 0)?"checked":"";
						$sel_i++;
						echo "<input type=\"radio\" name=\"type\" id='name".$row->idx."' value=\"".$row->name."\" ".$sel."><label for='name".$row->idx."'>".$row->name."</label>&nbsp;&nbsp;";
					}
				?>
			</td>
		</tr>
		<? } ?>

		<tr>
			<th>회사명 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR"><input type="text" name="company" maxlength="20" style="width:100%;" class="input" /></td>
		</tr>

		<tr>
			<th>사업자번호 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR"><input type="text" name="comp_num" maxlength="20" style="width:100%;" class="input"></td>
		</tr>

		<tr>
			<th>주소 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR">
				<p><input type="text" name="home_post1" id="home_post1" onclick="addr_search_for_daumapi('home_post1','home_addr1','home_addr2');" style="width:70px;padding:0px;text-align:center;" class="input" readonly /> <input type="button" onclick="addr_search_for_daumapi('home_post1','home_addr1','home_addr2');" class="button" value="주소검색" /></p>
				<p style="padding:3px 0px;"><input type=text name="home_addr1" id="home_addr1" maxlength="100" onclick="addr_search_for_daumapi('home_post1','home_addr1','home_addr2');" style="width:100%;" class="input" readonly /></p>
				<p><input type=text name="home_addr2" id="home_addr2" value="" maxlength=100 style="width:100%;" class="input"></p>
			</td>
		</tr>

		<tr>
			<th>배송가능 지역 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR">
				<span>꽃배송이 가능한 지역을 입력해 주세요</span><br> 
				<span style="font-size:0.8em">
				예)전국 배송 가능 -> 전국
				<br>서울시 서초구 배송가능 -> 서울 서초
				<br>서초구 및 남양주 배송가능 -> 서울 서초, 경기 남양주
				<br>서울 및 경기도 배송가능 -> 서울, 경기
				</span>
				<input type="text" name="deli_able_area" maxlength="20" style="width:100%;" class="input">
			</td>
		</tr>
		<tr>
			<th>담당자 명 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR"><input type="text" name="name" maxlength="20" style="width:100%;" class="input"></td>
		</tr>

		<tr>
			<th>전화번호 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR">
				<input type="text" name="tell1" maxlength="4" style="width:25%;" class="input">
				-
				<input type="text" name="tell2" maxlength="4" style="width:25%;" class="input">
				-
				<input type="text" name="tell3" maxlength="4" style="width:25%;" class="input">
			</td>
		</tr>

		<tr>
			<th>휴대폰 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR">
				<select name="phone1" class="select" style="width:25%;">
					<option value="X" selected="selected">선택</option>
					<option value="010">010</option>
					<option value="011">011</option>
					<option value="016">016</option>
					<option value="017">017</option>
					<option value="018">018</option>
					<option value="019">019</option>
				</select> - 
				<input type="text" name="phone2" maxlength="4" style="width:25%" class="input"> - 
				<input type="text" name="phone3" maxlength="4" style="width:25%" class="input">
			</td>
		</tr>

		<tr>
			<th>이메일 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR"><input type="text" name="mail" maxlength="40" style="width:100%;" class="input"></td>
		</tr>

		<tr>
			<th>웹사이트 주소</th>
			<td class="partnerinfoR"><input type="text" name="site" style="width:100%;" class="input"></td>
		</tr>

		<tr>
			<th>전년도 매출액</th>
			<td class="partnerinfoR"><input type="text" name="preSell" maxlength="20" style="width:100%;" class="input"></td>
		</tr>

		<tr>
			<th>직원수</th>
			<td class="partnerinfoR"><input type="text" name="memNo" maxlength="10" style="width:100%;" class="input"></td>
		</tr>

		<tr>
			<th>종합몰, 오픈마켓 및 입점몰</th>
			<td class="partnerinfoR"><textarea name="mall" style="width:100%; height:80px;"></textarea></td>
		</tr>

		<tr>
			<th>문의내용 <font color="#F02800">＊</font></th>
			<td class="partnerinfoR"><textarea name="contents" style="width:100%; height:80px;"></textarea></td>
		</tr>
	</table>

	<div class="basic_btn_area">
		<a href="javascript:history.back(-1);" class="basic_button">이전으로</a>
		<a href="javascript:;" onclick="sendForm(proposalFrom);" class="basic_button grayBtn" />문의하기</a>
	</div>

	<input type="hidden" name="mode" value="venderProposalInsert" />
	</FORM>

	<script>
		<!--
		function sendForm( form ) {

			if(form.company.value.length==0) {
				alert("회사명를 입력하세요.");
				form.company.focus(); return;
			}
			if(form.comp_num.value.length==0) {
				alert("사업자번호를 입력하세요.");
				form.comp_num.focus(); return;
			}
			
			if(form.home_addr1.value.length==0) {
				alert("사업장 주소를 입력하세요.");
				f_addr_search('proposalFrom','home_post','home_addr1',2); return;
			}
			if(form.home_addr2.value.length==0) {
				alert("사업장 상세 주소를 입력하세요.");
				form.home_addr2.focus(); return;
			}

			if(form.name.value.length==0) {
				alert("담당자 성명을 입력하세요.");
				form.name.focus(); return;
			}

			if(form.tell1.value.length==0) {
				alert("담당자 전화번호를 입력하세요.");
				form.tell1.focus(); return;
			}
			if(form.tell2.value.length==0) {
				alert("담당자 전화번호를 입력하세요.");
				form.tell2.focus(); return;
			}
			if(form.tell3.value.length==0) {
				alert("담당자 전화번호를 입력하세요.");
				form.tell3.focus(); return;
			}

			if(form.phone1.value=='X') {
				alert("담당자 핸드폰 앞자리를 선택하세요.");
				form.phone1.focus(); return;
			}
			if(form.phone2.value.length==0) {
				alert("담당자 핸드폰을 입력하세요.");
				form.phone2.focus(); return;
			}
			if(form.phone3.value.length==0) {
				alert("담당자 핸드폰을 입력하세요.");
				form.phone3.focus(); return;
			}

			if(form.mail.value.length==0) {
				alert("이메일을 입력하세요.");
				form.mail.focus(); return;
			}
			if(!IsMailCheck(form.mail.value)) {
				alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요.");
				form.mail.focus(); return;
			}

			if(form.contents.value.length==0) {
				alert("상세문의내용 입력하세요.");
				form.contents.focus(); return;
			}

			form.action = 'venderProposal.process.php';
			form.method = 'POST';
			form.submit();

		}
		//-->
	</script>

<!-- iOS에서는 position:fixed 버그가 있음, 적용하는 사이트에 맞게 position:absolute 등을 이용하여 top,left값 조정 필요 -->
<div id="layer" style="display:none;position:fixed;padding:15% 3%;box-sizing:border-box;background:rgba(0,0,0,0.7);z-index:901;-webkit-overflow-scrolling:touch;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="closeDaumPostcode()">
		<div style="position:absolute;top:3%;right:3%;color:#fff;font-size:4em;font-weight:500;">&times;</div>
	</div>
</div>


<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">

 // 우편번호 찾기 화면을 넣을 element
	var element_layer = document.getElementById('layer');

	function closeDaumPostcode() {
		// iframe을 넣은 element를 안보이게 한다.
		element_layer.style.display = 'none';
	}

	function addr_search_for_daumapi(post,addr1,addr2) {
		new daum.Postcode({
			oncomplete: function(data) {
				// 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

				// 각 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var addr = ''; // 주소 변수
				var extraAddr = ''; // 참고항목 변수

				//사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
				if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
					addr = data.roadAddress;
				} else { // 사용자가 지번 주소를 선택했을 경우(J)
					addr = data.jibunAddress;
				}

				// 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
				if(data.userSelectedType === 'R'){
					// 법정동명이 있을 경우 추가한다. (법정리는 제외)
					// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
					if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
						extraAddr += data.bname;
					}
					// 건물명이 있고, 공동주택일 경우 추가한다.
					if(data.buildingName !== '' && data.apartment === 'Y'){
						extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
					// 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
					if(extraAddr !== ''){
						extraAddr = ' (' + extraAddr + ')';
					}
					// 조합된 참고항목을 해당 필드에 넣는다.
					document.getElementById(addr2).value = extraAddr;
				
				} else {
					document.getElementById(addr2).value = '';
				}

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				document.getElementById(post).value = data.zonecode;
				document.getElementById(addr1).value = addr;
				// 커서를 상세주소 필드로 이동한다.
				document.getElementById(addr2).focus();

				// iframe을 넣은 element를 안보이게 한다.
				// (autoClose:false 기능을 이용한다면, 아래 코드를 제거해야 화면에서 사라지지 않는다.)
				element_layer.style.display = 'none';
			},
			width : '100%',
			height : '100%',
			maxSuggestItems : 5
		}).embed(element_layer);

		// iframe을 넣은 element를 보이게 한다.
		element_layer.style.display = 'block';

		// iframe을 넣은 element의 위치를 화면의 가운데로 이동시킨다.
		initLayerPosition();
	}

	// 브라우저의 크기 변경에 따라 레이어를 가운데로 이동시키고자 하실때에는
	// resize이벤트나, orientationchange이벤트를 이용하여 값이 변경될때마다 아래 함수를 실행 시켜 주시거나,
	// 직접 element_layer의 top,left값을 수정해 주시면 됩니다.
	function initLayerPosition(){
		var width = 100; //우편번호서비스가 들어갈 element의 width
		var height = 100; //우편번호서비스가 들어갈 element의 height
		var borderWidth = 0; //샘플에서 사용하는 border의 두께

		// 위에서 선언한 값들을 실제 element에 넣는다.
		element_layer.style.width = width + '%';
		element_layer.style.height = height + '%';
		element_layer.style.border = borderWidth + 'px solid #ddd';
		// 실행되는 순간의 화면 너비와 높이 값을 가져와서 중앙에 뜰 수 있도록 위치를 계산한다.
		//element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width)/2 - borderWidth) + 'px';
		//element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height)/2 - borderWidth) + 'px';
		element_layer.style.left = '0%';
		element_layer.style.top = '0%';
	}
</script>

<? include_once('footer.php'); ?>