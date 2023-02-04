<? 
include_once("./inc/function.php");

$currentPage = $_REQUEST["page"];
if(!$currentPage) $currentPage = 1;


$recordPerPage = 5; // 페이지당 게시글 리스트 수 
$pagePerBlock = 3; // 블록 갯수 

$pagetype="board";
?>
<!--ui 개선 20180723--->
<div id="content">
	<div class="h_area2">
		<h2>1:1 문의</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>
	<!-- 1:1문의내역 -->

	<div class="mtom">
		<h2>빠른시간 내에 답변드리도록 하겠습니다.<br>궁금한 사항은 언제든지 문의주세요.</h2>
		<table class="mtom_list">
			<col width="30"></col>
			<col width=""></col>
			<col width="60"></col>
			<tbody>
<?
	$setup[list_num] = 5;
	$sql = "SELECT COUNT(*) as t_count FROM tblpersonal ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$result = mysql_query($sql,get_db_conn());
	$row = mysql_fetch_object($result);
	$totalRecord = $row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $setup[list_num]) + 1;

	$sql = "SELECT idx,subject,date,re_date FROM tblpersonal ";
	$sql.= "WHERE id='".$_ShopInfo->getMemid()."' ";
	$sql.= "ORDER BY idx DESC LIMIT " . ($recordPerPage * ($currentPage - 1)) . ", " . $recordPerPage;
	$result = mysql_query($sql,get_db_conn());
	$cnt=0;
	while($row=mysql_fetch_object($result)) {
		$number = ($totalRecord-($setup[list_num] * ($currentPage-1))-$cnt);

		$date = substr($row->date,0,4)."-".substr($row->date,4,2)."-".substr($row->date,6,2);
		$re_date="-";
		if(strlen($row->re_date)==14) {
			$re_date = substr($row->re_date,0,4)."-".substr($row->re_date,4,2)."-".substr($row->re_date,6,2)."(".substr($row->re_date,8,2).":".substr($row->re_date,10,2).")";
		}
		
		if(strlen($row->re_date)==14) {
			$str_reply =  "<a class=\"black smallSE\">완료</a>";
		} else {
			$str_reply =  "<a class=\"white smallSE\">대기</a>";
		}

?>
			<tr>
				<td align="center"><?=$number?></td>
				<td class="mtomSubject"><a href="mypage_personal_view.php?idx=<?=$row->idx?>" rel="external"><?=strip_tags($row->subject)?></a>
				<p><em><?=$date?></em></p>
				</td>
				<td align="center"><?=$str_reply?></td>
				<!--<td><em class="point1"><?=$_ShopInfo->getMemid()?> </em></td>-->
			</tr>
<?
	$cnt++;
}
	mysql_free_result($result);
	if ($cnt==0) {
		echo "<tr><td colspan=4 align=center>문의내역이 없습니다.</td></tr>";
	}
?>
		</table>
	</div>
	<!-- //1:1문의내역 -->
	<div class="mtomButton">
		<a href="./mypage_personal_write.php" rel="external" class="basic_button grayBtn">문의하기</span></a>
	</div>

	<div id="paging_container">
		<div id="paging_box">
			<ul>
				<?
					_getPage($totalRecord,$recordPerPage,$pagePerBlock,$currentPage,$pagetype, $variable); 
				?>
			</ul>
		</div>
	</div>

</div>




<? 
//include_once('footer.php'); 
?>
