<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
$response = array();
$uploaddir = './upload/';
$milliseconds = round(microtime(true) * 1000);
$uploadfile = $uploaddir . basename("{$milliseconds}_{$_FILES['file']['name']}");

// $response['files'] = $_FILES;
// $response['server'] = $_SERVER;

// * url에 .php제거
// /etc/apache2/httpd.conf -> 설정 변경
// <Directory />
//     Options FollowSymLinks MultiViews
//     AddType application/x-httpd-php .php
// </Directory>
// 
// * 파일 권한체크
if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) { 
    $response['uploadPath'] = $uploadfile;
}

// if(($_FILES['file']['error'] > 0) || ($_FILES['file']['size'] <= 0)){
//     $response['error'] = "파일 업로드  에러!!!";
// } else {
//     if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) { 
//         $response['uploadPath'] = $uploadfile;
//     } else { 
//         $response['fail'] = "파일 업로드 실패!!!";
//     }
// }

echo json_encode($response);
?>