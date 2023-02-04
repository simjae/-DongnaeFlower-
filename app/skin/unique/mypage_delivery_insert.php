<?
	$exOk=0;
	$_VenderInfo = new _VenderInfo($_COOKIE[_vinfo]);
	if($_ShopInfo->getMemid()==$_VenderInfo->getId()){
		$Vender = 1;
		$exOk=1;
	}

	if(strlen($_ShopInfo->getMemid())==0) {
		echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');\"></body></html>";exit;
		Header("Location:".$Dir.FrontDir."login.php?chUrl=".getUrl());
		exit;
	}

	$type=$_POST['type'];
	$receiver_name=$_POST['receiver_name'];
	$receiver_tel1=ereg_replace("-","",$_POST['receiver_tel1']);
	$receiver_tel2=ereg_replace("-","",$_POST['receiver_tel2']);
	$receiver_email=$_POST['receiver_email'];
	$receiver_post=$_POST['rpost1'];
	$receiver_addr1=$_POST['raddr1'];
	$receiver_addr2=$_POST['raddr2'];
	$receiver_addr=mysql_escape_string($receiver_addr1)."=".mysql_escape_string($receiver_addr2);

	if($type=='insert'){
		$sql="INSERT tblorderreceiver SET ";
		$sql.="member_id='".$_ShopInfo->getMemid()."', ";
		$sql.="receiver_name='".$receiver_name."', ";
		$sql.="receiver_tel1='".$receiver_tel1."', ";
		$sql.="receiver_tel2='".$receiver_tel2."', ";
		$sql.="receiver_email='".$receiver_email."', ";
		$sql.="receiver_post='".$receiver_post."', ";
		$sql.="receiver_addr='".$receiver_addr."' ";
		mysql_query($sql,get_db_conn());
		DeleteCache("tblorderreceiver.cache");

		$onload="<script>alert('배송지 등록이 완료되었습니다.');location.href='mypage_delivery.php';</script>";
	}
?>

<style>
	.delivery_addr th{font-weight:normal;text-align:left;}
	.delivery_addr td{font-weight:normal;}
	.delivery_addr .input{height:32px;padding-left:5px;box-sizing:border-box;}
	.btn_s_line2{display:inline-block;line-height:32px;padding:0px 10px;border:1px solid #ddd;box-sizing:border-box;text-align:center;}
</style>

<div id="content">
	<div class="h_area2">
		<h2>배송지 입력</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div style="width:90%;margin:0 auto;padding:25px 0px;border-bottom:1px solid #eee;text-align:center;">
		<h4 style="line-height:20px;font-weight:normal;">상품 주문시 사용하는 배송지 정보를<br />미리 등록할 수 있습니다.</h4>
	</div>

	<div style="width:90%;margin:0 auto;padding:30px 0px;box-sizing:border-box;background:#fff;">
		<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
			<input type="hidden" name="type" />

			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="delivery_addr">
				<colgroup>
					<col width="25%" />
					<col width="" />
				</colgroup>
				<tr>
					<th>수신자명</th>
					<td style="padding:5px 0px;"><input type="text" name="receiver_name" maxlength="10" class="input" style="width:100%;" /></td>
				</tr>
				<tr>
					<th>전화번호</th>
					<td style="padding:5px 0px;">
						<input type="tel" name="receiver_tel1" maxlength="13" class="input" style="width:100%;" />
					</td>
				</tr>
				<tr>
					<th>휴대폰</th>
					<td style="padding:5px 0px;">
						<input type="tel" name="receiver_tel2" maxlength="13" class="input" style="width:100%;" />
					</td>
				</tr>
				<tr>
					<th>이메일</th>
					<td style="padding:5px 0px;"><input type="text" name="receiver_email" maxlength="40" class="input" style="width:100%;" /></td>
				</tr>
				<tr>
					<th>주소</th>
					<td style="padding:5px 0px;">
						<div>
							<input type="text" name="rpost1" maxlength="5" id="rpost1" class="input" style="width:30%;background:#f5f5f5;" onclick="addr_search_for_daumapi('rpost1','raddr1','raddr2')" readOnly />
							<a href="javascript:addr_search_for_daumapi('rpost1','raddr1','raddr2');" class="btn_s_line2">주소검색</a>
						</div>
						<div style="margin:4px 0px;">
							<input type="text" name="raddr1" maxlength="50" id="raddr1" class="input" style="width:100%;background:#f5f5f5;" readonly />
						</div>
						<input type="text" name="raddr2" maxlength="50" id="raddr2" class="input" style="width:100%;background:#f5f5f5;" />
					</td>
				</tr>
			</table>
			<div style="margin-top:20px;padding-top:20px;border-top:1px solid #eee;text-align:center;">
				<a href="mypage_delivery.php" class="btn_s_line2">목록으로</a>
				<a href="#" onclick="CheckForm()" class="btn_s_line2">배송지 등록하기</a>
			</div>
		</form>
	</div>
</div>

<!-- iOS에서는 position:fixed 버그가 있음, 적용하는 사이트에 맞게 position:absolute 등을 이용하여 top,left값 조정 필요 -->
<div id="layer" style="display:none;position:fixed;padding:15% 3%;box-sizing:border-box;background:rgba(0,0,0,0.7);z-index:1000;-webkit-overflow-scrolling:touch;">
	<div id="btnCloseLayer" style="position:absolute;right:0px;top:0px;left:0px;bottom:0px;z-index:0;" onclick="closeDaumPostcode()">
		<div style="position:absolute;top:3%;right:3%;color:#fff;font-size:4em;font-weight:500;">&times;</div>
	</div>
</div>

<!--<script src="https://dmaps.daum.net/map_js_init/postcode.v2.js"></script>-->
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script>
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

<script language="javascript">
	<!--
	function CheckForm(){
		if(document.form1.receiver_name.value.length==0) {
			alert("수신자명을 입력하세요.");
			document.form1.receiver_name.focus();
			return;
		}

		if(!IsNumeric(document.form1.receiver_tel1.value)) {
			alert("전화번호는 숫자만 입력 가능합니다.");
			document.form1.receiver_tel1.focus();
			return;
		}

		if(document.form1.receiver_tel2.value.length==0) {
			alert("휴대폰 번호를 입력하세요.");
			document.form1.receiver_tel2.focus();
			return;
		}
		if(!IsNumeric(document.form1.receiver_tel2.value)) {
			alert("휴대폰 번호는 숫자만 입력 가능합니다.");
			document.form1.receiver_tel2.focus();
			return;
		}

		if(document.form1.rpost1.value.length==0) {
			alert("우편번호를 입력하세요.");
			document.form1.rpost1.focus();
			return;
		}
		if(document.form1.raddr1.value.length==0) {
			alert("주소를 입력하세요.");
			document.form1.raddr1.focus();
			return;
		}
		if(document.form1.raddr2.value.length==0) {
			alert("주소를 입력하세요.");
			document.form1.raddr2.focus();
			return;
		}

		document.form1.type.value="insert";
		document.form1.submit();
	}

	$(function(){
		$('#close_delivery').on('click',function(){
			$('#insert_delivery_pop').hide();
		});
	});
	//-->
</script>

<?=$onload?>