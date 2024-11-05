<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

// 응답 초기화
$response = [];

while(list($key,$value)=each($Faq_array)){
    $response['faqArray'][] = [$key, $value]; //$key는 카테고리를 눌렀을 때 프론트가 나에게 넘겨줘야 하는 값.    
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>