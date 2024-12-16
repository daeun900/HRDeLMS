<?php // 전달 값 => id / pwd   리런값 => [ 통과 = Y | 해당 아이디 없음 = N1 | 비번 불일치 = N2 | 휴면계정 = N3 ]
// 입력 값 받아오기
include "../include/include_function.php";

require_once ('./include/KISA_SHA256.php');

$data = json_decode(file_get_contents('php://input'), true);

session_start();

$deviceId = Replace_Check_XSS2($data['deviceId']);
$id = Replace_Check_XSS2($data['id']);
$pwd = Replace_Check_XSS2($data['pwd']);
$encryptedPwd = encrypt_SHA256($pwd);

 
// 응답 초기화
$response = [];
$result = '';

$sql4select = "SELECT ID, Pwd, Sleep, Name, TestID, EduManager, PassChange, Mandatory, 
                    AES_DECRYPT(UNHEX(Mobile),'$DB_Enc_Key') AS Mobile, AES_DECRYPT(UNHEX(BirthDay),'$DB_Enc_Key') AS BirthDay 
                FROM Member WHERE BINARY(ID)='$id' AND UseYN='Y'";
$query4select = mysqli_query($connect, $sql4select);
$row = mysqli_fetch_array($query4select);
if($row){ //회원 정보가 있으면
    $idFromDB = $row['ID'];
    $pwdFromDB = $row['Pwd'];
    $sleepYN = $row['Sleep'];
    $nm = $row['Name'];
    $testID = $row['TestID'];
    $eduManager = $row['EduManager'];
    $pwchg = $row['PassChange'];
    $agreement = $row['Mandatory'];
    $Mobile = $row['Mobile'];
    $BirthDay = $row['BirthDay'];
    
    if($sleepYN == 'Y'){
        $response['result'] = 'N3'; //휴면계정
        
    }elseif($idFromDB == $id && $pwdFromDB == $encryptedPwd){ //통과
        
        if($agreement == 'N' || $pwchg == "N") { //$result 값이 A => 필수동의 안함, P => 비번 변경 필요, AP => 필수동의 후, 비번 변경까지 필요
            if($agreement == 'N'){ //필수동의 안한 회원
                $result = 'A';
            }
            if($pwchg == 'N') { //비밀번호 변경 필요 회원
                $result .= 'P';
            }
        }else{// 정상 통과
            $result = 'Y';            
        }
        
        //최종로그인 날짜, IP 등록
        $sql4Update = "UPDATE Member SET LastLogin=NOW(), LastLoginIP='$UserIP' WHERE ID='$id'";
        mysqli_query($connect, $sql4Update);
        
        //로그인 히스토리 등록
        $Sql4History = "INSERT INTO LoginHistory(ID, Device, IP, RegDate) VALUES('$id', 'APP_$deviceId', '$UserIP', NOW())";
        mysqli_query($connect, $Sql4History);
        
        //로그인 중복처리를 위한
        $sql4Delete = "DELETE FROM LoginIng WHERE ID='$id'";
        mysqli_query($connect, $sql4Delete);
        
        //로그인 상태 등록
        $maxno = max_number("idx","LoginIng");
        $sessionID = session_id();
        $sql4Insert = "INSERT INTO LoginIng(idx, ID, SessionID, IP, RegDate) VALUES($maxno, '$id', '$sessionID', '$UserIP', NOW())";
        mysqli_query($connect, $sql4Insert);
        
        $_SESSION['LoginMemberID'] = $id;
        $_SESSION["LoginName"] = $nm;
        $_SESSION["LoginEduManager"] = $eduManager;
        $_SESSION["LoginTestID"] = $testID;
//         $_SESSION["DeviceId"] = $deviceId;
        
        $_SESSION["IsPlaying"] = "N";
        
        $response['result'] = $result;
        $response['name'] = $nm;
        $response['mobile'] = $Mobile;
        $response['birthday'] = $BirthDay;
        
    }elseif($pwdFromDB != $encryptedPwd){
        $response['result'] = 'N2';//비번불일치
    }
}else{ //회원 정보가 없으면
    $response['result'] = 'N1'; //해당 아이디 없음
}
if (!$connect) {
    $response['result'] = 'include_function.php 파일 확인해야하는 경우';
}
// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>