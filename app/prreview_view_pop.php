<?
	$Dir = "../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");
	include_once($Dir."app/inc/function.php");


?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<meta name="viewport" content="width=420, user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-cache" />
<link rel="stylesheet" href="./css/common.css" />
<link rel="stylesheet" href="./css/skin/default.css" />

<script type="text/javascript" src="/m/js/jquery-1.10.2.min.js"></script>
</head>

<style>

.prreviewWriteWrap .headerWrap{
	border-bottom: solid 1px #cccccc;
	background: #ffffff;
    overflow: hidden;
}

.prreviewWriteWrap .headerWrap h2 {
    display: block;
    background: #ffffff;
    text-align: center;
    font-size: 1.4em;
    padding: 8px 12px;
    color: #000000;
    font-weight: 500;
	margin: 20px;
}

.responseWrap img{
	width: 60px;
	border-radius: 50px;
	float: left;
}
.responseWrap .triangle-isosceles {
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
.responseWrap .triangle-isosceles:after {
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
.responseWrap .triangle-isosceles:before {
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

.responseWrap .triangle-isosceles.left {
	float:left;
	margin-left:8px;
	background:#F9D2D9;
}


.responseWrap .triangle-isosceles.left:after {
	top:8px; /* controls vertical position */
	left:-7px; /* value = - border-left-width - border-right-width */
	bottom:auto;
	border-width:8px 8px 8px 0;
	border-color:transparent #F9D2D9;
}
.responseWrap .triangle-isosceles.left:before {
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
    font-size: 1.4em;
    padding: 8px 12px;
    color: #000000;
    font-weight: 500;
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
	border-bottom: solid 1px;
}
.prreview_mypage .myreviewCount .documentIcon {
	margin:5px 15px 0 0;
    width: 18px;
}
.prreview_mypage .myreviewCount .dateText {
	margin-top: 5px;
    margin-left: 18px;
    font-size: 1.2em;
    color: #000000;
    line-height: 1.7em;
}
.reviewWrap{margin: 20px;}
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
.starWrap .reviewStar{width: 15px; margin-right:3px;}
.reviewImg img{width: 100%;}
.reivewContent p{font-weight: 500;color: #514341;}





</style>
<body style="background:none;">
	<div class="prreviewWriteWrap">
		<div class="headerWrap">
			<h2>별점 및 리뷰</h2>
		</div>
			<!-- 종합 START -->
<?

	$productcode = !_empty($_REQUEST['productcode'])?trim($_REQUEST['productcode']):"";
	$aoidx = !_empty($_REQUEST['aoidx'])?trim($_REQUEST['aoidx']):"";

	$reviewSQL="SELECT
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
						tblproductreview AS tpr
						LEFT JOIN tblproduct AS tp ON
						tpr.productcode = tp.productcode
						LEFT JOIN tblvenderinfo AS tv ON
						tp.vender = tv.vender
						LEFT JOIN tblvenderstore AS tvs ON
						tv.vender = tvs.vender
					WHERE
						tpr.id = '".$_ShopInfo->getMemid()."' AND
						tpr.productcode = '".$productcode."'
					ORDER BY
						tpr.date DESC";
	
	$reviewResult = mysql_query($reviewSQL,get_db_conn());
	$reviewNum = mysql_num_rows($reviewResult);
	if ($reviewNum > 0) {
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
				<!--
				<a class="reviewDelete f_right" data-productcode="<?=$productcode?>" data-num=<?=$num?>>리뷰 삭제</a>
				-->
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
						echo "<div class=\"responseWrap\"><img src='".$profileImage."'alt=\"icon\"><div class= \"triangle-isosceles left\">".$reviewcontent[1]."</div></div>";
					}
				}else{
					echo "review 없음";
				}
			?>
			</div>
		</div>
		<?
			}
		} else {
		?>
		<div class="reviewWrap">
			<font style="font-weight:500;color:#514341;font-size:1.0rem;">삭제된 리뷰입니다.</font>
		</div>
		<?
		}
		?>
	</div>
</body>
</html>