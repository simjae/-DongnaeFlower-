<?
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

//옵션 클래스 2016-09-26 Seul
include_once($Dir."lib/class/option.php");
$optClass = new Option;

$basket_idx   = $_POST['basket_idx'];
$com_idx   = $_POST['com_idx'];
$ordertype		= $_POST["ordertype"];

$basket = basketTable($ordertype);

// 옵션 변경 시
if (isset($_POST['seletedOptComIdx']) && !_empty($_POST['seletedOptComIdx']) && $_POST['seletedOptComIdx']!=0) {
    $prdcode = $_POST['prdcode'];
    $seletedOptComIdx = $_POST['seletedOptComIdx'];
    
    $where = sprintf("WHERE basketidx = %d AND productcode = '%s'",$basket_idx, $prdcode);
    
    $sql = "SELECT * FROM  ".$basket." ".$where;
    if (false !== $result = mysql_query($sql,get_db_conn())) {
        $row = mysql_fetch_object($result);
        mysql_free_result($result);
        
        $chk_where = "";
        if (strlen($_ShopInfo->getMemid()) > 0) {
            $chk_where = sprintf(" id = '%s' AND",$_ShopInfo->getMemid());
        } else {
            $chk_where = sprintf(" tempkey = '%s' AND",$_ShopInfo->getTempkey());
        }
        
        $chk_sql = sprintf("SELECT * FROM  ".$basket." WHERE".$chk_where." productcode = '%s' AND com_idx = %d", $prdcode, $seletedOptComIdx);
        $chk_result = mysql_query($chk_sql,get_db_conn());
        $chk_row = mysql_fetch_object($chk_result);
        
        if ($chk_row->com_idx == $seletedOptComIdx) {
			//이미 장바구니에 존재하는 옵션입니다.
			echo "1";
			exit;
        } else {
			$upd_sql = sprintf("UPDATE  ".$basket." SET com_idx = %d ".$where, $seletedOptComIdx);
            
            if (false !== mysql_query($upd_sql, get_db_conn())) {
				//옵션이 수정되었습니다.
				echo "2";
				exit;
            }
        }
    }
} else if(isset($_POST['seletedOptComIdx']) && !_empty($_POST['seletedOptComIdx']) && $_POST['seletedOptComIdx']==0) {
	//해당 옵션은 품절입니다.
	echo "3";
	exit;
}
?>