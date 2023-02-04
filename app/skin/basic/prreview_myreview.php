<?
$Dir="../";
include_once($Dir."lib/init.php");
// include_once($Dir."lib/init.debug.php");
//이게 디버그확인해준는 php
include_once($Dir."lib/lib.php");

if (strlen($_ShopInfo->getMemid()) == 0) {
	echo "<html><head><title></title></head><body onload=\"alert('회원 아이디가 존재하지 않습니다.');\"></body></html>";
	exit;
	Header("Location:" . $Dir . FrontDir . "login.php?chUrl=" . getUrl());
	exit;
}
$type = $_POST['type'];
$idx = $_POST['idx'];
$vidx = isset($_GET['vidx'])?trim($_GET['vidx']):"";
$productSQL ="SELECT 
					TP.* 
					,TVS.brand_name
					,TVI.com_addr
					, IFNULL(
						(SELECT 
							ROUND(AVG(marks),1)
						FROM 
							tblproductreview AS TPR
						f_LEFT JOIN
							tblproduct AS TP1
						ON
							TPR.productcode = TP1.productcode
						WHERE
							TP1.vender = TP.vender
						GROUP BY TP1.vender)
						,0) AS avg_marks
					, IFNULL(
						(SELECT 
							count(TP2.vender)
						FROM 
							tblproductreview AS TPR
						f_LEFT JOIN
							tblproduct AS TP2
						ON
							TPR.productcode = TP2.productcode
						WHERE
							TP2.vender = TP.vender
						GROUP BY TP2.vender)
						,0) AS marks_count
				FROM 
					tblproduct AS TP
				f_LEFT JOIN
					tblvenderstore AS TVS
				ON
					TP.vender = TVS.vender
				f_LEFT JOIN
					tblvenderinfo AS TVI
				ON
					TP.vender = TVI.vender
				WHERE 
					productcode = '".$productcode."' ";
	$imagesrc = $Dir."data/shopimages/product/";
	if(false !== $productRes = mysql_query($productSQL,get_db_conn())){
		$productrowcount = mysql_num_rows($productRes);

		if($productrowcount>0){
			$productRow = mysql_fetch_assoc($productRes);
			$productname = $productRow['productname'];
			$productimage =$productRow['minimage'];
			$productprice = number_format($productRow['sellprice']);
			$brand_name =$productRow['brand_name'];
			$com_addr =$productRow['com_addr'];
			$avg_marks =$productRow['avg_marks'];
			$marks_count = number_format($productRow['marks_count']);
			$src = $imagesrc.$productimage;
			$size = _getImageRateSize($src,80);
		}
	}
	
	?>
<style>
.myreview_wrap{
	overflow:hidden;
}
.myreview_wrap img{
	width: 60px;
	border-radius: 50px;
	float: left;
}
.myreview_wrap .triangle-isosceles {
	max-width:70%;
	width:fit-content;
	position:relative;
	padding:15px;
	margin:8px 0 8px;
	color:#372e2b;
	font-weight: 500;
	background:#f4f4f4; /* default background for browsers without gradient support */
	/* css3 */
	border-radius:6px;

	word-break:break-all;
	word-wrap:break-word;
}

/* creates triangle */
.myreview_wrap .triangle-isosceles:after {
	content:"";
	position:absolute;
	bottom:-15px; /* value = - border-top-width - border-bottom-width */
	left:8px; /* controls horizontal position */
	border-style:solid;
	/* reduce the damage in FF3.0 */
	display:block;
	width:0;
}
/* creates triangle */
.myreview_wrap .triangle-isosceles:before {
	content:"";
	position:absolute;
	bottom:-15px; /* value = - border-top-width - border-bottom-width */
	left:8px; /* controls horizontal position */
	border-style:solid;

	/* reduce the damage in FF3.0 */
	display:block;
	width:0;
}

/* Variant : for left/right positioned triangle
------------------------------------------ */

.myreview_wrap .triangle-isosceles.left {
	float:left;
	margin-left:8px;
	background:#F9D2D9;
}


.myreview_wrap .triangle-isosceles.left:after {
	top:8px; /* controls vertical position */
	left:-7px; /* value = - border-left-width - border-right-width */
	bottom:auto;
	border-width:8px 8px 8px 0;
	border-color:transparent #F9D2D9;
}
.myreview_wrap .triangle-isosceles.left:before {
	top:8px; /* controls vertical position */
	left:-7px; /* value = - border-left-width - border-right-width */
	bottom:auto;
	border-width:8px 8px 8px 0;
	border-color:transparent #ffffff;
}
.pick{
	overflow: hidden;
}	
.pickbanner{
	width: fit-content;
	font-weight: 600;
	font-size: 0.9em;
	color: #e9387a;
	margin: 5px 7px 5px 0;
	padding: 5px 9px 5px 9px;
	border-radius: 15px;
	line-height: 0.9em;
	box-shadow: 1px 1px 4px #ffe0e3;
	background-color: #ffe0e3;
	float: left;
}
.f_left{
	float: left;
}
.f_right{
	float: right;
}
.h_area2 h2 {
    display: block;
    background: #ffffff;
    text-align: center;
    font-size: 1.7em;
    padding: 8px 12px 12px 12px;
    color: #000000;
    font-weight: 600;
}
.prreview_mypage .contentWrap{
	overflow: hidden;
	margin: 0px auto;
    padding: 0px 15px;
    border: 0px;
    font-size: 14px;
    background: #ffffff;
    overflow: hidden;
	border-bottom: solid 1px;

}
.reviewListWrap{
	margin: 20px;
}
.listGroup{
	margin: 10px 0 10px 0;
}
.prreview_mypage .myreviewCount{
	overflow: hidden;
	padding: 20px 10px 20px 20px;
	border-bottom: solid 1px #9e9e9e66;
}
.prreview_mypage .myreviewCount .documentIcon {
	margin:5px 15px 0 13px;
    width: 18px;
}
.prreview_mypage .myreviewCount .dateText {
	margin-top: 5px;
    margin-left: 18px;
    font-size: 1.2em;
    color: #000000;
    line-height: 1.7em;
}
.reviewWrap{margin: 20px 20px 35px 20px;}
.reviewTitle span{font-size: 15px; font-weight: bold;}
.reviewTitle .reviewDelete{
	width: fit-content;
	font-weight: 600;
	font-size: 0.9em;
	color: #717071;
	margin: 0 7px 0 0;
	padding: 5px;
	border-radius: 15px;
	line-height: 0.9em;
	box-shadow: 1px 1px 4px #888888;
}
.starWrap{margin:10px 0 10px 0;}
.starWrap .reviewStar{width: 15px; margin-right: 3px;}
.reviewImg img{width: 100%;}
.reivewContent p{font-weight: 500;color: #514341;}

.circle {overflow: hidden;width: 40px;height: 40px;border-radius: 100%;background-color: white;box-shadow: 2px 1px 6px #77777742;}
.replyBalloon {position:relative;width:75%;background:#ffe6e6;border-radius: 10px;}
.replyBalloon:after {border-top:15px solid #ffe6e6;border-left: 15px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;content:"";position:absolute;top:10px;left:-10px;}

.noInfoImg {width: 45px;height: 45px;background: url(/app/skin/basic/svg/no_info_img.svg);float: left;}

</style>
<div id="content">
	<div class="h_area2">
		<h2>내 리뷰 보기</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<?
	$reviewSQL ="SELECT
				tpr.productcode,
				tpr.num,
				tpr.id,
				tpr.img,
				tpr.content,
				tpr.marks,
				tv.vender,
				tvs.brand_name as bName,
				tpr.badge01,
				tpr.badge02,
				tpr.badge03,
				tpr.badge04,
				tpr.badge05,
				tpr.badge06,
				tpr.badge07,
				tpr.badge08
			FROM
				tblproductreview tpr
				LEFT JOIN tblproduct tp ON
				tpr.productcode = tp.productcode
				LEFT JOIN tblvenderinfo tv ON
				tp.vender = tv.vender
				LEFT JOIN tblvenderstore tvs ON
				tv.vender = tvs.vender
			WHERE
				tpr.del_flg = 0 AND
				tpr.id = '".$_ShopInfo->getMemid()."'
			ORDER BY
				tpr.date DESC";
	$reviewResult = mysql_query($reviewSQL,get_db_conn());
	$reviewCount = mysql_num_rows($reviewResult);
	?>
	<div class="prreview_mypage">
		<div class="myreviewCount">
			<div class="documentIcon f_left"><img src="/app/skin/basic/svg/review_subtitle.svg"></div>
			<div class="dateText">내가 쓴 리뷰, <?=$reviewCount?>개</div>
		</div>
	</div>
	<!--리뷰 목록 시작 -->
	<?	
	
	while($reviewRow = mysql_fetch_object($reviewResult)){
		$imageUrl="/data/shopimages/productreview/".$reviewRow->img;
		$profileImage = $reviewRow->profile_photo;
		$reviewcontent = explode("=",$reviewRow->content);
		$bName = $reviewRow->bName;
		$num = $reviewRow->num;
		$productcode =$reviewRow->productcode;
		$badge01 = $reviewRow->badge01;
		$badge02 = $reviewRow->badge02;
		$badge03 = $reviewRow->badge03;
		$badge04 = $reviewRow->badge04;
		$badge05 = $reviewRow->badge05;
		$badge06 = $reviewRow->badge06;
		$badge07 = $reviewRow->badge07;
		$badge08 = $reviewRow->badge08;

		if($profileImage == ""){
			$profileImage = "/images/no_img.gif";
		}
		else{
			$profileImage = "/data/profilephoto/".$profileImage;
		}
		$reviewName = mb_substr($reviewRow->name, 0, 1,"UTF-8")."*".mb_substr($reviewRow->name, 2,10,"UTF-8");
	?>
		<div class="reviewWrap">
			<div class="reviewTitle">
				<span><?=$bName?></span>
				<a class="reviewDelete f_right" data-productcode="<?=$productcode?>" data-num=<?=$num?>>리뷰 삭제</a>
			</div>
			<div class="starWrap">
				<?for($i = 0 ; $i < 5 ; $i++){
					$reviewStarImg = "review_star_off";
					if($i < $reviewRow->marks){
						$reviewStarImg = "review_star_on";
					}
					echo "<img class=\"reviewStar\" src=\"/app/skin/basic/svg/".$reviewStarImg.".svg\">";
				}?>		
				<span>이번 주</span>
			</div>
			<div class="reviewImg" >
			<?if($reviewRow->img != ""){?>
				<img src="<?=$imageUrl?>" alt="reviewImg">
			<?}?>
			</div>
			<div class="reivewContent">

			<? 
			if(count($reviewcontent) > 0){
				echo "<p>".$reviewcontent[0]."</p>";
				?>
				<div class="pick">
				<?
				if($badge01 =="Y"){
					echo "<div class=\"pickbanner\">가성비값</div>";
				}if($badge02 =="Y"){
					echo "<div class=\"pickbanner\">고급져요</div>";
				}if($badge03 =="Y"){
					echo "<div class=\"pickbanner\">장인정신</div>";
				}if($badge04 =="Y"){
					echo "<div class=\"pickbanner\">빨리와요</div>";
				}if($badge05 =="Y"){
					echo "<div class=\"pickbanner\">친절해요</div>";
				}if($badge06 =="Y"){
					echo "<div class=\"pickbanner\">센스쟁이</div>";
				}if($badge07 =="Y"){
					echo "<div class=\"pickbanner\">신선해요</div>";
				}if ($badge08 =="Y"){
					echo "<div class=\"pickbanner\">풍성해요</div>";
				}
				?>
				</div>
				<?
				if(count($reviewcontent) == 2){
					//echo "<div class=\"myreview_wrap\"><img src='".$profileImage."'alt=\"icon\">";
					//echo "<div class= \"triangle-isosceles left\">".$reviewcontent[1]."</div>";
					
					echo '<div class="replyContent" style="margin-left:8px;margin-top:20px;">';
					echo '		<div class="circle" style="float:left;">';
					echo '			<img src='.$profileImage.' style="width:40px;height:40px;"alt="icon">';
					echo '		</div>';
					echo '		<div class="replyBalloon" style="margin-left:65px;padding-top:10px;padding-bottom:5px;padding-left:10px;min-height:30px;">';
					echo '			<p style="color:#464646; font-size:1.2em;">'.$reviewcontent[1].'</p>';
					echo '		</div>';
					echo '</div>';
				}
			}
			?>
			</div>
		</div>
		<?
		}
		if ($reviewCount == 0) {
			echo '<div class="noReviewContent" style="margin-top:15px;margin-left:15px;">';
			echo '			<p style="color:#464646; font-size:1.2em;">작성한 리뷰가 존재하지 않습니다.</p>';
			echo '</div>';
		}
		?>
</div>
<?
if($_POST["action"] == "delete"){
	// $deleteSQL = "DELETE FROM tblproductreview WHERE num = '".$num."' and productcode ='".$productcode."' and id = '".$_ShopInfo->getMemid()."' ";  //>>>>>>1
	$num = $_REQUEST["num"];
	$deleteSQL = "UPDATE tblproductreview SET del_flg = 1 WHERE num = ".$num." and id = '".$_ShopInfo->getMemid()."' ";  //>>>>>>1
	echo $deleteSQL;
	mysql_query($deleteSQL,get_db_conn());
	echo "<script>alert('게시글이 삭제되었습니다.');location.href='board_list.php?num=".$num."';</script>";
}

?>
<!-- 주문정보 END -->

	<?= $onload ?>



<script>
function refreshMemList(){
	location.reload();
}

$(".reviewWrap").find("a").click(function(){
	var num= $(this).data("num");
	var productcode = $(this).data("productcode");
	var action = "delete";
	console.log(num, productcode);
	if(confirm("삭제된 리뷰는 복구하실 수 없습니다. 정말 삭제하시겠습니까?")){
		$.ajax({
			url:"prreview_myreview.php",
			method:"POST",
			data:{num:num, action:action},
			dataType:"text",
			success:function(data){
				refreshMemList();
				alert("리뷰가 성공적으로 삭제되었습니다.");
			}
		});
	}
});


	//delete FROM `tblproductreview` where num = 9 and id = "social_K_1891458663" and productcode = 900000000000000003

</script>