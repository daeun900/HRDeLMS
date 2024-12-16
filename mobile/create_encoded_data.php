<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);
$lectureCode = Replace_Check($data['lectureCode']);

session_start();

// 응답 초기화
$response = [];

$siteCode = $CheckPlus_sitecode; // NICE로부터 부여받은 사이트 코드
$sitePwd = $CheckPlus_sitepasswd; // NICE로부터 부여받은 사이트 패스워드

$encodePath = $Auth_Mobile_path;

$authType = ""; // 없으면 기본 선택화면, X: 공인인증서, M: 핸드폰, C: 카드

$popGubun 	= "N"; // Y : 취소버튼 있음 / N : 취소버튼 없음
$customize 	= "Mobile"; // 없으면 기본 웹페이지 / Mobile : 모바일페이지

$gender = ""; // 없으면 기본 선택화면, 0: 여자, 1: 남자

$reqSeq = $lectureCode."_".date('YmdHis');     // 요청 번호, 이는 성공/실패후에 같은 값으로 되돌려주게 되므로

// CheckPlus(본인인증) 처리 후, 결과 데이타를 리턴 받기위해 다음예제와 같이 http부터 입력합니다.
// 리턴url은 인증 전 인증페이지를 호출하기 전 url과 동일해야 합니다. ex) 인증 전 url : http://www.~ 리턴 url : http://www.~
$returnUrl = $SiteURL."/mobile/checkplus_success.php";	// 성공시 이동될 URL
$errorUrl = $MobileSiteURL."/checkplus_fail.php";		// 실패시 이동될 URL

// reqseq값은 성공페이지로 갈 경우 검증을 위하여 세션에 담아둔다.
$_SESSION["REQ_SEQ"] = $reqSeq;

// 입력될 plain 데이타를 만든다.
$plainData = "7:REQ_SEQ" . strlen($reqSeq) . ":" . $reqSeq .
                "8:SITECODE" . strlen($siteCode) . ":" . $siteCode .
                "9:AUTH_TYPE" . strlen($authType) . ":". $authType .
                "7:RTN_URL" . strlen($returnUrl) . ":" . $returnUrl .
                "7:ERR_URL" . strlen($errorUrl) . ":" . $errorUrl .
                "11:POPUP_GUBUN" . strlen($popGubun) . ":" . $popGubun .
                "9:CUSTOMIZE" . strlen($customize) . ":" . $customize .
                "6:GENDER" . strlen($gender) . ":" . $gender ;

$encData = `$encodePath ENC $siteCode $sitePwd $plainData`;

if( $encData == -1 ){
    $returnMsg = "암/복호화 시스템 오류입니다.";
    $encData = "";
}else if( $encData== -2 ){
    $returnMsg = "암호화 처리 오류입니다.";
    $encData = "";
}else if( $encData== -3 ){
    $returnMsg = "암호화 데이터 오류 입니다.";
    $encData = "";
}else if( $encData== -9 ){
    $returnMsg = "입력값 오류 입니다.";
    $encData = "";
}

$response['encData'] = $encData;

$response['$encodePath'] = $encodePath;
$response['$siteCode'] = $siteCode;
$response['$sitePwd'] = $sitePwd;
$response['$plainData'] = $plainData;

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>