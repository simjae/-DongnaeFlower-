<?php
//게시판 유형 분리 작업 2016-03-17 Seul
include_once("header.php");
include_once($Dir."app/inc/paging_inc.php");
include_once($Dir."board/file.inc.php"); //게시판 유형 분리 시 필요함 (앨범형 사진, 블로그형 댓글사진) 2016-03-16 Seul
include ($Dir."board/lib.inc.php"); //블로그형 댓글 작성,댓글 출력 시 필요함 2016-03-16 Seul


//썸내일 폴더 생성
if(!is_dir(DirPath.DataDir."shopimages/board/".$board."/thumb")){
    mkdir(DirPath.DataDir."shopimages/board/".$board."/thumb");
    chmod(DirPath.DataDir."shopimages/board/".$board."/thumb",0777);
}
//썸내일 폴더 생성

function user_mime_content_type($filename)
{
    if(!function_exists('mime_content_type'))
    {
        $type = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt'=>'application/vnd.oasis.opendocument.text',
            'ods'=>'application/vnd.oasis.opendocument.spreadsheet',
        );
        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $type))
        {
            return $type[$ext];
        }
        elseif (function_exists('finfo_open'))
        {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else
        {
            return 'application/octet-stream';
        }
    }
    else
    {
        return mime_content_type($filename);
    }
}

function boards_thumb($fileurl, $file, $board, $max_width) {
    //썸네일 파일 확인
    $is_file_exist = file_exists(DirPath.DataDir."shopimages/board/".$board."/thumb/M_".$file);
    if ($is_file_exist) {
        //존재
        $fileurl_thumb = DirPath.DataDir."shopimages/board/".$board."/thumb/M_".$file;
    }else {
        if(!$max_width){
            $max_width = "300";
        }

        $fileurl_thumb = DirPath.DataDir."shopimages/board/".$board."/thumb/M_".$file;

        $info_image = user_mime_content_type($file);

        switch($info_image){
            case "image/gif";
                $src_img=ImageCreateFromGIF($fileurl);
                break;
            case "image/jpeg";
                $src_img=ImageCreateFromJPEG($fileurl);
                break;
            case "image/png";
                $src_img=ImageCreateFromPNG($fileurl);
                break;
        }

        $img_info = getImageSize($fileurl);//원본이미지의 정보를 얻어옵니다
        $img_width = $img_info[0];
        $img_height = $img_info[1];

        $dst_width=$max_width;

        $dst_height=$max_width*($img_height/$img_width);
        $dst_img = imagecreatetruecolor($dst_width, $dst_height); //타겟이미지를 생성합니다
        ImageCopyResized($dst_img, $src_img, 0, 0, 0, 0, $dst_width, $dst_height, $img_width, $img_height); //타겟이미지에 원하는 사이즈의 이미지를 저장합니다

        ImageInterlace($dst_img);

        switch($info_image){
            case "image/gif";
                ImageGif ($dst_img,  $fileurl_thumb);
                break;
            case "image/jpeg";
                ImageJpeg ($dst_img,  $fileurl_thumb);
                break;
            case "image/png";
                ImagePng ($dst_img,  $fileurl_thumb);
                break;
        }
        ImageDestroy($dst_img);
        ImageDestroy($src_img);
    }
    return $fileurl_thumb;
}

$setup = setup_info();
if(!$_ShopInfo->getMemid() && ($_REQUEST["board"] == "all" || $_REQUEST["board"] == "qna")) {
    $errmsg="회원만 이용 가능합니다.\\n\\n로그인 후 이용하시기 바랍니다. - all";
    echo "<html><head><title></title></head><body onload=\"alert('".$errmsg."');location.replace('/app');\"></body></html>";exit;

}else if($_ShopInfo->getMemid() && ($_REQUEST["board"] == "all" || $_REQUEST["board"] == "qna")) {
    $_member_id = $_ShopInfo->getMemid();

    $_total = @mysql_fetch_row(@mysql_query("SELECT COUNT(*) FROM tblboard WHERE userid = '".$_member_id."' ",get_db_conn()));
    $setup[total_article] = $_total[0];
}

$curpage=isset($_GET['page'])?trim($_GET['page']):1;
$boardname = !_empty($_GET['board'])? trim($_GET['board']):"";
$board_name = $boardname;
$listnum = 10; // 페이지당 게시글 리스트 수

if($boardname ==""){
    echo '<script>alert("잘못된 페이지 접근입니다.");history.go(-1);</script>';exit;
}

$boardsettingSQL = "SELECT board_name, grant_view, grant_write, board_skin, thumb_mobile FROM tblboardadmin";
if($_REQUEST["board"] == "all"){
    $boardsettingSQL .= " WHERE board = 'notice' ";
}else{
    $boardsettingSQL .= " WHERE board = '".$boardname."' ";
}

$boardsettingGrantView=$boardsettingGrantWrite="";
if(false !== $boardsettingRes = mysql_query($boardsettingSQL,get_db_conn())){
    $boardsettingGrantView = $boardsettingGrantWrite="";
    $boardsettingGrantView = mysql_result($boardsettingRes,0,1);	// 게시판 조회 권한 N: 회원비회원 목록,글보기 모두 가능, U: 비회원은 목록보기만 가능, Y: 회원만가능
    $boardsettingGrantWrite = mysql_result($boardsettingRes,0,2);	// 게시판 쓰기 권한
    $boardTextName = mysql_result($boardsettingRes,0,0);	// 게시판 이름
    $boardType = mysql_result($boardsettingRes,0,3);	// 게시판 유형(리스트형L, 웹진형W, 앨범형I, 블로그형B, 룩북형C)

    $thumb_mobile = mysql_result($boardsettingRes,0,4);	//섬네일 크기
}

if($_REQUEST["board"] == "all") {
    $boardTextName = $setup[board_name];
}

if(strpos($boardType, "B")!==false){
    $listnum = 1; // 블로그형 게시판은 최대 1개 2016-03-16 Seul
}

if($boardsettingGrantView== "" || $boardsettingGrantView == "Y"){
    if($_ShopInfo->getMemid() == "" || $_ShopInfo->getMemid() == null){
        echo '<script>alert("목록보기 권한이 없습니다.");history.go(-1);</script>';
        exit;
    }
}

// 말머리
$subCateSQL="SELECT `subCategory`,`sub_title` FROM `tblboardadmin` WHERE `board` = '".$board."' ;";
$subCateRes=mysql_query($subCateSQL,get_db_conn());
$subCateRow=mysql_fetch_object($subCateRes);
$subCategoryArray=explode(",",$subCateRow->subCategory);

$subCategoryList="";
$subCategoryList_start="<!--";
$subCategoryList_end="-->";

if($num > 0){
    $boardSQL="SELECT `subCategory`,`vote` FROM `tblboard` WHERE board='".$board."' AND num = ".$num;
    $boardResult=mysql_query($boardSQL,get_db_conn());
    $boardRow=mysql_fetch_assoc ($boardResult);
    if( strlen($boardRow['subCategory']) > 0 ) $subCategoryView = "[".$boardRow['subCategory']."]&nbsp;";
}

if(count($subCategoryArray) > 0 AND strlen($subCategoryArray[0]) > 0){
    if($boardRow['subCategory'] AND $num > 0){
        $selSubCategory = $boardRow['subCategory'];
    }
    if($_GET['subCategory']){
        $selSubCategory = $_GET['subCategory'];
    }

    $subCategoryList_start="";
    $subCategoryList_end="";

    if($setup[btype]=="C"){ //룩북형일 때 카테고리 출력방식 변경(링크형)
        $subCategoryList.="<a href='board_list.php?board=".$board."' class='cate_link".($subCategory==''?" active":"")."'>전체</a>";
        foreach($subCategoryArray as $X){
            $X=trim($X);
            $sel=($selSubCategory == $X)?" active":"";
            $subCategoryList.="<span class='line'></span>";
            $subCategoryList.="<a href='board_list.php?board=".$board."&subCategory=".urlencode($X)."' class='cate_link".$sel."'>".$X."</a>";
        }
    }
}


$totallistSQL="SELECT * FROM tblboard";
if($_REQUEST["board"]=="all"){
    $totallistSQL.=" WHERE userid='".$_member_id."' ";
}
else if( $_REQUEST["board"]=="qna"){
    $totallistSQL.=" WHERE userid='".$_member_id."' AND board='".$boardname."' ";
}else{
    $totallistSQL.=" WHERE board='".$boardname."' ";
}
if(strlen($_GET['subCategory']) > 0){
    $totallistSQL.=" AND subCategory='".$_GET['subCategory']."'";
}
if(false !== $totallistRes=mysql_query($totallistSQL,get_db_conn())){
    $totallistrowcount=mysql_num_rows($totallistRes);
    mysql_free_result($totallistRes);
}else{
    echo '<script>alert("게시판이 지정되지 않았습니다.");history.go(-1)</script>';exit;
}


if($_REQUEST["board"] == "all"){
    $listSQL="SELECT a.*, b.board_name FROM tblboard a, tblboardadmin b WHERE a.board = b.board AND a.userid = '".$_member_id."'";
}
else if( $_REQUEST["board"]=="qna"){
    $listSQL="SELECT a.* FROM tblboard a WHERE a.userid = '".$_member_id."' AND a.board ='".$boardname."'";
}else{
    $listSQL="SELECT a.* FROM tblboard a WHERE a.board ='".$boardname."'";
}

if(strlen($_GET['subCategory']) > 0){
    $listSQL.=" AND a.subCategory='".$_GET['subCategory']."'";
}
$listSQL.=" AND a.notice!=1";
$listSQL.=" ORDER BY a.thread, a.pos ASC LIMIT ".($listnum * ($curpage - 1)) . ", " . $listnum;
?>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#lightgallery').lightGallery({
                thumbnail:true,
                animateThumb: false,
                showThumbByDefault: false,
                mode: 'lg-fade',
                download: false,
                speed: 300
            });
        });
    </script>
	
<?if($_ShopInfo->getMemid() && $_REQUEST["board"] == "qna") {?>
	<!-- Channel Plugin Scripts -->
	<script>
		(function() {
			var w = window;
			if (w.ChannelIO) {
			  return (window.console.error || window.console.log || function(){})('ChannelIO script included twice.');
		}
		var ch = function() {
		  ch.c(arguments);
		};
		ch.q = [];
		ch.c = function(args) {
		  ch.q.push(args);
		};
		w.ChannelIO = ch;
		function l() {
		  if (w.ChannelIOInitialized) {
			return;
		  }
		  w.ChannelIOInitialized = true;
		  var s = document.createElement('script');
		  s.type = 'text/javascript';
		  s.async = true;
		  s.src = 'https://cdn.channel.io/plugin/ch-plugin-web.js';
		  s.charset = 'UTF-8';
		  var x = document.getElementsByTagName('script')[0];
		  x.parentNode.insertBefore(s, x);
		}
		if (document.readyState === 'complete') {
		  l();
		} else if (window.attachEvent) {
		  window.attachEvent('onload', l);
		} else {
		  window.addEventListener('DOMContentLoaded', l, false);
		  window.addEventListener('load', l, false);
		}
	  })();
	  ChannelIO('boot', {
		"pluginKey": "6cbffc8d-7c04-4ec8-ad87-35ad1444d986"
	  });
	  ChannelIO('show');
	</script>
	<!-- End Channel Plugin -->

<?}?>
    <script src="/js/lightgallery-all.min.js"></script>
    <script src="/js/jquery.mousewheel.min.js"></script>
    <link href="/js/lightgallery.css" rel="stylesheet">
    <!-- Magnific Popup core JS file -->
    <script src="/js/jquery.magnific-popup.js"></script>
    <script language=javascript>
        //유튜브 동영상 뷰 처리
        $(document).ready(function() {
            $('.popup-youtube').magnificPopup({
                type: 'iframe',
                mainClass: 'mfp-fade',
                removalDelay: 160,
                preloader: false,
                fixedContentPos: false
            });
        });
    </script>

    <!-- Magnific Popup core CSS file -->
    <link href="/js/magnific-popup.css" rel="stylesheet" />

    <style type="text/css">
        .boardTop{margin:20px 0px;}
        .boardTop .cate_link{display:inline-block;padding-bottom:4px;}
        .boardTop span:after{content:"";padding:0px 15px;color:#ccc;font-family:돋움;}
        .boardTop .cate_link.active{border-bottom:1px solid #666;color:#666;font-weight:bold;}

        .gallery_type > ul {
            margin-bottom: 0;
            overflow:hidden;
        }
        .gallery_type > ul > li {
            float: left;
            position:relative;
            width:calc(50% - 0.5px);
            margin-right:1px;
            margin-bottom:1px;
            background:#f9f9f9;
        }
        .gallery_type > ul > li:nth-child(2n) {
            margin-right: 0px;
        }
        .gallery_type > ul > li a {
            display: block;
            line-height:0%;
            overflow: hidden;
            position: relative;
        }
        .gallery_type > ul > li a > img {
            -webkit-transition: -webkit-transform 0.15s ease 0s;
            -moz-transition: -moz-transform 0.15s ease 0s;
            -o-transition: -o-transform 0.15s ease 0s;
            transition: transform 0.15s ease 0s;
            -webkit-transform: scale3d(1, 1, 1);
            transform: scale3d(1, 1, 1);
        }
        .gallery_type > ul > li a:hover > img {
            -webkit-transform: scale3d(1.1, 1.1, 1.1);
            transform: scale3d(1.1, 1.1, 1.1);
        }
        .gallery_type > ul > li a:hover .demo-gallery-poster > img {
            opacity: 1;
        }
        .gallery_type > ul > li a .demo-gallery-poster {
            background-color: rgba(0, 0, 0, 0.1);
            bottom: 0;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            -webkit-transition: background-color 0.15s ease 0s;
            -o-transition: background-color 0.15s ease 0s;
            transition: background-color 0.15s ease 0s;
        }
        .gallery_type > ul > li a .demo-gallery-poster > img {
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            opacity: 0;
            position: absolute;
            top: 50%;
            -webkit-transition: opacity 0.3s ease 0s;
            -o-transition: opacity 0.3s ease 0s;
            transition: opacity 0.3s ease 0s;
        }
        .gallery_type > ul > li a:hover .demo-gallery-poster {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .gallery_type .justified-gallery > a > img {
            -webkit-transition: -webkit-transform 0.15s ease 0s;
            -moz-transition: -moz-transform 0.15s ease 0s;
            -o-transition: -o-transform 0.15s ease 0s;
            transition: transform 0.15s ease 0s;
            -webkit-transform: scale3d(1, 1, 1);
            transform: scale3d(1, 1, 1);
            height: 100%;
            width: 100%;
        }
        .gallery_type .justified-gallery > a:hover > img {
            -webkit-transform: scale3d(1.1, 1.1, 1.1);
            transform: scale3d(1.1, 1.1, 1.1);
        }
        .gallery_type .justified-gallery > a:hover .demo-gallery-poster > img {
            opacity: 1;
        }
        .gallery_type .justified-gallery > a .demo-gallery-poster {
            background-color: rgba(0, 0, 0, 0.1);
            bottom: 0;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            -webkit-transition: background-color 0.15s ease 0s;
            -o-transition: background-color 0.15s ease 0s;
            transition: background-color 0.15s ease 0s;
        }
        .gallery_type .justified-gallery > a .demo-gallery-poster > img {
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            opacity: 0;
            position: absolute;
            top: 50%;
            -webkit-transition: opacity 0.3s ease 0s;
            -o-transition: opacity 0.3s ease 0s;
            transition: opacity 0.3s ease 0s;
        }
        .gallery_type .justified-gallery > a:hover .demo-gallery-poster {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .gallery_type .video .demo-gallery-poster img {
            height: 48px;
            margin-left: -24px;
            margin-top: -24px;
            opacity: 0.8;
            width: 48px;
        }
        .gallery_type.dark > ul > li a {
            border: 3px solid #04070a;
        }
        .home .gallery_type {
            padding-bottom: 80px;
        }

        .gallery_type .over_view_contents{
            background:rgba(0,0,0,0.8);
            cursor:pointer;
            opacity:0;
            -webkit-transition: opacity 0.3s ease 0s;
            -o-transition: opacity 0.3s ease 0s;
            transition: opacity 0.3s ease 0s;
        }
        .gallery_type .over_view_contents h4{
            color:#fff;
        }
        .gallery_type .over_view_contents.opacity{
            opacity:0.8;
            -webkit-transition: opacity 0.4s ease 0s;
            -o-transition: opacity 0.4s ease 0s;
            transition: opacity 0.4s ease 0s;
        }
        .lg-sub-html{
            max-height:150px;
            padding:10px 20px 50px 20px;
            overflow-y:scroll;
        }
        .lg-sub-html p{
            color:rgba(255, 255, 255, 0.4);
            font-size:0.9em;
            text-align:justify;
        }
    </style>
<? if(strpos($boardType, "C")!==false || strpos($boardType, "I")!==false){ ?>
    <style>.boardwrap{margin:0px;}</style>
<? } ?>

    <div id="content">
        <div class="h_area2">
            <h2><?=$boardTextName?></h2>
            <a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
            <a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
        </div>
<?if($_ShopInfo->getMemid() && $_REQUEST["board"] == "qna") {?>
        <div class="boardTop" style="text-align:center;">
			<font class="fc_dgry">채널톡으로 1:1 문의를 시작해 주세요</font>
			</br>
			<font class="fc_pink">*채널톡이 표시되지 않는 경우 하단의 채널톡 버튼을 클릭 해 주세요.</font>
			</br>
			<font class="fc_pink">*아이폰의 경우 채널톡 연동은 구현중입니다.</font>
		</div>
<? } 
else {?>
        <?=$subCategoryList_start?>
        <div class="boardTop" style="text-align:center;"><?=$subCategoryList?></div>
        <?=$subCategoryList_end?>

        <div class="boardwrap">
            <?
            if(strpos($boardType, "C")!==false){
                //Look Book형 게시판은 전체 카운트 미출력
            }else{
                ?>
                <div class="rowcount">전체 <?=$totallistrowcount?>건</div>
            <? } ?>

            <?
            /*
            if($boardsettingGrantWrite == "Y"){
                if($_ShopInfo->getMemid() != "" || $_ShopInfo->getMemid() != null){
        ?>
        <div class="board_write_bt">
            <? if($_REQUEST["board"] != "all"){ ?>
                <a class="basic_button" href="board_write.php?board=<?=$board?>" rel="external">글쓰기</a>
            <? } ?>
        </div>
        <?
                }
            }else if($boardsettingGrantWrite == "N"){
        ?>
        <div class="board_write_bt">
            <? if($_REQUEST["board"] != "all"){ ?>
                <a class="basic_button" href="board_write.php?board=<?=$board?>" rel="external">글쓰기</a>
            <? } ?>
        </div>
        <? } */ ?>

            <!-- 글쓰기 권한이 없으면 로그인 페이지로 이동 -->
            <div class="board_write_bt">
                <? if($_REQUEST["board"] != "all" && $_REQUEST["board"] != "faq" && $_REQUEST["board"] != "notice"){ ?>
                    <a class="basic_button" href="board_write.php?board=<?=$board?>" rel="external">글쓰기</a>
                <? } ?>
            </div>

            <div class="board_list">
                <?
                /* 공지사항 */
                $nSql = "SELECT num, title, name, writetime, access, is_secret FROM tblboard WHERE board='".$board."' AND notice='1' ORDER BY thread ASC ";
                $nResult = mysql_query($nSql,get_db_conn());
                while($nRow = mysql_fetch_array($nResult)) {
                    $nRow[title] = stripslashes($nRow[title]);
                    $nRow[title]=getTitle($nRow[title]);
                    $nRow[title]=getStripHide($nRow[title]);
                    $nRow[title]=len_title($nRow[title], 20);
                    //$nRow[writetime] = getTimeFormat($nRow[writetime]);
                    $nRow[writetime]=date("Y-m-d",$nRow[writetime]);

                    echo '
						<a href="board_view.php?num='.$nRow[num].'&view=1&board='.$board.'">
							<div class="listLine" style="background:#f8f8f8;">
								<p class="title"><span style="display:inline-block;padding:0px 6px;background:#ee6b1c;border-radius:4px;color:#fff;font-size:0.8em;">공지</span> '.$nRow[title].'</p>
								<p class="writer">
									'.$nRow[writetime].' <span class="hline">|</span> '.$nRow[name].' <span class="hline">|</span> '.$nRow[access].'
								</p>
							</div>
						</a>
					';
                }
                mysql_free_result($nResult);
                /* 공지사항 */


                if(false !== $listRes = mysql_query($listSQL,get_db_conn())){
                    $listrowcount = mysql_num_rows($listRes);
                    if($listrowcount>0){

                        if(strpos($boardType, "C")!==false){
                            echo "<div class='gallery_type'>";
                            echo "<ul id='lightgallery' class='list-unstyled row'>";
                        }

                        while($listRow = mysql_fetch_object($listRes)){
                            $title = $listRow->title;

                            //게시판 카테고리 유무 확인
                            if( strlen($listRow->subCategory) > 0 ) {
                                $title = "[".$listRow->subCategory."] ".$title;
                            }

                            if(mb_strlen($title) > 33){
                                $title = _strCut($title,33,5,$charset);
                            }else{
                                $title = $title;
                            }

                            if($listRow->is_secret == 1){
                                $write_addr = "./passwd_confirm.php?type=view&num=".$listRow->num."&view=1&board=".$listRow->board;
                            }else{
                                $write_addr = "./board_view.php?num=".$listRow->num."&view=1&board=".$listRow->board;
                            }

                            unset($total_comment);
                            /*
                            if($listRow->total_comment >= 1){
                                $total_comment = "<font color=\"#FF0000\">(".$listRow->total_comment.")</font>";
                            }
                            */

                            $view_num = $listRow->num;

                            if(strpos($boardType, "L")!==false) {
                                ?>
                                <!-- 리스트형 -->
                                <a href="<?=$write_addr?>">
                                    <div class="listLine">
                                        <p class="title">
                                            <?if($listRow->pos >= 1){?>
                                                <img src="./images/review_replyicn.png" style="vertical-align:top" /> <?=$title?><?=$total_comment?>
                                            <?}else{
                                                if($listRow->is_secret == "0"){
                                                    echo $title.$total_comment;
                                                }else{
                                                    ?>
                                                    <img src="./images/lock.gif"/><?=$title?><?=$total_comment?>
                                                <?	}
                                            }?>
                                        </p>
                                        <p class="writer" style="margin-bottom:8px;">
                                            <? if($_REQUEST["board"] == "all") { ?>
                                                <?=$listRow->board_name?>
                                                <span class="hline">|</span>
                                            <? } ?>
                                            <?=date("Y-m-d",$listRow->writetime)?>
                                            <span class="hline">|</span>
                                            <?=$listRow->name?>
                                            <span class="hline">|</span>
                                            <?=$listRow->access?>
                                        </p>
                                    </div>
                                </a>
                                <?
                            }
                            else if(strpos($boardType, "W")!==false) {
                                ?>
                                <!-- 웹진형 -->
                                <a href="<?=$write_addr?>" >
                                    <table width="100%" style="border-bottom:1px solid #ebebeb;padding:15px 0px;">
                                        <colgroup>
                                            <col width="30%" />
                                            <col width="" />
                                        </colgroup>
                                        <tr>
                                            <td>
                                                <?
                                                if($listRow->filename){
                                                    $filename="../data/shopimages/board/".$boardname."/".$listRow->filename;
                                                    $filename = boards_thumb($filename, $listRow->filename, $boardname, $thumb_mobile);
                                                }else{
                                                    $filename="../images/no_img.gif";
                                                }
                                                ?>
                                                <img width="100%" src='<?=$filename?>'>
                                            </td>

                                            <td>
                                                <p class="title">
                                                    <?if($listRow->pos >= 1){?>
                                                        <img src="./images/review_replyicn.png" style="vertical-align:top" /> <?=$title?><?=$total_comment?>
                                                    <?}else{
                                                        if($listRow->is_secret == "0"){
                                                            echo $title.$total_comment;
                                                        }else{
                                                            ?>
                                                            <img src="./images/lock.gif"/><?=$title?><?=$total_comment?>
                                                        <?	}
                                                    }?>
                                                </p>
                                                <p class="writer"><?=date("Y-m-d",$listRow->writetime)?> <span class="hline">|</span> <?=$listRow->name?> <span class="hline">|</span> <?=$listRow->access?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </a>
                                <?
                            }
                            else if(strpos($boardType, "I")!==false) {
                                ?>
                                <!-- 앨범형 -->
                                <table width="100%" cellpadding="0" cellspacing="0" width="100%" style="margin-top:10px">
                                    <colgroup>
                                        <col width="50%" />
                                        <col width="" />
                                    </colgroup>
                                    <tr>
                                        <td>
                                            <?
                                            if($listRow->movie_url && $listRow->thumb_use=='N'){ //섬네일 직접 등록
                                                $filename="/data/shopimages/board/".$boardname."/".$listRow->movie_image;
                                                $link_url_start="<a class='popup-youtube' href='http://www.youtube.com/watch?v=".$listRow->movie_url."'>";
                                            }else if($listRow->movie_url && $listRow->thumb_use=='Y'){ //유튜브 섬네일 사용
                                                $filename="https://img.youtube.com/vi/".$listRow->movie_url."/maxresdefault.jpg";
                                                $link_url_start="<a class='popup-youtube' href='http://www.youtube.com/watch?v=".$listRow->movie_url."'>";
                                            }else{ //둘다 아닐 때는 기본 첨부이미지
                                                if($listRow->filename){
                                                    $filename="../data/shopimages/board/".$boardname."/".$listRow->filename;
                                                    $filename = boards_thumb($filename, $listRow->filename, $boardname, $thumb_mobile);
                                                }else{
                                                    $filename="../images/no_img.gif";
                                                }
                                            }

                                            $movie_icon="";
                                            if($listRow->movie_url){
                                                $movie_icon="<div style='position:absolute;top:50%;left:50%;width:50px;height:50px;margin-top:-25px;margin-left:-25px;'><img src='/board/images/icon_play.png' width='100%' alt= '' /></div>";
                                            }
                                            ?>
                                            <?=$link_url_start?>
                                            <div style="position:relative;margin-bottom:8px;border-right:1px solid #fff;background:url('<?=$filename?>') no-repeat;background-position:center;background-size:cover;font-size:0px;line-height:0%">
                                                <img width="100%" src="/m/skin/basic/img/list_bg_dot.png" border="0" alt="" />
                                                <?=$movie_icon?>
                                            </div>
                                            <?=$link_url_end?>

                                            <a href="<?=$write_addr?>">
                                                <p class="title">
                                                    <? if($listRow->pos >= 1){ ?>
                                                        <img src="./images/re_mark.gif"/><?=$title?><?=$total_comment?>
                                                        <?
                                                    }else{
                                                        if($listRow->is_secret == "0"){
                                                            echo $title.$total_comment;
                                                        }else{
                                                            ?>
                                                            <img src="./images/lock.gif"/><?=$title?><?=$total_comment?>
                                                            <?
                                                        }
                                                    }
                                                    ?>
                                                </p>
                                                <p class="writer"><?=date("Y-m-d",$listRow->writetime)?> | <?=$listRow->name?> | <?=$listRow->access?></p>
                                            </a>
                                        </td>
                                        <td>
                                            <?
                                            $link_url_start="";
                                            $link_url_end="</a>";
                                            if($listRow = mysql_fetch_object($listRes)){
                                                if(mb_strlen($listRow->title) > 21){
                                                    $title=_strCut($listRow->title,21,5,$charset);
                                                }else{
                                                    $title=$listRow->title;
                                                }
                                                if($listRow->is_secret == 1){
                                                    $write_addr="./passwd_confirm.php?type=view&num=".$listRow->num."&view=1&board=".$boardname;
                                                    $link_url_start="<a href='./passwd_confirm.php?type=view&num=".$listRow->num."&view=1&board=".$boardname."'>";
                                                }else{
                                                    $write_addr="./board_view.php?num=".$listRow->num."&view=1&board=".$boardname;
                                                    $link_url_start="<a href='./board_view.php?num=".$listRow->num."&view=1&board=".$boardname."'>";
                                                }

                                                unset($total_comment);

                                                if($listRow->movie_url && $listRow->thumb_use=='N'){ //섬네일 직접 등록
                                                    $filename="/data/shopimages/board/".$boardname."/".$listRow->movie_image;
                                                    $link_url_start="<a class='popup-youtube' href='http://www.youtube.com/watch?v=".$listRow->movie_url."'>";
                                                }else if($listRow->movie_url && $listRow->thumb_use=='Y'){ //유튜브 섬네일 사용
                                                    $filename="https://img.youtube.com/vi/".$listRow->movie_url."/maxresdefault.jpg";
                                                    $link_url_start="<a class='popup-youtube' href='http://www.youtube.com/watch?v=".$listRow->movie_url."'>";
                                                }else{ //둘다 아닐 때는 기본 첨부이미지
                                                    if($listRow->filename){
                                                        $filename="../data/shopimages/board/".$boardname."/".$listRow->filename;
                                                        $filename = boards_thumb($filename, $listRow->filename, $boardname, $thumb_mobile);
                                                    }else{
                                                        $filename="../images/no_img.gif";
                                                    }
                                                }

                                                $movie_icon="";
                                                if($listRow->movie_url){
                                                    $movie_icon="<div style='position:absolute;top:50%;left:50%;width:50px;height:50px;margin-top:-25px;margin-left:-25px;'><img src='/board/images/icon_play.png' width='100%' alt= '' /></div>";
                                                }
                                                ?>
                                                <?=$link_url_start?>
                                                <div style="position:relative;margin-bottom:8px;border-left:1px solid #fff;background:url('<?=$filename?>') no-repeat;background-position:center;background-size:cover;font-size:0px;line-height:0%">
                                                    <img width="100%" src="/m/skin/basic/img/list_bg_dot.png" border="0" alt="" />
                                                    <?=$movie_icon?>
                                                </div>
                                                <?=$link_url_end?>

                                                <a href="<?=$write_addr?>">
                                                    <p class="title">
                                                        <? if($listRow->pos >= 1){ ?>
                                                            <img src="./images/re_mark.gif"/><?=$title?><?=$total_comment?>
                                                            <?
                                                        }else{
                                                            if($listRow->is_secret=="0"){
                                                                echo $title.$total_comment;
                                                            }else{
                                                                ?>
                                                                <img src="./images/lock.gif"/><?=$title?><?=$total_comment?>
                                                                <?
                                                            }
                                                        }
                                                        ?>
                                                    </p>
                                                    <p class="writer"><?=date("Y-m-d",$listRow->writetime)?> | <?=$listRow->name?> | <?=$listRow->access?></p>
                                                </a>
                                                <?
                                            }else{
                                                echo "";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>

                                <?
                            }
                            else if(strpos($boardType, "B")!==false) {
                                ?>
                                <!-- 블로그형 -->
                                <table width="100%">
                                    <tr>
                                        <td align="left">
                                            <p class="title">
                                                <?if($listRow->pos >= 1){?>
                                                    <img src="./images/re_mark.gif"/><?=$title?><?=$total_comment?>
                                                <?}else{
                                                    if($listRow->is_secret == "0"){
                                                        echo $title.$total_comment;
                                                    }else{
                                                        ?>
                                                        <img src="./images/lock.gif"/><?=$title?><?=$total_comment?>
                                                    <?	}
                                                }?>
                                            </p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right" style="padding-bottom:10px;">
                                            <span class="writer"><?=date("Y-m-d",$listRow->writetime)?><span class="hline">|</span><?=$listRow->name?><span class="hline">|</span> <?=$listRow->access?></span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center">
                                            <? if($listRow->filename!="") { ?>
                                                <?
                                                if($listRow->filename){
                                                    $filename="../data/shopimages/board/".$boardname."/".$listRow->filename;
                                                    $filename = boards_thumb($filename, $listRow->filename, $boardname, $thumb_mobile);
                                                }else{
                                                    $filename="../images/no_img.gif";
                                                }
                                                ?>
                                                <img width="80%" src="<?=$filename?>" style="margin:8px;">
                                            <? } ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center">
                                            <p style="margin:10px;" class="blog_conVeiw"><?=$listRow->content?></p>
                                        </td>
                                    </tr>
                                </table>

                                <!-- 댓글 -->

                                <?
                                if ($setup[use_comment]=="Y") {

                                    $com_query = "SELECT * FROM tblboardcomment WHERE parent='".$listRow->num."'";
                                    $com_result = @mysql_query($com_query,get_db_conn());
                                    $com_rows = @mysql_num_rows($com_result);

                                    unset($com_list);
                                    while($com_row = mysql_fetch_array($com_result)) {
                                        $com_list[count($com_list)] = $com_row;
                                    }
                                    mysql_free_result($com_result);
                                }

                                $secuCmtViewCnt=0;

                                //_pr($com_list);
                                for ($jjj=0;$jjj<count($com_list);$jjj++) {
                                    // 단일댓글
                                    if( $_ShopInfo->getMemid() == $com_list[$jjj][id] AND $setup[onlyCmt] == "Y" ) $secuCmtViewCnt++;
                                }

                                if ($setup[use_comment] == "Y" && $member[grant_comment]=="Y") {
                                    if( $secuCmtViewCnt == 0 OR strlen($_ShopInfo->id) > 0 ){
                                        $cmtFile = ($setup[fileYN] == "Y") ? "<input type=\"file\" name=\"img\" class=\"input\" style=\"width:98%;\" />" : ""; // 파일첨부

                                    }
                                }

                                @include ("./comment_write.php");

                                echo "<div class=\"reviewList\">";
                                for ($jjj=0;$jjj<count($com_list);$jjj++) {
                                    $c_num = $com_list[$jjj][num];
                                    $c_name = $com_list[$jjj][name];

                                    if($setup[use_comip]!="Y") {
                                        $c_uip=$com_list[$jjj][ip];
                                    }

                                    unset($comUserId);

                                    $c_writetime = getTimeFormat($com_list[$jjj][writetime]);
                                    $c_comment = nl2br(stripslashes($com_list[$jjj][comment]));
                                    $c_ip = $com_list[$jjj][ip];
                                    $c_comment = getStripHide($c_comment);

                                    // 비밀댓글
                                    $secuCmtView = true;
                                    if( $setup["secuCmt"] == "Y" ){
                                        $secuCmtView = false;
                                        if( $_ShopInfo->getMemid() == $com_list[$jjj][id] OR strlen($_ShopInfo->id) > 0 ){
                                            $secuCmtView = true;
                                        }
                                    }


                                    // 관리자 댓글의 댓글
                                    $adminComment = "";
                                    $adminCommSQL = "SELECT * FROM `tblboardcomment_admin` WHERE `board` = '".$board."' AND `board_no`= '".$listRow->num."' AND `comm_no`= '".$c_num."' ORDER BY `idx` ASC";
                                    $adminCommResult = mysql_query( $adminCommSQL );
                                    $adminCommNums = mysql_num_rows($adminCommResult);

                                    if($adminCommNums > 0) {
                                        $adminComment .= "<div class=\"adminComment\">";
                                        while( $adminCommRow = mysql_fetch_assoc ( $adminCommResult ) ) {
                                            $adminComment .= "<p><strong>관리자</strong> : ".$adminCommRow['comment']."</p>"; //(".$adminCommRow['reg_date'].")
                                        }
                                        $adminComment .= "</div>";
                                    }

                                    // 파일
                                    $filesname = DirPath.DataDir."shopimages/board/".$board."/".$com_list[$jjj]['file'];
                                    $filessize = @getimagesize($filesname);
                                    $c_comment_file_max_width = $setup[comment_width];
                                    $c_comment_file_width = ( $c_comment_file_max_width < $filessize[0] ) ? ($c_comment_file_max_width) : $filessize[0];
                                    $c_comment_file = ( strlen($com_list[$jjj]['file']) > 0 ) ? "<img src='".$filesname."' width='80' />" : "";

                                    $actionurl="";
                                    if(strlen($com_list[$jjj][id])>0 && $com_list[$jjj][id] == $_ShopInfo->getMemid()){
                                        $actionurl="comment_delete.php";
                                    }else{
                                        $actionurl="board_pwdconfirm.php";
                                    }
                                    //$actionurl="";
                                    if( $secuCmtView ) @include ("./comment_list.php");
                                }
                                echo "</div>";
                            }

                            //룩북형
                            else if(strpos($boardType, "C")!==false) {

                                if($listRow->filename){
                                    $filename="../data/shopimages/board/".$boardname."/".$listRow->filename;
                                    $filename = boards_thumb($filename, $listRow->filename, $boardname, $thumb_mobile);
                                }else{
                                    $filename="../images/no_img.gif";
                                }
                                ?>
                                <li style="overflow:hidden;" class="overView col-xs-6 col-sm-4 col-md-3" data-src="/data/shopimages/board/<?=$boardname."/".$listRow->filename?>" data-sub-html="<h4><?=$subject?></h4><p><?=strip_tags($listRow->content)?></p>">
                                    <div style="line-height:0%;background:url('<?=$filename?>');background-size:cover;background-position:center;"><img width="100%" src="/m/skin/basic/img/list_bg_dot.png" border="0" alt="" /></div>
                                </li>
                                <?
                            }
                        }

                        if(strpos($boardType, "C")!==false){
                            echo "</ul>";
                            echo "</div>";
                        }
                    }else{
                        ?>
                        <p class="err_td"><span>등록된 글이 없습니다.</span></p>
                        <?
                    }
                }
                ?>
            </div>
        </div>

        <div id="page_wrap">
            <?
            $pageLink = $_SERVER['PHP_SELF']."?page=%u&board=".$boardname; // 링크
            $pagePerBlock = ceil($totallistrowcount/$listnum);
            $paging = new pages($pageparam);
            $paging->_init(array('page'=>$curpage,'total_page'=>$pagePerBlock,'links'=>$pageLink,'pageblocks'=>3))->_solv();
            echo $paging->_result('fulltext');
            ?>
        </div>
    </div>

    <form name="cdelform" method="post" >
        <input type="hidden" name="pagetype" value="comment_delpop">
        <input type="hidden" name="board" value="<?=$board_name?>"/>
        <input type="hidden" name="num" value=""/>
        <input type="hidden" name="c_num" value=""/>
        <input type="hidden" name="mode" value="delete"/>
        <input type=hidden name="pageindex" value="<?=$curpage?>">
    </form>
<?}?>
    <script type="text/javascript">
        <!--
        function comment_delete(num,c_num,actionurl) {
            var _form = document.cdelform;
            _form.action = actionurl;
            _form.num.value=num;
            _form.c_num.value=c_num;

            if(_form.num.value =="" || _form.c_num.value==""){
                alert("필수 정보가 누락되었습니다.");
            }else{
                _form.submit();
            }
            //document.location.href="comment_delete.php?board=<?=$board?>&num="+num+"&c_num="+c_num;
            return;

        }
        //-->
    </SCRIPT>

<? include_once('footer.php'); ?>