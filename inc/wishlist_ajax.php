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
			if($row->group_code=="NO") {	//���� �з�
				$errmsg= "�ǸŰ� ����� ��ǰ�Դϴ�.";
			} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//�׷�ȸ���� ����
				$errmsg= "�ش� �з��� ���� ������ �����ϴ�.";
			}
		} else {
			$errmsg= "�ش� �з��� �������� �ʽ��ϴ�.";
		}
		mysql_free_result($result);

		$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
		$sql.= "WHERE productcode='".$code."' ";
		$result=mysql_query($sql,get_db_conn());
		if($row=mysql_fetch_object($result)) {
			if($row->display!="Y") {
				$errmsg="�ش� ��ǰ�� �ǸŰ� ���� �ʴ� ��ǰ�Դϴ�.";
			}
			if($row->group_check!="N") {
				if(strlen($_ShopInfo->getMemid())>0) {
					$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
					$sqlgc.= "WHERE productcode='".$code."' ";
					$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
					$resultgc=mysql_query($sqlgc,get_db_conn());
					if($rowgc=@mysql_fetch_object($resultgc)) {
						if($rowgc->groupcheck_count<1) {
							$errmsg="�ش� ��ǰ�� ���� ��� ���� ��ǰ�Դϴ�.";
						}
						@mysql_free_result($resultgc);
					} else {
						$errmsg="�ش� ��ǰ�� ���� ��� ���� ��ǰ�Դϴ�.";
					}
				} else {
					$errmsg="�ش� ��ǰ�� ȸ�� ���� ��ǰ�Դϴ�.";
				}
			}
			if(strlen($errmsg)==0) {
				if(strlen(dickerview($row->etctype,0,1))>0) {
					$errmsg="�ش� ��ǰ�� �ǸŰ� ���� �ʽ��ϴ�.";
				}
			}
			if(strlen($row->option1)>0)  $option1=1;
			if(strlen($row->option2)>0)  $option2=1;
		} else {
			$errmsg="�ش� ��ǰ�� �������� �ʽ��ϴ�.";
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
			$errmsg= "WishList���� ".$maxcnt."�� ������ ����� �����մϴ�.\\n\\nWishList���� �ٸ� ��ǰ�� �����Ͻ� �� ����Ͻñ� �ٶ��ϴ�.";
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