<?
include "header.php";
include_once($Dir."lib/ext/base_func.php");
include_once($Dir."lib/ext/product_func.php");
include_once($Dir."lib/ext/order_func.php");
include_once($Dir."lib/class/pages.php");

if(strlen($_ShopInfo->getMemid())==0) {
	echo "<script>location.href='login.php?chUrl=".getUrl()."';</script>";
	exit;
}

$sql = "SELECT * FROM tblmember WHERE id='".$_ShopInfo->getMemid()."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	if($row->member_out=="Y") {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');location.href='login.php';\"></body></html>";exit;
	}

	if($row->authidkey!=$_ShopInfo->getAuthidkey()) {
		$_ShopInfo->SetMemNULL();
		$_ShopInfo->Save();
		echo "<html><head><title></title></head><body onload=\"alert('처음부터 다시 시작하시기 바랍니다.');location.href='login.php';\"></body></html>";exit;
	}
}
mysql_free_result($result);

function get_totaldays($year,$month) {
	$date = 1;
	while(checkdate($month,$date,$year)) {
		$date++;
	}

	$date--;

	return $date;
}

$s_year=(int)$_POST["s_year"];
$s_month=(int)$_POST["s_month"];
$s_day=(int)$_POST["s_day"];

$e_year=(int)$_POST["e_year"];
$e_month=(int)$_POST["e_month"];
$e_day=(int)$_POST["e_day"];

if($e_year==0) $e_year=(int)date("Y");
if($e_month==0) $e_month=(int)date("m");
if($e_day==0) $e_day=(int)date("d");

$stime=mktime(0,0,0,($e_month-1),$e_day,$e_year);
if($s_year==0) $s_year=(int)date("Y",$stime);
if($s_month==0) $s_month=(int)date("m",$stime);
if($s_day==0) $s_day=(int)date("d",$stime);

$ordgbn=$_POST["ordgbn"];
if(!preg_match("/^(A|S|C|R)$/",$ordgbn)) {
	$ordgbn="A";
}


//리스트 세팅
$setup[page_num] = 5;
$setup[list_num] = 3;

$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];

if ($block != "") {
	$nowblock = $block;
	$curpage  = $block * $setup[page_num] + $gotopage;
} else {
	$nowblock = 0;
}

if (($gotopage == "") || ($gotopage == 0)) {
	$gotopage = 1;
}

?>

<!--<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>-->

<SCRIPT LANGUAGE="JavaScript">
	<!--
	var NowYear=parseInt(<?=date('Y')?>);
	var NowMonth=parseInt(<?=date('m')?>);
	var NowDay=parseInt(<?=date('d')?>);
	function getMonthDays(sYear,sMonth) {
		var Months_day = new Array(0,31,28,31,30,31,30,31,31,30,31,30,31)
		var intThisYear = new Number(), intThisMonth = new Number();
		datToday = new Date();													// 현재 날자 설정
		
		intThisYear = parseInt(sYear);
		intThisMonth = parseInt(sMonth);
		
		if (intThisYear == 0) intThisYear = datToday.getFullYear();				// 값이 없을 경우
		if (intThisMonth == 0) intThisMonth = parseInt(datToday.getMonth())+1;	// 월 값은 실제값 보다 -1 한 값이 돼돌려 진다.
		

		if ((intThisYear % 4)==0) {													// 4년마다 1번이면 (사로나누어 떨어지면)
			if ((intThisYear % 100) == 0) {
				if ((intThisYear % 400) == 0) {
					Months_day[2] = 29;
				}
			} else {
				Months_day[2] = 29;
			}
		}
		intLastDay = Months_day[intThisMonth];										// 마지막 일자 구함
		return intLastDay;
	}

	function ChangeDate(gbn) {
		year=document.form1[gbn+"_year"].value;
		month=document.form1[gbn+"_month"].value;
		totdays=getMonthDays(year,month);

		MakeDaySelect(gbn,1,totdays);
	}

	function MakeDaySelect(gbn,intday,totdays) {
		document.form1[gbn+"_day"].options.length=totdays;
		for(i=1;i<=totdays;i++) {
			var d = new Option(i);
			document.form1[gbn+"_day"].options[i] = d;
			document.form1[gbn+"_day"].options[i].value = i;
		}
		document.form1[gbn+"_day"].selectedIndex=intday;
	}

	function GoSearch(gbn) {
	//	if(gbn=="") return;
		switch(gbn) {
			case "TODAY":
				s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
				break;
			case "15DAY":
				s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay)-15);
				break;
			case "1MONTH":
				s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-1, parseInt(NowDay));
				break;
			case "3MONTH":
				s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-3, parseInt(NowDay));
				break;
			case "6MONTH":
				s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-6, parseInt(NowDay));
				break;
			case "12MONTH":
				s_date = new Date(parseInt(NowYear), parseInt(NowMonth)-12, parseInt(NowDay));
				break;
			default :
				location.href="orderlist.php";
				//s_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
				break;
		}

		e_date = new Date(parseInt(NowYear), parseInt(NowMonth), parseInt(NowDay));
		document.form1.s_year.value=parseInt(s_date.getFullYear());
		document.form1.s_month.value=parseInt(s_date.getMonth());
		document.form1.e_year.value=NowYear;
		document.form1.e_month.value=NowMonth;
		totdays=getMonthDays(parseInt(s_date.getFullYear()),parseInt(s_date.getMonth()));
		MakeDaySelect("s",parseInt(s_date.getDate()),totdays);
		totdays=getMonthDays(NowYear,NowMonth);
		MakeDaySelect("e",NowDay,totdays);

		document.form1.submit();
	}

	function CheckForm() {
		s_year=document.form1.s_year.value;
		s_month=document.form1.s_month.value;
		s_day=document.form1.s_day.value;
		s_date = new Date(parseInt(s_year), parseInt(s_month), parseInt(s_day));

		e_year=document.form1.e_year.value;
		e_month=document.form1.e_month.value;
		e_day=document.form1.e_day.value;
		e_date = new Date(parseInt(e_year), parseInt(e_month), parseInt(e_day));
		tmp_e_date = new Date(parseInt(e_year), parseInt(e_month)-12, parseInt(e_day));

		if(s_date>e_date) {
			alert("조회 기간이 잘못 설정되었습니다. 기간을 다시 설정해서 조회하시기 바랍니다.");
			return;
		}
		if(s_date<tmp_e_date) {
			alert("조회 기간이 12개월을 넘었습니다. 12개월 이내로 설정해서 조회하시기 바랍니다.");
			return;
		}
		document.form1.submit();
	}

	function GoOrdGbn(temp) {
		document.form1.ordgbn.value=temp;
		document.form1.submit();
	}

	function OrderDetailPop(ordercode) {
		
		$("#layer_popup").show();
		$("#layer_content").attr("src","orderdetailpop.php?ordercode="+ordercode);
//		document.detailform.ordercode.value=ordercode;
//		window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
//		document.detailform.submit();
	}

	function DeliSearch(deli_url){
		window.open(deli_url,"배송추적","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizeble=yes,copyhistory=no,width=600,height=550");
	}

	function DeliveryPop(ordercode) {
		$("#layer_popup").show();
		document.deliform.ordercode.value=ordercode;
//		window.open("about:blank","delipop","width=600,height=370,scrollbars=no");
		document.deliform.submit();
	}

	function GoPage(block,gotopage) {
		document.form2.block.value=block;
		document.form2.gotopage.value=gotopage;
		document.form2.submit();
	}

	function productAll(chk_name) {
		
		chk_all = document.getElementById(chk_name+"_all");
		
		chk = document.getElementsByName(chk_name);
		for(i=0;i<chk.length;i++) {
			chk[i].checked=chk_all.checked;
		}
	}

	function order_one_cancel(ordercode, productcode, can, tempkey,uid) {
		if (can=="yes") {
			if (confirm("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다")) {
			window.open("<?=$Dir?>app/order_one_cancel_pop.php?ordercode="+ordercode+"&productcode="+productcode+"&uid="+uid,"one_cancel","width=610,height=500,scrollbars=yes");
			}
		}else{
			if (confirm("입금확인중 주문은 '전체취소'만 가능합니다. \n전체취소를 원하시는 경우 구매를 원하는 상품을 다시 주문해주세요.\n이주문을 지금 주문 전체취소하시겠습니까?")) {


				document.detailform.tempkey.value=tempkey;
				document.detailform.type.value="cancel";

				document.detailform.ordercode.value=ordercode;
				window.open("about:blank","orderpop","width=610,height=500,scrollbars=yes");
				document.detailform.submit();

				document.detailform.tempkey.value="";
				document.detailform.type.value="";

			}
			//alert("입금확인중 주문은 '전체취소'만 가능합니다. \n전체 취소 후 구매를 원하는 상품을 다시 주문하여 주십시오.");
		}
	}

	function order_multi_cancel(ordercode) {
		chk_name= "chk_"+ordercode;
		chk_uid_name= "chk_uid_"+ordercode;
		
		productcode = "";
		uid = "";
		product_chk = 0;

		chk = document.getElementsByName(chk_name);
		chk_uid = document.getElementsByName(chk_uid_name);
		for(i=0;i<chk.length;i++) {
			if (chk[i].checked) {
				

				if (productcode=="") {
					productcode = chk[i].value;
				}else{
					productcode = productcode+"$$"+chk[i].value;
				}

				if (uid=="") {
					uid = chk_uid[i].value;
				}else{
					uid = uid+"$$"+chk_uid[i].value;
				}

				product_chk++
			}
		}

		if (product_chk==0) {
			alert("선택된 상품이 없습니다.");
		}else{
			if (confirm("주문취소가 완료되면 지급예정된 적립금 및 주문시 사용쿠폰이 모두 취소되며 취소된 주문건은 다시 되돌릴 수 없습니다")) {
				window.open("<?=$Dir?>app/order_one_cancel_pop.php?ordercode="+ordercode+"&productcode="+productcode+"&uid="+uid,"one_cancel","width=610,height=500,scrollbars=yes");
			}
		}
	}
	//-->
</SCRIPT>

<?
echo "<form name=form1 method=post action=\"".$_SERVER[PHP_SELF]."\">\n";
echo "<input type=hidden name=ordgbn value=\"".$ordgbn."\">\n";
?>
<div style="display:none">
<SELECT onchange="ChangeDate('s')" name="s_year" align="absmiddle" style="font-size:11px;">
<?
for($i=date("Y");$i>=(date("Y")-2);$i--) {
	echo "<option value=\"".$i."\"";
	if($s_year==$i) echo " selected";
	echo " style=\"color:#444444;\">".$i."</option>\n";
}
?>
</SELECT> <SELECT onchange="ChangeDate('s')" name="s_month" style="font-size:11px;">
<?
for($i=1;$i<=12;$i++) {
	echo "<option value=\"".$i."\"";
	if($s_month==$i) echo " selected";
	echo " style=\"color:#444444;\">".$i."</option>\n";
}
?>
</SELECT> <SELECT name="s_day" style="font-size:11px;">
<?
for($i=1;$i<=get_totaldays($s_year,$s_month);$i++) {
	echo "<option value=\"".$i."\"";
	if($s_day==$i) echo " selected";
	echo " style=\"color:#444444;\">".$i."</option>\n";
}
?>
</SELECT><b> ~ </b> <SELECT onchange="ChangeDate('e')" name="e_year" style="font-size:11px;">
<?
for($i=date("Y");$i>=(date("Y")-2);$i--) {
	echo "<option value=\"".$i."\"";
	if($e_year==$i) echo " selected";
	echo " style=\"color:#444444;\">".$i."</option>\n";
}
?>
</SELECT> <SELECT onchange="ChangeDate('e')" name="e_month" style="font-size:11px;">
<?
for($i=1;$i<=12;$i++) {
	echo "<option value=\"".$i."\"";
	if($e_month==$i) echo " selected";
	echo " style=\"color:#444444;\">".$i."</option>\n";
}
?>
</SELECT> <SELECT name="e_day" style="font-size:11px;">
<?
for($i=1;$i<=get_totaldays($e_year,$e_month);$i++) {
	echo "<option value=\"".$i."\"";
	if($e_day==$i) echo " selected";
	echo " style=\"color:#444444;\">".$i."</option>\n";
}
?>
</SELECT>
</div>
<?
include ($skinPATH."orderlist.php");
echo "</form>\n";
?>

<form name=form2 method=post action="<?=$_SERVER[PHP_SELF]?>">
	<input type=hidden name=block value="<?=$block?>">
	<input type=hidden name=gotopage value="<?=$gotopage?>">
	<input type=hidden name=ordgbn value="<?=$ordgbn?>">
	<input type=hidden name=s_year value="<?=$s_year?>">
	<input type=hidden name=s_month value="<?=$s_month?>">
	<input type=hidden name=s_day value="<?=$s_day?>">
	<input type=hidden name=e_year value="<?=$e_year?>">
	<input type=hidden name=e_month value="<?=$e_month?>">
	<input type=hidden name=e_day value="<?=$e_day?>">
	<input type=hidden name=search_period value="<?=$search_period?>">
</form>

<form name=detailform method=post action="./orderdetailpop.php" target="layer_content">
	<input type=hidden name=ordercode>
	<input type=hidden name=tempkey>
	<input type=hidden name=type>
</form>

<form name=deliform method=post action="deliverypop.php" target="layer_content">
	<input type=hidden name=ordercode>
</form>

<form name="reviewForm" method="post">
	<input type="hidden" name="productcode" value=""/>
</form>

<script>
	function reviewWrite(prcode){
		$("#layer_popup").show();
		$("#layer_content").attr("src","./prreview_write_pop.php?productcode="+prcode);

	}

// 현금영수증 발급 레이어 보이기
function view_cash_receipt(ordercode) {
	if (ordercode == "") {
		alert("잘못된 요청입니다.");
		return;
	}

	var target = jQuery("#view_"+ordercode),
		isReq  = jQuery("#btn_"+ordercode).data("msg");

	if (target.is(":visible")) {
		target.hide();
	} else {
		if (isReq != "Y") {
			jQuery(".hiddenDiv").hide();
			target.show();
		} else {
			alert("현금영수증이 이미 발급되었습니다.");
		}
	}
}

// 소득공제 인지, 사업자증빙용인지 선택
function choice_type(ordercode, type) {
	if (ordercode == "" || (type != 0 && type != 1)) {
		alert("잘못된 요청입니다.");
		return;
	}

	var personal = "#per_"+ordercode,
		company  = "#com_"+ordercode;

	if (type == 0) {	// 개인소득공제
		jQuery(company).hide();
		jQuery(personal).show();
	} else {			// 사업자증빙용
		jQuery(personal).hide();
		jQuery(company).show();
	}
}

// 현금영수증 발급 요청
function request_cash_receipt(ordercode) {
	if (ordercode == "") {
		alert("잘못된 요청입니다.");
		return;
	}

	var pg_type   = "<?php echo $pg_type?>",
		useopt    = jQuery("input[name='useopt_"+ordercode+"']:checked").val(),
		num_type1 = jQuery("input[name='num_type1_"+ordercode+"']:checked").val(),
		num_type2 = jQuery("input[name='num_type2_"+ordercode+"']:checked").val(),
		reg_num   = jQuery("input[name='reg_num_"+ordercode+"']").val(),
		com_num   = jQuery("input[name='com_num_"+ordercode+"']").val(),
		data      = "ordercode="+ordercode;

	if (pg_type == "") {
		alert("연결된 PG사가 없습니다.");
		return;
	}

	data += "&useopt="+useopt;

	if (useopt == 1) {
		data += "&com_num="+com_num;
	} else {
		data += "&reg_num="+reg_num;
	}

	jQuery.ajax({
		method : "POST",
		url : "/paygate/"+pg_type+"/receipt_result.php",
		data : data,
		dataType : 'json',
		success : function(result) {
			if (result.msg == "OK") {
				var target = jQuery("#btn_"+ordercode);
				target.text("현금영수증 발급완료");
				target.data("msg", "Y");
				alert("현금영수증이 정상적으로 발급되었습니다.");
			} else {
				alert("현금영수증 발급이 실패하였습니다. ("+result.msg+")");
			}
			view_cash_receipt(ordercode);
		}
	});
}
function PopupClose(){
	$("#layer_popup").hide();
	$("#layer_content").attr("src","about:blank");
}
</script>

<div id="layer_popup" style="display: none; position: fixed; box-sizing: border-box; background: rgba(0, 0, 0, 0.7); z-index: 999; width: 100%; height: 100%; border: 0px solid rgb(221, 221, 221); left: 0%; top: 0%;">
	<div style="position:absolute;top: 75px;right:3%;color:black;font-size:4em;font-weight:500;z-index: 900;"  onclick="PopupClose()">×</div>
	<div style="position: relative; width: 100%; height: 100%; z-index: 0; overflow: hidden auto; min-width: 300px; margin: 0px; padding: 0px;">
		<iframe frameborder="0" id="layer_content" name="layer_content" src="about:blank" style="position: absolute; left: 0px; top: 50px; width: 100%; height: calc(100% - 50px); border: 0px none; margin: 0px; padding: 0px; overflow: hidden; min-width: 300px;"></iframe>
	</div>
</div>
<? include ("footer.php") ?>