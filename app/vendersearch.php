<?
include_once("./header.php");
include_once($Dir."app/inc/paging_inc.php");

$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1;
$recordPerPage = 8; // 페이지당 게시글 리스트 수 
$pagePerBlock = 5; // 블록 갯수

$prsearch=$_REQUEST[prsearch];

$terms = isset($_REQUEST[terms])? $_REQUEST[terms]:"";
$sc_text = isset($_REQUEST[sc_text])? $_REQUEST[sc_text]:$vdsearch;
$mode = isset($_REQUEST[mode])? $_REQUEST[mode]:"";

$pagetype = "product";
$variable = "mode=".$mode."&terms=".$terms."&sc_text=".$sc_text."&";

?>
<style>
	.vender_wrap {margin: 0px auto;padding: 0px 15px;border: 0px;font-size: 14px;background: #ffffff;overflow: hidden;}
	.vender_wrap .title {
		color: #282828;
		font-weight:500;
		font-size:1.2em;
		float:left;}
	.vender_wrap .titleArrow {margin-right:19px; float:right;}
	.vender_wrap img {margin:0px; padding:0px; vertical-align:middle;}
	.vender_wrap .vender_table{width:100%;}
	.vender_wrap .vender_table th{font-weight: normal;text-align: left;padding: 0.6em;letter-spacing: -0.5px;}
	.vender_wrap .vender_table td{    padding: 0.3em 0px;
		text-align: left;}
	.vender_wrap .vender_table .grayBtn{
		padding: 0em 0.6em;
		height: 40px;
		font-size: 0.83em;
		border: 1px sold #333333;
		background: #ffffff;
		color: #777777;
		border-radius: 2px;
	}
	.vender_wrap .vender_table p {
		color: #4f4f4f;
		font-weight: normal;
		line-height: 1.5em;
		font-size: 13px;
		margin: 0;
		letter-spacing: -1px;
		padding: 16px 0px 5px;}
	.vender_wrap .vender_table .basic_input {
		height: 40px;
		line-height: 40px;
		padding-left: 0.9em;
		box-sizing: border-box;
		background: #ffffff;
		/* color: #777777; */}
		
	.vender_wrap .vender_group{
		border-bottom:1px solid #9e9e9e36;
		overflow:hidden;
	}
	.vender_wrap .vender_group .bannerImg{height:100px;width:100px;overflow:hidden;}
	.vender_wrap .vender_group .review_content .count1{margin-left:10px;font-weight:600;color:#000000;}
	.vender_wrap .vender_group .review_content .count2{font-weight:400;color:#000000;}
	.vender_wrap .vender_group .store_info{
		text-overflow:ellipsis;
		white-space:nowrap;}
	.vender_wrap .vender_group .imageWrap{width:80px;padding:20px;float:left;}
	.vender_wrap .vender_group .textWrap{padding-top:24px;float:left;width:calc(100vw - 150px);}
	.vender_wrap .vender_group .textGroup{padding:5px 0;overflow:hidden;}
	
</style>
<div id="content">
	<div class="h_area2">
		<h2>꽃집검색</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<div class="sc_wrap">
	<!--
		<div class="sc_terms">
			<form name="searchForm" method="get" action="<?=$_SERVER[PHP_SELF]?>">
				<span class="basic_select">
					<select name="terms" class="terms">
						<option value="brand_name">꽃집 이름</option>
						<option value="brand_description">키워드</option>
					</select>
				</span>
				<input type="text" name="sc_text" value="<?=$sc_text?>" class="basic_input" />
				<input type="button" name="btn_submit" id="btn_submit" class="basic_button" value="검색" />
				<input type="hidden" name="mode" value="search" />
			</form>
			<div style="clear:both"></div>
		</div>
	-->
		<div class="vender_wrap">
		<?
			$sql = "SELECT 
						vs.vender
						,vs.id
						,vs.brand_name
						,vs.brand_description
						,(SELECT cont FROM vender_multicontents AS pm WHERE pm.vender = vs.vender LIMIT 1) AS banner_file
						,vi.com_addr
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
								tp.vender = vi.vender
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
								tp.vender = vi.vender
							GROUP BY tp.vender)
							,0) AS marks_count
					FROM 
						tblvenderstore vs 
					LEFT JOIN 
						tblvenderinfo vi 
					ON 
						vs.id = vi.id 
					WHERE ";
			
			switch($terms){
				case "brand_name":
					$sql.= "(UPPER(brand_name) LIKE UPPER('%".$sc_text."%')) ";
				break;
				case "brand_description":
					$sql.= "(UPPER(brand_description) LIKE UPPER('%".$sc_text."%')) ";
				break;
				case "all":
					$sql.= "(UPPER(brand_description) LIKE UPPER('%".$sc_text."%')) ";
					$sql.= "OR (UPPER(brand_name) LIKE UPPER('%".$sc_text."%')) ";
					$sql.= "OR (UPPER(com_addr) LIKE UPPER('%".$sc_text."%')) ";
				break;
				default:
					$sql.= "1=1 ";
				break;
			}
			
			$sql.="ORDER BY vender DESC ";
			
			$cnt_result = mysql_query($sql,get_db_conn());
			$cnt = mysql_num_rows($cnt_result);	
			mysql_free_result($cnt_result);

			$sql.="LIMIT ".($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
			$result = mysql_query($sql, get_db_conn());
			while($row=mysql_fetch_object($result)) {
				$vender = $row->vender;
				$id = $row->id;
				$brand_name = $row->brand_name;
				$brand_description = $row->brand_description;
				$banner_file = $row->banner_file;
				$avg_marks = $row->avg_marks;
				$marks_count = number_format($row->marks_count);
				if( $banner_file == "" ){
					$banner_file = "/images/no_img.gif";
				}
				else{
					$banner_file = "/data/shopimages/vender/".$banner_file;
				}
				$com_addr = $row->com_addr;
		?>
				<div class="vender_group" onclick="location.href='/app/venderinfo.php?vidx=<?=$vender?>'">
					<div class="imageWrap">
						<div class="imageBox" style="border: 1px solid #9e9e9e36; background:#f8f8f8 url('<?=$banner_file?>') no-repeat;border-radius:10px;background-position:center;background-size:cover;height:80px"></div>
					</div>
					<div class="textWrap">
						<div class="textGroup">
							<div class="title">
								<?=$brand_name?>
							</div>
							<div class="titleArrow">
								&#xE001
							</div>
						</div>
						<div class="textGroup review_content">
								<img src="/app/skin/basic/svg/review_star_on.svg" style="height:12px;" alt="star"> 
								<span class="count1"><?=$avg_marks?></span>
								<span class="count2">(<?=$marks_count?>)</span>
						</div>
						<div class="textGroup store_info">
							<?=$com_addr?>
						</div>
					</div>
				</div>
		<?
			}
		?>
		</div>
	</div>
</div>

<div id="paging_container">
	<div id="paging_box">
		<ul>
			<?
				_getPage($cnt,$recordPerPage,$pagePerBlock,$currentPage,$pagetype,$variable); 
			?>
		</ul>
	</div>
</div>

<!-- Once the page is loaded, initalize the plug-in. -->
<script type="text/javascript">
	$("#btn_submit").click(function(){
		var _form = document.searchForm;

		if($("input[name=sc_text]").val() == "" || $("input[name=sc_text]").val() == ""){
			alert("검색어를 입력하세요.");
			$("input[name=sc_text]").focus();
			return false;
		}else{
			$("input[name=sc_text]").hide();
			_form.submit();
			return;
		}
	});
</script>

<? include "footer.php"; ?>