<?php
include "../include/include_function.php";

$data = json_decode(file_get_contents('php://input'), true);

$type = Replace_Check($data['type']); //S이면 search, C이면 category

// 응답 초기화
$response = [];
$condition = '';//카테고리가 '전체'일 경우 조건절 생략되게 초기 설정

if($type == 'S'){// search에 사용되는 api 로직 ------------------------------------------------------------------------
    
    $searchedWord = Replace_Check($data['searchedWord']);
    $condition = "(Title LIKE '%$searchedWord%' OR Content LIKE '%$searchedWord%') AND";
    
}elseif($type == 'C'){// category에 사용되는 api 로직 ------------------------------------------------------------------
    
    $category = Replace_Check($data['category']);
    if($category != 'T'){//카테고리가 '전체'일 경우 조건절 생략되게 변수값 변경 피하기
        $condition = "Category='$category' AND";        
    }
    
}//----------------------------------------------------------------------------------------------------------------
    
$sql4select = "SELECT * FROM Faq WHERE $condition Del='N' AND UseYN='Y' ORDER BY OrderByNum ASC, RegDate ASC";
$query4select = mysqli_query($connect, $sql4select);
if($query4select && mysqli_num_rows($query4select)){
    while($row = mysqli_fetch_array($query4select)){
        extract($row);
        $response['faqInfo'][] =  [$idx, $Title, $Category]; //$idx는 내용 보기를 눌렀을 때 프론트가 나에게 넘겨줘야 하는 값.
    }
}

// JSON 응답 전송
header('Content-Type: application/json');
echo json_encode($response);
?>