<?php
include "../include/include_function.php";
require_once ('./include/KISA_SHA256.php');
$data = json_decode(file_get_contents('php://input'), true);

$newPw1 = Replace_Check($data['newPw1']);
$newPw2 = Replace_Check($data['newPw2']);
$id = Replace_Check($data['id']);

function validatePassword($str) {
    if (preg_match('/(\w)\1{5,}/', $str) || isContinuedValue($str, 6)) {
        $msg = '비밀번호에 6자 이상의 연속 또는 반복 문자 및 숫자를 사용하실 수 없습니다.';
        return false;
    }
    
    $pwRule1 = '/^(?=.*[a-zA-Z])(?=.*[0-9]).{10,}$/';
    $pwRule2 = '/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-]).{10,}$/';
    $pwRule3 = '/^(?=.*[0-9])(?=.*[!@#$%^*+=-]).{10,}$/';
    $pwRule4 = '/^(?=.*[a-zA-Z])(?=.*[!@#$%^*+=-])(?=.*[0-9]).{8,}$/';
    $pwValid = false;
    
    if (strlen($str) >= 10) {
        if (preg_match($pwRule1, $str) && preg_match('/[a-zA-Z]/', $str) && preg_match('/[0-9]/', $str)) {
            $pwValid = true;
        }
        
        if (preg_match($pwRule2, $str) && preg_match('/[a-zA-Z]/', $str) && preg_match('/[!@#$%^*+=-]/', $str)) {
            $pwValid = true;
        }
        
        if (preg_match($pwRule3, $str) && preg_match('/[0-9]/', $str) && preg_match('/[!@#$%^*+=-]/', $str)) {
            $pwValid = true;
        }
    } elseif (strlen($str) >= 8) {
        if (preg_match($pwRule4, $str)) {
            if (preg_match('/[a-zA-Z]/', $str) && preg_match('/[0-9]/', $str) && preg_match('/[!@#$%^*+=-]/', $str)) {
                $pwValid = true;
            }
        }
    }
    
    return $pwValid;
}

function isContinuedValue($str, $length) {
    $asciiCodes = array_map('ord', str_split($str));
    $count = 1;
    
    for ($i = 1; $i < count($asciiCodes); $i++) {
        if ($asciiCodes[$i] === $asciiCodes[$i - 1] + 1 || $asciiCodes[$i] === $asciiCodes[$i - 1]) {
            $count++;
            if ($count >= $length) {
                return true;
            }
        } else {
            $count = 1;
        }
    }
    return false;
}

$msg = '';

if($newPw1 == $newPw2 && validatePassword($newPw1)){
    $enc_pwd = encrypt_SHA256($newPw1); //비밀번호 암호화
    $sql4Update = "UPDATE Member SET Pwd='$enc_pwd', PassChange='Y' WHERE ID='$id'";
    $row = mysqli_query($connect, $sql4Update);
    if($row){
        $response['result'] = 'P';        
    }else{
        $response['result'] = 'E1'; //변경가능 조건에는 부합하나, DB 업데이트 실패
    }
}else{
    $response['result'] = 'E2'; //변경 가능 조건에 부합하지 않음.
}

$response['msg'] = $msg;

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>