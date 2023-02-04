<?
include_once("header.php");
include_once($Dir."lib/ext/func.php");

//솔루션타입 확인
$shopconfig = shopconfig();

if(strlen($_ShopInfo->getMemid())>0) {
    header("Location:mypage_usermodify.php");
    exit;
}

// 아이디 및 메일 체크 상태.
$idChk			= 0;
$mailChk		= 0;
$loginType		= ($_GET['loginType']) ? $_GET['loginType'] : $_POST['loginType'];
$type			= $_POST["type"];

// SNS API 연결 상태
if ($loginType == "naver") {
    // 네이버 로그인 API 에서 유저 정보 취득.
    $result		= $naver->getUserProfile();
    $result		= json_decode($result);
    $info		= $result->response;
	$name = $info->name;
} else if ($loginType == "kakao") {
    // 카카오 로그인 API 에서 유저 정보 취득.
    $result		= $kakao->getUserProfile();
    $result		= json_decode($result);
    $info		= $result->properties;
    $info->id	= $result->id;
	$nickname = $info->nickname;
	$name = $nickname;
} else if ($loginType == "facebook") {
    $userid		= $_SESSION[FB_LOGIN_SESS]['userid'];
    $username	= $_SESSION[FB_LOGIN_SESS]['username'];
} else if ($loginType == "apple") {
    $email		= $_SESSION["AP_LOGIN"]['email'];
    $userid		= $_SESSION["AP_LOGIN"]['userid'];
}
$ip = getenv("REMOTE_ADDR");

//회원가입 약관정보 가져오기
$sql="SELECT agreement,agreement2,privercy FROM tbldesign ";
$result=mysql_query($sql,get_db_conn());
$row=mysql_fetch_object($result);
$agreement=$row->agreement;
$agreement2=$row->agreement2;
$privercy_exp=@explode("=", $row->privercy);
$privercy=$privercy_exp[1];
mysql_free_result($result);

//일반회원 약관
if(strlen($agreement)==0) {
    $buffer="";
    $fp=fopen($Dir.AdminDir."agreement.txt","r");
    if($fp) {
        while (!feof($fp)) {
            $buffer.= fgets($fp, 1024);
        }
    }
    fclose($fp);
    $agreement=$buffer;
}

//도매회원 약관
if(strlen($agreement2)==0) {
    $buffer2="";
    $fp=fopen($Dir.AdminDir."agreement2.txt","r");
    if($fp) {
        while (!feof($fp)) {
            $buffer2.= fgets($fp, 1024);
        }
    }
    fclose($fp);
    $agreement2=$buffer2;
}

//개인정보취급방침
if(strlen($privercy)==0) {
    $buffer="";
    $fp=fopen($Dir.AdminDir."privercy2.txt","r");
    if($fp) {
        while (!feof($fp)) {
            $buffer.= fgets($fp, 1024);
        }
    }
    fclose($fp);
    $privercy=$buffer;
}

$reserve_join=(int)$_data->reserve_join;
$recom_ok=$_data->recom_ok;
$recom_url_ok=$_data->recom_url_ok;
$armemreserve=explode("", $_data->recom_memreserve_type);
$recom_memreserve=(int)$_data->recom_memreserve;
$recom_addreserve=(int)$_data->recom_addreserve;
$recom_limit=$_data->recom_limit;
if(strlen($recom_limit)==0) $recom_limit=9999999;
$group_code=$_data->group_code;
$member_addform=$_data->member_addform;

unset($adultauthid);
unset($adultauthpw);
if(strlen($_data->adultauth)>0) {
    $tempadult=explode("=",$_data->adultauth);
    if($tempadult[0]=="Y") {
        $adultauthid=$tempadult[1];
        $adultauthpw=$tempadult[2];
    }
}

$type=$_POST["type"];

$extconf = array();
if(false !== $eres = mysql_query("select * from extra_conf where type='memconf'",get_db_conn())){
    if(mysql_num_rows($eres)){
        while($erow = mysql_fetch_assoc($eres)){
            $extconf[$erow['name']] = $erow['value'];
        }
    }
}

unset($straddform);
unset($scriptform);
unset($stretc);
if(strlen($member_addform)>0) {
    $straddform.="<tr><td height=\"10\" colspan=\"4\"></td></tr>";
    $straddform.="<tr height=\"23\" bgcolor=\"#585858\">\n";
    $straddform.="	<td colspan=4 align=center style=\"font-size:11px;\"><font color=\"FFFFFF\" ><b>추가정보를 입력하세요.</b></font></td>\n";
    $straddform.="</tr>\n";
    $straddform.="<tr><td height=\"5\" colspan=\"4\"></td></tr>";
    
    $fieldarray=explode("=",$member_addform);
    $num=sizeof($fieldarray)/3;
    for($i=0;$i<$num;$i++) {
        if (substr($fieldarray[$i*3],-1,1)=="^") {
            $fieldarray[$i*3]="<font color=\"#F02800\"><b>＊</b></font><font color=\"#000000\"><b>".substr($fieldarray[$i*3],0,strlen($fieldarray[$i*3])-1)."</b></font>";
            $field_check[$i]="OK";
        } else {
            $fieldarray[$i*3]="<font color=\"#000000\"><b>".$fieldarray[$i*3]."</b></font>";
        }
        
        $stretc.="<tr>\n";
        $stretc.="	<td align=\"left\"  style=\"padding-left:14px\">".$fieldarray[$i*3]."</td>\n";
        
        $etcfield[$i]="<input type=text name=\"etc[".$i."]\" value=\"".$etc[$i]."\" size=\"".$fieldarray[$i*3+1]."\" maxlength=\"".$fieldarray[$i*3+2]."\" id=\"etc_".$i."\" class=\"input\" style=\"BACKGROUND-COLOR:#F7F7F7;\">";
        
        $stretc.="	<td colspan=\"3\">".$etcfield[$i]."</td>\n";
        $stretc.="</tr>\n";
        $stretc.="<tr>\n";
        $stretc.="	<td height=\"10\" colspan=\"4\" background=\"".$Dir."images/common/mbjoin/memberjoin_p_skin_line.gif\"></td>";
        $stretc.="</tr>\n";
        
        if ($field_check[$i]=="OK") {
            $scriptform.="try {\n";
            $scriptform.="	if (document.getElementById('etc_".$i."').value==0) {\n";
            $scriptform.="		alert('필수입력사항을 입력하세요.');\n";
            $scriptform.="		document.getElementById('etc_".$i."').focus();\n";
            $scriptform.="		return;\n";
            $scriptform.="	}\n";
            $scriptform.="} catch (e) {}\n";
        }
    }
    $straddform.=$stretc;
}

if($type=="insert") {
    $history="-1";
    $sslchecktype="";
    if($_POST["ssltype"]=="ssl" && strlen($_POST["sessid"])==64) {
        $sslchecktype="ssl";
        $history="-2";
    }
    if($sslchecktype=="ssl") {
        $secure_data=getSecureKeyData($_POST["sessid"]);
        if(!is_array($secure_data)) {
            echo "<html><head><title></title></head><body onload=\"alert('보안인증 정보가 잘못되었습니다.');history.go(".$history.");\"></body></html>";exit;
        }
        foreach($secure_data as $key=>$val) {
            ${$key}=$val;
        }
    } else {
        $id				= trim($_POST["id"]);
        $passwd1		= $_POST["passwd1"];
        $passwd2		= $_POST["passwd2"];
        $name			= trim($_POST["name"]);
        $email			= trim($_POST["email"]);
        $news_mail_yn	= $_POST["news_mail_yn"];
        $news_sms_yn	= $_POST["news_sms_yn"];
        $home_tel		= trim($_POST["home_tel"]);
        $mobile			= trim($_POST["mobile"]);
        $rec_id			= trim($_POST["rec_id"]);
        $etc			= $_POST["etc"];
        
        $birth			= trim($_POST["birth"]);
        $gender			= trim($_POST["gender"]);
        $mcode			= trim($_POST["mcode"]);
        
        $vDiscrNo		= trim($_POST["vDiscrNo"]);
        $uniqNo			= trim($_POST["uniqNo"]);
        $scitype		= trim($_POST["scitype"]);
        $sciReqNum		= trim($_POST["sciReqNum"]);
        
    }
    
    // 160314 sns 로그인으로 회원가입하는 경우.
    $nickname = "";
    if (!_empty($loginType)) {
        if ($loginType == "naver") {
            $id = $naver->getSocialId().$info->id;
 //           $name = $info->name;
        } else if ($loginType == "kakao") {
            $id = $kakao->getSocialId().$info->id;
            $nickname = $info->nickname;
//			$name = $nickname;
        } else if ($loginType == "facebook") {
            $id = $facebook->getSocialId().$userid;
//            $name = $username;
            unset($_SESSION[FB_LOGIN_SESS]);
        } else if ($loginType == "apple") {
            $id = $userid;
//            $name = $username;
            unset($_SESSION["AP_LOGIN"]);
        }
    }
    
    $onload="";
    
    for($i=0;$i<10;$i++) {
        if(strpos($etc[$i],"=")) {
            $onload="추가정보에 입력할 수 없는 문자가 포함되었습니다.";
            break;
        }
        if($i!=0) {
            $etcdata=$etcdata."=";
        }
        $etcdata=$etcdata.$etc[$i];
    }
    
    if($recom_ok=="Y" && strlen($rec_id)>0) {
        $sql = "SELECT COUNT(*) as cnt FROM tblmember WHERE id='".trim($rec_id)."' AND member_out!='Y' ";
        $rec_result = mysql_query($sql,get_db_conn());
        $rec_row = mysql_fetch_object($rec_result);
        $rec_num = $rec_row->cnt;
        mysql_free_result($rec_result);
        
        $rec_cnt=0;
        $sql = "SELECT rec_cnt FROM tblrecommendmanager WHERE rec_id='".trim($rec_id)."'";
        $rec_result = mysql_query($sql,get_db_conn());
        if($rec_row = mysql_fetch_object($rec_result)) {
            $rec_cnt = (int)$rec_row->rec_cnt;
        }
        mysql_free_result($rec_result);
    }
    
    if(_empty($onload)) {
        
        if (_empty($loginType)) {
            if(strlen(trim($id))==0) {
                $onload="아이디 입력이 잘못되었습니다.";
            } else if(!IsAlphaNumeric($id)) {
                $onload="아이디는 영문,숫자를 조합하여 4~12자 이내로 입력하셔야 합니다.";
            } else if(!eregi("(^[0-9a-zA-Z]{4,12}$)",$id)) {
                $onload="아이디는 영문,숫자를 조합하여 4~12자 이내로 입력하셔야 합니다.";
            } else if(strlen(trim($name))==0) {
                $onload="이름 입력이 잘못되었습니다.";
            } else if(strlen(trim($email))==0) {
                $onload="이메일을 입력하세요.";
            } else if(!ismail($email)) {
                $onload="이메일 입력이 잘못되었습니다.";
            } else if(strlen(trim($mobile))==0) {
                $onload="휴대전화를 입력하세요.";
            }
        }
        
        if($rec_num==0 && strlen($rec_id)!=0) {
            $onload="추천인 ID 입력이 잘못되었습니다.";
        }
        
        if(!$onload) {
            if (_empty($loginType)) {
                // 160331 사이트 회원가입일 경우에만 체크.
                if(!$onload) {
                    $sql = "SELECT id FROM tblmember WHERE id='".$id."' ";
                    $result=mysql_query($sql,get_db_conn());
                    if($row=mysql_fetch_object($result)) {
                        $onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
                    }
                    mysql_free_result($result);
                }
                if(!$onload) {
                    $sql = "SELECT id FROM tblmemberout WHERE id='".$id."' ";
                    $result=mysql_query($sql,get_db_conn());
                    if($row=mysql_fetch_object($result)) {
                        $onload="ID가 중복되었습니다.\\n\\n다른 아이디를 사용하시기 바랍니다.";
                    }
                    mysql_free_result($result);
                }
                if(!$onload) {
                    $sql = "SELECT email FROM tblmember WHERE email='".$email."' ";
                    $result=mysql_query($sql,get_db_conn());
                    if($row=mysql_fetch_object($result)) {
                        $onload="이메일이 중복되었습니다.\\n\\n다른 이메일을 사용하시기 바랍니다.";
                    }
                    mysql_free_result($result);
                }
            }
            
            if(!$onload) {
                
                if(!_empty($birth)){
                    $birth = str_replace("-", "", $birth);
                    $birth = preg_replace('/[^0-9]/','',$birth);
                }
                
                if($extconf['reqgender'] == 'Y' && _empty($gender)){
                    $onload = '성별을 필수 입력값 입니다.';
                }
                
                if(!$onload && $extconf['reqbirth'] == 'Y' && _empty($birth)){
//                    $onload = '생일은 필수 입력값 입니다.';
                }
            }
            
            if(!$onload) {
                //insert
                $date=date("YmdHis");
                
                if($news_mail_yn=="Y" && $news_sms_yn=="Y") {
                    $news_yn="Y";
                } else if($news_mail_yn=="Y") {
                    $news_yn="M";
                } else if($news_sms_yn=="Y") {
                    $news_yn="S";
                } else {
                    $news_yn="N";
                }
                if($_data->member_baro=="Y") $confirm_yn="N";
                else $confirm_yn="Y";
                
                /* 추천인 입력 */
                $url_cnt = 1;
                while($url_cnt > 0){
                    $tmpurlid = rand(10000,99999);
                    $sql = "SELECT count(1) cnt FROM tblmember WHERE url_id='".$tmpurlid."'";
                    $url_result = mysql_query($sql,get_db_conn());
                    if($url_row = mysql_fetch_object($url_result)) {
                        $url_cnt = (int)$url_row->cnt;
                    }
                    mysql_free_result($url_result);
                }
                $url_id = $tmpurlid;
                
                // 160330 어디에 사용하는지 잘 모르겠음. 원래 쿠키 방식으로 굽고 있음.
                $_SESSION['my_url_id']	= $url_id;
                $_SESSION['my_id']		= $id;
                $_SESSION['my_name']	= !_empty($name) ? $name : $nickname;
                if (!_empty($email)) {
                    $_SESSION['my_email']	= $email;
                }
                
                $sql = "INSERT tblmember SET ";
                $sql.= "id			= '".$id."', ";
                if (_empty($loginType)) {
                    $sql.= "passwd		= '".md5($passwd1)."', ";
                }
                $sql.= "name		= '".$name."', ";
                $sql.= "nickname	= '".$nickname."', ";
                $sql.= "email		= '".$email."', ";
                $sql.= "mobile		= '".$mobile."', ";
                $sql.= "news_yn		= '".$news_yn."', ";
                $sql.= "gender		= '".$gender."', ";
                $sql.= "birth		= '".$birth."', "; //
                // 본인인증
                if(!empty($vDiscrNo)) $sql.= "vDiscrNo			= '".$vDiscrNo."', ";
                $sql.= "uniqNo			= '".$uniqNo."', ";
                
                $sql.= "reserve		= '".$reserve_join."', ";
                $sql.= "joinip		= '".$ip."', ";
                $sql.= "ip			= '".$ip."', ";
                $sql.= "date		= '".$date."', ";
                $sql.= "confirm_yn	= '".$confirm_yn."', ";
                if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && strlen($rec_id)>0) {
                    $sql.= "rec_id	= '".$rec_id."', ";
                }
                if(strlen($group_code)>0) {
                    $sql.= "group_code='".$group_code."', ";
                }
                $sql.= "etcdata		= '".$etcdata."', ";
                $sql.= "loginType	= '".$loginType."', ";
                $sql.= "url_id		= '".$url_id."', ";
                $sql.= "devices		= 'P' ";
                
                $insert=mysql_query($sql,get_db_conn());
                if (mysql_errno()==0) {
                    
                    if ($reserve_join>0) {
                        $sql = "INSERT tblreserve SET ";
                        $sql.= "id			= '".$id."', ";
                        $sql.= "reserve		= ".$reserve_join.", ";
                        $sql.= "reserve_yn	= 'Y', ";
                        $sql.= "content		= '가입축하 적립금입니다. 감사합니다.', ";
                        $sql.= "orderdata	= '', ";
                        $sql.= "date		= '".date("YmdHis",time()-1)."' ";
                        $insert = mysql_query($sql,get_db_conn());
                        $_SESSION['join_reserve'] = 1;
                    }
                    
                    // 추천인 적립금
                    if($recom_ok=="Y" && $rec_num!=0 && $rec_cnt<$recom_limit && strlen($rec_id)>0) {
                        $arr = array();
                        $arr['recomMem'] = $rec_id; // 추천인 아이디
                        $arr['newMeme'] = $id; // 추천 받은 회원 아이디
                        recommandJoin( $arr );
                    }
                    
                    //쿠폰발생 (회원가입시 발급되는 쿠폰)
                    if($_data->coupon_ok=="Y") {
                        $date = date("YmdHis");
                        $sql = "SELECT coupon_code, date_start, date_end FROM tblcouponinfo ";
                        $sql.= "WHERE display='Y' AND issue_type='M' ";
                        $sql.= "AND (date_end>'".substr($date,0,10)."' OR date_end='')";
                        $result = mysql_query($sql,get_db_conn());
                        
                        $sql="INSERT INTO tblcouponissue (coupon_code,id,date_start,date_end,date) VALUES ";
                        $couponcnt ="";
                        $count=0;
                        
                        while($row = mysql_fetch_object($result)) {
                            if($row->date_start>0) {
                                $date_start=$row->date_start;
                                $date_end=$row->date_end;
                            } else {
                                $date_start = substr($date,0,10);
                                $date_end = date("Ymd",mktime(0,0,0,substr($date,4,2),substr($date,6,2)+abs($row->date_start),substr($date,0,4)))."23";
                            }
                            $sql.=" ('".$row->coupon_code."','".$id."','".$date_start."','".$date_end."','".$date."'),";
                            $couponcnt="'".$row->coupon_code."',";
                            $count++;
                        }
                        mysql_free_result($result);
                        if($count>0) {
                            $sql = substr($sql,0,-1);
                            mysql_query($sql,get_db_conn());
                            if(!mysql_errno()) {
                                $couponcnt = substr($couponcnt,0,-1);
                                $sql = "UPDATE tblcouponinfo SET issue_no=issue_no+1 ";
                                $sql.= "WHERE coupon_code IN (".$couponcnt.")";
                                mysql_query($sql,get_db_conn());
                                $_SESSION['join_coupon'] = 1;
                            }
                        }
                    }
                    
                    //가입메일 발송 처리
                    if(strlen($email)>0) {
                        SendJoinMail($_data->shopname, $_data->shopurl, $_data->design_mail, $_data->join_msg, $_data->info_email, $email, $name, $shopconfig->type);
                    }
                    
                    //가입 SMS 발송 처리
                    $sql = "SELECT * FROM tblsmsinfo WHERE (mem_join='Y' OR admin_join='Y') ";
                    $result= mysql_query($sql,get_db_conn());
                    if($row=mysql_fetch_object($result)) {
                        $sms_id=$row->id;
                        $sms_authkey=$row->authkey;
                        
                        $admin_join=$row->admin_join;
                        $mem_join=$row->mem_join;
                        $msg_mem_join=$row->msg_mem_join;
                        
                        $pattern=array("(\[ID\])","(\[NAME\])");
                        $replace=array($id,$name);
                        $msg_mem_join=preg_replace($pattern,$replace,$msg_mem_join);
                        $msg_mem_join=AddSlashes($msg_mem_join);
                        
                        $mem_join_msg = $row->mem_join_msg;
                        $mem_join_msg = preg_replace($pattern, $replace, $mem_join_msg);
                        $mem_join_msg = addslashes($mem_join_msg);
                        
                        //$smsmessage=$name."님이 ".$id."로 회원가입하셨습니다.";
                        $adminphone=$row->admin_tel;
                        if(strlen($row->subadmin1_tel)>8) $adminphone.=",".$row->subadmin1_tel;
                        if(strlen($row->subadmin2_tel)>8) $adminphone.=",".$row->subadmin2_tel;
                        if(strlen($row->subadmin3_tel)>8) $adminphone.=",".$row->subadmin3_tel;
                        
                        $fromtel=$row->return_tel;
                        mysql_free_result($result);
                        
                        $mobile=str_replace(" ","",$mobile);
                        $mobile=str_replace("-","",$mobile);
                        $adminphone=str_replace(" ","",$adminphone);
                        $adminphone=str_replace("-","",$adminphone);
                        
                        $etcmessage="회원가입 축하메세지(회원)";
                        $date=0;
                        if($mem_join=="Y" && strlen($mobile)>0) {
                            $temp=SendSMS($sms_id, $sms_authkey, $mobile, "", $fromtel, $date, $msg_mem_join, $etcmessage);
                        }
                        
                        if($row->sleep_time1!=$row->sleep_time2) {
                            $date="0";
                            $time = date("Hi");
                            if($row->sleep_time2<"12" && $time<=substr("0".$row->sleep_time2,-2)."59") $time+=2400;
                            if($row->sleep_time2<"12" && $row->sleep_time1>$row->sleep_time2) $row->sleep_time2+=24;
                            
                            if($time<substr("0".$row->sleep_time1,-2)."00" || $time>=substr("0".$row->sleep_time2,-2)."59") {
                                if($time<substr("0".$row->sleep_time1,-2)."00") $day = date("d");
                                else $day=date("d")+1;
                                $date = date("Y-m-d H:i:s",mktime($row->sleep_time1,0,0,date("m"),$day,date("Y")));
                            }
                        }
                        $etcmessage="회원가입 축하메세지(관리자)";
                        if($admin_join=="Y") {
                            $temp=SendSMS($sms_id, $sms_authkey, $adminphone, "", $fromtel, $date, $mem_join_msg, $etcmessage);
                        }
                    }
                    
					$template_id = "50050";
					$to = array($mobile);
					$vals = array("name"=>$name);
					sendTalkGroup($template_id,$to,$vals);
					
                    $_SESSION['join_yes'] = 1;
					
					//로그인 처리
					$authidkey = md5(uniqid(""));
					$_ShopInfo->setMemid($id);
					$_ShopInfo->setAuthidkey($authidkey);
					$_ShopInfo->setMemname($name);
					$_ShopInfo->setMemreserve(0);
					$_ShopInfo->setMememail($email);
					$_ShopInfo->Save();


					$sql = "UPDATE tblmember SET ";
					$sql.= "authidkey		= '".$authidkey."', ";
					if($passwd_type=="hash" || $passwd_type=="password" || $passwd_type=="old_password") {
						$sql.= "passwd		= '".md5($passwd)."', ";
					}
					$sql.= "ip				= '".getenv("REMOTE_ADDR")."', ";
					$sql.= "logindate		= '".date("YmdHis")."', ";
					$sql.= "logincnt		= logincnt+1 ";
					$sql.= "WHERE id='".$_ShopInfo->getMemid()."'";
					mysql_query($sql,get_db_conn());

					$loginday = date("Ymd");
					$sql = "SELECT id_list FROM tblshopcountday ";
					$sql.= "WHERE date='".$loginday."'";
					$result = mysql_query($sql,get_db_conn());
					if($row3 = mysql_fetch_object($result)){
						if(!strpos(" ".$row3->id_list,"".$_ShopInfo->getMemid()."")){
							$id_list=$row3->id_list.$_ShopInfo->getMemid()."";
							$sql = "UPDATE tblshopcountday SET id_list='".$id_list."',login_cnt=login_cnt+1 ";
							$sql.= "WHERE date='".$loginday."'";
							mysql_query($sql,get_db_conn());
						}
					} else {
						$id_list="".$_ShopInfo->getMemid()."";
						$sql = "INSERT INTO tblshopcountday (date,count,login_cnt,id_list) VALUES ('".$loginday."',1,1,'".$id_list."')";
						mysql_query($sql,get_db_conn());
					}


                    $URL = $Dir."app/main.php";
                    
                    echo "<html><head><title></title></head><body><script>location.replace('".$URL."');</script></body></html>";
                } else {
                    $onload="ID가 중복되었거나 회원등록 중 오류가 발생하였습니다.";
                }
            }
        }
    }
    if(strlen($onload)>0) {
        echo "<html><head><title></title></head><body onload=\"alert('".$onload."');history.go(".$history.")\"></body></html>";exit;
    }
}

if(strlen($news_mail_yn)==0) $news_mail_yn="Y";
if(strlen($news_sms_yn)==0) $news_sms_yn="Y";


//성별, 생년월일 필드값 상태

$ext_cont = array();
$esql = "select * from extra_conf where type='memconf'";
if(false !== $eres = mysql_query($esql,get_db_conn())){
    $erowcount = mysql_num_rows($eres);
    if($erowcount>0){
        while($erow = mysql_fetch_assoc($eres)){
            $ext_cont[$erow['name']] = $erow['value'];
        }
    }else{
        $ext_cont['reqgender']=$ext_cont['reqbirth']="H";
    }
}
?>
</HEAD>

<div id="content">
	<div class="h_area2">
		<h2>회원가입</h2>
		<a href="main.php" class="btn_home" rel="external"><span class="vc">홈</span></a>
		<a href="javascript:history.back()" class="btn_prev" rel="external"><span>이전</span></a>
	</div>

	<form name="form1" action="<?=$_SERVER[PHP_SELF]?>" method="post">
		<input type="hidden" name="type" value="" />
		<input type="hidden" name="idChk" value="<?=$idChk?>" />
		<input type="hidden" name="mailChk" value="<?=$mailChk?>" />
		<?if (strlen($loginType)>0) {?>
		<input type="hidden" name="loginType" value="<?=$loginType?>" />
		<? } ?>
		<input type="hidden" name="agreement" value="N" />
		<input type="hidden" name="privercy" value="N" />
		<? if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MJOIN"]=="Y"){ ?>
		<input type="hidden" name="shopurl" value="<?=getenv("HTTP_HOST")?>" />
		<? } ?>
		<? include ("member_join_form.php"); ?>
	</form>
</div>

<script type="text/javascript">
	<!--
	function IsMailCheck(email) {
		isMailChk = /^[^@ ]+@([a-zA-Z0-9\-]+\.)+([a-zA-Z0-9\-]{2}|net|com|gov|mil|org|edu|int)$/;
		if(isMailChk.test(email)) {
			return true;
		} else {
			return false;
		}
	}

	function chkCtyNo(obj) {
		if (obj.length == 14) {
			var calStr1 = "2345670892345", biVal = 0, tmpCal, restCal;

			for (i=0; i <= 12; i++) {
				if (obj.substring(i,i+1) == "-")
					tmpCal = 1
				else
					biVal = biVal + (parseFloat(obj.substring(i,i+1)) * parseFloat(calStr1.substring(i,i+1)));
			}

			restCal = 11 - (biVal % 11);

			if (restCal == 11) {
				restCal = 1;
			}

			if (restCal == 10) {
				restCal = 0;
			}

			if (restCal == parseFloat(obj.substring(13,14))) {
				return true;
			} else {
				return false;
			}
		}
	}

	function strnumkeyup2(field) {
		if (!isNumber(field.value)) {
			alert("숫자만 입력하세요.");
			field.value=strLenCnt(field.value,field.value.length - 1);
			field.focus();
			return;
		}
		if (field.name == "resno1") {
			if (field.value.length == 6) {
				form1.resno2.focus();
			}
		}
	}

	function CheckFormData(data) {
		var numstr = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		var thischar;
		var count = 0;
		data = data.toUpperCase( data )

		for ( var i=0; i < data.length; i++ ) {
			thischar = data.substring(i, i+1 );
			if ( numstr.indexOf( thischar ) != -1 )
				count++;
		}
		if ( count == data.length )
			return(true);
		else
			return(false);
	}

	function AdultCheck(resno1,resno2) {
		gbn=resno2.substring(0,1);
		date=new Date();
		if(gbn=="3" || gbn=="4") {
			year="20"+resno1.substring(0,2);
		} else {
			year="19"+resno1.substring(0,2);
		}
		age=parseInt(date.getYear())-parseInt(year);
	}


	function CheckForm() {
		var form = document.form1,
			gendercheck = "<?=$extconf['reqgender']?>",
			birthcheck  = "<?=$extconf['reqbirth']?>",
			gendercount = 0;
		
		gendercheck = "Y";

		if(gendercheck == "Y"){
			for(var i=0;i<form.gender.length;i++){
				if(form.gender[i].checked==true){
					gendercount++;
				}
			}
		}


		//이용약관
			var chk1=document.form1.policy.checked;
			if(!chk1){
				alert("이용약관에 동의하셔야 회원가입이 가능합니다.");
				form.policy.focus();
				return;
			}

		//개인정보 취급방침
			var chk2=document.form1.protect.checked;
			if(!chk2){
				alert("개인정보 취급방침에 동의하셔야 회원가입이 가능합니다.");
				form.protect.focus();
				return;
			}

		//개인정보수집 및 이용
			var chk3=document.form1.protectuse.checked;
			if(!chk3){
				alert("개인정보수집 및 이용 동의하셔야 회원가입이 가능합니다.");
				form.protectuse.focus();
				return;
			}


	<? if (!$loginType) { /* SNS에서 넘어 오지 않았을 때 */ ?>
		if(form.id.value.length==0) {
			alert("아이디를 입력하세요."); form.id.focus(); return;
		}
		if(form.id.value.length<4 || form.id.value.length>12) {
			alert("아이디는 4자 이상 12자 이하로 입력하셔야 합니다."); form.id.focus(); return;
		}
		if (CheckFormData(form.id.value)==false) {
			alert("ID는 영문, 숫자를 조합하여 4~12자 이내로 등록이 가능합니다."); form.id.focus(); return;
		}
		if(form.idChk.value=="0") {
			alert("아이디 중복 체크를 하셔야 합니다!");
			idcheck();
			return;
		}
		if(form.passwd1.value.length==0) {
			alert("비밀번호를 입력하세요."); form.passwd1.focus(); return;
		}
		if(form.passwd1.value!=form.passwd2.value) {
			alert("비밀번호가 일치하지 않습니다."); form.passwd2.focus(); return;
		}
		if(form.email.value.length==0) {
			alert("이메일을 입력하세요."); form.email.focus(); return;
		}
		if(!IsMailCheck(form.email.value)) {
			alert("이메일 형식이 맞지않습니다.\n\n확인하신 후 다시 입력하세요."); form.email.focus(); return;
		}
		if(form.mailChk.value=="0") {
			alert("이메일 중복 체크를 하셔야 합니다!");
			mailcheck();
			return;
		}
	<? } /* SNS에서 넘어 오지 않았을 때 */ ?>

		if(form.name.value.length==0) {
			alert("고객님의 이름을 입력하세요."); form.name.focus(); return;
		}
		if(form.name.value.length>10) {
			alert("이름은 한글 5자, 영문 10자 이내로 입력하셔야 합니다."); form.name.focus(); return;
		}
		if(form.mobile.value.length==0) {
			alert("휴대전화를 입력하세요."); form.mobile.focus(); return;
		}
		if(gendercheck == "Y" && gendercount <= 0){
			alert("성별을 선택하세요");form.gender.value.focus();return;
		}
		if(birthcheck == "Y" && form.birth.value==""){
//			alert("생년월일을 입력하세요");form.birth.value.focus();return;
		}

	<?=$scriptform?>

		form.type.value="insert";

	<?if($_data->ssl_type=="Y" && strlen($_data->ssl_domain)>0 && strlen($_data->ssl_port)>0 && $_data->ssl_pagelist["MJOIN"]=="Y") {?>
		form.action='https://<?=$_data->ssl_domain?><?=($_data->ssl_port!="443"?":".$_data->ssl_port:"")?>/<?=RootPath.SecureDir?>member_join.php';
	<?}?>
		if(confirm("회원가입을 하겠습니까?")) {
			form.submit();
		} else {
			return;
		}
	}

	function f_addr_search(form,post,addr,gbn) {
		window.open("./addr_search.php?form="+form+"&post="+post+"&addr="+addr+"&gbn="+gbn,"f_post","resizable=yes,scrollbars=yes,x=100,y=200,width=370,height=250");
	}

	function idcheck() {
		var _form = document.form1;
		if(_form.id.value.length ==0){
			alert("아이디를 입력하세요");
			_form.id.focus();
		}else{
			//window.open("./id_check.php?id="+document.form1.id.value,"","");

			$('#show_contents').html("");
			$.post('id_check.php?id='+_form.id.value, function(data){
				$('#show_contents').html(data);
			});

			setTimeout(function(){
				$('#wrap_layer_popup').dialog({
					create:function(){
						$(this).parent().css({position:"fixed"});
					},
					title: '아이디 중복체크',
					modal: true,
					width: '90%',
					height: 'auto'
				});
			},200);
		}
	}

	function mailcheck() {
		var _form = document.form1;
		if(!IsMailCheck(_form.email.value)) {
			alert("이메일 형식이 맞지않습니다.\n확인 후 다시 입력하세요.");
			_form.mailChk.value="0";
			_form.email.focus();
			return;
		}else{
			//window.open("./mailcheck.php?email="+document.form1.email.value,"","");

			$('#show_contents').html("");
			$.post('mailcheck.php?email='+_form.email.value, function(data){
				$('#show_contents').html(data);
			});

			setTimeout(function(){
				$('#wrap_layer_popup').dialog({
					create:function(){
						$(this).parent().css({position:"fixed"});
					},
					title: '이메일 중복체크',
					modal: true,
					width: '90%',
					height: 'auto'
				});
			},200);
		}
	}

	//약관보기
	$(document).ready(function() {
		$("#gnb_button").hide();
		$("#prsearch").hide();
		$("#basket").hide();
		$(".viewPolicyBtn").on("click", function() {
			$("#policyView").show();
		});
		$("#policyView .viewCloseBtn").on("click", function() {
			$("#policyView").hide();
		});

		$(".viewProtectBtn").on("click", function() {
			$("#ProtectView").show();
		});
		$("#ProtectView .viewCloseBtn").on("click", function() {
			$("#ProtectView").hide();
		});

		$(".viewprotectUseBtn").on("click", function() {
			$("#protectUseView").show();
		});
		$("#protectUseView .viewCloseBtn").on("click", function() {
			$("#protectUseView").hide();
		});
	});
	//-->
</script>

<?// include_once('footer.php'); ?>