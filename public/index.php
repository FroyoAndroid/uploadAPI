<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
use Aws\S3\S3Client;

require realpath(__DIR__ . '/../vendor/autoload.php');

$s3_config = require('../app/s3_config.php');

/*print($s3_config['s3']['key']);
print($s3_config['s3']['secret']);
print($s3_config['s3']['bucket']);*/


$s3 = new S3Client([
    'key' => $s3_config['s3']['key'],
    'secret' => $s3_config['s3']['secret'],
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$app = new Slim\Slim();

$app->response()->headers->set('Access-Control-Allow-Headers', 'Content-Type');
$app->response()->headers->set('Access-Control-Allow-Methods', 'GET, POST');
$app->response()->headers->set('Access-Control-Allow-Origin', '*');

$app->post('/upload',function() use ($app, $s3, $s3_config){
    $app->response()->header("Content-Type", "application/json");
    $allowed = array('image/png', 'image/jpeg', 'video/mp4');
    if (!isset($_FILES['uploads'])) {
        $result = [
            "status" => "error",
            "message" => "No files to upload!!"
        ];
        print(json_encode($result));
        return;
    } else if ($_FILES['uploads']['size'] > (intval(ini_get('upload_max_filesize')) * 1024 * 1024)) {
        $result = [
            "status" => "error",
            "message" => "File is too long!!!"
        ];
        print(json_encode($result));
        return;
    } else if($_FILES['uploads']['name'] > 1){
        $result = [
            "status" => "error",
            "message" => "Multiple  Files Upload Not Supported!!!"
        ];
        print(json_encode($result));
        return;
    } else if( (in_array($_FILES['uploads']['type'], $allowed)) && ($_FILES['uploads']['error'] === 0)){
        $name = uniqid('vid-' . date('Ymd') . '-');
        $ext = explode(".", $_FILES['uploads']["name"]);
        if(move_uploaded_file($_FILES['uploads']['tmp_name'], 'uploads/' . $name .'.'. strtolower($ext[1]))){
            try {
                $file_ext = strtolower($ext[1]);
                $s3_result = $s3->putObject([
                    'Bucket' => $s3_config['s3']['bucket'],
                    'Key' => "{$name}.{$file_ext}",
                    'Body' => fopen('uploads/' . $name . '.' .$file_ext, 'rb'),
                    'ACL' => 'public-read'
                ]);
                $result = [
                    "status" => "success",
                    "message" => array("url"=>$s3_result['ObjectURL'],"file_type"=>$_FILES['uploads']['type'])
                ];
                print(json_encode($result));
                unlink('uploads/' . $name . '.' .$file_ext);
            } catch (\Aws\S3\Exception\S3Exception $e) {
                $result = [
                    "status" => "error",
                    "message" => $e->getMessage()
                ];
                print(json_encode($result));
            }
        };
    } else{
        $result = [
            "status" => "error",
            "message" => "Unsupported File Format"
        ];
        print(json_encode($result));
    }
});

$app->run();
