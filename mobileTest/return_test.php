<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$input = Replace_Check($data['input']); //S이면 search, C이면 category

// 응답 초기화
$response = [];
$result = get_result_4_return_test($input);
$result = 'assa';

function get_result_4_return_test($input){
    if($input == 'A'){
        if($input == 'A'){
            $result = 'B';
            return;
            if($input == 'A'){
                $result = 'C';
            }
        }

        $result = 'dkdk';
    }
    return $result;
}

$response['result'] =  [$result]; //$idx는 내용 보기를 눌렀을 때 프론트가 나에게 넘겨줘야 하는 값.

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>