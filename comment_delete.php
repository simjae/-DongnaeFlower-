<?
//블로그형 게시물 location 변경 2016-03-17 Seul
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/shopdata.php");
include ($Dir."board/head.php");

$pageIndex=$_POST["pageindex"]; //블로그형 게시물 페이지 번호 2016-03-16 Seul
$c_num=$_POST['c_num'];

$boardTypeSQL = "SELECT * FROM tblboardadmin WHERE board='".$board."'";
$boardTypeResult = mysql_query($boardTypeSQL,get_db_conn());
$boardTypeRow = mysql_fetch_object($boardTypeResult);
$boardType = $boardTypeRow->board_skin;

if(strpos($boardType, "B")!==false) //블로그형 게시물은 location 주소 달라짐 2016-03-16 Seul
{
	$Loc = "board_list.php?page=$pageIndex&board=$board";
}
else
{
	$Loc = "board_view.php?num=$num&board=$board";
}

if ($setup[use_comment] != "Y") {
	$errmsg="해당 게시판은 댓글 기능을 지원하지 않습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('$Loc');;\"></body></html>";exit;
}

if ($member[grant_comment]!="Y") {
	$errmsg="이용권한이 없습니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('$Loc');\"></body></html>";exit;
}

$qry = "SELECT * FROM tblboardcomment WHERE board='".$board."' AND parent='".$num."' AND num='".$c_num."' ";
$result1 = mysql_query($qry,get_db_conn());
$ok_result = mysql_num_rows($result1);

if ((!$ok_result) || ($ok_result == -1)) {
	$errmsg="삭제할 댓글이 없습니다.\\n\\n다시 확인하시기 바랍니다.";
	echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('$Loc');\"></body></html>";exit;
} else {
	$row1 = mysql_fetch_array($result1);
}


if ($_POST["mode"] == "delete") {
	if($member[admin]!="SU" AND $member['id'] != $row1['id'] ) {
		if (strlen($_POST["up_passwd"])==0) {
			$errmsg="잘못된 경로로 접근하셨습니다.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('$Loc');;\"></body></html>";exit;
		}

		if (($row1[passwd]!=$_POST["up_passwd"]) && ($setup[passwd]!=$_POST["up_passwd"])) {
			$errmsg="비밀번호가 일치하지 않습니다.\\n\\n다시 확인 하십시오.";
			echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('$Loc');\"></body></html>";exit;
		}
	}
	$del_sql = "DELETE FROM tblboardcomment WHERE board='".$board."' AND parent='".$num."' AND num = '".$_POST["c_num"]."'";
	$delete = mysql_query($del_sql,get_db_conn());

	if ($delete) {
		@mysql_query("UPDATE tblboard SET total_comment = total_comment - 1 WHERE board='".$board."' AND num='".$num."'",get_db_conn());

		// 관리자 댓글도 삭제..
		$del_admin_sql = "DELETE FROM tblboardcomment_admin WHERE board='".$board."' AND board_no='".$num."' AND comm_no = '".$_POST["c_num"]."'";
		mysql_query($del_admin_sql,get_db_conn());

	}

	header("Location:$Loc");exit;
} else {
	$info_msg="댓글 입력시 등록한 비밀번호를 입력하세요.";
	if($member[admin]=="SU" OR $member['id'] == $row1['id']) {
		$admin_hide_start = "정말 삭제하시겠습니까?<!--";
		$admin_hide_end = "-->";
		$info_msg="";
	}
}
	

?>