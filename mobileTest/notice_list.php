<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

// 응답 초기화
$response = [];

$sql4select = "SELECT * FROM Notice WHERE Del='N' AND UseYN='Y' 
				ORDER BY CASE WHEN Notice = 'Y' THEN 0 ELSE 1 END, RegDate DESC, idx DESC";
$query4select = mysqli_query($connect, $sql4select);
if($query4select && mysqli_num_rows($query4select)){
    while($row = mysqli_fetch_array($query4select)){
        extract($row);
        if($Notice == 'Y'){
            $response['notiInfo1'][] =  [$Title, $RegDate, $ViewCount, $idx]; // [공지] 인 데이터들    
        }else{
            $response['notiInfo2'][] =  [$Title, $RegDate, $ViewCount, $idx]; // [공지]가 아닌 일반 데이터들              
        }
    }
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>