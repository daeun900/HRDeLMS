<?
include "./include/include_function.php"; //DB���� �� ���� �Լ� ����

if($UserDevice=="Mobile") { //������� ���
    $url = $MobileSiteURL;
}else{ //PC�� ���
    $url = "/landingpage/main.html";
}

header( "Location: $url" );
?>
