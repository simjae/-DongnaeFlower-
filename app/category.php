<?
include "header.php";

$result_code = mysql_query("select use_same_product_code from tblmobileconfig");
$row_code = mysql_fetch_array($result_code);
$row_code[0];

$qry_mobile_display = "";
if($row_code[0]=="N")
{	$qry_mobile_display = " AND mobile_display = 'Y'";	}


?>

<SCRIPT LANGUAGE="JavaScript">
</SCRIPT>

<?
include ("./category_skin.php");
?>


<? include "footer.php"; ?>
