<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);
$encData = $_REQUEST["EncodeData"];

// 응답 초기화
$response = [];

//**************************************************************************************************************
//서비스명 :  체크플러스 - 안심본인인증 서비스
//페이지명 :  체크플러스 - 결과 페이지

//인증 후 결과값이 null로 나오는 부분은 관리담당자에게 문의 바랍니다.
//**************************************************************************************************************

// session_start();

$siteCode = $CheckPlus_sitecode;					// NICE로부터 부여받은 사이트 코드
$sitePwd = $CheckPlus_sitepasswd;				// NICE로부터 부여받은 사이트 패스워드
$encodePath = $Auth_Mobile_path;
//문자열 점검
if(preg_match('~[^0-9a-zA-Z+/=]~', $encData, $match)) {echo "입력 값 확인이 필요합니다 : ".$match[0]; exit;} // 문자열 점검 추가.
if(base64_encode(base64_decode($encData))!=$encData) {echo "입력 값 확인이 필요합니다"; exit;}

$Sql4Test = "INSERT INTO test(column1,column2 ) VALUES('encData','$encData')";
mysqli_query($connect, $Sql4Test);

if ($encData != "") {
    $plainData = `$encodePath DEC $siteCode $sitePwd $encData`;		// 암호화된 결과 데이터의 복호화

    $Sql4Test = "INSERT INTO test(column1,column2 ) VALUES('plainData','$plainData')";
    mysqli_query($connect, $Sql4Test);
        

    //echo "[plaindata]  " . $plainData . "<br>";
    
    if($plainData == -1){
        $returnMsg  = "암/복호화 시스템 오류";
    }else if ($plainData == -4){
        $returnMsg  = "복호화 처리 오류";
    }else if ($plainData == -5){
        $returnMsg  = "HASH값 불일치 - 복호화 데이터는 리턴됨";
    }else if ($plainData == -6){
        $returnMsg  = "복호화 데이터 오류";
    }else if ($plainData == -9){
        $returnMsg  = "입력값 오류";
    }else if ($plainData == -12){
        $returnMsg  = "사이트 비밀번호 오류";
    }else{
        // 복호화가 정상적일 경우 데이터를 파싱합니다.
        $requestNumber = GetValue($plainData , "REQ_SEQ");
        $responseNumber = GetValue($plainData , "RES_SEQ");
        $authType = GetValue($plainData , "AUTH_TYPE");
        $name = GetValue($plainData , "NAME");
        //$name = GetValue($plainData , "UTF8_NAME"); //charset utf8 사용시 주석 해제 후 사용
        $birthDate = GetValue($plainData , "BIRTHDATE");
        $gender = GetValue($plainData , "GENDER");
        $nationalInfo = GetValue($plainData , "NATIONALINFO");	//내/외국인정보(사용자 매뉴얼 참조)
        $dupInfo = GetValue($plainData , "DI");
        $connInfo = GetValue($plainData , "CI");
        $mobileNo = GetValue($plainData , "MOBILE_NO");
        $mobileCo = GetValue($plainData , "MOBILE_CO");
        
        $Sql4Test = "INSERT INTO test(column1,column2 ) VALUES('REQ_SEQ2', '$requestNumber')";
        mysqli_query($connect, $Sql4Test);

        $name = iconv("EUC-KR","UTF-8",$name);
        
       /* if(strcmp($_SESSION["REQ_SEQ"], $requestNumber) != 0){
            //echo "세션값이 다릅니다. 올바른 경로로 접근하시기 바랍니다.<br>";
            $requestNumber = "";
            $responseNumber = "";
            $authType = "";
            $name = "";
            $birthDate = "";
            $gender = "";
            $nationalInfo = "";
            $dupInfo = "";
            $connInfo = "";
            $mobileNo = "";
            $mobileCo = "";
        }*/
    }
}

//해당 함수에서 에러 발생 시 $len => (int)$len 로 수정 후 사용하시기 바랍니다.
function GetValue($str, $name){
    $pos1 = 0;  //length의 시작 위치
    $pos2 = 0;  //:의 위치
    
    while($pos1 <= strlen($str)){
        $pos2 = strpos( $str , ":" , $pos1);
        $len = substr($str , $pos1 , $pos2 - $pos1);
        $key = substr($str , $pos2 + 1 , $len);
        $pos1 = $pos2 + $len + 1;
        
        if($key == $name){
            $pos2 = strpos( $str , ":" , $pos1);
            $len = substr($str , $pos1 , $pos2 - $pos1);
            $value = substr($str , $pos2 + 1 , $len);
            return $value;
        }else{
            // 다르면 스킵한다.
            $pos2 = strpos( $str , ":" , $pos1);
            $len = substr($str , $pos1 , $pos2 - $pos1);
            $pos1 = $pos2 + $len + 1;
        }
    }
}

//본인 인증한 휴대폰 번호가 회원정보와 일치하는지 확인
$sql = "SELECT AES_DECRYPT(UNHEX(Mobile),'$DB_Enc_Key') AS Mobile, Name, AES_DECRYPT(UNHEX(BirthDay),'$DB_Enc_Key') AS BirthDay FROM Member WHERE ID='$LoginMemberID'";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_array($result);

$mobileDB = $row["Mobile"];
$NameDB = $row["Name"];
$birthDayDB = $row["BirthDay"];

$mobileDB = str_replace("-","",$mobileDB); //회원정보의 휴대폰 번호
$birthDayDB = str_replace("-","",$birthDayDB);
$mobileNo = str_replace("-","",$mobileNo); //NICE에서 리턴받은 휴대폰번호

if (empty($mobileDB) || empty($mobileNo) || empty($NameDB) || empty($name) || empty($birthDayDB) || empty($birthDate)) {
    $response['result'] = 'Error: One or more values are empty';
} else if (($mobileDB !== $mobileNo) || ($NameDB !== $name) || ($birthDayDB !== $birthDate)) {
    $response['result'] = 'Unequal';
} else {
    $response['result'] = 'Equal';
}

$logData = [//나이스 측으로 받은 정보들 담기.  로그인 시, 비교해야하는 데이터들 함께 넘겨주기. 프론트에서는 storage 에 저장하기. 여기서 리턴해준 데이터와 로그인시 넘겨준 데이터를  비교하는 로직은 프론트가.
    'mobileDB' => $mobileDB,
    'mobileNo' => $mobileNo,
    'NameDB' => $NameDB,
    'name' =>  $name,
    'birthDayDB' => $birthDayDB,
    'birthDate' => $birthDate,
];

/*
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
*/

// JSON 데이터를 React Native로 전송
header("Content-Type: text/html; charset=utf-8");

echo "<script>
    window.ReactNativeWebView.postMessage('" . json_encode($logData, JSON_UNESCAPED_UNICODE) . "');
</script>";
exit;
?>