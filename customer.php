<? 
	include_once('header.php'); 
	//고객센터 번호
	$row_phone = mysql_fetch_array(mysql_query("select info_tel from tblshopinfo"));
	include $skinPATH."customer.php";
?>
<? include_once('footer.php'); ?>