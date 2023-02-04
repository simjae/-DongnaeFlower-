<?
		$Dir="../";
		include_once($Dir."lib/init.php");
		include_once($Dir."lib/lib.php");
		include_once($Dir."lib/shopdata.php");

		$num = trim($_POST['num']);
		$board = trim($_POST['board']);
		$type = trim($_POST['type']);
		//$url = trim($_POST['url']);
		$url = "http://".$_SERVER['HTTP_HOST']."/m/board_view.php?num=".$num."&board=".$board;

		$content_sql = "SELECT * FROM tblboard WHERE board = '".$board."' AND num = ".$num;
		$content_result= mysql_query($content_sql, get_db_conn());
		$content_row = mysql_fetch_object($content_result);
		$view_num = $content_row->num;
		$view_board = $content_row->board;
		$view_title = $content_row->title;
		$view_content = $content_row->content;
		$view_filename = $content_row->filename;
/*
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
*/
		if(strlen($view_filename)>0){
			$imgurl = 'http://'.$_SERVER['HTTP_HOST']."/data/shopimages/".$view_board."/".$view_filename;
		}
		$attechLoc=$Dir."data/shopimages/event/";
		$fbcontent = strip_tags(htmlspecialchars_decode($view_content));
		

		$kakaoinfoSQL = "SELECT state, secret FROM tblshopsnsinfo WHERE type ='k' ";

		$kakaousestate = $kakaousekey = "";
		if(false !== $kakaoinfoRes = mysql_query($kakaoinfoSQL,get_db_conn())){
			$kakaoinfocount = mysql_num_rows($kakaoinfoRes);
			if($kakaoinfocount>0){
				$kakaousestate = trim(mysql_result($kakaoinfoRes,0,0));
				$kakaousekey = trim(mysql_result($kakaoinfoRes,0,1));
			}
		}
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
		<script src="https://developers.kakao.com/sdk/js/kakao.min.js"></script>
		<script type="text/javascript">
		var productUrl = "<?=$url?>";
		var productName = "<?=strip_tags($view_title)?>";
		var imagetUrl = "http://<?=$_SERVER['HTTP_HOST']?>/data/shopimages/<?=$view_board?>/<?=$attech?>";
		var kakaousestate = "<?=$kakaousestate?>";
		var kakaokey = "<?=$kakaousekey?>";

		function goFaceBook() {
			var href = "http://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(productUrl)+ "&t=" + encodeURIComponent(productName);
			var a = window.open(href, 'Facebook', '');
			if (a) {
				a.focus();
			}
		}

		function goTwitter() {
			var href = "http://twitter.com/share?text=" + encodeURIComponent(productName) + " " + encodeURIComponent(productUrl);
			var a = window.open(href, 'Twitter', '');
			if (a) {
				a.focus();
			}
		}

		function goPinterest() {
			var href = "http://www.pinterest.com/pin/create/button/?url=" + encodeURIComponent(productUrl) + "&media=" + encodeURIComponent(imagetUrl) + "&description=" + encodeURIComponent(productName);
			var a = window.open(href, 'Pinterest', '');
			if (a) {
				a.focus();
			}
		}

		function goGooglePlus() {
			var href = "https://plus.google.com/share?url=" + encodeURIComponent(productUrl);
			var a = window.open(href, 'GooglePlus', '');
			if (a) {
				a.focus();
			}
		}

		function goKakaoTalk() {
			if(kakaousestate == "Y" && kakaokey.length > 0){
			    // // 사용할 앱의 JavaScript 키를 설정해 주세요.
			    Kakao.init(kakaokey);
			    // // 카카오링크 버튼을 생성합니다. 처음 한번만 호출하면 됩니다.
			    Kakao.Link.createTalkLinkButton({
			      container: '#kakao-link-btn',
			      webButton: {
			        text: productName,
			        url: productUrl // 앱 설정의 웹 플랫폼에 등록한 도메인의 URL이어야 합니다.
			      }
			    });
			}else{
				alert("카카오 키가 발급이 되어있지 않거나\n사용설정이 되어있지 않습니다.");
				return;
			}
		}
		function goKakaoStory() {
			if(kakaousestate == "Y" && kakaokey.length > 0){
			    // 사용할 앱의 JavaScript 키를 설정해 주세요.
			    Kakao.init(kakaokey);
			    // 스토리 공유 버튼을 생성합니다.
			    Kakao.Story.share({
			    	url: productUrl,
				    text: productName + ' #' + productName + ' :)'
			    });
			}else{
				alert("카카오 키가 발급이 되어있지 않거나\n사용설정이 되어있지 않습니다.");
				return;
			}
		}

		function snsSendCheck(type){
			if(type == "t"){
				goTwitter();
			}
			else if(type == "f"){
				goFaceBook();
			}
			else if(type == "p"){
				goPinterest();
			}
			else if(type == "g"){
				goGooglePlus();
			}
			else if(type == "kt"){
				goKakaoTalk();
			}
			else if(type == "ks"){
				goKakaoStory();
			}
		}
	</script>
	</head>
	<body onload="snsSendCheck('<?=$type?>');">
	</body>
</html>