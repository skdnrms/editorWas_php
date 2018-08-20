<?php
$CONVERT_SERVER_URL = "http://synapeditor.iptime.org:3000/convertDocToPb";

$filename = $_FILES['docFile']['name'];
$filedata = $_FILES['docFile']['tmp_name'];
$filesize = $_FILES['docFile']['size'];
$filetype = $_FILES['docFile']['type'];

// send doc file
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $CONVERT_SERVER_URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");   
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);   
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);  
curl_setopt($ch, CURLOPT_TIMEOUT, 100);
curl_setopt($ch, CURLOPT_POSTFIELDS, array("file" => curl_file_create($filedata, $filetype, $filename)));
$result = curl_exec($ch);
curl_close($ch);

// zip파일 저장
$uploaddir = './upload/';
$milliseconds = round(microtime(true) * 1000);
$zipfilename = "{$milliseconds}_{$filename}";
$zipfile = $uploaddir . basename("{$zipfilename}.zip");
file_put_contents($zipfile, $result);

// unzip
$zip = new ZipArchive;
$unzippath = $uploaddir . $zipfilename;
if ($zip -> open($zipfile) === TRUE) {
    $zip -> extractTo($unzippath);
    $zip -> close();
}

// pb unzip
$serializedData = array();
$pbfile = gzopen("{$unzippath}/" . basename("document.word.pb"), "rb");
gzseek($pbfile, 16);
while (!gzeof($pbfile)) {
    $data = gzuncompress(gzread($pbfile, 1024));            // read up to 100 bytes
    // $data[4] = chr(88);                      // Modify the value of the 5th byte
    $nbytes = strlen($data);                 // get the number of bytes actuall read
    for ($i = 0; $i < $nbytes; $i++) {
        $subdata = substr($data, $i, 1);       // extract a single byte
        $arr = unpack("C*", $subdata);        // convert to decimal
        foreach ($arr as $key => $value) {
            array_push($serializedData, $value);
        }
    }
}
gzclose($pbfile);

$response = array();
$response['serializedData'] = $serializedData;

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
echo json_encode($response);









// $serializeData = array();
// $pbfile = gzopen("{$unzippath}/" . basename("document.word.pb"), "rb");
// $pointer = 0;
// echo gzinflate($pbfile);
// gzseek($pbfile, 16);
// while (!gzeof($pbfile)) {
    // gzseek($pbfile, $pointer);
    // print ' [ ';
    // $readpb = gzread($pbfile, 1024);
    // $compressed   = gzcompress($readpb, 1024), 9);
    // echo gzuncompress($compressed);


    // print gzgetc($pbfile);
    // echo gzread($pbfile, 1);
    // $contents = @gzinflate(gzread($pbfile, 1024));
    // echo $contents;
    // echo '/';
    // $char = gzgetc($pbfile);
    // $compressed = gzcompress($char);
    // $uncompressed = gzuncompress($compressed);
    // print '(';
    // print $char;
    // print '/';
    // print $compressed;
    // print '/';
    // print $uncompressed;
    // print ')';
    // print gzgetc($pbfile) & 0xFF;
    // print ' | ';
    // print bindec(gzgetc($pbfile)); // & 0xFF;
    // print ' | ';
    // print decbin(gzgetc($pbfile));
    // print ' | ';
    // print $pointer;
    // print ' ] ';
    // print '\n';
    // $pointer += 1;
    // array_push($serializeData, gzgetc($pbfile));
// }

// echo "result : " . $pointer;
// gzclose($pbfile);
// echo json_encode($serializeData);


// $response = array();
// $response['serializeData'] = $serializeData;

// header('Access-Control-Allow-Origin: *');
// header('Content-type: application/json');
// echo json_encode($response);
?>