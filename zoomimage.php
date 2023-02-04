<?
	$url = $_GET['iurl'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Cache-Control" content="no-cache" />
		<meta name="viewport" content="width=device-width" />
		<link rel="stylesheet" href="./css/skin/default.css" />
		<link rel="stylesheet" href="./css/common.css" />
		<style>
			body{margin:0px;padding:0px;border:0px;}
			img{width:100%;}
			.basic_button{padding:0em 0.6em}
		</style>
	</head>
	<body>
		<img src="<?=$url?>" />

		<div style="text-align:center"><button onClick="self.close();" class="basic_button">닫기</button></div>
	</body>
</html>