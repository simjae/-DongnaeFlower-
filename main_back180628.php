<?	include_once('header.php'); 
	include_once($Dir."lib/mobile_eventpopup.php");?>

<!-- 내용 -->
<div id="content">
	<section id="banner_wrap">
		<div class="banners">
			<div id="banner_list" class="banner_list">
<?
				$bannerSQL = "SELECT image,url,target FROM tblmobilebanner ORDER BY date DESC LIMIT 5";
				$rowcount=0;
				if(false !== $bannerRes = mysql_query($bannerSQL,get_db_conn())){
					$rowcount = mysql_num_rows($bannerRes);
					if($rowcount>0){
						while($bannerRow = mysql_fetch_assoc($bannerRes)){
?>
				<div><a href="<?=$bannerRow['url']?>" target="<?=$bannerRow['target']?>"><img src="<?=$configPATH.$bannerRow['image']?>" class="img_small" /></a></div>
<?
						}
					}else{
?>
				<div><img src="<?=$configPATH?>@main_banner.png" alt="배너를 등록하세요~" /></div>
<?
					}
				}
?>
			</div>
			<nav id="banner_navi">
				<? for($navicount=0;$navicount < $rowcount;$navicount++){ ?>
				<img class="naviImg<?=$navicount?>" src="/m/images/poff.png" alt="" />
				<? } ?>
			</nav>
		</div>
	</section>

<?
	switch($mainsort){
		case "1" :
			include $mobilePATH."main_display_product.php";
			include $mobilePATH."main_pavorite_menu.php";
			include $mobilePATH."main_notice_list.php";
		break;
		case "2" :
			include $mobilePATH."main_display_product.php";
			include $mobilePATH."main_notice_list.php";
			include $mobilePATH."main_pavorite_menu.php";
		break;
		case "3" :
			include $mobilePATH."main_pavorite_menu.php";
			include $mobilePATH."main_display_product.php";
			include $mobilePATH."main_notice_list.php";
		break;
		case "4" :
			include $mobilePATH."main_pavorite_menu.php";
			include $mobilePATH."main_notice_list.php";
			include $mobilePATH."main_display_product.php";
		break;
		case "5" :
			include $mobilePATH."main_notice_list.php";
			include $mobilePATH."main_display_product.php";
			include $mobilePATH."main_pavorite_menu.php";
		break;
		case "6" :
			include $mobilePATH."main_notice_list.php";
			include $mobilePATH."main_pavorite_menu.php";
			include $mobilePATH."main_display_product.php";
		break;
		default :
			include $mobilePATH."main_planning_product.php";
			include $mobilePATH."main_direct_menu.php";
			include $mobilePATH."main_notice.php";
	}
?>
	</div>

	<script type="text/javascript" src="./js/jquery.touchSwipe-1.2.5.js"></script>
	<script type="text/javascript" src="./js/jquery.baramangSwipe-1.0.js"></script>
	<script type="text/javascript" src="./js/banner.js"></script>

	<script type="text/javascript">
		var bannerImages = BaramangSwipe.mainBanner("#banner_list", "#banner_navi",3000);
		bannerImages.load().bannerNavigator();
	</script>

	<? include_once('footer.php'); ?>
	<?php echo $onload ?>
	<? include_once($Dir."lib/mobile_eventlayer.php") ?>