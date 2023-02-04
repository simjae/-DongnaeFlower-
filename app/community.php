<?php
include_once("./header.php");
//상품 Q&A 노출 설정
$qna_state_sql = "SELECT use_mobile_qna FROM tblmobileconfig WHERE use_mobile_site = 'Y' ";
$qna_state_reuslt = mysql_query($qna_state_sql, get_db_conn());
$qna_state_row = mysql_fetch_object($qna_state_reuslt);

$get_qna_name = _getQnaName($_data->etcfield);
$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$get_qna_name."' ";
$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
$get_qna_row = mysql_fetch_array($get_qna_result);
$set_qna_list_view =$get_qna_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
?>
<!--ui개선 2018.723-->

<div id="content">
	<div class="h_area2">
		<h2>고객지원</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<!-- 카테고리 리스트 -->
	<div class="category_listWrap">
		<ul class="list_type02">
		<?
			$boardListSQL = "SELECT board_name, board, grant_view, grant_mobile FROM tblboardadmin WHERE grant_mobile !='N' ORDER BY board ASC";
			if(false !== $boardListRes = mysql_query($boardListSQL,get_db_conn())){
				$boardListrowcount = mysql_num_rows($boardListRes);

				if($boardListrowcount>0){
					$grant_view = $boardname = $section="";
					while($boardListRow = mysql_fetch_assoc($boardListRes)){
						$grant_view = $boardListRow['grant_view'];
						$grant_mobile = $boardListRow['grant_mobile'];
						$boardname = $boardListRow['board_name'];
						$section = $boardListRow['board'];
						$href="";
						if($section != "share"){
							$href = "board_list.php?board=".$section;
						}else{
							$href = "board_share_list.php";
						}
						if($grant_mobile =="Y"){
							if($grant_view == "Y"){
								if(strlen($_ShopInfo->getMemid())>0){
				?>
					<li><a href="<?=$href?>"><?=$boardname?></a></li>
				<?
								}
							}else{
				?>
					<li><a href="<?=$href?>"><?=$boardname?></a></li>
				<?
								}
							}
						}
					}
				}
			?>
			<li><a href="agreement.php" rel="external">이용약관</a></li>
			<li><a href="privercy.php" rel="external">개인정보취급방침</a></li>
		</ul>
	</div>
	<!-- //카테고리 리스트 -->

</div>
<?
include_once("./footer.php");
?>