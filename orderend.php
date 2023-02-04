<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;

$ordercode=$_POST["ordercode"];

include "header.php";

if(substr($ordercode,0,8)<=date("Ymd",strtotime("-3","day"))) {
	echo "<html><head></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.'); location.href='./'\"></body></html>";
	exit;
}

$sql = "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
$result=mysql_query($sql,get_db_conn());
if($row=mysql_fetch_object($result)) {
	$_ord=$row;
	$gift_price=$_ord->price-$row->deli_price;

	$receiver_addr = explode('주소 : ',$_ord->receiver_addr);
	$zipCode  = explode('우편번호 : ',$receiver_addr[0]);

	$sql = "select mobile from tblmember where id='".$_ord->id."'";
	$resultm=mysql_query($sql,get_db_conn());
	if($rowm=mysql_fetch_object($resultm)) {
		if (strlen($rowm->mobile)>0) $mobile = $rowm->mobile;
		$mobile=explode("-",replace_tel(check_num($mobile)));
	}

} else {
	echo "<html></head><body onload=\"alert('잘못된 경로로 접근하셨습니다.'); location.href='./'\"></body></html>";
	exit;
}
mysql_free_result($result);

if (preg_match("/^(V|O|Q|C|P|M)$/", $_ord->paymethod) && $_ord->deli_gbn=="C") {
	$_ord->pay_data = "결제 중 주문취소";
}

$gift_type=explode("|",$_data->gift_type);
$gift_cnt=0;
if (($_ord->paymethod=="B" || (preg_match("/^(V|O|Q|C|P|M){1}/", $_ord->paymethod) && strcmp($_ord->pay_flag,"0000")==0)) && $_ord->deli_gbn=="N" && strlen($_ShopInfo->getGifttempkey())>0) {
	if ($gift_type[2]=="A" || strlen($gift_type[2])==0 || ($gift_type[2]=="B" && $_ord->paymethod=="B")) {
		if (($gift_type[0]=="M" && strlen($_ShopInfo->getMemid())>0) || $gift_type[0]=="C") { // 회원전용, 비회원+회원
			$sql = "SELECT COUNT(*) as gift_cnt FROM tblgiftinfo ";
			if($gift_type[1]=="N") {
				$sql.= "WHERE gift_startprice<=".$gift_price." AND gift_endprice>".$gift_price." ";
			} else  {
				$sql.= "WHERE gift_startprice<=".$gift_price." ";
			}
			$sql.= "AND (gift_quantity is NULL OR gift_quantity>0) ";
			$result=mysql_query($sql,get_db_conn());
			$row=mysql_fetch_object($result);
			$gift_cnt=$row->gift_cnt;
			mysql_free_result($result);
		}
	}
}
$gift_cnt=0;

# Push메세지 토큰, 토큰 디바이스 취득
$token_sql .= "SELECT member.token member_token, member.token_device member_token_device, venderinfo.token vender_token, venderinfo.token_device vender_token_device ";
$token_sql .= "FROM `tblorderinfo` orderinfo ";
$token_sql .= "LEFT JOIN `tblorderproduct` orderproduct ON ";
$token_sql .= "orderinfo.ordercode = orderproduct.ordercode ";
$token_sql .= "LEFT JOIN `tblmember` member ON ";
$token_sql .= "orderinfo.id = member.id ";
$token_sql .= "LEFT JOIN `tblvenderinfo` venderinfo ON ";
$token_sql .= "orderproduct.vender = venderinfo.vender ";
$token_sql .= "WHERE orderinfo.ordercode='".$ordercode."' ";
$token_result=mysql_query($token_sql,get_db_conn());
if($row=mysql_fetch_object($token_result)) {
	//Push메세지 - 소비자
	$member_token = $row->member_token;
	$member_token_device = $row->member_token_device;
	$member_push_title = "주문 결제가 완료되었습니다.";
	$member_push_message = "꽃집에서 주문 내역을 확인중입니다.";
	
	sendPush($member_push_title, $member_push_message, $member_token);
	
	//Push메세지 - 스토어
	$vender_token = $row->vender_token;
	$vender_token_device = $row->vender_token_device;
	$vender_push_title = "제안한 스페셜 오더가 채택되었습니다.";
	$vender_push_message = "지금 주문 내용을 확인 후, [상품 준비 중]으로 상태를 변경해주세요.";
	
	sendPush($vender_push_title, $vender_push_message, $vender_token);
}

//알림톡 전송용 파라미터 취득
$at_sql ="SELECT
				mb.mobile,
				mb.name,
				CONCAT(receiveDate,' ',op.receiveTime) AS receiveDateTime,
				vi.com_name,
				vi.com_tel,
				op.receiveTypeText,
				(SELECT SUM(price) FROM tblorderproduct WHERE ordercode = op.ordercode AND productcode NOT LIKE 'COU%') AS productprice_vender,
				(SELECT SUM(price) FROM tblorderproduct WHERE ordercode = op.ordercode) AS productprice_member
			FROM
				tblorderinfo oi
				LEFT JOIN tblorderproduct op ON
				oi.ordercode = op.ordercode
				LEFT JOIN tblvenderinfo vi ON
				op.vender = vi.vender
				LEFT JOIN tblmember mb ON
				oi.id = mb.id
			WHERE
				oi.ordercode = '".$ordercode."' AND
				NOT(op.productcode LIKE '99%' OR op.productcode LIKE 'COU%')";
$at_result = mysql_query($at_sql,get_db_conn());
while($at_row = mysql_fetch_object($at_result)) {
	$mobile = $at_row->mobile;
	$name = $at_row->name;
	$receiveDateTime = $at_row->receiveDateTime;
	$com_name = $at_row->com_name;
	$com_tel = $at_row->com_tel;
	$receiveTypeText = $at_row->receiveTypeText;
	$productprice_vender = number_format($at_row->productprice_vender);
	$productprice_member = number_format($at_row->productprice_member);
}

//알림톡 50051 - 소비자 결제 완료 알림톡
$template_id_member = "50051";
$to_member = array($mobile);
$vals_member = array("name"=>$name,"date"=>$receiveDateTime,"shopname"=>$com_name,"delitype"=>$receiveTypeText,"price"=>$productprice_member,"ordercode"=>$ordercode);
sendTalkGroup($template_id_member,$to_member,$vals_member);

//알림톡 50056 - 스토어 결제 완료 알림톡
$template_id_vender = "50056";
$to_vender = array($com_tel);
$vals_vender = array("shopname"=>$com_name,"ordercode"=>$ordercode,"price"=>$productprice_vender);
sendTalkGroup($template_id_vender,$to_vender,$vals_vender);

include "./orderend_skin.php";
include "footer.php";
?>