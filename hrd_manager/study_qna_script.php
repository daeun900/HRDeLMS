<?
include "../include/include_function.php";
include "./include/include_admin_check.php";

$idx = Replace_Check($idx);
$mode = Replace_Check($mode);
$Notice = Replace_Check($Notice);
$Title = Replace_Check($Title);
$UseYN = Replace_Check($UseYN);
$Content = Replace_Check2($Content);

$FileDel1 = Replace_Check($FileDel1);
$FileDel2 = Replace_Check($FileDel2);
$FileDel3 = Replace_Check($FileDel3);
$FileDel4 = Replace_Check($FileDel4);
$FileDel5 = Replace_Check($FileDel5);

$UserID = Replace_Check($UserID);

$col = Replace_Check($col);
$sw = Replace_Check($sw);

$cmd = false;
$Folder = "/StudyCounsel";

include "./include/include_upload_file.php";


if($mode=="reply") { //답글 작성---------------------------------------------------------------------------------------------------------

	if($FileDel1=="Y") {
		$FileQuery1 = ", FileName1='', RealFileName1=''";
	}else{
		if($FileName1) {
			$FileQuery1 = ", FileName1='$FileName1', RealFileName1='$RealFileName1'";
		}
	}

	if($FileDel2=="Y") {
		$FileQuery2 = ", FileName2='', RealFileName2=''";
	}else{
		if($FileName2) {
			$FileQuery2 = ", FileName2='$FileName2', RealFileName2='$RealFileName2'";
		}
	}

	if($FileDel3=="Y") {
		$FileQuery3 = ", FileName3='', RealFileName3=''";
	}else{
		if($FileName3) {
			$FileQuery3 = ", FileName3='$FileName3', RealFileName3='$RealFileName3'";
		}
	}

	if($FileDel4=="Y") {
		$FileQuery4 = ", FileName4='', RealFileName4=''";
	}else{
		if($FileName4) {
			$FileQuery4 = ", FileName4='$FileName4', RealFileName4='$RealFileName4'";
		}
	}

	if($FileDel5=="Y") {
		$FileQuery5 = ", FileName5='', RealFileName5=''";
	}else{
		if($FileName5) {
			$FileQuery5 = ", FileName5='$FileName5', RealFileName5='$RealFileName5'";
		}
	}

	$Sql = "UPDATE StudyCounsel SET Name2='$Name2', Contents2='$Contents2', RegDate2=NOW(), Status='$Status' $FileQuery1 $FileQuery2 $FileQuery3 $FileQuery4 $FileQuery5 WHERE idx=$idx";
	$Row = mysqli_query($connect, $Sql);

	$cmd = true;
	$url = "study_qna.php?col=".$col."&sw=".$sw;

} //글 수정-------------------------------------------------------------------------------------------------------------------------

if($mode=="del") { //글 삭제---------------------------------------------------------------------------------------------------------

	$Sql = "UPDATE StudyCounsel SET Del='Y' WHERE idx=$idx";
	$Row = mysqli_query($connect, $Sql);

	$cmd = true;
	$url = "study_qna.php?col=".$col."&sw=".$sw;

} //글 삭제-------------------------------------------------------------------------------------------------------------------------

if($Row && $cmd) {
	$ProcessOk = "Y";
	$msg = "처리되었습니다.";
}else{
	$ProcessOk = "N";
	$msg = "오류가 발생했습니다.";
}

mysqli_close($connect);
?>
<script type="text/javascript" src="./include/jquery-1.11.0.min.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
	alert("<?=$msg?>");
	top.$("#SubmitBtn").show();
	top.$("#Waiting").hide();
	<?if($ProcessOk=="Y") {?>
	top.location.href="<?=$url?>";
	<?}?>
//-->
</SCRIPT>