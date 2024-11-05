<?
include "../include/include_function.php";
include "./include/include_admin_check.php";

$idx = Replace_Check($idx);
$mode = Replace_Check($mode);
$Title = Replace_Check($Title);
$Content = Replace_Check2($Content);
$Contents2 = Replace_Check2($Contents2);

$FileNameA1 = Replace_Check($file1); //첨부파일1
$RealFileNameA1 = Replace_Check($fileName1); //첨부파일1 실제파일
$FileNameA2 = Replace_Check($file2); //첨부파일2
$RealFileNameA2 = Replace_Check($fileName2); //첨부파일1 실제파일
$FileNameA3 = Replace_Check($file3); //첨부파일3
$RealFileNameA3 = Replace_Check($fileName3); //첨부파일1 실제파일
$FileNameA4 = Replace_Check($file4); //첨부파일4
$RealFileNameA4 = Replace_Check($fileName4); //첨부파일1 실제파일
$FileNameA5 = Replace_Check($file5); //첨부파일5
$RealFileNameA5 = Replace_Check($fileName5); //첨부파일1 실제파일

$UserID = Replace_Check($UserID);

$col = Replace_Check($col);
$sw = Replace_Check($sw);

$cmd = false;
// $Folder = "/Counsel";
// include "./include/include_upload_file.php";

//답글 작성
if($mode=="reply") {
    //발송할 메세지 확인
    $Sql = "SELECT * FROM SendMessage WHERE MessageMode='qna'";
    $Result = mysqli_query($connect, $Sql);
    $Row = mysqli_fetch_array($Result);
    
    if($Row) {
        $Massage = $Row['Massage'];
        $TemplateCode 	= $Row['TemplateCode'];
        $TemplateMessage 	= $Row['TemplateMessage'];
    }
    
    $FileQuery1 = ", FileName1='$FileNameA1', RealFileName1='$RealFileNameA1'";
    $FileQuery2 = ", FileName2='$FileNameA2', RealFileName2='$RealFileNameA2'";
    $FileQuery3 = ", FileName3='$FileNameA3', RealFileName3='$RealFileNameA3'";
    $FileQuery4 = ", FileName4='$FileNameA4', RealFileName4='$RealFileNameA4'";
    $FileQuery5 = ", FileName5='$FileNameA5', RealFileName5='$RealFileNameA5'";

	$Sql = "UPDATE Counsel SET Name2='$Name2', Contents2='$Contents2', RegDate2=NOW(), Status='$Status' $FileQuery1 $FileQuery2 $FileQuery3 $FileQuery4 $FileQuery5 WHERE idx=$idx";
	echo $Sql;
	$Row = mysqli_query($connect, $Sql);

	//처리상태 완료시 카카오톡 발송
	if($Status=="B") {
		$Sql_m = "SELECT AES_DECRYPT(UNHEX(Mobile),'$DB_Enc_Key') AS Mobile FROM Member WHERE ID='$UserID'";
		$Result_m = mysqli_query($connect, $Sql_m);
		$Row_m = mysqli_fetch_array($Result_m);

		if($Row_m) {
			$Mobile = $Row_m['Mobile'];
			$msg_mobile = str_replace("-","",$Mobile);
			$msg_var = "";
			$user_id = $UserID;
			$input_id = $LoginAdminID;
			
			//발송 로그 기록
			$maxno = max_number("idx","SmsSendLog");
			$etc1 = $maxno;
			$Sql = "INSERT INTO SmsSendLog(idx, ID, Study_Seq, Massage, Code, Mobile, InputID, RegDate) VALUES($maxno, '$UserID', '', '$TemplateMessage', '', '$msg_mobile', '$LoginAdminID', NOW())";
			$Row = mysqli_query($connect, $Sql);

			$kakaotalk_result = mts_mms_send($msg_mobile, $TemplateMessage, $TRAN_CALLBACK, "", $TemplateCode);

			if($kakaotalk_result=="Y") $cmd = true;
			else $cmd = false;
		}
	}else{
		$cmd = true;
	}
	$url = "qna.php?col=".$col."&sw=".$sw;
}

//글 삭제
if($mode=="del") {
	$Sql = "UPDATE Counsel SET Del='Y' WHERE idx=$idx";
	$Row = mysqli_query($connect, $Sql);

	$cmd = true;
	$url = "qna.php?col=".$col."&sw=".$sw;
}

if($Row && $cmd) {
	$ProcessOk = "Y";
	$msg = "처리되었습니다.";
}else{
	$ProcessOk = "N";
	$msg = "오류가 발생했습니다. 관리자에게 문의하세요.";
}

mysqli_close($connect);
?>
<script type="text/javascript" src="./include/jquery-1.11.0.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">
	alert("<?=$msg?>");
	top.$("#SubmitBtn").show();
	top.$("#Waiting").hide();
	<?if($ProcessOk=="Y") {?>
	top.location.href="<?=$url?>";
	<?}?>
</SCRIPT>