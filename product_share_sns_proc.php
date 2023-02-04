<?
		$Dir="../";
		include_once($Dir."lib/init.php");
		include_once($Dir."lib/lib.php");
		include_once($Dir."lib/shopdata.php");

		$num = $_POST['num'];
		$board = $_POST['board'];
		$type = $_POST['type'];

		$content_sql= "SELECT * FROM tblboard WHERE board = '".$board."' AND num = ".$num;
		
		if(false !== $contentRes = mysql_query($content_sql,get_db_conn())){
			$contentsrowcount = mysql_num_rows($contentRes);

			if($contentsrowcount>0){
				$content_row = mysql_fetch_object($content_result);
					$view_num = $content_row->num;
					$view_board = $content_row->board;
					$view_title = $content_row->title;
					$view_content = $content_row->content;
					$view_filename = $content_row->filename;
			}
		}else{
			exit;
		}
		if(strlen($view_filename)>0){
			$imgurl = 'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/".$view_board."/".$view_filename;
		}
		$attechLoc=$Dir."data/shopimages/event/";
		$fbcontent = strip_tags(htmlspecialchars_decode($view_content));
		

?>
<!DOCTYPE html>
<html>
	<head>
		<?
			if($type == "f"){
		?>
		<meta property="og:title" content="<?=$view_title?>" />
		<meta property="og:description" content="<?=$fbcontent?>" />
		<?if(strlen($view_filename)>0){?>
		<meta property="og:image" content="<?=$imgurl?>" />
		<?
				}
			}
		?>
		<script type="text/javascript">
		var productUrl = "http://<?=$_data->shopurl?>m/board_view.php?num=<?=$view_num?>&board=<?=$view_board?>";
		var productName = "<?=strip_tags($view_title)?>";
		var imagetUrl = "http://<?=$_SERVER['HTTP_HOST']?>/data/shopimages/<?=$view_board?>/<?=$attech?>";
		function goFaceBook()
		{
			var href = "http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(productUrl)+ "&t=" + encodeURIComponent(productName);
			var a = window.open(href, 'Facebook', '');
			if (a) {
				a.focus();
			}
		}

		function goTwitter()
		{
			var href = "http://twitter.com/share?text=" + encodeURIComponent(productName) + " " + encodeURIComponent(productUrl);
			var a = window.open(href, 'Twitter', '');
			if (a) {
				a.focus();
			}
		}

		function snsSendCheck(type){

				if(type =="t"){
					goTwitter();
				}else if(type =="f"){
					goFaceBook();
				}

		}
	</script>
	</head>
	<body onload="snsSendCheck('<?=$type?>');">
	</body>
</html>