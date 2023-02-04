<?
	$Dir="../../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");

	$code = $_POST['code'];

	$wish_chk = true;
	$wish_sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' AND productcode='".$code."' ";
	$wish_result = mysql_query($wish_sql, get_db_conn());
	$wish_row = mysql_fetch_object($wish_result);
	if($wish_row->cnt>0)
		$wish_chk = false;
	
	if($wish_chk){
		$codeA=substr($code,0,3);
		$codeB=substr($code,3,3);
		$codeC=substr($code,6,3);
		$codeD=substr($code,9,3);
		
		$option1 = 0;
		$option2 = 0;
		$opts = 0;

		$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
		$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			if($row->group_code=="NO") {	//숨김 분류
				$errmsg= "판매가 종료된 상품입니다.";
			} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
				$errmsg= "해당 분류의 접근 권한이 없습니다.";
			}
		} else {
			$errmsg= "해당 분류가 존재하지 않습니다.";
		}
		mysql_free_result($result);

		$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
		$sql.= "WHERE productcode='".$code."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			if($row->display!="Y") {
				$errmsg="해당 상품은 판매가 되지 않는 상품입니다.";
			}
			if($row->group_check!="N") {
				if(strlen($_ShopInfo->getMemid())>0) {
					$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
					$sqlgc.= "WHERE productcode='".$code."' ";
					$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
					$resultgc=mysql_query($sqlgc,get_db_conn());
					if($rowgc=@mysql_fetch_object($resultgc)) {
						if($rowgc->groupcheck_count<1) {
							$errmsg="해당 상품은 지정 등급 전용 상품입니다.";
						}
						@mysql_free_result($resultgc);
					} else {
						$errmsg="해당 상품은 지정 등급 전용 상품입니다.";
					}
				} else {
					$errmsg="해당 상품은 회원 전용 상품입니다.";
				}
			}
			if(strlen($errmsg)==0) {
				if(strlen(dickerview($row->etctype,0,1))>0) {
					$errmsg="해당 상품은 판매가 되지 않습니다.";
				}
			}
			if(strlen($row->option1)>0)  $option1=1;
			if(strlen($row->option2)>0)  $option2=1;
		} else {
			$errmsg="해당 상품이 존재하지 않습니다.";
		}
		mysql_free_result($result);
		
		if(strlen($errmsg)>0) {

		}

		$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
		$result2=mysql_query($sql,get_db_conn());
		$row2=mysql_fetch_object($result2);
		$totcnt=$row2->totcnt;
		mysql_free_result($result2);
		$maxcnt=100;

		if($totcnt<$maxcnt) {
			$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$sql.= "AND productcode='".$code."' AND opt1_idx='".$option1."' ";
			$sql.= "AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
			$result2=mysql_query($sql,get_db_conn());
			$row2=mysql_fetch_object($result2);
			$cnt=$row2->cnt;
			mysql_free_result($result2);
			if($cnt<=0) {
				$sql = "INSERT tblwishlist SET ";
				$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
				$sql.= "productcode	= '".$code."', ";
				$sql.= "opt1_idx	= '".$option1."', ";
				$sql.= "opt2_idx	= '".$option2."', ";
				$sql.= "optidxs		= '".$opts."', ";
				$sql.= "date		= '".date("YmdHis")."' ";
				mysql_query($sql,get_db_conn());
			} else {
				$sql = "UPDATE tblwishlist SET date='".date("YmdHis")."' ";
				$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
				$sql.= "AND productcode='".$code."' ";
				$sql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
				mysql_query($sql,get_db_conn());
			}
			$msg = "on";
		} else {
			$errmsg= "WishList에는 ".$maxcnt."개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.";
		}
	}else{
		$sql = "DELETE FROM tblwishlist where id = '".$_ShopInfo->getMemid()."' and productcode = '".$code."' ";
		mysql_query($sql, get_db_conn());
		$msg = "off";
	}

	if(strlen($errmsg)>0)
		$msg = $errmsg;

	echo rawurlencode( iconv("CP949", "UTF-8", $msg));
?>