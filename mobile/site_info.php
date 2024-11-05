<?php
include "../include/include_function.php";

// $data = json_decode(file_get_contents('php://input'), true);
// 응답 초기화
$response = [];

$sql4select = "SELECT Phone, KakaoURL FROM SiteInfomation ORDER BY RegDate DESC LIMIT 0,1";
$query4select = mysqli_query($connect, $sql4select);
$row = mysqli_fetch_array($query4select);


if($row){
    extract($row);
    $response['phone'] =  $Phone;
    $response['kakaoUrl'] =  $KakaoURL;
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>