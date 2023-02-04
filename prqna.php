<?
$pridx=$_pdata->pridx;
$curpage=!_empty($_GET['page'])?trim($_GET['page']):1;
$targetname=$qnasetup->board;
$listnum=3;

$rowcountSQL="SELECT thread FROM tblboard WHERE board = '".$targetname."' AND pridx = '".$pridx."' AND depth = '0' ";

if(false !== $rowcountRes = mysql_query($rowcountSQL, get_db_conn())){
	$boardrowcount = mysql_num_rows($rowcountRes);
	mysql_free_result($rowcountRes);
}

$qna_sql = "SELECT * FROM tblboard WHERE board='$prqnaboard' and pridx = '".$pridx."' ORDER BY thread ASC, pos LIMIT ". ($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
$qna_result=mysql_query($qna_sql,get_db_conn());
$qna_num_rows = mysql_num_rows($qna_result);

//$get_qna_name = _getQnaName($_data->etcfield);

$get_qna_sql = "SELECT * FROM tblboardadmin WHERE board = '".$targetname."' ";

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
?>
	<div class="qna">
		<div class="detail_more">
			<p style="float:left;margin-top:10px;font-size:13px;"><?=$boardrowcount?>건</strong>의 상품문의가 있습니다.</p>
			<?
				if($set_qna_list_write == "Y" || $set_qna_list_write == "A"){
					if($_ShopInfo->getMemid() != "" || $_ShopInfo->getMemid() != null){
				?>
					<div style="float:right"><a href="board_write.php?board=qna&pridx=<?=$pridx?>" rel="external">문의하기</a></div>
				<?
					}
				}else if($set_qna_list_write == "N"){
			?>
				<div class="write_btn" style="float:right;"><a href="board_write.php?board=qna&pridx=<?=$pridx?>&prd=yes" rel="external">문의하기</a></div>
			<?}?>
		</div>

		<? if($boardrowcount > 0){ ?>
		<table class="qna_list" style="margin-top:40px;border-top:1px dotted #ebebeb;">
			<colgroup>
				<col width="25%" />
				<col width="" />
				<col width="20%" />
			</colgroup>

			<? /*
			<thead>
				<tr>
					<th scope="col" class="head_date">날짜</th>
					<th scope="col" class="head_title">제목</th>
					<th scope="col" class="head_writer">작성자</th>
				</tr>
			</thead>
			*/ ?>

			<tbody>
				<?
					if($qna_state_row->use_mobile_qna == "Y"){
						$boardListSQL = "SELECT * FROM tblboard WHERE board = '".$targetname."' AND pridx = '".$pridx."' ORDER BY thread ASC ";

						if(false !== $boardListRes = mysql_query($boardListSQL,get_db_conn())){
							while($boardListRow = mysql_fetch_assoc($boardListRes)){
								$subject = _strCut($boardListRow['title'],16,5,$charset);
								if($boardListRow['pos'] >= "1"){
									$printsubject = '<img src="./images/re_mark.gif"/> '.$subject;
								}else{
									$printsubject = $subject;
								}
								$printsubject = $subject;
								$writer = $boardListRow['name'];
								$regdate = date("Y/m/d",$boardListRow['writetime']);
								if($boardListRow['is_secret'] == "1"){
									$link='javascript:isSecret();';
								}else{
									$link='board_view.php?board='.$targetname.'&productcode='.$productcode.'&num='.$boardListRow['num'].'&prd=yes#tapTop';
								}
				?>
				<tr>
					<td class="date"><div class="cell_td"><?=$regdate?></div></td>
					<td class="title"><a class="page_block" href="<?=$link?>" rel="external"><?=$printsubject?></a></td>
					<td class="writer"><div class="cell_td"><?=$writer?></div></td>
				</tr>
				<?
							}
						}else{
				?>
					<tr>
						<td colspan="3" class="err_td">등록된 글이 없습니다.</td>
					</tr>
				<?
						}
					}else{
				?>
				<tr>
					<td colspan="3" class="err_td"><span>모바일샵 상품Q&A 노출 설정이 되어있지 않습니다.</span></td>
				</tr>
				<? } ?>
			</tbody>
		</table>

		<div id="page_wrap">
			<?
				$pageLink = $_SERVER['PHP_SELF']."?productcode=".$productcode."&page=%u"; // 링크
				$pagePerBlock = ceil($boardrowcount/$listnum);
				$paging = new pages($pageparam);
				$paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
				echo $paging->_result('fulltext');
			?>
		</div>
	<? } ?>
	</div>

	<script>
		function isSecret(){
			alert("해당 문의 글은 잠금기능이 설정된 게시글로\직접 게시판에 가셔서 확인하셔야 합니다.");
		}
	</script>