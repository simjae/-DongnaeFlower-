<?
	if($reviewdate!="N") $colspan=4;
	$qry = "WHERE productcode='".$productcode."' ";
	if($_data->review_type=="A") $qry.= "AND display='Y' ";
	$sql = "SELECT COUNT(*) as t_count, SUM(marks) as totmarks FROM tblproductreview ";
	$sql.= $qry;

	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$totalRecord = (int)$row->t_count;
	$totmarks = (int)$row->totmarks;
	$marks=@ceil($totmarks/$t_count);
	mysql_free_result($result);

	$reviewimagedir = $Dir."data/shopimages/productreview/";

	$reviewcounterSQL = "SELECT ";
	$reviewcounterSQL .= "COUNT(num) AS total ";
	$reviewcounterSQL .= ",SUM(IF(img IS NULL OR img ='',1,0)) AS basic ";
	$reviewcounterSQL .= ",SUM(IF(img IS NOT NULL AND img !='',1,0)) AS photo ";
	$reviewcounterSQL .= ",SUM(IF(best = 'Y',1,0)) AS best ";
	$reviewcounterSQL .= "FROM tblproductreview ";
	$reviewcounterSQL .= "WHERE productcode = '".$productcode."' ";
	if($_data->review_type=="A") $reviewcounterSQL.= "AND display='Y' ";
	if(false !== $reviewcountRes = mysql_query($reviewcounterSQL,get_db_conn())){
		$reviewcountRow = mysql_fetch_assoc($reviewcountRes);
		mysql_free_result($reviewcountRes);
	}

	$counttotal = ($reviewcountRow['total'])?trim($reviewcountRow['total']):"0";
	$countbasic = ($reviewcountRow['basic'])?trim($reviewcountRow['basic']):"0";
	$countphoto = ($reviewcountRow['photo'])?trim($reviewcountRow['photo']):"0";
	$countbest = ($reviewcountRow['best'])?trim($reviewcountRow['best']):"0";

	$reviewtype = !_empty($_GET['review'])?trim($_GET['review']):"all";
	$sort = !_empty($_GET['sort'])?trim($_GET['sort']):"";
	$locationlink = $_SERVER['PHP_SELF']."?productcode=".$productcode."&sort=".$sort;

	$addsql = "";
	$hotoclass=$basicclass=$bestclass=$allclass="white";
	switch($reviewtype){
		case "photo":
			$addsql = "AND img IS NOT NULL AND img !='' ";
			$hotoclass = "on";
		break;
		case "basic":
			$addsql = "AND img IS NULL OR img ='' ";
			$basicclass = "on";
		break;
		case "best":
			$addsql = "AND best = 'Y' ";
			$bestclass = "on";
		break;
		case "all":
		default:
			$allclass = "on";
		break;
	}
?>

<a name="retypert" id="retypert"></a>

<div class="detail_more">
	<? if($counttotal==0){ ?><p style="float:left;margin-top:10px;font-size:13px;">첫번째 리뷰의 주인공이 되세요!</p><? } ?>
	<div style="float:right;"><a href="prreview_write.php?productcode=<?=$productcode?>">상품평 작성하기</a></div>
	<div class="write_close" style="display:none"><img src="/m/images/skin/default/write.png">작성창 닫기</div>
</div>

<div class="detail_review review_container" id="review_form_box">

	<div class="h_area2">
		<h2>상품평 작성</h2>
		<div class="btn_prev write_close"><span>이전</span></div>
	</div>

	<form name="reviewForm" action="./review_write_proc.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="code" value="<?=substr($productcode,0,12)?>" />
		<input type="hidden" name="productcode" value="<?=$productcode?>" />
		<input type="hidden" name="page" value="<?=$currentPage?>" />
		<input type="hidden" name="mode" value="write" />
		<input type="hidden" name="sort" value="<?=$sort?>" />

		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="detail_review_write">
			<tr>
				<td colspan="2"><input type="text" name="rname" maxlength="6" class="input" placeholder="작성자 입력" /></td>
			</tr>
			<tr>
				<td width="50%">
					<span class="basic_select">
						<select name="quality">
							<option selected>품질</option>
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5">★★★★★</option>
						</select>
					</span>
				</td>
				<td width="50%">
					<span class="basic_select">
						<select name="price">
							<option selected>가격</option>
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5">★★★★★</option>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<span class="basic_select">
						<select name="delitime">
							<option selected>배송</option>
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5">★★★★★</option>
						</select>
					</span>
				</td>
				<td>
					<span class="basic_select">
						<select name="recommend">
							<option selected>추천</option>
							<option value="1">★</option>
							<option value="2">★★</option>
							<option value="3">★★★</option>
							<option value="4">★★★★</option>
							<option value="5">★★★★★</option>
						</select>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="2"><textarea name="rcontent" placeholder="내용을 입력해 주세요."></textarea></td>
			</tr>
			<tr>
				<td colspan="2">

					<div class="filebox addfile">
						<label for="attech">파일첨부</label>
						<input type="file" name="attech" id="attech" class="upload-hidden" />
						<input class="upload-name width2" placeholder="파일을 첨부해 주세요." disabled />
					</div>

					<style>
						.filebox input[type="file"] {position: absolute;width: 1px;height: 1px;padding: 0;margin: -1px;overflow: hidden;clip:rect(0,0,0,0);border: none;}
						.filebox label {display: inline-block;padding: 0.4em 0.8em;color: #999;font-size: inherit;line-height: normal;vertical-align: middle;background-color: #fdfdfd;cursor: pointer;border: 1px solid #ebebeb;border-bottom-color: #e2e2e2;border-radius: .25em;}
						.filebox label:hover {color: #555;font-size: inherit;line-height: normal;vertical-align: middle;background-color: #f9f9f9;cursor: pointer;border: 1px solid #555555;border-bottom-color: #555555;border-radius: .25em;}
						/* named upload */
						.filebox .upload-name {display: inline-block;padding: 0em;font-size: inherit;font-family: inherit;line-height: normal;vertical-align: middle;background: #ffffff;border: none; -webkit-appearance: none; -moz-appearance: none; appearance: none;}
						.filebox.addfile label {border: 1px solid #e5e5e5;border-radius:15px;}
					</style>

					<!--파일첨부 js-->
					<script>
						$(document).ready(function(){
							var fileTarget=$('.filebox .upload-hidden');

							fileTarget.on('change', function(){
								if(window.FileReader){
									var filename=$(this)[0].files[0].name;
								} else {
									var filename=$(this).val().split('/').pop().split('\\').pop();
								}
								$(this).siblings('.upload-name').val(filename);
							});
						});
					</script>

					<? /*<input type="file" name="attech" id="attech" style="width:100%;line-height:34px;background:#fff;" />*/ ?>
					<p>(<?=$_MSG_UNIT?>이상의 이미지는 첨부하실 수 없습니다.)</p>
				</td>
			</tr>
		</table>

		<div class="detail_review_bt">
			<div id="btn_submit">리뷰등록</div>
			<div id="btn_reset" class="write_close">창닫기</div>
		</div>

		<input type="hidden" name="MAX_FILE_SIZE" value="<?=$_MAX_FILE_SIZE?>" />
	</form>
</div>


<? if($counttotal>0){ ?>
<script type="text/javascript">
	$(function(){
		//리뷰탭 활성화
		$(".review_tab li").on("click",function(){
			$(".review_tab li").removeClass("on");
			$(this).addClass("on");
			return false;
		});
	});

	//타입별 리뷰 리스트 출력
	function reviewSel(type){
		$('#type').val(type);
		url='prreview_list.php';

		$.post(url, $("#prreview_form").serialize(), function(args){
			$("#prreview_list").html(args);
		});
	}
</script>

<div class="detail_review_list">

	<!-- 포토 리뷰 -->
	<style>
		.prreview_photo_review{display:flex;margin:15px 0px;padding:15px;border-bottom:1px solid #eee;box-sizing:border-box;overflow-x:scroll;-webkit-overflow-scrolling:touch;}
		.prreview_photo_review li{flex:none;float:left;width:30%;padding-right:10px;line-height:0%;}
		.prreview_photo_review li:last-child{padding-right:15px;}
	</style>

	<?
		$photoReviewSql="SELECT * FROM tblproductreview WHERE productcode='".$productcode."' AND img IS NOT NULL AND img !='' ";
		if($_data->review_type=="A"){ $photoReviewSql.= "AND display='Y' "; }
		$photoReviewSql.="ORDER BY date DESC LIMIT 0, 10";
		$photoReviewResult=mysql_query($photoReviewSql,get_db_conn());
		$photoReviewNums=mysql_num_rows($photoReviewResult);

		if($photoReviewNums > 0){ //등록된 포토후기가 있을 때만 출력
	?>
	<h4 style="display:none;margin:20px 0px 10px 0px;">PHOTO REVIEW</h4>
<div style="margin:0px -15px;">
	<ul class="prreview_photo_review">
		<?
			$i=0;
			while($photoReviewRow=mysql_fetch_object($photoReviewResult)){
				$photoSrc=$reviewimagedir.$photoReviewRow->img;
				$width=getimagesize($photoSrc); //이미지 크기 구하기
				if($width[0]>$width[1]){ //이미지 가로가 더 길면
					$addStyle="background-size:cover;";
				}else{ //이미지 세로가 더 길거나 길이가 같으면
					$addStyle="background-size:cover;";
				}
		?>
		<!-- 슬라이드 공백이미지 추가 -->
		<li>
			<a href="photoreview.php?prcode=<?=$productcode?>&num=<?=$i?>"><div style="background:url('<?=$photoSrc?>') no-repeat;background-position:center;<?=$addStyle?>"><img src="/images/common/trans.gif" width="100%" alt="" /></div></a>
		</li>
		<?
				$i++;
			}
		?>
	</ul>
</div>
	<? } ?>

	<ul class="review_tab">
		<li onclick="reviewSel('all');" class="on">전체(<?=$counttotal?>)</li>
		<li onclick="reviewSel('best');">베스트(<?=$countbest?>)</li>
		<li onclick="reviewSel('photo');">포토(<?=$countphoto?>)</li>
		<li onclick="reviewSel('basic');">일반(<?=$countbasic?>)</li>
	</ul>

	<? //print_r($_pdata); ?>

	<form name="prreview_form" id="prreview_form" action="<?=$_SERVER[PHP_SELF]?>" method="post">
		<input type="hidden" name="type" id="type" />
		<input type="hidden" name="productcode" value="<?=$productcode?>" />

		<div id="prreview_list" class="review_list">
			<ul class="review_content">
			<?
				$sql="SELECT * FROM tblproductreview WHERE productcode='".$productcode."' ";
				if($_data->review_type=="A"){
					$sql.= "AND display='Y' ";
				}
				$sql.="ORDER BY date DESC LIMIT 0, 10";
				$result=mysql_query($sql,get_db_conn());
				$nums=mysql_num_rows($result);

				if($nums>0){
					$cnt=0;
					while($row=mysql_fetch_object($result)){
						$attechfile=$contents=$num=$averstarcount=$writer=$viewstar=$regdate=$src=$imagearea=$size=$viewtype="";
						$attechfile=$row->img;
						$averstarcount=$row->marks;
						$contents=explode("=",$row->content);
						$writer=$row->name;
						$regdate=substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2);
						$src=$reviewimagedir.$attechfile;

						$width=getimagesize($src); //이미지 크기 구하기
						if($width[0]>$width[1]){ //이미지 가로가 더 길면
							$addStyle="background-size:auto 100%;";
						}else{ //이미지 세로가 더 길거나 길이가 같으면
							$addStyle="background-size:100% auto;";
						}

						$size = _getImageRateSize($src,80);

						for($i=1;$i<=5;$i++){
							if($i <= $averstarcount){
								$viewstar.='<span>★</span>';
							}else{
								$viewstar.='★';
							}
						}

						#이미지 처리부분
						if(strlen($attechfile)>0){
							$imagearea = '<img src="'.$src.'" '.$size.' />';
							$viewtype ="<img src=\"skin/default/img/icon_photo.png\" alt=\"\" /> ";
						}else{
							$imagearea = $viewstar;
						}

						if( $row->best=="Y"){
							$viewtype .="<img src=\"skin/default/img/icon_best.png\" alt=\"\" /> ";
						}

						echo "<li onclick=\"view_review('".$cnt."',all)\">";

						echo "<div style='overflow:hidden;'>";
						echo "	<p class=\"review_writer\" style='float:left;'>".$regdate."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$writer."</p>";
						echo "	<p class=\"review_point\" style='float:right;'>".$viewstar."</p>";
						echo "</div>";

						echo "<p class='review_prname' style='color:#aaa;'>".$_pdata->productname."</p>";

						echo "<div style='position:relative;overflow:hidden;'>";
						echo "<p class=\"review_text\" style='width:90%;'>".$contents[0]."</p>";
						echo "<span style='position:absolute;bottom:0px;right:0px;'>".$viewtype."</span>";
						echo "</div>";


						echo "</li>";
						echo "<div style=\"clear:both;\"></div>";
						echo "<li id=\"reviewspan\" style=\"display:none;margin:0px;margin-top:-1px;\">";
						echo $contents[0];
						if(strlen($attechfile)>0){
							echo "<div style='margin-top:20px;'><img src='".$src."' style='max-width:100%;' alt='' /></div>";
						}
						echo "</li>";

						$cnt++;
					}
				}else{
					echo "<li style=\"padding:15px 0px;text-align:center;\">이 상품 리뷰의 첫번째 주인공이 되세요!</li>";
				}
			?>
			</ul>
		</div>
	</form>
</div>
<? } ?>


<script type="text/javascript">
	$(".write_btn").click(function(){
		$('html,body').css("overflow","hidden");

		var loginid = "<?=$_ShopInfo->getMemid()?>";
		var writetype = "<?=$_data->review_memtype?>";

		if(writetype =="Y"){
			if(loginid.length > 0 && loginid !=""){
				$(".review_container").css("display", "block");
				$(".write_btn").css("display","none");
				$(".write_close").css("display", "inline-block");
			}else{
				if(confirm("상품평 작성은 회원 전용입니다.\로그인 하시겠습니까?")){
					window.location='/m/login.php?chUrl='+"<?=getUrl()?>";
				}
			}
		}else{
			$(".review_container").css("display", "block");
			$(".write_btn").css("display","none");
			$(".write_close").css("display", "inline-block");
		}
		return;
	});

	$(".write_close").click(function(){
		$('html,body').css("overflow","");
		$(".review_container").css("display", "none");
		$(".write_close").css("display", "none");
		$(".write_btn").css("display","inline-block");
	});

	var form = document.reviewForm;
	$("#review_form_box #btn_submit").click(function(){
		if($("input[name=rname]").val() == "" || $("input[name=rname]").val() == null){
			alert("이름을 작성하세요.");
			$("input[name=rname]").focus();
			return false;

		}else if($("textarea[name=rcontent]").val() == "" || $("textarea[name=rcontent]").val() == null){
			alert("내용을 작성하세요.");
			$("textarea[name=rcontent]").focus();
			return false;

		}else{
			var filestate = document.getElementById('attech');
			if(filestate.value != "" || filestate.value == "undefined" || filestate.value == null){
				var imageMaxSize = "<?=$_MAX_FILE_SIZE?>";
				var fileSize = filestate.files[0].size;
				if(fileSize > imageMaxSize){
					alert("첨부할수 있는 최대 용량은 <?=$_MSG_UNIT?>입니다.");
					return false;
				}
			}

			if(confirm("후기를 등록하시겠습니까?")){
				$("#btn_submit").css("display", "none");
				form.submit();
				return;
			}else{
				return false;
			}
		}
	});

	$("#btn_reset").click(function(){
		form.reset();
		return;
	});

	function moreReview(){
		var _form = document.moreReviewForm;

		if(_form.prcode.value == "" || _form.review.value == ""){
			alert("정상적인 경로로 이용해 주세요");
			return;
		}
		_form.submit();
		return;
	}

	function reviewSelect(type){
		var rlink ="<?=$locationlink?>";
		location.href=rlink+"&review="+type+"#retypert";
		return;
	}

	function view_review(cnt,tp) {
		if(typeof(document.all.reviewspan)=="object" && typeof(document.all.reviewspan.length)!="undefined") {
			for(i=0;i<document.all.reviewspan.length;i++) {
				if(cnt==i) {
					if(document.all.reviewspan[i].style.display=="none") {
						document.all.reviewspan[i].style.display="";
					} else {
						document.all.reviewspan[i].style.display="none";
					}
				} else {
					document.all.reviewspan[i].style.display="none";
				}
			}
		} else {
			if(document.all.reviewspan.style.display=="none") {
				document.all.reviewspan.style.display="";
			} else {
				document.all.reviewspan.style.display="none";
			}
		}
	}
</script>

<form name="moreReviewForm" action="./prreview_list.php" method="get">
	<input type="hidden" name="prcode" value="<?=$productcode?>" />
	<input type="hidden" name="review" value="<?=$reviewtype?>" />
</form>