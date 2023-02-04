<?
	$Dir = "../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	$prcode = !_empty($_POST['prcode'])?trim($_POST['prcode']):"";
	$snstype = !_empty($_POST['snstype'])?trim($_POST['snstype']):"";

	if(strlen($prcode)<=0 || strlen($snstype)<=0	){
		exit;
	}
	
	$inquireSQL = "SELECT * FROM tblproduct WHERE productcode = '".$prcode."' ";

	if(false !== $inquireRes = mysql_query($inquireSQL,get_db_conn())){
		$inquirerowcount = mysql_num_rows($inquireRes);

		if($inquirerowcount>0){
			$inquireRow = mysql_fetch_assoc($inquireRes);

			mysql_free_result($inquireRes);
		}
	}
	
	$fbimagesrc = 'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/product/".$inquireRow['minimage'];
	$fbcontent = strip_tags($inquireRow['content']);
	$fbtitle = strip_tags($inquireRow['productname']);

?>
<!DOCTYPE html>
<html>
	<head>
		<meta property="og:title" content="<?=$fbtitle?>" />
		<meta property="og:description" content="<?=$fbcontent?>" />
		<meta property="og:image" content="<?=$fbimagesrc?>" />
		<script>
			var productUrl = "http://<?=$_data->shopurl?>m/productdetail_tab01.php?productcode=<?=$prcode?>";
			var productName = "<?=$fbtitle?>";
			var imagetUrl = "http://<?=$_SERVER['HTTP_HOST']?>/data/shopimages/product/<?=$inquireRow['minimage']?>";

			function sendSNS(type){
				switch(type){
					case 'FB':
						var href = "http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(productUrl)+ "&t=" + encodeURIComponent(productName);
						var a = window.open(href, 'Facebook', '');
						if (a) {
							a.focus();
						}
					break;
					case 'TW':
						var href = "http://twitter.com/share?text=" + encodeURIComponent(productName) + " " + encodeURIComponent(productUrl);
						var a = window.open(href, 'Twitter', '');
						if (a) {
							a.focus();
						}
					break;
					case 'PI':
						var href = "http://www.pinterest.com/pin/create/button/?url=" + encodeURIComponent(productUrl) + "&media=" + encodeURIComponent(imagetUrl) + "&description=" + encodeURIComponent(productName);
						var a = window.open(href, 'Pinterest', '');
						if (a) {
							a.focus();
						}
					break;
					case 'GO':
						var href = "https://plus.google.com/share?url=" + encodeURIComponent(productUrl);
						var a = window.open(href, 'GooglePlus', '');
						if (a) {
							a.focus();
						}
					break;
				}
			}
		</script>
	</head>
	<body onload="sendSNS('<?=$snstype?>')">
	</body>
</html>