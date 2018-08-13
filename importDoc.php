<?php
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
$response = array();
$CONVERT_SERVER_URL = "http://synapeditor.iptime.org:3000/convertDocToPb";
$response['docFile'] = $_FILES['docFile'];
// $response['server'] = $_SERVER;

$filename = $_FILES['docFile']['name'];
$filedata = $_FILES['docFile']['tmp_name'];
$filesize = $_FILES['docFile']['size'];

$headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
// $postfields = array("file" => '@' . $filedata . ';filename=' . $filename);
$postfields = array("file" => curl_file_create($filedata, $_FILES['docFile']['type'], $filename));
$response['postfields'] = $postfields;
$ch = curl_init();
$options = array(
    CURLOPT_URL => $CONVERT_SERVER_URL,
    CURLOPT_HEADER => true,
    CURLOPT_POST => 1,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_POSTFIELDS => $postfields,
    CURLOPT_INFILESIZE => $filesize,
    CURLOPT_RETURNTRANSFER => true
); // cURL options
curl_setopt_array($ch, $options);
$response['result'] = curl_exec($ch);
curl_close($ch);

echo json_encode($response);
?>