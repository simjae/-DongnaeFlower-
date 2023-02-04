<?
	include_once('header.php');

	//회사소개
	if($crow = mysql_fetch_array(mysql_query("select * from tbldesign"))){
	}else{
		$crow[introtype]="C";
	}
	include $skinPATH."company.php";

	include_once('footer.php');
?>