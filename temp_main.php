<?
	$main_check=1;

	include_once('temp_header.php'); 
	include_once($Dir."lib/mobile_eventpopup.php");
	
	
?>

	<a href="./" rel="external">
		<div class="logo" <? if(file_exists($logo)==true) { echo "style=\"background:url('".$logo."') no-repeat;background-size:auto 100%;background-position:center;\""; } ?>></div>
	</a>
		<div style="position:absolute;top:5%;left:0px;width:100%;text-align:right;right:1%;"><a href="#" class="close_modal" style="color:#fff;font-size:2.5em;font-weight:lighter;padding-right:5%;">×</a></div>
		<div style="display:table;width:100%;height:100%;">
			<div style="display:table-cell;font-size:0px;text-align:center;vertical-align:middle;">
				<form name="tprSearchForm" action="productsearch.php" method="get">
				<input type="hidden" name="mode" value="search" />
				<input type="hidden" name="terms" value="productname" />
				<input type="text" name="prsearch" value="" placeholder="찾으시는 꽃을 입력해주세요 - 임시 php파일생성후 참조 경로만 변경했습니다. 원복필요하시다면 연락주세요" style="font-size:0.9rem;height:40px;width:80%;padding:0px 5px;border:0px solid rgba(255,255,255,0.5);box-sizing:border-box;color:#b0afaf;border-bottom:1px solid #b0afaf;" />
				</form>
			</div>
	</div>

<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		.bottom_menu { position: fixed; bottom: 0px; left: 0px; width: 100%; height: 50px; z-index:100; border-top: 1px solid black; background-color: white }
		.bottom_menu > div { float: left; width: 16.6%; height: 100%; text-align: center; padding-top: 13px; }
	</style>
	<script>
		var gbBottomMenuVisible = true;
	</script>
</head>
<body onscroll="body_scroll()">
	<div class="bottom_menu">
		<div>
			<img src="1.png">
		</div>
		<div>
			<img src="2.png">
		</div>
		<div>
			<img src="3.png">
		</div>
		<div>
			<img src="4.png">
		</div>
		<div>
			<img src="5.png">
		</div>
		<div>
			<img src="6.png">
		</div>
	</div>
</body>
</html>