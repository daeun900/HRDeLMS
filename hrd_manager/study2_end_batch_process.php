<?
include "../include/include_function.php";
include "./include/include_admin_check.php";

//트랜잭션 시작
mysqli_query($connect, "SET AUTOCOMMIT=0");
mysqli_query($connect, "BEGIN");

$error_count = 0;

$Seq = Replace_Check($Seq);


$Sql = "UPDATE Study SET ResultView='Y', StudyEnd='Y' WHERE Seq=$Seq";
$Row = mysqli_query($connect, $Sql);

if(!$Row) { //쿼리 실패시 에러카운터 증가
	$error_count++;
}


if($error_count>0) {
	mysqli_query($connect, "ROLLBACK");
	$result_msg = "<font color='red'>실패</font>";
}else{
	mysqli_query($connect, "COMMIT");
	$result_msg = "<font color='blue'>성공</font>";
}


echo $result_msg;

mysqli_close($connect);
?>