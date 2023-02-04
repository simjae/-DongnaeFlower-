<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	
	$promotiontype = !_empty($_POST['promotiontype'])?trim($_POST['promotiontype']):"";
	$prcode = !_empty($_POST['prcode'])?trim($_POST['prcode']):"";
	$promotionid = $_ShopInfo->getMemid();
	$amount = "1000";
	if(strlen($promotiontype) > 0 && strlen($prcode) > 0 && strlen($promotionid) >0 ) {

		$inquireSQL = "SELECT idx FROM promotion_reserve_log WHERE promotionid='".$promotionid."' ";
		$inquirerowcount="";
		if(false !== $inquireRes = mysql_query($inquireSQL,get_db_conn())){
			$inquirerowcount = mysql_num_rows($inquireRes);
			
			if($inquirerowcount <= 0){
				$payreserveSQL = "UPDATE tblmember SET reserve=reserve+".$amount." WHERE id = '".$promotionid."' ";

				if(false !== mysql_query($payreserveSQL,get_db_conn())){
					$reservelogSQL = "INSERT tblreserve SET ";
					$reservelogSQL .= "id = '".$promotionid."', ";
					$reservelogSQL .= "reserve = '".$amount."', ";
					$reservelogSQL .= "reserve_yn = 'Y', ";
					$reservelogSQL .= "content = '카카오스토리 및 카카오톡 상품정보 전송 클릭', ";
					$reservelogSQL .= "date = '".date('YmdHis')."' ";

					if(false !==mysql_query($reservelogSQL,get_db_conn())){
						
						$promotionlogSQL = "INSERT promotion_reserve_log set ";
						$promotionlogSQL .= "productcode = '".$prcode."', ";
						$promotionlogSQL .= "promotionid = '".$promotionid."', ";
						$promotionlogSQL .= "promotiontype = '".$promotiontype."', ";
						$promotionlogSQL .= "saveamount = '".$amount."', ";
						$promotionlogSQL .= "regdate = '".date('Y-m-d H:i:s')."' ";
						@mysql_query($promotionlogSQL,get_db_conn());
					}
				}
			}
		}
	}
?>
<script>
	parent.kakaocall('<?=$promotiontype?>');
</script>