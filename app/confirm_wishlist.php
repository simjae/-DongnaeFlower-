<?
include "header.php";

if(strlen($_ShopInfo->getMemid())==0) {
	Header("Location:login.php?chUrl=".getUrl());
	exit;
}



$productcode=$_POST["productcode"];
$opts=$_POST["opts"];
$option1=$_POST["option1"];
$option2=$_POST["option2"];
$opt_comidx = $_POST["opt_comidx"];

if (empty($opts))  $opts="0";
if (empty($option1))  $option1=0;
if (empty($option2))  $option2=0;
if (empty($opt_comidx))  $opt_comidx=0;



if(strlen($productcode)==18) {
	$codeA=substr($productcode,0,3);
	$codeB=substr($productcode,3,3);
	$codeC=substr($productcode,6,3);
	$codeD=substr($productcode,9,3);

	$sql = "SELECT * FROM tblproductcode WHERE codeA='".$codeA."' AND codeB='".$codeB."' ";
	$sql.= "AND codeC='".$codeC."' AND codeD='".$codeD."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->group_code=="NO") {	//숨김 분류
			echo "<html></head><body onload=\"alert('판매가 종료된 상품입니다.');window.close()\"></body></html>";exit;
		} else if(strlen($row->group_code)>0 && $row->group_code!="ALL" && $row->group_code!=$_ShopInfo->getMemgroup()) {	//그룹회원만 접근
			echo "<html></head><body onload=\"alert('해당 분류의 접근 권한이 없습니다.');window.close();\"></body></html>";exit;
		}
	} else {
		echo "<html></head><body onload=\"alert('해당 분류가 존재하지 않습니다.');window.close();\"></body></html>";exit;
	}
	mysql_free_result($result);

	$sql = "SELECT productname,quantity,display,option1,option2,option_quantity,etctype,group_check FROM tblproduct ";
	$sql.= "WHERE productcode='".$productcode."' ";
	$result=mysql_query($sql,get_db_conn());
	if($row=mysql_fetch_object($result)) {
		if($row->display!="Y") {
			$errmsg="해당 상품은 판매가 되지 않는 상품입니다.\\n";
		}
		if($row->group_check!="N") {
			if(strlen($_ShopInfo->getMemid())>0) {
				$sqlgc = "SELECT COUNT(productcode) AS groupcheck_count FROM tblproductgroupcode ";
				$sqlgc.= "WHERE productcode='".$productcode."' ";
				$sqlgc.= "AND group_code='".$_ShopInfo->getMemgroup()."' ";
				$resultgc=mysql_query($sqlgc,get_db_conn());
				if($rowgc=@mysql_fetch_object($resultgc)) {
					if($rowgc->groupcheck_count<1) {
						$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
					}
					@mysql_free_result($resultgc);
				} else {
					$errmsg="해당 상품은 지정 등급 전용 상품입니다.\\n";
				}
			} else {
				$errmsg="해당 상품은 회원 전용 상품입니다.\\n";
			}
		}
		if(strlen($errmsg)==0) {
			if(strlen(dickerview($row->etctype,0,1))>0) {
				$errmsg="해당 상품은 판매가 되지 않습니다.\\n";
			}
		}
		if(empty($option1) && strlen($row->option1)>0)  $option1=1;
		if(empty($option2) && strlen($row->option2)>0)  $option2=1;
	} else {
		$errmsg="해당 상품이 존재하지 않습니다.\\n";
	}
	mysql_free_result($result);
	
	if(strlen($errmsg)>0) {
		echo "<html></head><body onload=\"alert('".$errmsg."');window.close()\"></body></html>";
		exit;
	}

	$sql = "SELECT COUNT(*) as totcnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
	$result2=mysql_query($sql,get_db_conn());
	$row2=mysql_fetch_object($result2);
	$totcnt=$row2->totcnt;
	mysql_free_result($result2);
	$maxcnt=100;
	if($totcnt>=$maxcnt) {
		$sql = "SELECT b.productcode FROM tblwishlist a, tblproduct b ";
		$sql.= "LEFT OUTER JOIN tblproductgroupcode c ON b.productcode=c.productcode ";
		$sql.= "WHERE a.id='".$_ShopInfo->getMemid()."' AND a.productcode=b.productcode ";
		$sql.= "AND b.display='Y' ";
		$sql.= "AND (b.group_check='N' OR c.group_code='".$_ShopInfo->getMemgroup()."') ";
		$sql.= "GROUP BY b.productcode ";
		$result2=mysql_query($sql,get_db_conn());
		$i=0;
		$wishprcode="";
		while($row2=mysql_fetch_object($result2)) {
			$wishprcode.="'".$row2->productcode."',";
			$i++;
		}
		mysql_free_result($result2);
		$totcnt=$i;
		$wishprcode=substr($wishprcode,0,-1);
		if(strlen($wishprcode)>0) {
			$sql = "DELETE FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$sql.= "AND productcode NOT IN (".$wishprcode.") ";
			mysql_query($sql,get_db_conn());
			echo "<html></head><body onload=\"if(typeof(opener.setFollowFunc)!='undefined') { opener.setFollowFunc('Wishlist','selectmenu'); }\"></body></html>";
		}
	}
	if($totcnt<$maxcnt) {
		if(is_array($opt_comidx)) {
			for($i=0, $end=count($opt_comidx); $i<$end; $i++) {
				$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
				$sql.= "AND productcode='".$productcode."' AND opt1_idx='".$option1."' ";
				$sql.= "AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
				$sql.= "AND com_idx='".$opt_comidx[$i]."' ";
				$result2=mysql_query($sql,get_db_conn());
				$row2=mysql_fetch_object($result2);
				$cnt=$row2->cnt;
				mysql_free_result($result2);
				if($cnt<=0) {
					$sql = "INSERT tblwishlist SET ";
					$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
					$sql.= "productcode	= '".$productcode."', ";
					$sql.= "opt1_idx	= '".$option1."', ";
					$sql.= "opt2_idx	= '".$option2."', ";
					$sql.= "optidxs		= '".$opts."', ";
					$sql.= "com_idx='".$opt_comidx[$i]."', ";
					$sql.= "date		= '".date("YmdHis")."' ";
					mysql_query($sql,get_db_conn());
				} else {
					$sql = "UPDATE tblwishlist SET date='".date("YmdHis")."' ";
					$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
					$sql.= "AND productcode='".$productcode."' ";
					$sql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
					$sql.= "AND com_idx='".$opt_comidx[$i]."' ";
					mysql_query($sql,get_db_conn());
				}
			}
		} else {
			$sql = "SELECT COUNT(*) as cnt FROM tblwishlist WHERE id='".$_ShopInfo->getMemid()."' ";
			$sql.= "AND productcode='".$productcode."' AND opt1_idx='".$option1."' ";
			$sql.= "AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
			$sql.= "AND com_idx='".$opt_comidx."' ";
			$result2=mysql_query($sql,get_db_conn());
			$row2=mysql_fetch_object($result2);
			$cnt=$row2->cnt;
			mysql_free_result($result2);
			if($cnt<=0) {
				$sql = "INSERT tblwishlist SET ";
				$sql.= "id			= '".$_ShopInfo->getMemid()."', ";
				$sql.= "productcode	= '".$productcode."', ";
				$sql.= "opt1_idx	= '".$option1."', ";
				$sql.= "opt2_idx	= '".$option2."', ";
				$sql.= "optidxs		= '".$opts."', ";
				$sql.= "com_idx='".$opt_comidx."', ";
				$sql.= "date		= '".date("YmdHis")."' ";
				mysql_query($sql,get_db_conn());
			} else {
				$sql = "UPDATE tblwishlist SET date='".date("YmdHis")."' ";
				$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
				$sql.= "AND productcode='".$productcode."' ";
				$sql.= "AND opt1_idx='".$option1."' AND opt2_idx='".$option2."' AND optidxs='".$opts."' ";
				$sql.= "AND com_idx='".$opt_comidx."' ";
				mysql_query($sql,get_db_conn());
			}
		}
		echo "<html></head><body onload=\"if(typeof(opener.setFollowFunc)!='undefined') { opener.setFollowFunc('Wishlist','selectmenu'); }\"></body></html>";
	} else {
		echo "<html></head><body onload=\"alert('WishList에는 ".$maxcnt."개 까지만 등록이 가능합니다.\\n\\nWishList에서 다른 상품을 삭제하신 후 등록하시기 바랍니다.');window.close();\"></body></html>";exit;
	}
} else {
	echo "<html></head><body onload=\"window.close()\"></body></html>";exit;
}

?>

<script type="text/javascript">
<!--
function go_wishlist() {	
	location.href="wishlist.php";	
}
//-->
</SCRIPT>


<div class="h_area2">
	<h2>위시리스트</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
	<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
</div>
<div style="margin:20px; text-align:center;">
	해당 상품이 위시리스트 상품 목록에 등록되었습니다.<br />
	지금 등록한 상품을 목록에서 확인하시겠습니까?
</div>
<div style="margin-bottom:30px; text-align:center;">
	<a href="javascript:go_wishlist()" class="basic_button grayBtn" style="padding:0em 0.6em" rel="external">지금확인</a>
	<a href="javascript:history.back();" class="basic_button" style="padding:0em 0.6em" rel="external">아니오</a>
</div>


<?
include "footer.php";
?>