<?
$Dir = "../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");

$prcode = !_empty($_GET['prcode'])?trim($_GET['prcode']):"";

if($prcode == ""){
	echo '<script>alert("잘못된 페이지 접근입니다.");self.close();</script>';exit;
}

$contentsSQL = "SELECT content FROM tblproduct WHERE productcode = '".$prcode."' ";

if(false !== $contentsRes = mysql_query($contentsSQL,get_db_conn())){
	$contents = mysql_result($contentsRes,0,0);
	
	$printcontent = stripslashes($contents);
	mysql_free_result($contentsRes);
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
		<meta http-equiv="Cache-Control" content="no-cache" />
		<style>
			body,div{margin:0;padding:0px;border:0px;}
			.buttonarea{text-align:center;margin-bottom:20px;}
			.buttonarea button{padding:5px 0px;font-size:2em;width:15%;border:1px solid #ebebeb;background:#ffffff;}
		</style>
	</head>
	<body>
		<div>
			<?=$printcontent?>
		</div>
		<div class="buttonarea">
			<button onClick="self.close();">닫기</button>
		</div>
	</body>
</html>