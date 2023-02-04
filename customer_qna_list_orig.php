<?
	include_once("header.php");
	include_once($Dir."app/inc/paging_inc.php");
	$curpage=isset($_GET['page'])?trim($_GET['page']):1;

	$get_qna_name = _getQnaName($_data->etcfield);

	$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$get_qna_name."' ";
	$get_qna_result = mysql_query($get_qna_sql, get_db_conn());
	$get_qna_row = mysql_fetch_array($get_qna_result);

	$set_qna_list_view =$get_qna_row[grant_view]; // 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
	$set_qna_list_write = $get_qna_row[grant_write]; // 게시판 쓰기 권한

	if($set_qna_list_view == "Y"){
		if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
			echo '<script>alert("목록보기 권한이 없습니다.");history.go(-1);</script>';
			exit;
		}
	}

	// 모바일샵 Q&A 노출 설정 여부
	$qna_state_sql = "SELECT use_mobile_qna FROM tblmobileconfig WHERE use_mobile_site = 'Y' ";
	$qna_state_reuslt = mysql_query($qna_state_sql, get_db_conn());
	$qna_state_row = mysql_fetch_object($qna_state_reuslt);

	if($qna_state_row->use_mobile_qna == "Y"){

		$rows_sql = "SELECT * FROM tblboard WHERE board = '".$get_qna_name."' ";
		$rows_result = mysql_query($rows_sql, get_db_conn());
		$totalRecord = mysql_num_rows($rows_result); //전체 리스트 수

		mysql_free_result($result);

		$listnum = 10; // 페이지당 게시글 리스트 수
		//include_once("./paging.php");

		$qna_sql = "SELECT a.*, b.productcode,b.productname,b.etctype,b.sellprice,b.quantity,b.tinyimage FROM tblboard a LEFT OUTER JOIN tblproduct b ON a.pridx=b.pridx WHERE a.board='".$get_qna_name."' ORDER BY thread, pos ASC LIMIT ". ($listnum * ($curpage - 1)) . ", " . $listnum;

		//echo $qna_sql;

		$qna_result = mysql_query($qna_sql, get_db_conn());
		$qna_num_rows = mysql_num_rows($qna_result);
	}else{
		echo '<script>alert("모바일 샵에 상품Q&A 노출 설정이 되어있지 않습니다.");history.go(-1);</script>';
		exit;
	}

	$pagetype="board";

?>
<div id="content">
	<div class="h_area2">
		<h2>고객문의</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<div class="qna">
		<div style="margin-bottom:10px; overflow:hidden;">
			<div class="rowcount"><?=$totalRecord?>건의 문의글이 등록되어 있습니다.</div>

			<?
				if($set_qna_list_write == "Y" || $set_qna_list_write == "A"){
					if($_ShopInfo->getMemid() != "" || $_ShopInfo->getMemid() != null){
			?>
			<div class="qna_write_bt">
				<a class="button white medium" href="./customer_qna_write.php?board=qna" rel="external">글쓰기</a>
			</div>
			<?
					}
				}else if($set_qna_list_write == "N"){
			?>
			<div class="qna_write_bt">
				<a class="button white medium" href="./customer_qna_write.php?board=qna" rel="external">글쓰기</a>
			</div>
			<?}?>
		</div>

		<table class="qna_list">
			<thead>
				<tr>
					<th>작성일</th>
					<th>제목</th>
					<th>작성자</th>
				</tr>
			</thead>
			<tbody>
			<?
				if($qna_state_row->use_mobile_qna == "Y"){
					if($qna_num_rows > 0 || $qna_num_rows != null){
						while($qna_rows = mysql_fetch_object($qna_result)){
							if(mb_strlen($qna_rows->title,$charset) > 21){
								$title = _strCut($qna_rows->title,21,5,$charset);
							}else{
								$title = $qna_rows->title;
							}
							if($qna_rows->is_secret == 1){
								$write_addr = "./passwd_confirm.php?type=view&num=".$qna_rows->num."&board=".$get_qna_name;
							}else{
								$write_addr = "./customer_qna_view.php?num=".$qna_rows->num."&board=".$get_qna_name;
							}

							unset($total_comment);
							if($qna_rows->total_comment >= 1){

								//$total_comment = "<font color=\"#FF0000\">(".$qna_rows->total_comment.")</font>";
							}
			?>
						<tr>
							<td class="date"><div class="cell_td"><?=date("Y.m.d",$qna_rows->writetime)?></div></td>
							<td class="title">
								<a class="page_block" href="<?=$write_addr?>">
									<?if($qna_rows->pos >= 1){?>
										<img src="./images/re_mark.gif"/><?=$title?><?=$total_comment?>
									<?}else{
										if($qna_rows->is_secret == "0"){
											echo $title.$total_comment;
										}else{
									?>
										<img src="./images/lock.gif"/><?=$title?><?=$total_comment?>
									<?	}
									}?>
								</a>
							</td>
							<td class="writer"><div class="cell_td"><?=$qna_rows->name?></div></td>
						</tr>
			<?
						}
					}else{
			?>
					<tr>
						<td colspan="3" class="err_td"><span>등록된 글이 없습니다.</span></td>
					</tr>
			<?
					}
				}else{

			?>
				<tr>
					<td colspan="3" class="err_td"><span>모바일샵 상품Q&A 노출 설정이 되어있지 않습니다.</span></td>
				</tr>
			<?}?>
			</tbody>
		</table>
	</div>

	<div id="page_wrap">
			<?
				$pageLink = $_SERVER['PHP_SELF']."?page=%u"; // 링크
				$pagePerBlock = ceil($totalRecord/$listnum);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
	</div>
</div>

<? include_once('footer.php'); ?>