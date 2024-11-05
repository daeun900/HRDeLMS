<?
include "../../include/include_function.php";

$idx = Replace_Check_XSS2($idx);
$mode = Replace_Check_XSS2($mode);

if(empty($_SESSION['LoginMemberID'])) {
	echo "Login"; //로그인 필요
}else{
    if($mode == "A"){
    	$Sql = "UPDATE LectureRequest SET Status = 'C' WHERE idx=$idx AND ID='$LoginMemberID'";
    	$Row = mysqli_query($connect, $Sql);
        if($Row) echo "Success"; else echo "Fail";        
    }else if($mode == "B"){
        $Sql = "UPDATE LectureRequest2 SET Status = 'C' WHERE idx=$idx AND ID='$LoginMemberID'";
        $Row = mysqli_query($connect, $Sql);
        if($Row) echo "Success"; else echo "Fail";
    }  
}

mysqli_close($connect);
?>