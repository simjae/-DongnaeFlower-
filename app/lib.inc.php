<?
/*
if(substr(getenv("SCRIPT_NAME"),-12)=="/lib.inc.php"){
	header("HTTP/1.0 404 Not Found");
	exit;
}
*/

$board=$_REQUEST["board"];
$num=$_REQUEST["num"];
$exec=$_REQUEST["exec"];
$view=$_REQUEST["view"];
$block=$_REQUEST["block"];
$gotopage=$_REQUEST["gotopage"];
$search=$_REQUEST["search"];
$s_check=$_REQUEST["s_check"];
$pridx=$_REQUEST["pridx"];
$error=$_REQUEST["error"];

$nameLength=20;

$filepath = $Dir.DataDir."shopimages/board/".$board;
$file_icon_path = "images/file_icon";

unset($member);
unset($setup);

$server = getenv("SERVER_NAME");
$file = getenv("SCRIPT_NAME");
$query = getenv("QUERY_STRING");
$chUrl = "http://$server$file";

if($query) $chUrl.="?$query";

$setup = setup_info();
if(strlen($setup[board]) == 0 && $_REQUEST["board"] != "all") {
	echo "<html><head><title></title></head><body onload=\"alert('해당 게시판이 존재하지 않습니다.');location.href='/';\"></body></html>";exit;
}
if($setup[use_hidden]=="Y") {
	echo "<html><head><title></title></head><body onload=\"alert('해당 게시판은 사용하실 수 없는 게시판입니다');history.go(-1);\"></body></html>";exit;
}

$member= member_info();

function setup_info() {
	global $setup, $board;

	if($board == "all") { $board = "notice"; }
	if (isset($setup)) {
		return $setup;
	} else {
		$setup = @mysql_fetch_array(@mysql_query("SELECT * FROM tblboardadmin WHERE board ='".$board."'",get_db_conn()));
		if($setup[board_width]>0 && $setup[board_width]<100) $setup[board_width]=$setup[board_width]."%";
		else if($setup[board_width]==0) $setup[board_width]="100%";
		if($setup[comment_width]>0 && $setup[comment_width]<100) $setup[comment_width]=$setup[comment_width]."%";
		else if($setup[comment_width]==0) $setup[comment_width]="100%";
		if(strlen($setup[notice])>0) {
			$setup[notice]=getTitle($setup[notice]);
			$setup[notice]=getStripHide($setup[notice]);
		}
		if($setup[use_wrap]=="N") $setup[wrap]="off";
		else if($setup[use_wrap]=="Y") $setup[wrap]="on";

		//$setup[board_skin]="W01";
		if($setup[img_maxwidth]<=0 || strlen($setup[img_maxwidth])==0) $setup[img_maxwidth]=650;

		$setup[max_filesize] = $setup[max_filesize]*(1024*100);
		$setup[btype]=substr($setup[board_skin],0,1);
		$setup[title_length]=65;

		if($_REQUEST["board"] == "all") {
			$setup[board_skin] = "all";
			$setup[board_name] = "내가 쓴 글 모아보기";
		}

		return $setup;
	}
}

function member_info() {
	global $board, $member, $setup, $_ShopInfo;

	$member[id]=$_ShopInfo->getMemid();
	if($setup[writer_gbn]=="0") {	//회원이름으로 set
		$member[name]=$_ShopInfo->getMemname();
	} else if($setup[writer_gbn]=="1") {	//회원 아이디로 set
		$member[name]=$_ShopInfo->getMemid();
	}
	$member[email]=$_ShopInfo->getMememail();
	$member[group_code]=$_ShopInfo->getMemgroup();
	$member[authidkey]=$_ShopInfo->getAuthidkey();

	##########default setting#####
	$member[grant_write]="N";
	$member[grant_list]="N";
	$member[grant_view]="N";
	$member[grant_reply]="N";
	$member[grant_comment]="N";
	#############################

	$cadname=$board."_ADMIN";
	if(isCookieVal($_ShopInfo->getBoardadmin(),$cadname)) {
		$member[admin]="SU";
	}

	//게시물 쓰기권한 set
	if($setup[grant_write]=="N") $member[grant_write]="Y";
	else if($setup[grant_write]=="Y") {
		if(strlen($member[id])>0 && strlen($member[authidkey])>0) {
			$member[grant_write]="Y";
		} else if($member[admin]=="SU") {
			$member[grant_write]="Y";
		}
	} else if($setup[grant_write]=="A") {
		if($member[admin]=="SU") {
			$member[grant_write]="Y";
		}
	}
	//게시물 보기권한
	if($setup[grant_view]=="N") {
		$member[grant_list]="Y";
		$member[grant_view]="Y";
	} else if($setup[grant_view]=="U") {
		$member[grant_list]="Y";
		if(strlen($member[id])>0 && strlen($member[authidkey])>0) {
			$member[grant_view]="Y";
		}
	} else if($setup[grant_view]=="Y") {
		if(strlen($member[id])>0 && strlen($member[authidkey])>0) {
			$member[grant_list]="Y";
			$member[grant_view]="Y";
		}
	}
	//답변달기 권한
	if($setup[grant_reply]=="N") $member[grant_reply]="Y";
	else if($setup[grant_reply]=="Y") {
		if(strlen($member[id])>0 && strlen($member[authidkey])>0) {
			$member[grant_reply]="Y";
		} else if($member[admin]=="SU") {
			$member[grant_reply]="Y";
		}
	} else if($setup[grant_reply]=="A") {
		if($member[admin]=="SU") {
			$member[grant_reply]="Y";
		}
	}
	//댓글달기 권한
	if($setup[grant_comment]=="N") $member[grant_comment]="Y";
	else if($setup[grant_comment]=="Y") {
		if(strlen($member[id])>0 && strlen($member[authidkey])>0) {
			$member[grant_comment]="Y";
		} else if($member[admin]=="SU") {
			$member[grant_comment]="Y";
		}
	} else if($setup[grant_comment]=="A") {
		if($member[admin]=="SU") {
			$member[grant_comment]="Y";
		}
	}
	//특정회원그룹
	if(strlen($setup[group_code])==4) {
		$member[grant_write]="N";
		$member[grant_list]="N";
		$member[grant_view]="N";

		if($setup[group_code]==$member[group_code]) {
			if($setup[grant_write]!="A") {
				$member[grant_write]="Y";
			}
			$member[grant_list]="Y";
			$member[grant_view]="Y";
		}
	}

	if($member[admin]=="SU") {
		$member[grant_write]="Y";
		$member[grant_list]="Y";
		$member[grant_view]="Y";
		$member[grant_reply]="Y";
		$member[grant_comment]="Y";
	}
	return $member;
}

function len_title($title,$len_title) {
	$trim_len=strlen(substr($title,0,$len_title));
	if (strlen($title) > $trim_len){
		for($jj=0;$jj < $trim_len;$jj++) {
			$uu=ord(substr($title, $jj, 1));
			if( $uu > 127 ){
				$jj++;
			}
		}
		$n_title=mb_substr($title,0,$jj,'UTF-8');
		$n_title=$n_title."...";
	} else {
		$n_title = $title;
	}
	return $n_title;
}

function isFilter($filter,$memo,&$findFilter) {
	$use_filter = split(",",$filter);
	$isFilter = false;
	for($i=0;$i<count($use_filter);$i++) {
		if (eregi($use_filter[$i],$memo)) {
			$findFilter = $use_filter[$i];
			$isFilter = true;
			break;
		}
	}
	return $isFilter;
}

function reWriteForm() {
	global $exec, $_POST;
	if ($_POST[up_html]) $up_html = "checked";
	$up_subject = urlencode(stripslashes($_POST[up_subject]));
	$up_memo = urlencode(stripslashes($_POST[up_memo]));
	$up_name = urlencode(stripslashes($_POST[up_name]));

	echo "<form name=reWriteForm method=post action=".$PHP_SELF."?pagetype=write&exec=".$exec.">\n";
	echo "<input type=hidden name=\"mode\" value=\"reWrite\">\n";
	echo "<input type=hidden name=\"thisBoard[is_secret]\" value=\"$_POST[up_is_secret]\">\n";
	echo "<input type=hidden name=\"thisBoard[name]\" value=\"$up_name\">\n";
	echo "<input type=hidden name=\"thisBoard[passwd]\" value=\"$_POST[up_passwd]\">\n";
	echo "<input type=hidden name=\"thisBoard[email]\" value=\"$_POST[up_email]\">\n";
	echo "<input type=hidden name=\"thisBoard[use_html]\" value=\"$up_html\">\n";
	echo "<input type=hidden name=\"thisBoard[title]\" value=\"$up_subject\">\n";
	echo "<input type=hidden name=\"thisBoard[content]\" value=\"$up_memo\">\n";
	echo "<input type=hidden name=\"thisBoard[pos]\" value=\"$_POST[pos]\">\n";

	echo "<input type=hidden name=num value=\"$_POST[num]\">\n";
	echo "<input type=hidden name=board value=\"$_POST[board]\">\n";
	echo "<input type=hidden name=s_check value=\"$_POST[s_check]\">\n";
	echo "<input type=hidden name=search value=\"$_POST[search]\">\n";
	echo "<input type=hidden name=block value=\"$_POST[block]\">\n";
	echo "<input type=hidden name=gotopage value=\"$_POST[gotopage]\">\n";
	echo "<input type=hidden name=pridx value=\"$_POST[pridx]\">\n";
	echo "</form>\n";
	echo "<script>document.reWriteForm.submit();</script>";
	exit;
}

function sendMailForm($send_name,$send_email,$message,&$bodytext,&$mailheaders) {
	$mailheaders  = "From: $send_name <$send_email>\r\n";
	//$mailheaders .= "X-Mailer:SendMail\r\n";
	//$boundary = "--------" . uniqid("part");
	//$mailheaders .= "MIME-Version: 1.0\r\n";
	$mailheaders .= "Content-Type: text/html; charset=utf-8\r\n";
	$bodytext .= $message . "\r\n\r\n";
}

function getNewImage($writetime) {
	global $setup;
	$isnew=false;
	if($setup[newimg]=="0") {	//1일
		if(date("Ymd",$writetime)==date("Ymd")) {
			$isnew=true;
		}
	} else if($setup[newimg]=="1") {//2일
		if(date("Ymd",$writetime+(60*60*24*1))>=date("Ymd")) {
			$isnew=true;
		}
	} else if($setup[newimg]=="2") {//24시간
		if(($writetime+(60*60*24))>=time()) {
			$isnew=true;
		}
	} else if($setup[newimg]=="3") {//36시간
		if(($writetime+(60*60*36))>=time()) {
			$isnew=true;
		}
	} else if($setup[newimg]=="4") {//48시간
		if(($writetime+(60*60*48))>=time()) {
			$isnew=true;
		}
	}
	return $isnew;
}

function getTimeFormat($writetime) {
	global $setup;
	$reg_date="";
	if($setup[datedisplay]=="Y") {	//시간 포함
		$reg_date=date("Y/m/d H:i",$writetime);
	} else if($setup[datedisplay]=="O") {	//년월일만
		$reg_date=date("Y/m/d",$writetime);
	}
	return $reg_date;
}

function setBoardCookieArray($cookiename,$arrayval,$time=0,$path="",$domain="") {
	$tmp = serialize($arrayval);
	$time=time()+$time;
	setcookie($cookiename,$tmp,$time,$path,$domain);
	unset($tmp);
}

function getBoardCookieArray($cookie) {
	$tmp=array();
	if(isset($cookie)) {
		$tmp=unserialize(stripslashes($cookie));
	}
	return $tmp;
}

function isCookieVal($cookie,$cookiename) {
	$tmp=unserialize(stripslashes($cookie));
	if($tmp[$cookiename]=="OK") {
		return true;
	} else {
		return false;
	}
}

function getSecret($query,&$row) {
	global $_ShopInfo,$_POST,$setup,$member,$view,$board,$num,$block,$gotopage,$search,$s_check,$boardsep;

	if ($setup[use_lock]!="N") {
		$result = mysql_query($query,get_db_conn());
		$view_ok = mysql_num_rows($result);
		if (!$view_ok || $view_ok == -1) {
			echo "<html><head><title></title></head><body onload=\"alert('잘못된 경로의 글입니다.\\n\\n다시 확인 하십시오.'); document.location.replace('board.php?pagetype=list&board=$board&s_check=$s_check&search=$search&boardsep=$boardsep');\"></body></html>";exit;
		}
		$row = mysql_fetch_array($result);

		$rowset[is_secret] = $row[is_secret];
		$rowset[passwd] = $row[passwd];
		$rowset[passwd_self] = $row[passwd];
		$rowset[num] = $num;
		if ($row[pos] > 0) {
			$query2 = "SELECT num,passwd,is_secret FROM tblboard WHERE board='".$board."' AND thread = $row[thread] AND pos = 0 ";
			$result2 = mysql_query($query2,get_db_conn());
			$row2 = mysql_fetch_array($result2);

			$rowset[is_secret] = $row2[is_secret];
			$rowset[passwd] = $row2[passwd];
			$rowset[num] = $row2[num];
		}

		if ($rowset[is_secret] == "1") {
			$cname=$board."_".$row[thread]."_".$rowset[num]."S";
			if ($_POST[up_passwd] != "") {
				if(strlen($rowset[passwd])==16 || strlen($rowset[passwd_self])==16) {
					$sql9 = "SELECT PASSWORD('".$_POST["up_passwd"]."') AS new_passwd";
					$result9 = mysql_query($sql9,get_db_conn());
					$row9=@mysql_fetch_object($result9);
					$new_passwd = $row9->new_passwd;
					@mysql_free_result($result);
				} else {
					$new_passwd="";
				}

				if ($rowset[passwd_self]==$_POST[up_passwd] || $rowset[passwd]==$_POST[up_passwd] || $setup[passwd]==$_POST[up_passwd] || (strlen($new_passwd)>0 && ($rowset[passwd_self]==$new_passwd || $rowset[passwd]==$new_passwd))) {
					//게시판 관리자 쿠키 세팅
					if($setup[passwd]==$_POST[up_passwd]) {
						$cadname=$board."_ADMIN";
						$cadnamrarray=getBoardCookieArray($_ShopInfo->getBoardadmin());
						$cadnamrarray[$cadname]="OK";
						$_ShopInfo->setBoardadmin(serialize($cadnamrarray));
						$_ShopInfo->Save();
						$isSecret=true;
					} else {
						$cookiearray=getBoardCookieArray($_COOKIE["board_thread_numS"]);
						$cookiearray[$cname]="OK";
						setBoardCookieArray("board_thread_numS",$cookiearray,1800,"/".RootPath.BoardDir,"");
						$isSecret = true;
					}

					if(strlen($new_passwd)>0 && ($rowset[passwd]==$new_passwd || $rowset[passwd_self]==$new_passwd)) {
						if($row[pos] > 0 && $rowset[passwd]==$new_passwd) {
							@mysql_query("UPDATE tblboard SET passwd='".$_POST["up_passwd"]."' WHERE board='".$board."' AND num='".$rowset[num]."' ",get_db_conn());
							$rowset[passwd]=$_POST["up_passwd"];
						} else {
							@mysql_query("UPDATE tblboard SET passwd='".$_POST["up_passwd"]."' WHERE board='".$board."' AND num='".$num."' ",get_db_conn());
							$rowset[passwd]=$_POST["up_passwd"];
							$rowset[passwd_self]=$_POST["up_passwd"];
						}
					}
				} else {
					$error="1";
				}
			} else {
				$isSecret = isCookieVal($_COOKIE["board_thread_numS"],$cname);
			}
		} else {
			$isSecret = true;
		}

		if ($view=="1") {

			if ($isSecret || $member[admin]=="SU") {
				$isAccessUp=false;
				$cname=$board."_".$num."V";
				if($setup[hitplus]=="Y") {	//동일인 조회수 증가 금지 (30분으로 제한)
					if(isCookieVal($_COOKIE["board_thread_numV"],$cname)) {
						$isAccessUp=true;
					}
				}

				if(!$isAccessUp) {
					//cookie set
					$cookiearray=getBoardCookieArray($_COOKIE["board_thread_numV"]);
					$cookiearray[$cname]="OK";

					setBoardCookieArray("board_thread_numV",$cookiearray,1800,RootPath."/m/","");

					$qry = "UPDATE tblboard SET access=access+1 WHERE board='".$board."' AND num = '".$num."' ";
					$update = mysql_query($qry,get_db_conn());
				}

				echo "<script>location.href='board_view.php?num=$num&board=$board';</script>";
				exit;

			} else {
				echo "<script>location.href='passwd_confirm.php?type=view&view=1&num=$num&board=$board';</script>";
				exit;
			}
		} else {
			if (!$isSecret && $member[admin]!="SU") {
				echo "<script>location.href='passwd_confirm.php?type=view&view=1&num=$num&board=$board';</script>";
				exit;
			}
		}

	} else {
		if ($view == "1") {
			$isAccessUp=false;
			$cname=$board."_".$num."V";
			if($setup[hitplus]=="Y") {	//동일인 조회수 증가 금지 (30분으로 제한)
				if(isCookieVal($_COOKIE["board_thread_numV"],$cname)) {
					$isAccessUp=true;
				}
			}

			if(!$isAccessUp) {
				//cookie set
				$cookiearray=getBoardCookieArray($_COOKIE["board_thread_numV"]);
				$cookiearray[$cname]="OK";

				setBoardCookieArray("board_thread_numV",$cookiearray,1800,RootPath."/m/","");

				$qry = "UPDATE tblboard SET access = access+1 WHERE board='".$board."' AND num = '".$num."' ";
				$update = mysql_query($qry,get_db_conn());
			}
			echo "<script>location.href='board_view.php?num=$num&board=$board';</script>";
			exit;
		} else {
			$result = mysql_query($query,get_db_conn());
			$view_ok = mysql_num_rows($result);
			if (!$view_ok || $view_ok == -1) {
				echo "<html><head><title></title></head><body onload=\"alert('잘못된 경로의 글입니다.\\n\\n다시 확인 하십시오.'); document.location.replace('board_list.php?board=$board');\"></body></html>";exit;
			}
			$row = mysql_fetch_array($result);
		}
	}
}

function writeSecret($exec,$is_secret,$pos) {
	global $setup;

	if ($exec == "reply") $disabled = "disabled";
	if ($exec == "modify" && $pos != "0") $disabled = "disabled";

	if($setup[use_lock]=="A") {
		echo "<select name=tmp_is_secret disabled>
			<option value=\"0\">사용안함</option>
			<option value=\"1\" selected>잠금사용</option>
			</select> &nbsp; <FONT COLOR=\"red\">자동잠금기능</FONT>
		";
	} else if($setup[use_lock]=="Y") {
		${"select".$is_secret} = "selected";
		echo "<select name=tmp_is_secret $disabled>
			<option value=\"0\" $select0>사용안함</option>
			<option value=\"1\" $select1>잠금사용</option>
			</select>
		";
	}
}

function MakeBoardTop($setup , $designnewpageTables) {
	global $_ShopInfo,$imgdir;

	$boardtop="<h1 class='subpageTitle titcss_board'>".$setup[board_name]."</h1>";

	/*
	$boardtop = "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"table-layout:fixed\">\n";
	$boardtop.= "<tr>\n";
	$boardtop.="<td><div class=\"subpageTitle\">".$setup[board_name]."</div></td>";
	$boardtop.= "	<TD width=29><IMG SRC=".$imgdir."/board_titlebg_head.gif  ALT=></TD>\n";
	$boardtop.= "	<TD width=100% valign=top background=".$imgdir."/board_titlebg_bg.gif style=padding-top:25px;><span style=font-size:9pt;><b><font color=\"#000000\" style=\"font-size:11pt;\">".$setup[board_name]."</font></b></span></TD>\n";
	$boardtop.= "	<TD width=21><IMG SRC=".$imgdir."/board_titlebg_tail.gif ALT=></TD>\n";
	$boardtop.= "</tr>\n";
	$boardtop.= "</table>\n";
	*/

	$boardtop.= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"".$setup[board_width]."\" style=\"table-layout:fixed\">\n";
	$sql = "SELECT * FROM tblboardadmin ";
	$sql.= "ORDER BY date DESC ";
	$result=mysql_query($sql,get_db_conn());
	$boardgroup = "<select onchange=\"document.location.href=this.value\" style=\"font-size:11px;\">";
	while($row=mysql_fetch_object($result)) {
		if($row->use_hidden!="Y") {
			unset($select);
			if($setup[board]==$row->board) $select="selected";
			$boardgroup.= "<option value=\"board.php?pagetype=list&board=".$row->board."\" ".$select.">".strip_tags($row->board_name)."</option>\n";
		}
	}
	mysql_free_result($result);
	$boardgroup.= "</select>";

	$boardtop.= "<tr>\n";
	$boardtop.= "	<td align=\"right\" style=\"padding-bottom:5px;\">\n";
	//$boardtop.= "	".$boardgroup."\n";
	$boardtop.= "	</td>\n";
	$boardtop.= "</tr>\n";
	$boardtop.= "</table>\n";

	$sql = "SELECT body FROM ".$designnewpageTables." WHERE type='board' AND filename='".$setup[board]."' AND leftmenu='Y' ";
	$result = mysql_query($sql,get_db_conn());
	if($row= mysql_fetch_object($result)) {
		$pattern=array("(\[DIR\])","(\[BOARDGROUP\])","/\[BOARDNAME\]/");
		$replace=array(DirPath,$boardgroup,$setup[board_name]);
		$boardtop=preg_replace($pattern,$replace,$row->body);
	}
	mysql_free_result($result);
	return $boardtop;
}
?>