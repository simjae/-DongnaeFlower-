<?
define( KAKAO_SESSION_NAME, "KKO_SESSION" );
define( NAVER_SESSION_NAME, "NHN_SESSION" );
$Dir = "../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
//include_once($Dir."lib/shopdata.php");  //원래 web에서 logout 처리가 포함되어있다


	if(isset($_SESSION) && is_array($_SESSION) && $_SESSION[KAKAO_SESSION_NAME]){
		$kakao->logout();
	}
	if(isset($_SESSION) && is_array($_SESSION) && $_SESSION[NAVER_SESSION_NAME]){
		$naver->logout();
	}
$sql = "UPDATE tblmember SET authidkey='logout' WHERE id='".$_ShopInfo->getMemid()."' ";
mysql_query($sql,get_db_conn());
$_ShopInfo->SetMemNULL();
$_ShopInfo->Save();
$url = "./";

?>

<script>
	var broswerInfo = navigator.userAgent;
	if(broswerInfo.indexOf("dongne-flower user android")>-1){
		BRIDGE.appLogoutProc();
	}
	if(broswerInfo.indexOf("dongne-flower user android")>-1){
		window.webkit.messageHandlers.appLogoutProc.postMessage("");
	}
</script>
<?
echo "<script>location.href='".$url."';</script>";
?>