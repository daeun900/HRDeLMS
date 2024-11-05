<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

// 응답 초기화
$response = [];

while(list($key,$value)=each($Counsel_array)){
    $response['askArray'][] = [$key, $value]; //$key는 상담문의 등록을 할 때 프론트가 백에게 넘겨줘야 하는 값.
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>