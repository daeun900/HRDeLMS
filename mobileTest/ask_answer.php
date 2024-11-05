<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$idx = Replace_Check($data['idx']);

// 응답 초기화
$response = [];

$sql4select = "SELECT Contents2 FROM Counsel WHERE idx = $idx";
$query4select = mysqli_query($connect, $sql4select);
$row = mysqli_fetch_array($query4select);
if($row){    
    $response['answer'] = $row['Contents2'];
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>