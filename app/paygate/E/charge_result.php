<?php
$Dir = "../../../";
include_once( $Dir."lib/init.php" );
include_once( $Dir."lib/lib.php" );


//$TID = $nicepay->m_ResultData['TID'];
//$Amt = $nicepay->m_ResultData['Amt'];
//$PayMethod = $nicepay->m_ResultData['PayMethod'];
//$MallUserID = $nicepay->m_ResultData['MallUserID'];
//$GoodsName = $nicepay->m_ResultData['GoodsName'];
//$BuyerName = $nicepay->m_ResultData['BuyerName'];
//$BuyerTel = $nicepay->m_ResultData['BuyerTel'];
//$BuyerEmail = $nicepay->m_ResultData['BuyerEmail'];
//$ResultCode = $nicepay->m_ResultData['ResultCode'];
//$ResultMsg = $nicepay->m_ResultData['ResultMsg'];
//$DstAddr = $nicepay->m_ResultData['DstAddr'];
//$VbankBankName = $nicepay->m_ResultData['VbankBankName'];
//$VbankNum = $nicepay->m_ResultData['VbankNum'];
//$VbankExpDate = $nicepay->m_ResultData['VbankExpDate'];
//$CardQuota = $nicepay->m_ResultData['CardQuota'];
//$Moid = $nicepay->m_ResultData['Moid'];
//$AuthCode = $nicepay->m_ResultData['AuthCode'];
//$CardName = $nicepay->m_ResultData['CardName'];
$TID = "11111111111";
$Amt = "10320";
$PayMethod = "C";
$MallUserID = "dededed";
$GoodsName = "ffff";
$BuyerName = "kss";
$BuyerTel = "01000001111";
$BuyerEmail = "sum@0000";
$ResultCode = "0000";
$ResultMsg = "message";
$DstAddr = "addraa";
$VbankBankName = "";
$VbankNum = "kss";
$VbankExpDate = "2021";
$CardQuota = "cq1";
$Moid = "2021040812292111849A";
$AuthCode = "acc";
$CardName = "cn";
$paySuccess = true;
$pay_mod = "";

if ( 0 < strlen( RootPath ) )
{
    $hostscript = getenv( "HTTP_HOST" ).getenv( "SCRIPT_NAME" );
    $pathnum = @strpos( @$hostscript, @RootPath );
    $shopurl = substr( $hostscript, 0, $pathnum ).RootPath;
}
else
{
    $shopurl = getenv( "HTTP_HOST" )."/";
}
$return_host = getenv( "HTTP_HOST" );
$return_script = str_replace( getenv( "HTTP_HOST" ), "", $shopurl )."app/payprocess.php";
$return_resurl = "/app/payresult.php?ordercode=".$Moid;

$isreload = false;
$tblname = "";
$sql = "SELECT * FROM tblpordercode WHERE ordercode='".$Moid."' ";
$result = mysql_query( $sql, get_db_conn( ) );
if ( $row = mysql_fetch_object( $result ) )
{
    $paymethod = $row->paymethod;
    if ( preg_match( "/^(C|P)$/", $paymethod ) )
    {
        $tblname = "tblpcardlog";
    }
    else if ( preg_match( "/^(O|Q)$/", $paymethod ) )
    {
        $tblname = "tblpvirtuallog";
    }
    else if ( $paymethod == "M" )
    {
        $tblname = "tblpmobilelog";
    }
    else if ( $paymethod == "V" )
    {
        $tblname = "tblptranslog";
    }
}
mysql_free_result( $result );
if ( 0 < strlen( $tblname ) )
{
    $sql = "SELECT * FROM ".$tblname." WHERE ordercode='".$Moid."' ";
    $result = mysql_query( $sql, get_db_conn( ) );
    if ( $row = mysql_fetch_object( $result ) )
    {
        $isreload = true;
        $pay_data = $row->pay_data;
        $Amt = $row->price;
        if ( $row->ok == "Y" )
        {
            $PAY_GLAG = "0000";
            $DELI_GBN = "N";
        }
        else if ( $row->ok == "N" )
        {
            $PAY_FLAG = "9999";
            $DELI_GBN = "C";
        }
        if ( preg_match( "/^(C|P)$/", $paymethod ) )
        {
            $PAY_AUTH_NO = "00000000";
        }
    }
    mysql_free_result( $result );
}
$originMoney = "";
$payState = true;
if ( 0 < strlen( $Moid ) && 0 < strlen( $Amt ) )
{
    $verifySql = "SELECT price FROM tblorderinfotemp WHERE ordercode = '".$Moid."' ";
    if ( false !== ( $verifyRes = mysql_query( $verifySql, get_db_conn( ) ) ) )
    {
        $originMoney = mysql_result( $verifyRes, 0, 0 );
        if ( $originMoney != $Amt )
        {
            $payState = false;
        }
    }
    else
    {
        $payState = false;
    }
}
else
{
    $payState = false;
}
$returnMsg = "";
if ( $isreload != true && $payState == true )
{
    if ( $paySuccess == true )
    {
        $date = date( "YmdHis" );
        $PAY_FLAG = "0000";
        $DELI_GBN = "N";
        $MSG1 = $ResultMsg;
        $pay_data = $ResultMsg;
        $ok = "Y";
        $noinf = "N";
        switch ( $PayMethod )
        {
            case "CARD" :
                $tblname = "tblpcardlog";
                $paymethod = "C";
                if ( $pay_mod == "Y" )
                {
                    $paymethod = "P";
                }
                $PAY_AUTH_NO = $AuthCode;
                $MSG1 = "정상승인 - 승인번호 : ".$PAY_AUTH_NO;
                $pay_data = "승인번호 : ".$PAY_AUTH_NO."";
                $card_name = $CardName;
                break;
            case "BANK" :
                $tblname = "tblptranslog";
                $paymethod = "V";
                $PAY_AUTH_NO = "";
                $card_name = "";
                $noinf = "";
                $quota = "";
                break;
            case "CELLPHONE" :
                $tblname = "tblpmobilelog";
                $paymethod = "M";
                $PAY_AUTH_NO = "";
                $card_name = "";
                $noinf = "";
                $quota = "";
                break;
            case "VBANK" :
				$tblname = "tblpvirtuallog";
				$paymethod = "O";
				if ( $pay_mod == "Y" )
				{
					$paymethod = "Q";
				}
				$PAY_AUTH_NO = "";
				$card_name = "";
				$noinf = "";
				$quota = "";
				$pay_data = $VbankBankName." ".$VbankNum;
				break;
        }
//        $ok = "M";
        $sql = "INSERT INTO tblpordercode VALUES ('".$Moid."','".$paymethod."') ";
        if ( false !== mysql_query( $sql, get_db_conn( ) ) )
        {
            $sql = "INSERT ".$tblname." SET ";
            $sql .= "ordercode		= '".$Moid."', ";
            $sql .= "trans_code		= '".$TID."', ";
            $sql .= "pay_data		= '".$pay_data."', ";
            $sql .= "pgtype			= 'E', ";
            $sql .= "ok				= '".$ok."', ";
            $sql .= "okdate			= '".$date."', ";
            $sql .= "price			= '".$Amt."', ";
            if ( $use_pay_method == "100000000000" )
            {
                $sql .= "status			= 'N', ";
                $sql .= "paymethod		= '".$paymethod."', ";
                $sql .= "edidate			= '".$date."', ";
                $sql .= "cardname		= '".$card_name."', ";
                $sql .= "noinf			= '".$noinf."', ";
                $sql .= "quota			= '".$quota."', ";
            }
            else if ( $use_pay_method == "010000000000" )
            {
                $sql .= "bank_name		= '".$bank_name."', ";
            }
            else
            {
                if ( $use_pay_method == "001000000000" )
                {
                    $sql .= "status			= 'N', ";
                    $sql .= "paymethod		= '".$paymethod."', ";
                    $sql .= "sender_name		= '".$BuyerName."', ";
                    $sql .= "account			= '".$VbankNum."', ";
                }
                else
                {
                }
            }
            $sql .= "ip				= '".getenv( "REMOTE_ADDR" )."', ";
            $sql .= "goodname		= '".$GoodsName."', ";
            $sql .= "msg				= '".$MSG1."' ";
            if ( false !== mysql_query( $sql, get_db_conn( ) ) )
            {
                $returnMsg = "OK";
            }
            else
            {
                $returnMsg = "FAIL";
            }
        }
        else
        {
            $returnMsg = "FAIL";
        }
    }
    else
    {
        $PAY_FLAG = "9999";
        $DELI_GBN = "C";
        $MSG1 = $ResultMsg;
        $PAY_AUTH_NO = "";
        $pay_data = $ResultMsg;
        switch ( $PayMethod )
        {
            case "CARD" :
                $tblname = "tblpcardlog";
                $paymethod = "C";
                if ( $pay_mod == "Y" )
                {
                    $paymethod = "P";
                }
                $card_name = $CardName;
                break;
            case "BANK" :
                $tblname = "tblptranslog";
                $paymethod = "V";
                $card_name = "";
                $noinf = "";
                $quota = "";
                break;
            case "CELLPHONE" :
                $tblname = "tblpmobilelog";
                $paymethod = "M";
                $card_name = "";
                $noinf = "";
                $quota = "";
                break;
            case "VBANK" :
				$tblname = "tblpvirtuallog";
				$paymethod = "O";
				if ( $pay_mod == "Y" )
				{
					$paymethod = "Q";
				}
				$card_name = "";
				$noinf = "";
				$quota = "";
				break;
        }
        $sql = "INSERT INTO tblpordercode VALUES ('".$Moid."','".$paymethod."') ";
        mysql_query( $sql, get_db_conn( ) );
        $sql = "INSERT ".$tblname." SET ";
        $sql .= "ordercode		= '".$Moid."', ";
        $sql .= "trans_code		= '".$TID."', ";
        $sql .= "pay_data		= 'ERROR', ";
        $sql .= "pgtype			= 'E', ";
        $sql .= "ok				= 'N', ";
        $sql .= "okdate			= '".$date."', ";
        $sql .= "price			= '".$Amt."', ";
        if ( $use_pay_method == "100000000000" )
        {
            $sql .= "status			= 'N', ";
            $sql .= "paymethod		= '".$paymethod."', ";
            $sql .= "edidate			= '".$date."', ";
            $sql .= "cardname		= '".$card_name."', ";
            $sql .= "noinf			= '".$noinf."', ";
            $sql .= "quota			= '".$quota."', ";
        }
        else if ( $use_pay_method == "010000000000" )
        {
            $sql .= "bank_name		= '".$bank_name."', ";
        }
        else
        {
            if ( $use_pay_method == "001000000000" )
            {
                $sql .= "status			= 'N', ";
                $sql .= "paymethod		= '".$paymethod."', ";
                $sql .= "sender_name		= '".$buyr_name."', ";
                $sql .= "account			= '".$VbankNum."', ";
            }
            else
            {
            }
        }
        $sql .= "ip				= '".getenv( "REMOTE_ADDR" )."', ";
        $sql .= "goodname		= '".$good_name."', ";
        $sql .= "msg				= '".$MSG1."' ";
        if ( false !== mysql_query( $sql, get_db_conn( ) ) )
        {
            $returnMsg = "OK";
        }
        else
        {
            $returnMsg = "FAIL";
        }
    }
    $return_data = "ordercode=".$Moid."&real_price=".$Amt."&pay_data={$pay_data}&pay_flag={$PAY_FLAG}&pay_auth_no={$TID}&deli_gbn={$DELI_GBN}&message={$MSG1}";
    $return_data2 = ereg_replace( "'", "", $return_data );
    $sql = "INSERT INTO tblreturndata VALUES ('".$Moid."','".date( "YmdHis" )."','".$return_data2."') ";
    mysql_query( $sql, get_db_conn( ) );
    $temp = sendsocketpost( $return_host, $return_script, $return_data );
    if ( $temp != "ok" )
    {
        if ( 0 < strlen( AdminMail ) )
        {
            @mail( @AdminMail, @"[PG] ".$Moid." 결제정보 업데이트 오류", @"{$return_host}<br>{$return_script}<br>{$return_data}" );
        }
        mysql_query( "insert into tblreturndata values ('".$Moid."_test','".date( "YmdHis" )."','소켓통신실패')", get_db_conn( ) );
    }
    else
    {
        mysql_query( "DELETE FROM tblreturndata WHERE ordercode='".$Moid."'", get_db_conn( ) );
        mysql_query( "insert into tblreturndata values ('".$Moid."_test','".date( "YmdHis" )."','소켓통신성공')", get_db_conn( ) );
    }
}
echo "<script>location.href='".$return_resurl."';</script>";

?>