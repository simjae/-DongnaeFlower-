<?
	if($sellvidx){
		$vidx = isset($_REQUEST['sellvidx'])?trim($_REQUEST['sellvidx']):"";
	}else{
		$vidx = isset($_REQUEST['vidx'])?trim($_REQUEST['vidx']):"";
	}

	if($vidx){ //미니샵 상단일 때
		include_once($Dir."lib/venderlib.php");

		$sql="SELECT COUNT(p.pridx) AS prcount, v.com_name, v.com_owner, v.com_image ";
		$sql.="FROM tblproduct AS p LEFT OUTER JOIN tblvenderinfo AS v ON(p.vender = v.vender) ";
		$sql.="WHERE v.vender = '".$vidx."' AND p.display='Y'";
		$result=mysql_query($sql,get_db_conn());
		$corpname=mysql_result($result,0,1);

		//입점사 카테고리 출력 START
		$_MiniLib=new _MiniLib($vidx);
		$_MiniLib->_MiniInit();
		$_minidata=$_MiniLib->getMiniData();
		$corpname=$_minidata->brand_name;

		$code=$_REQUEST["code"];
		$tgbn="10";
		if(strlen($code)==0) {
			$code="000000";
		}else{
			$code = str_pad($code, 12, "0", STR_PAD_RIGHT);
		}

		$codeA=substr($code,0,3);
		$codeB=substr($code,3,3);
		$codeC=substr($code,6,3);
		$codeD=substr($code,9,3);

		if(strlen($codeA)!=3) $codeA="000";
		if(strlen($codeB)!=3) $codeB="000";
		if(strlen($codeC)!=3) $codeC="000";
		if(strlen($codeD)!=3) $codeD="000";
		if($codeA!="000") $likecode.=$codeA;
		if($codeB!="000") $likecode.=$codeB;
		if($codeC!="000") $likecode.=$codeC;
		if($codeD!="000") $likecode.=$codeD;

		$_MiniLib->getCode($tgbn,$code);
		$_MiniLib->getThemecode();

		$prdataA=$_MiniLib->prdataA;
		$prdataB=$_MiniLib->prdataB;
		$themeprdataA=$_MiniLib->themeprdataA;
		$themeprdataB=$_MiniLib->themeprdataB;
		//입점사 카테고리 출력 END
	}else{ //일반 상단일 때
		$sql="SELECT * FROM tblspecialmain WHERE special='1'"; //신상품
		$result=mysql_query($sql,get_db_conn());
		$nums=mysql_num_rows($result);

		$sql2="SELECT * FROM tblspecialmain WHERE special='2'"; //인기상품
		$result2=mysql_query($sql2,get_db_conn());
		$nums2=mysql_num_rows($result2);
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="<?=$charset?>">
	<title><?=$shopname?> 쇼핑몰 - 모바일</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
	<meta http-equiv="Cache-Control" content="no-cache" />
	<meta name="format-detection" content="telephone=no" />

	<!-- 바로가기 아이콘 -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon-precomposed" href="./upload/<?=$icon?>" />

	<link href="/m/skin/unique/css/common.css" rel="stylesheet" type="text/css"/>
	<link href="/m/skin/unique/css/default.css" rel="stylesheet" type="text/css"/>
	<link href="/m/skin/unique/css/swiper.min.css" rel="stylesheet" type="text/css">
	<link href="/m/skin/unique/css/normalize.css" rel="stylesheet" type="text/css" media="all">
	<link href="/m/skin/unique/css/top.css" rel="stylesheet" type="text/css">
	<link href="/m/skin/unique/css/main.css" rel="stylesheet" type="text/css">
	<link href="/m/skin/unique/css/bottom.css" rel="stylesheet" type="text/css"> 
	<link href="/m/skin/unique/css/detail.css" rel="stylesheet" type="text/css">
	<link href="/m/skin/unique/css/list.css" rel="stylesheet" type="text/css">
	<link href="/m/skin/unique/css/login.css" rel="stylesheet" type="text/css">
	<link href="/css/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="/lib/lib.js.php"></script>
	<script type="text/javascript" src="/m/js/common.js"></script>
	<script type="text/javascript" src="/m/skin/unique/js/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="/m/skin/unique/js/jquery.transform.js"></script>
	<script type="text/javascript" src="/m/skin/unique/js/swiper.min.js"></script>
	<script type="text/javascript" src="/m/skin/unique/js/common.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>

	<script type="text/javascript">
		<!--
		//카테고리 전체보기
		$(document).ready(function(){
			var sidebar = $('[data-sidebar]');
			sidebar.show(0, function() {
				sidebar.css('transition', 'all 0.3s ease');
			});
		});

		//카테고리 전체보기 메뉴열기(아이폰+크롬 조합에서 미동작 문제 수정)
		function openMenuAll(){
			$('.left_bg').fadeIn();
			$('#left_menu').addClass('on');
			$('body').addClass('lock');
		}
		//-->
	</script>
</head>
<body>

	<!-- 상단 -->
	<div id="top">
		<div class="wrapper">
			<div id="header">
				<div id="gnb_button" onclick="openMenuAll()"><!-- 메뉴 전체보기--></div>
				<div id="left_menu" data-sidebar>
					<div class="left_nav">
						<div class="logo"><?//=($vidx?$corpname:(file_exists($logo)==true?"<a href='./' rel='external'><img src='".$logo."' alt='' /></a>":""))?></div>
						<div class="left_login">
							<? if(!$_ShopInfo->getMemid()){ //로그아웃 상태일 때 ?>
								<div><a href="login.php" rel="external">LOGIN</a></div>
								<div><a href="member_join.php" rel="external">JOIN</a></div>
							<? }else{ //로그인 상태일 때 ?>
								<div><a href="logout.php" rel="external">LOGOUT</a></div>
								<div><a href="mypage.php" rel="external">MYPAGE</a></div>
							<? } ?>
						</div>
						<div class="left_etc">
							<div><a href="orderlist.php" rel="external">배송조회</a></div>
							<div><a href="wishlist.php" rel="external">찜하기</a></div>
							<div><a href="customer.php" rel="external">고객센터</a></div>
						</div>
						<div class="left_search">
							<form name="prSearchForm" action="productsearch.php" method="get">
								<input type="hidden" name="mode" value="search" />
								<input type="hidden" name="terms" value="productname" />
								<input type="text" name="prsearch" placeholder="상품을 검색해 주세요.">
								<input type="button" id="btn_search_submit" />
							</form>
						</div>
						<div class="left_category">
							<ul>
							<? if($vidx){ //미니샵 상단일 때 ?>
								<?
									$sqlqq = "SELECT SUBSTRING(a.productcode,1,3) as prcode, COUNT(*) as prcnt ";
									$sqlqq.= "FROM tblproduct AS a ";
									$sqlqq.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
									$sqlqq.= "WHERE 1=1 and a.vender='".$vidx."' AND a.display='Y' ";
									$sqlqq.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
									$sqlqq.= "GROUP BY prcode ";
									$resultqq=mysql_query($sqlqq,get_db_conn());
									while($rowqq=mysql_fetch_object($resultqq)) {
										$codesqq["A"][] = $rowqq->prcode;
										$codesqq["cnt"][$rowqq->prcode] = $rowqq->prcnt;
									}
									mysql_free_result($resultqq);


									$sqltt = "SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode ";
									$sqltt.= "WHERE codeA in ('".implode("','",$codesqq["A"])."') and codeB ='000' and codeC ='000' and codeD ='000' ";
									$sqltt.= "AND group_code!='NO' AND (type LIKE 'L%') ";
									$sqltt.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
									$resulttt=mysql_query($sqltt,get_db_conn());
									$nn = 0;
									while($rowtt=mysql_fetch_object($resulttt)) {

										echo "<li>";
										if(substr($code,0,3)==$rowtt->codeA){
											echo "<a style='display:block;' href='./productlist.php?vidx=".$vidx."&code=".$rowtt->codeA.$rowtt->codeB.$rowtt->codeC.$rowtt->codeD."'><b>".$rowtt->code_name."</b></A>";
										}else{
											echo "<a style='display:block;' href='./productlist.php?vidx=".$vidx."&code=".$rowtt->codeA.$rowtt->codeB.$rowtt->codeC.$rowtt->codeD."'>".$rowtt->code_name."</A>";
										}
										echo "</li>\n";

											//서브 카테고리
											if(substr($code,0,3)==$rowtt->codeA){
												//B
												$sqlqq1 = "SELECT SUBSTRING(a.productcode,1,6) as prcode, COUNT(*) as prcnt ";
												$sqlqq1.= "FROM tblproduct AS a ";
												$sqlqq1.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
												$sqlqq1.= "WHERE a.productcode like '".$rowtt->codeA."%' and a.vender='".$vidx."' AND a.display='Y' ";
												$sqlqq1.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
												$sqlqq1.= "GROUP BY prcode ";
												$resultqq1=mysql_query($sqlqq1,get_db_conn());
												while($rowqq1=mysql_fetch_object($resultqq1)) {
													$codesqq["B"][] = substr($rowqq1->prcode,3,3);
													$codesqq["cnt"][$rowqq1->prcode] = $rowqq1->prcnt;
												}
												mysql_free_result($resultqq1);
												

												$sqltt1 = "SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode ";
												$sqltt1.= "WHERE codeA = '".$rowtt->codeA."' and codeB in ('".implode("','",$codesqq["B"])."') and codeC ='000' and codeD ='000' ";
												$sqltt1.= "AND group_code!='NO' AND (type LIKE 'L%') ";
												$sqltt1.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
												$resulttt1=mysql_query($sqltt1,get_db_conn());
												while($rowtt1=mysql_fetch_object($resulttt1)) {
													echo "<li style='font-size: 1.3em;padding-left: 15px;'>";
													if(substr($code,0,6)==$rowtt1->codeA.$rowtt1->codeB){
														echo "<a style='display:block;' href='./productlist.php?vidx=".$vidx."&code=".$rowtt1->codeA.$rowtt1->codeB.$rowtt1->codeC.$rowtt1->codeD."'><b>".$rowtt1->code_name."</b></A>";
													}else{
														echo "<a style='display:block;' href='./productlist.php?vidx=".$vidx."&code=".$rowtt1->codeA.$rowtt1->codeB.$rowtt1->codeC.$rowtt1->codeD."'>".$rowtt1->code_name."</A>";
													}
													echo "</li>\n";


													if(substr($code,0,6)==$rowtt1->codeA.$rowtt1->codeB){
														//C

														$sqlqq2 = "SELECT SUBSTRING(a.productcode,1,9) as prcode, COUNT(*) as prcnt ";
														$sqlqq2.= "FROM tblproduct AS a ";
														$sqlqq2.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
														$sqlqq2.= "WHERE a.productcode like '".$rowtt1->codeA.$rowtt1->codeB."%' and a.vender='".$vidx."' AND a.display='Y' ";
														$sqlqq2.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
														$sqlqq2.= "GROUP BY prcode ";
														$resultqq2=mysql_query($sqlqq2,get_db_conn());
														while($rowqq2=mysql_fetch_object($resultqq2)) {
															$codesqq["C"][] = substr($rowqq2->prcode,6,3);
															$codesqq["cnt"][$rowqq2->prcode] = $rowqq2->prcnt;
														}
														mysql_free_result($resultqq2);

														$sqltt2 = "SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode ";
														$sqltt2.= "WHERE codeA = '".$rowtt1->codeA."' and codeB ='".$rowtt1->codeB."' and codeC in ('".implode("','",$codesqq["C"])."') and codeD ='000' ";
														$sqltt2.= "AND group_code!='NO' AND (type LIKE 'L%') ";
														$sqltt2.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
														$resulttt2=mysql_query($sqltt2,get_db_conn());
														while($rowtt2=mysql_fetch_object($resulttt2)) {
															echo "<li style='font-size: 1.1em;padding-left: 30px;'>";
															if(substr($code,0,9)==$rowtt2->codeA.$rowtt2->codeB.$rowtt2->codeC){
																echo "<a style='display:block;' href='./productlist.php?vidx=".$vidx."&code=".$rowtt2->codeA.$rowtt2->codeB.$rowtt2->codeC.$rowtt2->codeD."'><b>".$rowtt2->code_name."</b></A>";
															}else{
																echo "<a style='display:block;' href='./productlist.php?vidx=".$vidx."&code=".$rowtt2->codeA.$rowtt2->codeB.$rowtt2->codeC.$rowtt2->codeD."'>".$rowtt2->code_name."</A>";
															}
															echo "</li>\n";

															if(substr($code,0,9)==$rowtt2->codeA.$rowtt2->codeB.$rowtt2->codeC){
																//D

																$sqlqq3 = "SELECT SUBSTRING(a.productcode,1,12) as prcode, COUNT(*) as prcnt ";
																$sqlqq3.= "FROM tblproduct AS a ";
																$sqlqq3.= "LEFT OUTER JOIN tblproductgroupcode b ON a.productcode=b.productcode ";
																$sqlqq3.= "WHERE a.productcode like '".$rowtt2->codeA.$rowtt2->codeB.$rowtt2->codeC."%' and a.vender='".$vidx."' AND a.display='Y' ";
																$sqlqq3.= "AND (a.group_check='N' OR b.group_code='".$_ShopInfo->getMemgroup()."') ";
																$sqlqq3.= "GROUP BY prcode ";
																$resultqq3=mysql_query($sqlqq3,get_db_conn());
																while($rowqq3=mysql_fetch_object($resultqq3)) {
																	$codesqq["D"][] = substr($rowqq3->prcode,9,3);
																	$codesqq["cnt"][$rowqq3->prcode] = $rowqq3->prcnt;
																}
																mysql_free_result($resultqq3);

																$sqltt3 = "SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode ";
																$sqltt3.= "WHERE codeA = '".$rowtt2->codeA."' and codeB ='".$rowtt2->codeB."' and codeC ='".$rowtt2->codeC."' and codeD in ('".implode("','",$codesqq["D"])."')  ";
																$sqltt3.= "AND group_code!='NO' AND (type LIKE 'L%') ";
																$sqltt3.= "ORDER BY codeA,codeB,codeC,codeD ASC ";
																$resulttt3=mysql_query($sqltt3,get_db_conn());
																while($rowtt3=mysql_fetch_object($resulttt3)) {
																	echo "<li style='font-size: 0.9em;padding-left: 45px;'>";
																	if(substr($code,0,12)==$rowtt3->codeA.$rowtt3->codeB.$rowtt3->codeC.$rowtt3->codeD){
																		echo "<a style='display:block;' href='./productlist.php?vidx=".$vidx."&code=".$rowtt3->codeA.$rowtt3->codeB.$rowtt3->codeC.$rowtt3->codeD."'><b>".$rowtt3->code_name."</b></A>";
																	}else{
																		echo "<a style='display:block;' href='./productlist.php?vidx=".$vidx."&code=".$rowtt3->codeA.$rowtt3->codeB.$rowtt3->codeC.$rowtt3->codeD."'>".$rowtt3->code_name."</A>";
																	}
																	echo "</li>\n";
																}
															}
														}
													}
												}
											}
										
										$nn++;
									}
									mysql_free_result($resulttt);
								?>
							<? }else{ ?>

								<? if($nums>0 || $nums2>0){ ?>
									<? if($nums2>0){ ?><li><a href="productbest.php" rel="external">Best</a></li><? } ?>
									<? if($nums>0){ ?><li><a href="productnew.php" rel="external">New</a></li><? } ?>
								<? } ?>

								<?
									$exceptSql = "AND type not like 'X%' AND type not like 'S%' AND group_code!='NO' AND mobile_display != 'N' ORDER BY sequence DESC";
									$categorySql = "SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode WHERE codeA != '000' AND codeB = '000' AND codeC = '000' AND codeD ='000'".$exceptSql;
									if(false !== $categoryRes = mysql_query($categorySql,get_db_conn())){
										$ci = 0;
										$prcode="";
										while($categoryRow = mysql_fetch_assoc($categoryRes)){
											$prcode = $categoryRow['codeA'].$categoryRow['codeB'].$categoryRow['codeC'].$categoryRow['codeD'];
								?>
								<li>
									<a href="#" onClick="_toggle('<?=$ci?>');"><?=$categoryRow['code_name']?></a>
									<?
										$categorySubSql="SELECT codeA, codeB, codeC, codeD, code_name FROM tblproductcode WHERE codeA = '".$categoryRow['codeA']."' AND codeB != '000' AND codeC = '000' AND codeD ='000'".$exceptSql;
										if(false !== $categorySubRes = mysql_query($categorySubSql,get_db_conn())){
											$categorySubNum = mysql_num_rows($categorySubRes);
										}
									?>

									<ul id="subCatelist_<?=$ci?>" class="quick_category_list_se">
										<li><a style="display:block;" href="./productlist.php?code=<?=$categoryRow['codeA']?>">전체보기</a></li>

									<? if($categorySubNum>0){
											$sprcode= "";
											while($categorySubRow = mysql_fetch_assoc($categorySubRes)){
												$sprcode = $categorySubRow['codeA'].$categorySubRow['codeB'].$categorySubRow['codeC'].$categorySubRow['codeD'];
									?>
											<li><a style="display:block;" href="./productlist.php?code=<?=$sprcode?>"><?=$categorySubRow['code_name']?></a></li>
										<?
												}
											}
										?>
									</ul>
								</li>
								<?
										$ci++;
										}
									}
								?>

							<? } ?>
							</ul>


							<!-- 추가 메뉴 -->
							<?
								$totQuery = " SELECT count(*) as cnt FROM tbldesign_top WHERE t_muse = '1' and ( t_type = '1' or t_type = '2' or t_type = '3' ) and t_catetype != '3' and t_normal != '0'   ";
								$ontot = mysql_query($totQuery);
								$ontotrow = mysql_fetch_array($ontot);
								if($ontotrow[cnt] > 0){
							?>
							<ul>
								<?
								$ci = 0;
								
								$sql_top = "SELECT * FROM tbldesign_top WHERE t_muse = '1' and ( t_type = '1' or t_type = '2' or t_type = '3' ) and t_catetype != '3' and t_normal != '0'  order by t_pos ASC, no ASC  ";
								$result_top=mysql_query($sql_top,get_db_conn());
								while($row_top=mysql_fetch_object($result_top)) {


									$t_usename = "";
									if($row_top->t_usename){
										$t_usename = $row_top->t_usename;
									}else{
										$t_usename = $row_top->t_name;
									}
									
									$link_url = "";
									$on_linka = strpos($row_top->t_plink, "[");
									if ($on_linka !== false) {
										$link_url = $row_top->t_plink;
									} else {
										if($row_top->t_catetype == "2"){
											$link_url = "/m/productlist.php?code=".$row_top->t_cate;
											//카테고리 검색
											$sql_db = "SELECT code_name FROM tblproductcode WHERE codeA = '".$row_top->t_cate."' and codeB = '000' and codeC = '000' and codeD = '000' ";
											$result_db=mysql_query($sql_db,get_db_conn());
											if ($row_db=mysql_fetch_object($result_db)) {
												$t_usename = $row_db->code_name;
											}
										}else{
											if($row_top->t_mlink)	{
												$link_url = $row_top->t_mlink;
											}else{
												$link_url = "/m/";
											}
										}
									}

								
									if($link_url){
										echo "<li><a href='".$link_url."' >".$t_usename."</a></li>";
									}else{
										echo "<li>".$t_usename."</li>";
									}
								?>
								<? } ?>
							</ul>
							<? } ?>
							
							<?
							$onnewpage = array();

							$sql_top1 = "SELECT * FROM tbldesign_top WHERE t_muse = '1' and ( t_type = '1' or t_type = '2' or t_type = '3' ) and t_catetype = '3' order by t_pos ASC, no ASC  ";
							$result_top1=mysql_query($sql_top1,get_db_conn());
							while($row_top1=mysql_fetch_object($result_top1)) {
								if($row_top1->t_catetype == "3"){
									$onnewpage[] = $row_top1->t_cate;
								}
							}
							
							if(count($onnewpage) > 0){
							?>
							<ul>
							<?
								$in_list = empty($onnewpage)?'NULL':"'".join("','", $onnewpage)."'";
								//	사용자 페이지 노출 여부
								$exposure_sql = "SELECT * FROM tbldesignnewpage WHERE type='newpage' AND exposure='Y' and code IN({$in_list}) ";
								$exposure_result=mysql_query($exposure_sql,get_db_conn());

								while($exposure_row=mysql_fetch_object($exposure_result)){
									if(strlen($exposure_row->filename)<4) {
										$exposure_url="";
										$exposure_url="newpage.php?code=".$exposure_row->code;
							?>
								<li><a href="<?=$exposure_url?>"><?=$exposure_row->subject?></a></li>
							<?
									}
								}
							?>
							</ul>
							<? } ?>

							<!-- 추가 메뉴2 -->
							<?
								$totQuery = " SELECT count(*) as cnt FROM tbldesign_top WHERE t_muse = '1' and ( t_type = '1' or t_type = '2' or t_type = '3' ) and t_catetype != '3' and t_normal != '1'  ";
								$ontot = mysql_query($totQuery);
								$ontotrow = mysql_fetch_array($ontot);
								if($ontotrow[cnt] > 0){
							?>
							<ul>
								<?
								$ci = 0;
								
								$sql_top = "SELECT * FROM tbldesign_top WHERE t_muse = '1' and ( t_type = '1' or t_type = '2' or t_type = '3' ) and t_catetype != '3' and t_normal != '1'  order by t_pos ASC, no ASC  ";
								$result_top=mysql_query($sql_top,get_db_conn());
								while($row_top=mysql_fetch_object($result_top)) {


									$t_usename = "";
									if($row_top->t_usename){
										$t_usename = $row_top->t_usename;
									}else{
										$t_usename = $row_top->t_name;
									}
									
									$link_url = "";
									$on_linka = strpos($row_top->t_plink, "[");
									if ($on_linka !== false) {
										$link_url = $row_top->t_plink;
									} else {
										if($row_top->t_catetype == "2"){
											$link_url = "/m/productlist.php?code=".$row_top->t_cate;
											//카테고리 검색
											$sql_db = "SELECT code_name FROM tblproductcode WHERE codeA = '".$row_top->t_cate."' and codeB = '000' and codeC = '000' and codeD = '000' ";
											$result_db=mysql_query($sql_db,get_db_conn());
											if ($row_db=mysql_fetch_object($result_db)) {
												$t_usename = $row_db->code_name;
											}
										}else{
											if($row_top->t_mlink)	{
												$link_url = $row_top->t_mlink;
											}else{
												$link_url = "/m/";
											}
										}
									}

								
									if($link_url){
										echo "<li><a href='".$link_url."' >".$t_usename."</a></li>";
									}else{
										echo "<li>".$t_usename."</li>";
									}
								?>
								<? } ?>
							</ul>
							<? } ?>

							<ul>
								<li><a href="reviewall.php" rel="external">Review</a></li>
								<li><a href="board_list.php?board=qna" rel="external">Q&amp;A</a></li>
								<li><a href="board_list.php?board=notice" rel="external">Notice</a></li>
								<li><a href="community.php" rel="external">커뮤니티</a></li>
							</ul>
							
							<!--
							<?
								$sql = "SELECT * FROM tbldesign "; //회사소개+브랜드스토리+어바웃어스
								$result=mysql_query($sql,get_db_conn());
								if($crow=mysql_fetch_object($result)){
									$brandstory=$crow->brandstory;
									$aboutus=$crow->aboutus;
								}
							?>

							<ul>
								<li><a href="company.php" rel="external">회사소개</a></li>
								<? if($brandstory){ ?><li><a href="brandstory.php" rel="external">Brand Story</a></li><? } ?>
								<? if($aboutus){ ?><li><a href="aboutus.php" rel="external">About Us</a></li><? } ?>
							</ul>
							-->

						</div>
					</div>
					<div class="left_close">&times;</div>
				</div>

				<? if($vidx){ //미니샵일 때 미니샵명 출력 ?>
					<a href="/m/venderinfo.php?vidx=<?=$vidx?>" rel="external">
						<div class="logo mini_logo"><?=$corpname?></div>
					</a>
				<? }else{ //대표로고 출력(기본) ?>
					<a href="./" rel="external">
						<div class="logo" <? if(file_exists($logo)==true) { echo "style=\"background:url('".$logo."') no-repeat;background-size:auto 100%;background-position:center;\""; } ?>></div>
					</a>
				<? } ?>

				<a href="#" rel="external" class="prsearch" id="prsearch"><!-- 상품 검색 --></a>
				<a href="basket.php" rel="external"><div class="cart" value="<?=$basketcount?>"></div></a>
			</div>

			<ul id="top_menu">
				<!-- 섹션상품(신/인기상품) 바로가기 -->
				<? if($nums>0 || $nums2>0){ ?>
					<? if($nums2>0){ ?><li><a href="productbest.php" rel="external">Best</a></li><? } ?>
					<? if($nums>0){ ?><li><a href="productnew.php" rel="external">New</a></li><? } ?>
				<? } ?>
				<li><a href="reviewall.php" rel="external">Review</a></li>
				<li><a href="board_list.php?board=qna" rel="external">Q&amp;A</a></li>
			</ul>
		</div>
	</div>

	<div id="mosearch" style="display:none;position:fixed;top:0px;left:0px;width:100%;height:100%;background-color: rgba(58, 58, 58, 0.95);z-index:1000;">
		<div style="position: absolute;top: 17px;text-align: right;right: 10px;"><a href="#" class="close_modal" style="color: #fff;font-size: 4em;font-weight: border;padding-right: 5%;
}">×</a></div>
		<div style="display:table;width:100%;height:100%;">
			<div style="display:table-cell;font-size:0px;text-align:center;vertical-align:middle;">
				<form name="tprSearchForm" action="productsearch.php" method="get">
				<input type="hidden" name="mode" value="search" />
				<input type="hidden" name="terms" value="productname" />
				<input type="text" name="prsearch" value="" placeholder="상품을 검색하세요." style="font-size: 1.5rem;height: 40px;width: 80%;padding: 0px 5px;border: 0px solid rgba(255,255,255,0.5);box-sizing: border-box;color: #ffffff;border-bottom: 2px solid #ffffff;letter-spacing: -1px;" />
				<input type="button" value="" id="btn_tsearch_submit" style="height: 40px;width: 20px;margin-top: 0px;padding: 0px 10px;background: url(/m/skin/unique/img/search2.png) no-repeat;background-size: 100%;background-position: center;border: none;vertical-align: top;border-bottom: 2px solid #ffffff;" />
				</form>
			</div>
		</div>
	</div>

	<div style="height:60px;" id="gotop"></div>

	<script language="javascript">
		<!--
		//상품검색 - 왼쪽메뉴
		$("#btn_search_submit").click(function(){
			var _form = document.prSearchForm;

			if($("#left_menu input[name=prsearch]").val() == "" || $("#left_menu input[name=prsearch]").val() == ""){
				alert("검색어를 입력하세요.");
				$("#left_menu input[name=prsearch]").focus();
				return false;
			}else{
				$("#left_menu input[name=prsearch]").hide();
				_form.submit();
				return;
			}
		});

		//상품검색 - 상단메뉴
		$('#prsearch').click(function(){
			$('#mosearch').fadeIn(200);
		});
		$('.close_modal').click(function(){
			$('#mosearch').fadeOut(200);
		});
		$("#btn_tsearch_submit").click(function(){
			var _form = document.tprSearchForm;

			if($("#mosearch input[name=prsearch]").val() == "" || $("#mosearch input[name=prsearch]").val() == ""){
				alert("검색어를 입력하세요.");
				$("#mosearch input[name=prsearch]").focus();
				return false;
			}else{
				$("#mosearch input[name=prsearch]").hide();
				_form.submit();
				return;
			}
		});
		//-->
	</script>