<?
	$sql = "INSERT INTO tblvenderstorevisittmp VALUES ('".$vidx."','".date("Ymd")."','".getenv("REMOTE_ADDR")."') ";
	mysql_query($sql,get_db_conn());
	if (mysql_errno()!=1062) {
		$sql = "INSERT INTO tblvenderstorevisit VALUES ('".$vidx."','".date("Ymd")."','1') ";
		mysql_query($sql,get_db_conn());
		if (mysql_errno()==1062) {
			$sql = "UPDATE tblvenderstorevisit SET cnt=cnt+1 WHERE vender='".$vidx."' AND date='".date("Ymd")."' ";
			mysql_query($sql,get_db_conn());
		}
		$sql = "UPDATE tblvenderstorecount SET count_total=count_total+1 ";
		$sql.= "WHERE vender='".$vidx."' ";
		mysql_query($sql,get_db_conn());
	}
	//vidx가 파라미터값 던진값
	//꽃집에는 이미 vidx value를 가지고있고 클릭시에 파라미터값으로 value를 던져줌 
	//echo는 묻지도 따지지도않고 클라이언트 화면에 그냥 출력됨, 그러므로 다시 admin에서 sql 확인요망
	$curpage = isset($_GET['page'])?trim($_GET['page']):"1";
	
	if((strlen($vidx) <= 0) || $vidx == "0"){
		echo '<script>alert(\"필수값이 누락되어 페이지를 열람하실 수 없습니다.\");history.go(-1);</script>';exit;
	}

	$infoSQL = "SELECT 
					0 AS prcount
					, vs.brand_name
					, v.com_owner
					, v.com_image
					, v.id
					, v.com_addr_pointx
					, v.com_addr_pointy
					, vs.brand_description
					, v.com_addr
					, vs.cust_info
					, IFNULL(
						(SELECT 
							ROUND(AVG(marks),1)
						FROM 
							tblproductreview AS tpr
						LEFT JOIN
							tblproduct AS tp
						ON
							tpr.productcode = tp.productcode
						WHERE
							tp.vender = v.vender
						GROUP BY tp.vender)
						,0) AS avg_marks
					, IFNULL(
						(SELECT 
							count(tp.vender)
						FROM 
							tblproductreview AS tpr
						LEFT JOIN
							tblproduct AS tp
						ON
							tpr.productcode = tp.productcode
						WHERE
							tp.vender = v.vender
						GROUP BY tp.vender)
						,0) AS marks_count 
					, (	SELECT 
							COUNT(frequenter_idx)
						FROM 
							tblfrequenter 
						WHERE 
							vender = v.vender 
							AND member_id = '".$_ShopInfo->getMemid()."') AS favo_cnt 
					, vs.vender_sns_naver_blog
					, vs.vender_sns_instagram
					, vs.vender_sns_facebook
					, vs.vender_sns_kakao
					, vs.vender_sns_youtube
					, vs.minishop_holiday  ";
	$infoSQL .= "FROM 
					tblvenderinfo AS v ";
	$infoSQL .= "LEFT JOIN tblvenderstore AS vs ON v.vender = vs.vender ";
	$infoSQL .= "WHERE v.vender = '".$vidx."' ";
	$imgsrc = $Dir."data/shopimages/vender/";
	
	if(false !== $infoRes = mysql_query($infoSQL,get_db_conn())){
		$infoNumRows = mysql_num_rows($infoRes);
		if($infoNumRows > 0){
			$prcount = mysql_result($infoRes,0,0);
			$brand_name = mysql_result($infoRes,0,1);
			$corprep = mysql_result($infoRes,0,2);
			$imagerep = mysql_result($infoRes,0,3);
			$corpid = mysql_result($infoRes,0,4);
			$com_addr_pointx = mysql_result($infoRes,0,5);
			$com_addr_pointy = mysql_result($infoRes,0,6);
			$brand_description = mysql_result($infoRes,0,7);
			$com_addr = mysql_result($infoRes,0,8);
			$cust_info = mysql_result($infoRes,0,9);
			$avg_marks = mysql_result($infoRes,0,10);
			$marks_count = number_format(mysql_result($infoRes,0,11));
			$favo_cnt = mysql_result($infoRes,0,12);
			$favoBtnClass = $favo_cnt>0?"favSel":"";
			$vender_sns_naver_blog = mysql_result($infoRes,0,13);
			$vender_sns_instagram = mysql_result($infoRes,0,14);
			$vender_sns_facebook = mysql_result($infoRes,0,15);
			$vender_sns_kakao = mysql_result($infoRes,0,16);
			$vender_sns_youtube = mysql_result($infoRes,0,17);
			$minishop_holiday = mysql_result($infoRes,0,18);
			
		}else{
			echo '<script>alert(\"등록된 입점사가 아닙니다.\");history.go(-1);</script>';exit;
		}
		
		mysql_free_result($infoRes);
	}else{
		echo '<script>alert(\"연결이 지연되었습니다.\n 잠시 후 다시 시도 해 주시기 바랍니다.\");history.go(-1);</script>';exit;
	}
?>

<style>
	.banner h2{text-align: center;margin: 25px;color:black;}
	.bannerImg{height:300px;overflow: hidden;}
	.alarmpop{
		background-color:#DC2872; 
		padding:20px 0 20px 0;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	.alarmpop .alarmTitle{font-size:0.9rem;color:white;font-weight: 500;}
	.alarmContent{font-size:0.9rem;color:#000000;display:none;background:#f3f3f3;height:fit-content;}
	.alarmContent .contentText{padding: 20px;line-height: 1.1rem;background-color: #ffeeee;color: #464646;}
	.alarmpop img{margin: 0 11px 0 20px; width: 16px;}
	.shop{color:black; font-weight: bold; margin: 28px 20px 24px 20px; display: flex;justify-content: space-between;line-height: 1.5em;}
	.shop_title{font-size: 1.7em;}
	.shop_content{font-size:1.4em;}
	.count1{font-weight:900; font-size:1.2em; color:#282828;}
	.count2{font-weight:400; font-size:1.2em; color:#282828;}
	
	.review{background-color:#ffeeee; padding: 15px 5px;}

	.review .review_circle {
		display: flex; 
		text-align:center;
		font-size: 0.8em;
		color: #000000;
		font-weight:400;}
	.review .review_circle img{width: 40px;}
	.review .review_circle .badge{margin: 5px;}
	.review .noneBadge{
		font-size: 1.2em;
		font-weight: 400;
		text-align: center;
		width: 100%;
		color: #666666;
	}
	.titleButton {margin:19px 19px 15px 19px;overflow: hidden;}
	.titleButton .brandName {
		font-weight:700;
		color:#282828;
		float:left;
		font-size: 1.3em;
		line-height: 1.5em;}
	.titleButton .btnWrap {float:right;}
	.titleButton .btnWrap .shopBtn{
		margin-left: 5px;
		display:table; 
		float:left;
		color: #E61E6E;
		border-radius: 20px;
		border:solid #E61E6E 1px;
		position: relative;
		box-shadow: 3px 3px 5px 0px RGBA(0,0,0,0.05);
		font-size: 0.8em;
		padding: 3px 12px;
		font-weight:500;
	}
	.titleButton .btnWrap .shopBtn::after{
		content: attr(value);
	}
	.titleButton .btnWrap .shopBtn.favSel{
		color: #ffffff;
		border: solid #ec2b80 1px;
		background: #ec2b80;
	}
	.shop_main{margin:15px 19px; color:#282828; overflow: hidden;}
	.shop_main_wrap{margin-bottom:20px;font-size: 1.2em; font-weight:400; color:#282828;}
	.shop_main_wrap .contentWrap {overflow:hidden;margin-bottom:10px;width:100%;}
	.shop_main_wrap .contentWrap .main_title {width: 17%;float:left;}
	.shop_main_wrap .contentWrap .main_content{float:left;width: 83%;}
	.shop_main_wrap .contentWrap .main_content p{font-size: 1.0em; font-weight:400;}
	.shop_main .venderMap{border: solid 1px #9e9e9e36; border-radius: 10px; width: calc(100% - 66px);height: 200px;margin-left:64px;}
	.sns_icon{display:inline-flex; margin-left:64px;margin-top:10px;}
	.sns_icon .icon{margin: 3px;}
	.sns_icon .icon img{width: 25px;}
	
	.shopIntro .shop_main .shopEdit{margin: 20px 0 0 0;}
	.shopIntro .shop_main .shopEdit h3{color:#282828;font-size: 1.3em;}
	.shopIntro .shop_main .shopEdit .content{width: 100%; height:auto;font-size: 1.2em; line-height: 140%; margin: 20px 0 0 0;}
	.shopIntro .shop_main .shopEdit .content img{width: 100%;height:auto;border-radius: 10px;}
	.shopReview{background-color:#ffeeee; height:70px;display: flex; justify-content: space-between; color:#000000;}
	.shopReview div{margin:27px 25px;}
	.shopReview .reviewText {position: relative;right: 8px;}
	.shopReview .reviewText span{ font-size: 1.3em; margin-right: 5px;}
	.member{margin:0 15px 0 15px;}
	.memberWrap{display: flex;margin: 0 0 20px 5px;}
	.memberTitle{margin:5px 0 0 10px; color: #282828;}
	.member{margin:20px 15px 20px 15px;}
	.member .memberWrap .circle{overflow:hidden;width:40px;height:40px;border-radius:100%;background-color: white; box-shadow: 2px 1px 6px #77777742}
	.member .memberWrap .circle img{width:40px;}
	.member .memberWrap .memberTitle .starWrap {margin-top:5px;}
	.member .memberWrap .memberTitle .starWrap .memberStar{width:14px;margin-right:2px;}
	.memberImg{width: 100%;margin-bottom:40px; }
	.memberImg img{width: 100%;height:auto;border-radius: 10px;}

</style>
<div class="shopIntro">
	<?if($pagetype!="include"){?>
		<div class="h_area2">
			<h2>꽃집소개</h2>
			<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
			<?
				if($pagetype!="pop"){
			?>
					<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
			<?
				}
			?>
		</div>
	<?
		$imageSQL="SELECT cont FROM vender_multicontents AS pm WHERE pm.vender ='".$vidx."' ORDER BY midx";
		$imageRes = mysql_query($imageSQL , get_db_conn());
		$imageCnt = mysql_num_rows($imageRes);
		if( $imageCnt > 0 ){
	?>
			<div class="bannerImg">
				<div class="swiper-wrapper">
					<?
					while($imageRow = mysql_fetch_object($imageRes)){
						$background_url = "/data/shopimages/vender/".$imageRow->cont;
						echo "<div class='swiper-slide' style=\"width:100%;height:auto;min-height:100px;border:none;padding:0px;background:url('".$background_url."') no-repeat;background-position:center;background-size:cover;\"></div>\n";
					}
					?>
				</div>
				<div class="swiperPageWrap" style="padding: 8px 20px;margin: -9% auto 0 auto;">
					<div class="swiper-page-num" style="font-weight:500;"></div>
				</div>
			</div>
	<?
		}
	?>
		<div class="alarmpop">
			<?
			$noticSQL = "SELECT content, subject "; 
			$noticSQL .= "FROM tblvendernotice ";
			$noticSQL .= "WHERE vender = $vidx ";
			
			if(false !== $noticeRes = mysql_query($noticSQL,get_db_conn())){
				$noticeNumRows = mysql_num_rows($noticeRes);
				$subject = "등록된 공지사항이 없습니다.";
				if($noticeNumRows > 0){
					$content = mysql_result($noticeRes,0,0);
					$subject = mysql_result($noticeRes,0,1);
				}
				
				mysql_free_result($infoRes);
			}
			?>
			<div class="alarmTitle" onclick="noticeContentEvent()"><img src="/app/skin/basic/svg/venderinfo_point.svg" alt="point"><?=$subject?></div>
		</div>
		<div class="alarmContent">
			<div class="contentText">
				<?=$content?>
			</div>
		</div>
<?
	}
?>
	<div class="shop">
		<div class="shop_title"><?=$brand_name?></div> 
		<div class="shop_content">
				<img src="/app/skin/basic/svg/review_star_on.svg" style="margin-right: 5px; height:16px;" alt="star"> 
				<span class="count1"><?=$avg_marks?></span>
				<span class="count2">(<?=$marks_count?>)</span>
		</div>
	</div>
	<div class="review">
		<div class="review_circle">
			<?
				$badgeSql="SELECT 
								COUNT(IF(tpr.badge01='Y', 1, null)) AS badge01
								,COUNT(IF(tpr.badge02='Y', 1, null)) AS badge02
								,COUNT(IF(tpr.badge03='Y', 1, null)) AS badge03
								,COUNT(IF(tpr.badge04='Y', 1, null)) AS badge04
								,COUNT(IF(tpr.badge05='Y', 1, null)) AS badge05
								,COUNT(IF(tpr.badge06='Y', 1, null)) AS badge06
								,COUNT(IF(tpr.badge07='Y', 1, null)) AS badge07
								,COUNT(IF(tpr.badge08='Y', 1, null)) AS badge08
							FROM 
								tblproductreview AS tpr
							LEFT JOIN
								tblproduct AS tp
							ON
								tpr.productcode = tp.productcode
							WHERE
								tp.vender = '".$vidx."'
							GROUP BY tp.vender";
				$badge_result = mysql_query($badgeSql,get_db_conn());
				$row = mysql_fetch_object($badge_result);
				$badgeCountArr = array($row->badge01,$row->badge02,$row->badge03,$row->badge04,$row->badge05,$row->badge06,$row->badge07,$row->badge08);
				$badgeTextArr = array("가성비갑","고급져요","장인정신","빨리와요","친절해요","센스쟁이","신선해요","풍성해요");
				$badgeCount = 0;
				for($i = 0 ; $i < 8 ; $i++){
					if($badgeCountArr[$i] > 10){
			?>
						<div class="badge">
							<img src="/app/skin/basic/svg/badge0<?=$i+1?>.svg" alt="<?=$badgeTextArr[$i]?>">
							<br><?=$badgeTextArr[$i]?>
						</div>
			<?
						$badgeCount++;
					}
				}
			?>
		</div>
		<?
			if(!$badgeCount){
				echo '<div class="noneBadge"><img src="/app/skin/basic/svg/badge_none.svg" style="height:40px;"></div>';
			}
		?>
	</div>
	<div class="titleButton">
		<div class="brandName"><?=$brand_name?></div>
		<?if(!$vender){?>
			<div class="btnWrap">
				<div class="shopBtn <?=$favoBtnClass?>" value="찜하기" onclick="toggleBookmarkEvent(this);">
				</div>
				<?
				$tvRequestScript = "targetVenderRequestOpen('".$vidx."','".$brand_name."')";
				if($pagetype=="pop"){
					$tvRequestScript = "parent.targetVenderRequestOpen('".$vidx."','".$brand_name."')";
				}
				?>
				<div class="shopBtn" value="단골 주문하기" onclick="<?=$tvRequestScript?>">
				</div>
			</div>
		<?}?>
	</div>
	<div class="shop_main">
		<div class="shop_main_wrap">
			<div class="contentWrap">
				<div class="main_title">
					<h4 style="font-size: 0.9em;font-weight: 500;margin-top: 2px;">운영시간</h4>
				</div>
				<div class="main_content">
					<?
						$temp=explode("=", $cust_info);
						$closed = "";
						for ($i=0; $i<= count($temp);$i++) {
							if (substr($temp[$i],0,6)=="TIME1="){
								$time1 = substr($temp[$i],6);
								if($time1 == 0){
									$closed.= "평일 휴무<br>";
								}
							}		
							else if (substr($temp[$i],0,6)=="TIME2="){
								$time2 =substr($temp[$i],6);
								if($time2 == 0){
									$closed.= "토요일 휴무<br>";
								}
							}	
							else if (substr($temp[$i],0,6)=="TIME3="){
								$time3 =substr($temp[$i],6);
								if($time3 == 0){
									$closed.= "일요일 휴무<br>";
								}
							}
						}
						$closed.= $minishop_holiday;

						
					?>
					<?if($time1 != 0){?>
						<p>평일 <span><?=$time1?></span></p>
					<?}
					if($time2 != 0){?>
						<p>토요일 <span><?=$time2?></span></p>
					<?}
					if($time3 != 0){?>
						<p>일요일 <span><?=$time3?></span></p>
					<?}?>
				</div>
			</div>
			<div class="contentWrap">
				<div class="main_title">
					<h4 style="font-size: 0.9em;font-weight: 500;margin-top: 2px;">휴무</h4>
				</div>
				<div class="main_content">
					<p><span><?=$closed?></span></p>
				</div>
			</div>
			<div class="contentWrap" style="display:flex">
				<div class="main_title">
					<h4 style="font-size: 0.9em;font-weight: 500;">주소</h4>
				</div>
				<div class="main_content" style="line-height: 1.3em;">
					<?=$com_addr?>
				</div>
			</div>
		</div>
		
		<div id="venderMap" class="venderMap"></div>
		<div class="sns_icon">
			<?if($vender_sns_facebook){?>
				<a href="https://<?=str_replace("https://","",$vender_sns_facebook)?>" target="_blank">
					<div class="icon"><img src="/app/skin/basic/svg/venderinfo_facebook.svg" alt="facebook"></div>
				</a>
			<?}?>
			<?if($vender_sns_instagram){?>
				<a href="https://<?=str_replace("https://","",$vender_sns_instagram)?>" target="_blank">
					<div class="icon"><img src="/app/skin/basic/svg/venderinfo_instagram.svg" alt="instagram"></div>
				</a>
			<?}?>
			<?if($vender_sns_naver_blog){?>
				<a href="https://<?=str_replace("https://","",$vender_sns_naver_blog)?>" target="_blank">
					<div class="icon"><img src="/app/skin/basic/svg/venderinfo_naver.svg" alt="naver"></div>
				</a>
			<?}?>
			<?if($vender_sns_kakao){?>
				<a href="https://<?=str_replace("https://","",$vender_sns_kakao)?>" target="_blank">
					<div class="icon"><img src="/app/skin/basic/svg/venderinfo_kakao.svg" alt="kakao"></div>
				</a>
			<?}?>
			<?if($vender_sns_youtube){?>
				<a href="https://<?=str_replace("https://","",$vender_sns_youtube)?>" target="_blank">
					<div class="icon"><img src="/app/skin/basic/svg/venderinfo_youtube.svg" alt="youtube"></div>
				</a>
			<?}?>
		</div>
		<div class="shopEdit">
			<h3>꽃집 소개</h3>
			<div class="content"><?=$brand_description?></div>
		</div>
		
	</div>
	<div class="shopReview">
		<div class="reviewText">
			<span style="font-weight:bold;">꽃집 리뷰</span>
		</div>
		<div class="reviewCount">
			<img style="width: 15px;margin-right: 5px;" src="/app/skin/basic/svg/review_star_on.svg"alt="star"> 
			<span class="count1"><?=$avg_marks?></span>
			<span class="count2">(<?=$marks_count?>)</span>
		</div>
	</div>
	<div class="member">
	<?

		$reviewSQL = "SELECT tpr.name,tpr.id, tpr.content, tpr.marks , tpr.img  , mb.profile_photo ";
		$reviewSQL .= "FROM tblproductreview AS tpr ";
		$reviewSQL .= "LEFT JOIN tblproduct AS tp ON tpr.productcode = tp.productcode ";
		$reviewSQL .= "LEFT JOIN tblvenderstore AS tvs ON tp.vender = tvs.vender ";
		$reviewSQL .= "LEFT JOIN tblmember AS mb ON tpr.id = mb.id ";
		$reviewSQL .= "WHERE tp.vender = '".$vidx."' ";
		$reviewSQL .= "ORDER BY tpr.date DESC LIMIT 5";
		$reviewResult = mysql_query($reviewSQL,get_db_conn());
		while($reviewRow = mysql_fetch_object($reviewResult)){
			$imageUrl="/data/shopimages/productreview/".$reviewRow->img;
			$profileImage = $reviewRow->profile_photo;
			$reviewcontent = explode("=",$reviewRow->content);
			if($profileImage == ""){
				$profileImage = "/images/no_img.gif";
			}
			else{
				$profileImage = "/data/profilephoto/".$profileImage;
			}
			$reviewName = mb_substr($reviewRow->name, 0, 1,"UTF-8")."*".mb_substr($reviewRow->name, 2,10,"UTF-8");
		?>
			<div class="memberWrap">
				<div class="circle"><img src="<?=$profileImage?>" alt="icon"></div>
				<div class="memberTitle">
					<h3><?=$reviewName?></h3>
					<div class="starWrap">
						<?for($i = 0 ; $i < 5 ; $i++){
							$reviewStarImg = "review_star_off";
							if($i < $reviewRow->marks){
								$reviewStarImg = "review_star_on";
							}
							echo "<img class=\"memberStar\" src=\"/app/skin/basic/svg/".$reviewStarImg.".svg\">";
						}?>		
						<span style="color:#969696; font-weight:500;">이번 주</span>
					</div>
				</div>
			</div>
			<div class="memberImg" >
				<?if($reviewRow->img != ""){?>
					<img src="<?=$imageUrl?>" alt="reviewImg">
				<?}?>
				<p style="margin-top: 10px;color:#464646; font-size:1.2em;"><?=$reviewcontent[0]?></p>
			</div>
		<?
		}
		?>
	</div>
</div>
<form name="venderForm" method="post">
	<input type="hidden" name="vender" value="<?=$vidx?>">
	<input type="hidden" name="member_id" value="<?=$_ShopInfo->getMemid()?>">
</form>
<script type="text/javascript">
	var settings = {
						loop: true,
						pagination: {
							el: '.swiper-page-num',
							type: 'fraction'
						},
						autoplay: {
							delay: 5000,
							disableOnInteraction: false,
						}
					}
	$(document).ready(function() {
		<?if( $imageCnt > 0 ){?>
			var swiperObj = new Swiper('.bannerImg', settings);
		<?}?>
		initVenderMap();
	});

	function initVenderMap() {
		var pointx = "<?=$com_addr_pointx?>"
		var pointy = "<?=$com_addr_pointy?>"
		map = new naver.maps.Map('venderMap', {
						center: new naver.maps.LatLng(pointy, pointx),
						zoom: 16,
						logoControl: false
					});	
		// do Something
	

		marker = new naver.maps.Marker({
			position: new naver.maps.LatLng(pointy, pointx),
			zIndex: 100,
			icon: {
				content: [
							'<div class="venderMapGroup" value="0">',
								'<i class="map marker alternate icon"></i>',
								'<span class="shd">',
									'<?=$brand_name?>',
								'</span>',
							'</div>'
						].join('')
			}
		});
		marker.setMap(map);
	}
	
	function menuSelecter(){
		initMap();
		$("#timesale_map").show();
		$("#mapBtn").addClass("selected");
	}

	function toggleBookmarkEvent(obj) {	
		var formData = $("form[name=venderForm]").serialize() ;
		$.ajax({
			url: "/api/vender_favorite.php",
			type: "post",
			data: formData,
			dataType : 'json',
			error: function(xhr, status, error){
				alert("찜하기 처리중에 오류가 발생했습니다.");
			},
			success : function(data){
				if(data["proc"]=="del"){
					alert("단골꽃집에서 삭제되었습니다.");
					$(obj).removeClass("favSel");
				}
				else if(data["proc"]=="ins"){
					alert("단골꽃집으로 등록되었습니다.");
					$(obj).addClass("favSel");
				}
			}
		})
	}
	
	function noticeContentEvent(){
		if(!$(".alarmContent").is(':visible')){
			$(".alarmContent").slideDown(200);
		}
		else{
			$(".alarmContent").slideUp(200);
		}
	}

</script>
