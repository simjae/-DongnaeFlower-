<?
	$pridx=$_pdata->pridx;
	$num=$_REQUEST["num"];

	$qnablock=$_REQUEST["qnablock"];
	$qnagotopage=$_REQUEST["qnagotopage"];

	if ($qnablock != "") {
		$nowblock = $qnablock;
		$curpage  = $qnablock * $qnasetup->page_num + $qnagotopage;
	} else {
		$nowblock = 0;
		$curpage="";
	}

	if (($qnagotopage == "") || ($qnagotopage == 0)) {
		$qnagotopage = 1;
	}
	$colspan=4;
	if($qnasetup->datedisplay!="N") $colspan=5;

	$sql = "SELECT COUNT(*) as t_count FROM tblboard WHERE board='".$qnasetup->board."' AND pridx='".$pridx."' ";
	if ($qnasetup->use_reply != "Y") {
		$sql.= "AND pos = 0 AND depth = 0 ";
	}
	$result=mysql_query($sql,get_db_conn());
	$row=mysql_fetch_object($result);
	$t_count=$row->t_count;
	mysql_free_result($result);
	$pagecount = (($t_count - 1) / $qnasetup->list_num) + 1;

	$qna_all=$Dir.BoardDir."board.php?board=".$qnasetup->board;
	if($qnasetup->grant_write=="N") {
		$qna_write=$Dir.BoardDir."board.php?pagetype=write&board=".$qnasetup->board."&exec=write&pridx=".$pridx."";
	} else if($qnasetup->grant_write=="Y") {
		if(strlen($_ShopInfo->getMemid())>0) {
			$qna_write=$Dir.BoardDir."board.php?pagetype=write&board=".$qnasetup->board."&exec=write&pridx=".$pridx."";
		} else {
			$qna_write="javascript:check_login()";
		}
	} else {
		$qna_write="javascript:view_qnacontent('W')";
	}

	$isgrantview=false;
	if($qnasetup->grant_view=="N") {
		$isgrantview=true;
	} else if($setup[grant_view]=="U") {
		if(strlen($_ShopInfo->getMemid())>0) {
			$isgrantview=true;
		}
	}

	if(strlen($qnasetup->group_code)==4) {
		$isgrantview=false;
		$qna_write="javascript:view_qnacontent('W')";
		if($qnasetup->group_code==$_ShopInfo->getMemgroup()) {
			$isgrantview=true;
			if($qnasetup->grant_write!="A") {
				$qna_write=$Dir.BoardDir."board.php?pagetype=write&board=".$qnasetup->board."&exec=write&pridx=".$pridx."";
			}
		}
	}
?>

<div class="qna_view">
<?
	$imgdir=$Dir.BoardDir."images/skin/".$qnasetup->board_skin;
	$sql = "SELECT * FROM tblboard WHERE board='".$qnasetup->board."' and num = '".$num."' AND pridx='".$pridx."' ";
	if ($qnasetup->use_reply != "Y") {
		$sql.= "AND pos = 0 AND depth = 0 ";
	}

	$result=mysql_query($sql,get_db_conn());
	$j=0;

	
	$row=mysql_fetch_object($result);
		if($row->is_secret =="1"){
			echo '<script>alert("잘못된 접근 방법 입니다.");history.go(-1);</script>';exit;
		}

		$row->title = stripslashes($row->title);
		if($qnasetup->use_html!="Y") {
			$row->title = strip_tags($row->title);
			$row->content = strip_tags($row->content);
		}
		$row->title = strip_tags($row->title);
		$row->title=getTitle($row->title);
		$row->title=getStripHide($row->title);
		$row->content=getStripHide(stripslashes($row->content));
		if($row->use_html!="1") {
			$row->content=nl2br($row->content);
		}
		$row->name = stripslashes(strip_tags($row->name));

		if($qnasetup->datedisplay=="Y") {
			$date=date("Y/m/d H:i",$row->writetime);
		} else if($qnasetup->datedisplay=="O") {
			$date=date("Y/m/d",$row->writetime);
		}

		unset($subject);
		if ($row->deleted!="1") {
			if($isgrantview) {
				if($row->is_secret!="1") {
					$subject = "<a href=\"javascript:view_qnacontent('".$j."')\">";
				} else {
					$subject = "<a href=\"javascript:view_qnacontent('S')\">";
				}
			} else {
				$subject = "<a href=\"javascript:view_qnacontent('N')\">";
			}
		} else {
			$subject = "<a href=\"javascript:view_qnacontent('D')\">";
		}
		$depth = $row->depth;
		if($qnasetup->title_length>0) {
			$len_title = $qnasetup->title_length;
		}
		$wid = 1;
		if ($depth > 0) {
			if ($depth == 1) {
				$wid = 6;
			} else {
				$wid = (6 * $depth) + (4 * ($depth-1));
			}
			$subject .= "<img src=\"".$imgdir."/x.gif\" width=\"".$wid."\" height=\"2\" border=\"0\">";
			$subject .= "<img src=\"".$imgdir."/re_mark.gif\" border=\"0\">";
			if ($len_title) {
				$len_title = $len_title - (3 * $depth);
			}
		}
		$title = $row->title;
		if ($len_title) {
			$title = titleCut($len_title,$title);
		}
		$subject .=  $title;
		if ($row->deleted!="1") {
			$subject .= "</a>";
		}
		unset($new_img);
		$isnew=false;
		if($qnasetup->newimg=="0") {	//1일
			if(date("Ymd",$row->writetime)==date("Ymd")) {
				$isnew=true;
			}
		} else if($qnasetup->newimg=="1") {//2일
			if(date("Ymd",$row->writetime+(60*60*24*1))>=date("Ymd")) {
				$isnew=true;
			}
		} else if($qnasetup->newimg=="2") {//24시간
			if(($row->writetime+(60*60*24))>=time()) {
				$isnew=true;
			}
		} else if($qnasetup->newimg=="3") {//36시간
			if(($row->writetime+(60*60*36))>=time()) {
				$isnew=true;
			}
		} else if($qnasetup->newimg=="4") {//48시간
			if(($row->writetime+(60*60*48))>=time()) {
				$isnew=true;
			}
		}

		if ($isnew) {
			$subject .= "&nbsp;<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\">";
			$new_img .= "<img src=\"".$imgdir."/icon_new.gif\" border=\"0\" align=\"absmiddle\">";
		}
		if ($qnasetup->use_comment=="Y" && $row->total_comment > 0) {
			$subject .= "&nbsp;<img src=\"".$imgdir."/icon_memo.gif\" border=\"0\" align=\"absmiddle\">&nbsp;<font style=\"font-size:8pt;\">(<font color=\"#FF0000\">".$row->total_comment."</font>)</font>";
		}

		$comment_tot = $row->total_comment;
		$user_name = $row->name;
		$str_name = $user_name;
		$hit = $row->access;
?>

		<!--<h2>1:1 문의를 통한 문의내역 및 답변을 볼 수 있습니다.</h2>-->
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="view_table">
			<tr>
				<th>제목</th>
				<td><span class="point1"><? if ($depth > 0) { echo "[답변]";	} else { echo "";	}?></span> <?=$title?></td>
			</tr>
			<tr>
				<th>작성일</th>
				<td><?=$date?></td>
			</tr>
			<tr>
				<th>작성자</th>
				<td><?=$str_name?></td>
			</tr>
		</table>



<?
		if($isgrantview) {
			if($row->is_secret!="1") {
			?>
				<div class="content_wrap">
					<dl>
						<dt><?=$row->content?></dt>
					</dl>
				</div>
			<?
			}
		}

		mysql_free_result($result);
?>

	<div class="basic_btn_area">
		<a style="cursor:pointer" rel="external" onClick="history.back()" class="basic_button">목록보기</a>
		<a href="./passwd_confirm.php?board=qna&num=<?=$_REQUEST[num]?>&type=modify" rel="external" class="basic_button">수정하기</a>
	</div>
</div>