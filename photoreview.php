<?
//include "header.php";

$Dir = "../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$productcode=$_GET['prcode'];
$num=$_GET['num'];
$reviewimagedir = $Dir."data/shopimages/productreview/";

$photoReviewSql2="SELECT * FROM tblproductreview WHERE productcode='".$productcode."' ";
$photoReviewSql2.="AND img IS NOT NULL AND img !='' ";
if($_data->review_type=="A"){ $photoReviewSql.= "AND display='Y' "; }
$photoReviewSql2.="ORDER BY date DESC LIMIT 0, 10";
$photoReviewResult2=mysql_query($photoReviewSql2,get_db_conn());
$photoReviewNums2=mysql_num_rows($photoReviewResult2);
?>
<!doctype html>
<html>
<head>
	<meta charset="<?=$charset?>">
	<title><?=$shopname?> 쇼핑몰 - 모바일</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="format-detection" content="telephone=no" />
	<link rel="stylesheet" href="/m/skin/basic/css/common.css" />
	<link href="/m/skin/basic/css/default.css" rel="stylesheet" type="text/css"/>
	<link href="/m/skin/basic/css/swiper.min.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript" src="/m/skin/basic/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="/m/skin/basic/js/swiper.min.js"></script>

	<style>
		#pop_photoreview{margin:0;padding:20px;box-sizing:border-box;background:#fff;overflow:hidden;}
		#pop_photoreview .photo_review_content{margin:15px 0px;overflow:hidden;}
	</style>
</head>

<body>
<div class="h_area2">
	<h2>PHOTO REVIEW (<?=$photoReviewNums2?>)</h2>
	<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
	<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
</div>

<div id="pop_photoreview" class="slide_photo_review">
	<div class="swiper-wrapper">
		<?
			while($photoReviewRow2=mysql_fetch_object($photoReviewResult2)){
				$photoSrc2=$reviewimagedir.$photoReviewRow2->img;
				$width2=getimagesize($photoSrc2); //이미지 크기 구하기
				if($width2[0]>$width2[1]){ //이미지 가로가 더 길면
					$addStyle2="background-size:auto 100%;";
				}else{ //이미지 세로가 더 길거나 길이가 같으면
					$addStyle2="background-size:100% auto;";
				}
		?>
			<div class="swiper-slide">
				<div style="background:url('<?=$photoSrc2?>') no-repeat;background-position:center;line-height:0%;<?=$addStyle2?>"><img src="/images/common/trans.gif" width="100%" alt="" /></div>
				<div class="photo_review_content">
					<p style="float:left;">
						<?
							for($i=1;$i<=5;$i++){
								if($i <= $photoReviewRow2->marks){
									echo "<span style='padding:0px;color:#ff6600;'>★</span>";
								}else{
									echo '★';
								}
							}
						?>
					</p><!-- 별점 -->
					<p style="float:right;font-size:0.9em;">
						<?=$photoReviewRow2->name?>, <?=substr($photoReviewRow2->date,0,4)."-".substr($photoReviewRow2->date,4,2)."-".substr($photoReviewRow2->date,6,2)?>
					</p><!-- 작성자/작성일 -->
				</div>
				<p style="text-align:justify;"><?=$photoReviewRow2->content?></p><!-- 내용 -->
			</div>
		<? } ?>
	</div>
</div>

<script language="javascript">
	//슬라이드 처리
	var swiper = new Swiper('.slide_photo_review', {
		slidesPerView: 1, //한번에 보여줄 숫자
		initialSlide:<?=$num?>, //슬라이드 시작번호
		spaceBetween: 10, //슬라이드 간 여백
	});
</script>
</body>
</html>

<?// include ("footer.php") ?>