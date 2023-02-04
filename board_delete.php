<?php
include_once("header.php");

$board_name = isset($_REQUEST[board])? $_REQUEST[board]:"";
$board_num = isset($_REQUEST[num])? $_REQUEST[num]:"";
$board_pass = isset($_REQUEST[pass])? $_REQUEST[pass]:"";

$get_board_sql = "SELECT * FROM tblboardadmin WHERE board = '".$board_name."' ";
$get_board_result = mysql_query($get_board_sql, get_db_conn());
$get_board_row = mysql_fetch_array($get_board_result);

if(empty($board_name) || empty($board_num)){
	echo '<script>alert("잘못된 경로로 접근하였습니다.");history.go(-1);</script>';
	exit;
}

$content_sql= "SELECT * FROM tblboard WHERE board = '".$board_name."' AND num = ".$board_num;
$content_result= mysql_query($content_sql, get_db_conn());
$content_row = mysql_fetch_object($content_result);


if($content_row->passwd != $board_pass){
	echo '<script>alert("비밀번호가 맞지 않습니다.");history.go(-1);</script>';
	exit;
}else{
	$del_sql="DELETE FROM tblboard WHERE board='".$board_name."' AND num=".$board_num;
	mysql_query($del_sql, get_db_conn());

	echo "<script>alert('게시글이 삭제되었습니다.');location.href='board_list.php?board=".$board_name."';</script>";
}
?>