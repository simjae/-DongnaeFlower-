<?
    /* ============================================================================== */
    /* =   PAGE : 에스크로 상태 변경 PAGE                                           = */
    /* = -------------------------------------------------------------------------- = */
    /* =   아래의 ※ 주의 ※ 부분을 꼭 참고하시어 연동을 진행하시기 바랍니다.       = */
    /* = -------------------------------------------------------------------------- = */
    /* =   연동시 오류가 발생하는 경우 아래의 주소로 접속하셔서 확인하시기 바랍니다.= */
    /* =   접속 주소 : http://kcp.co.kr/technique.requestcode.do                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
    <title>*** KCP [AX-HUB Version] ***</title>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
    <link href="../sample/css/style.css" rel="stylesheet" type="text/css"/>

    <script language="javascript">
    function  jsf__go_mod( form )
    {
        if(form.mod_method.value=="mod_method_not_sel")
        {
          alert( "거래수단을 선택하십시오.");
          return false;
        }
        if(form.mod_type.value=="mod_type_not_sel")
        {
          alert( "취소 구분을 선택하십시오.");
          return false;
        }
        else if ( form.tno.value.length < 14 )
        {
            alert( "KCP 거래 번호를 입력하세요." );
            form.tno.focus();
            form.tno.select();
            return false;
        }
        /* 입력데이터 유효성 검증 Start */
        else if (form.mod_method.value == "VCNT" && form.mod_account.value=="")
        {
            alert( "환불 수취 계좌번호를 입력하세요." );
            form.mod_account.focus();
            form.mod_account.select();
            return false;
        }
        else if (form.mod_method.value == "VCNT" && form.mod_depositor.value=="")
        {
            alert( "환불 수취 계좌주명을 입력하세요." );
            form.mod_depositor.focus();
            form.mod_depositor.select();
            return false;
        }
        else if (form.mod_method.value == "VCNT" && form.mod_bankcode.value=="mod_bankcode_not_sel")
        {
            alert( "환불 수취 은행코드를 선택해 주세요." );
            return false;
        }
        /* 입력데이터 유효성 검증 End */
        return true;
    }

    function type_chk( form )
    {
        /* mod_type 초기화 */
        document.getElementById("type_R").style.display = "none";

        /* 신용카드 : 취소 */
        /* 계좌이체 : 취소 */
        if (form.mod_method.value == "CARD" || form.mod_method.value == "ACNT")
        {
            form.sub_mod_type.value = "STSC";
            form.mod_sub_type.value = "MDSC03";

            document.getElementById("type_R").style.display = "none";
        }

        /* 가상계좌 : 환불 , 부분환불 */
        if (form.mod_method.value == "VCNT")
        {
            form.sub_mod_type.value = "STHD";
            form.mod_sub_type.value = "MDSC00";

            document.getElementById("type_R").style.display = "block";
        }
    }

    </script>
</head>

<body>

    <div id="sample_wrap">

    <form name="mod_escrow_form" method="post" action="pp_cli_hub.php">

                 <!-- 타이틀 Start-->
                    <h1>[취소요청] <span>이 페이지는 에스크로 구매확인후 취소(환불)하는 샘플(예시) 페이지입니다.</span></h1>
                 <!-- 타이틀 End -->

                    <!-- 상단 테이블 Start -->
                    <div class="sample">
                    <p>
                    소스 수정시 소스 안에 <span>※ 주의 ※</span>표시가 포함된 문장은 가맹점의 상황에 맞게 적절히 수정<br/>
                    적용하시기 바랍니다.<br/>
                    <span>이 페이지는 에스크로 구매확인후 취소(환불)을 요청하는 페이지 입니다.</span><br/>
                    결제가 승인되면 결과값으로 KCP 거래번호(tno)값을 받을 수 있습니다.<br/>
                    가맹점에서는 이 KCP 거래번호(tno)값으로 에스크로 상태변경 요청 할 수 있습니다.
                    </p>
                    <!-- 상단 테이블 End -->

                <!-- 취소 요청 정보 입력 테이블 Start -->
                    <h2>&sdot; 에스크로 구매확인 후 취소 정보</h2>
                    <table class="tbl" cellpadding="0" cellspacing="0">

                    <!-- 취소(환불) 결제 수단 -->
                    <tr>
                        <th>취소(환불) 결제 수단</th>
                        <td>
                          <select name="mod_method" onChange="javascript:type_chk(this.form);">
                            <option value="mod_method_not_sel" selected>선택하십시오</option>
                            <option value="CARD">신용카드 취소</option>
                            <option value="ACNT">계좌이체 취소</option>
                            <option value="VCNT">가상계좌 환불</option>
                          </select>
                        </td>
                    </tr>
                    <!-- Input : 결제된 건의 거래번호(14 byte) 입력 -->
                    <tr>
                        <th>KCP 거래번호</th>
                        <td><input type="text" name="tno" value=""  class="w200" maxlength="14"/></td>
                    </tr>
                     <!-- Input : 변경 사유(mod_desc) 입력 -->
                    <tr>
                        <th>변경 사유</th>
                        <td><input type="text" name="mod_desc" value="" class="w200" maxlength="50"/></td>
                    </tr>
                    </table>

                <div id="type_R" style="display:none">
                    <table class="tbl" cellpadding="0" cellspacing="0">
                     <!-- Input : 환불 계좌주명 입력 -->
                    <tr>
                        <th>환불 계좌주명</th>
                        <td>
                            <input type='text' name='mod_depositor' class='w200' value='' size='20' maxlength='20'>
                        </td>
                    </tr>
                     <!-- Input : 환불 계좌번호 입력 -->
                    <tr>
                        <th>환불 계좌 번호</th>
                        <td>
                            <input type='text' name='mod_account' class='w200' value='' size='20' maxlength='20'>
                        </td>
                    </tr>
                     <!-- Input : 환불 은행코드 입력 -->
                    <tr>
                        <th>환불은행코드</th>
                        <td>
                            <select name="mod_bankcode">
                                <option value="mod_bankcode_not_sel" selected>선택</option>
                                <option value="BK39">경남은행</option>
                                <option value="BK03">기업은행</option>
                                <option value="BK32">부산은행</option>
                                <option value="BK07">수협중앙회</option>
                                <option value="BK48">신협</option>
                                <option value="BK71">우체국</option>
                                <option value="BK23">제일은행</option>
                                <option value="BK06">주택은행</option>
                                <option value="BK81">하나은행</option>
                                <option value="BK34">광주은행</option>
                                <option value="BK11">농협중앙회</option>
                                <option value="BK02">산업은행</option>
                                <option value="BK53">시티은행</option>
                                <option value="BK05">외환은행</option>
                                <option value="BK09">장기신용</option>
                                <option value="BK35">제주은행</option>
                                <option value="BK16">축협중앙회</option>
                                <option value="BK27">한미은행</option>
                                <option value="BK04">국민은행</option>
                                <option value="BK31">대구은행</option>
                                <option value="BK25">서울은행</option>
                                <option value="BK26">신한은행</option>
                                <option value="BK20">우리은행</option>
                                <option value="BK37">전북은행</option>
                                <option value="BK21">조흥은행</option>
                                <option value="BK83">평화은행</option>
                            </select>
                        </td>
                    </tr>
                </table>
                </div>

                <!-- 에스크로 상태변경 요청/처음으로 -->
                    <!-- 변경 버튼 테이블 Start -->
                    <div class="btnset">
                    <input name="" type="submit" class="submit" value="변경요청" onclick="return jsf__go_mod(this.form);" alt="에스크로 구매확인을 요청합니다"/>
					<a href="../index.html" class="home">처음으로</a>
                    </div>
                    <!-- 변경 버튼 테이블 End -->
                </div>
            <div class="footer">
                Copyright (c) KCP INC. All Rights reserved.
            </div>
        </table>
<?
    /* ============================================================================== */
    /* =   1-1. 취소 요청 필수 정보 설정                                            = */
    /* = -------------------------------------------------------------------------- = */
    /* =   ※ 필수 - 반드시 필요한 정보입니다.                                      = */
    /* = ---------------------------------------------------------------------------= */
?>
        <input type="hidden" name="req_tx"          value="mod_escrow" />
        <input type="hidden" name="mod_type"        value="STE9" />
        <input type="hidden" name="mod_sub_type"    value="" />
        <input type="hidden" name="sub_mod_type"    value="" />
<?
    /* = -------------------------------------------------------------------------- = */
    /* =   1. 취소 요청 정보 END                                                    = */
    /* ============================================================================== */
?>
    </form>
</div>
</body>
</html>

