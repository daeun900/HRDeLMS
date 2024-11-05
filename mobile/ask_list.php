<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = Replace_Check($data['id']);
// 응답 초기화
$response = [];

$sql4select = "SELECT * FROM Counsel WHERE ID='$id' AND Del='N' ORDER BY idx DESC";
$query4select = mysqli_query($connect, $sql4select);
if($query4select && mysqli_num_rows($query4select)){
    while($row = mysqli_fetch_array($query4select)){
        extract($row);
        $response['askList'][] =  [$Title, $RegDate, $Status, $idx];
    }
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>