<?php // 리턴값 - [ 'N1' 보안코드 이상  | 'N2' 문의 데이터 insert 단계 이상  | 'N3' 회원 데이터 select 단계 이상 | 'Y' 정상적으로 insert 완료 ]
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$id = Replace_Check($data['id']);
$name = Replace_Check($data['name']);
$pass_code_info = Replace_Check($data['pass_code_info']);
$type = Replace_Check($data['type']);
$title = Replace_Check($data['title']);
$content = Replace_Check($data['content']);

// 응답 초기화
$response = [];

//보안코드가 정상적인지 2차 체크 ----------------------------------------------------------------------------------------
if( strlen($pass_code_info) == 11 && ctype_digit(substr($pass_code_info, 0, 5)) && ctype_alpha(substr($pass_code_info, 5, 1)) && ctype_digit(substr($pass_code_info, 6, 5)) ){
    $img = substr($pass_code_info, 0, 5);
    $isPassed = substr($pass_code_info, 5, 1);
    $input = substr($pass_code_info, 6, 5);
}

if($isPassed == 'Y' && $img == $input){ //보안코드 관련 이상이 없다면
    $response['result'] = 'dkdk';

    //휴대폰번호, 이메일 조회해오기.
    $sql4select = "SELECT Mobile, Email FROM Member WHERE ID = '$id'";
    $query4select = mysqli_query($connect, $sql4select);
    $row = mysqli_fetch_array($query4select);
    if($row){    
        extract($row);
        
        $sql4insert = "INSERT INTO Counsel(ID, Name, Category, Mobile, Email, Title, Contents, RegDate, Del, Status)
    		              VALUES('$id', '$name', '$type', '$Mobile', '$Email', '$title', '$content', NOW(), 'N', 'A')";
        $query4insert = mysqli_query($connect, $sql4insert);
        if($query4insert){
            $response['result'] = 'Y';
        }else{
            $response['result'] = 'N2'; // 문의 데이터 insert 단계 이상
        }
    }else{
        $response['result'] = 'N3'; // 회원 데이터 select 단계 이상
    }
    
}else{
    $response['result'] = 'N1'; //보안코드 이상
}
// $response['result'] = 'aaaa';

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>