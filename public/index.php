<?php

require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__.'/../app/s3_config.php';
print_r($config);

$app = new Slim\Slim();
$app->response()->headers->set('Access-Control-Allow-Headers', 'Content-Type');
$app->response()->headers->set('Access-Control-Allow-Methods', 'GET, POST');
$app->response()->headers->set('Access-Control-Allow-Origin', '*');

$app->get('/', function() use($app){
    echo "\nhello";
   /* try{
        $app->response->headers->set('Content-Type','application/json');
        $alluser = User::all();

        if($alluser){
            $result = [
                "status" => "success",
                "data" => json_decode($alluser->toJson())
            ];

        }else{
            $result = [
                "status" => "success",
                "data" => "No Data"
            ];
        }
        print(json_encode($result));
    }catch(Exception $error){
        print_r($error);
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }*/

//        $user = new User;
//    $user->username = "Test User2";
//   echo $user->save();
//    $alluser = User::all();
//    $alluser = User::where('first_name','niraj')->first();
//    echo $alluser->toJson();
});
$app->get('/news',function() use($app){
    try{
        $app->response->headers->set('Content-Type', 'application/json');
        $allnews = News::all();

        if($allnews){
            $result = [
                "status" => "success",
                "data" => json_decode($allnews->toJson())
            ];

        }else{
            $result = [
                "status" => "success",
                "data" => "No Data"
            ];
        }
        print(json_encode($result));
    }catch (Exception $error){
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->post('/create/news',function() use($app){
    try{
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true); // parse the JSON into an associative array
        $news = new News;
        $news->news_id =  uniqid('news-'.date('Ymd').'-');
        $news->news_title = $allPostVars['news_title'];
        $news->news_content = $allPostVars['news_content'];
        $news->news_img = $allPostVars['img_name'];
        $news->likes = 0;
        if(($news->save()) > 0){
            $result = [
                "status" => "success",
                "message" => "News Created"
            ];
            print(json_encode($result));
        };
    }catch (Exception $error){
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->post('/check/user', function() use($app){
    try{
        $app->response->headers->set('Content-Type', 'application/json');
        $result = array();
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true);
        $data = User::where('email',$allPostVars['email'])->where('password',md5($allPostVars['password']))->first();
        if($data){
            $result["status"] = "success";
            $result["data"] = json_decode($data->toJson());
        }else{
            $result["status"] = "error";
            $result["data"] = "Incorrect Credential.";
        }
        print_r(json_encode($result));

    }catch (Exception $error){
        $result = [
            "status" => "error",
            "message" => "Incorrect Credential.",
            "error" => $error->getMessage()
        ];
        print(json_encode($result));
    }

});

$app->post('/create/user', function() use($app){

    try{
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true); // parse the JSON into an associative array
//      $allPostVars = $app->request->post(); if the data is coming through form-data
        $user = new User;
        $user->first_name = $allPostVars["first_name"];
        $user->last_name = $allPostVars["last_name"];
        $user->password = md5($allPostVars["password"]);
        $user->email = $allPostVars["email"];
        $user->city = $allPostVars["city"];
        $user->age = $allPostVars["age"];
        $user->role = "GENERAL";
        $user->time_stamp = date('Y-m-d H:i:s');
        if(($user->save()) > 0){
            $result = [
                "status" => "success",
                "message" => "User Created"
            ];
            print(json_encode($result));
        };
    }catch(Exception $error){
         $result = [
             "status" => "error",
             "message" => $error->getMessage()
         ];
         print(json_encode($result));
    }
});

$app->post('/upload/image',function() use($app){
    $app->response()->header("Content-Type", "application/json");
    if (!isset($_FILES['uploads'])) {
        echo "No files uploaded!!";
        return;
    }

    $imgs = array();

    $files = $_FILES['uploads'];
    //echo end((explode(".", $files["name"])));
    $cnt = count($files['name']);

    if($cnt>1){
        echo "Multiple file upload not supported";
        /*for($i = 0 ; $i < $cnt ; $i++) {
            echo $files['error'][$i];
            if ($files['error'][$i] === 0) {
                $name = uniqid('img-'.date('Ymd').'-');
                if (move_uploaded_file($files['tmp_name'][$i], 'uploads/' . $name) === true) {
                    $imgs[] = array('url' => '/uploads/' . $name, 'name' => $files['name'][$i]);
                }

            }
        }*/
    }else{

       if($files['error'] === 0){
           $name = uniqid('img-'.date('Ymd').'-');
           $ext = explode(".",$files["name"]);
           if (move_uploaded_file($files['tmp_name'], 'uploads/images/' . $name .'.'. $ext[1]) === true) {
               $imgs[] = array('url' => '/uploads/images/' . $name . '.' . $ext[1], 'name' => $files['name']);
           }
       }
    }

    /*$imageCount = count($imgs);

    if ($imageCount == 0) {
        echo 'No files uploaded!!  <p><a href="/">Try again</a>';
        return;
    }else{
        print_r($imgs);
    }*/
});

$app->post('/upload/video',function() use($app){
	
	ini_set('upload_max_filesize', '2000M');
	echo ini_get('upload_max_filesize');
    //$app->response()->header("Content-Type", "application/json");
    if (!isset($_FILES['uploads'])) {
        echo "No files uploaded!!";
        return;
    }
    print_r($_FILES);
    $imgs = array();

    $files = $_FILES['uploads'];
    //echo end((explode(".", $files["name"])));
    $cnt = count($files['name']);

    if($cnt>1){
        echo "Multiple file upload not supported";
        /*for($i = 0 ; $i < $cnt ; $i++) {
            echo $files['error'][$i];
            if ($files['error'][$i] === 0) {
                $name = uniqid('img-'.date('Ymd').'-');
                if (move_uploaded_file($files['tmp_name'][$i], 'uploads/' . $name) === true) {
                    $imgs[] = array('url' => '/uploads/' . $name, 'name' => $files['name'][$i]);
                }

            }
        }*/
    }else{

        if($files['error'] === 0){
            $name = uniqid('vid-'.date('Ymd').'-');
            $ext = explode(".",$files["name"]);
            if (move_uploaded_file($files['tmp_name'], 'uploads/videos/' . $name .'.'. $ext[1]) === true) {
                $imgs[] = array('url' => '/uploads/videos/' . $name . '.' . $ext[1], 'name' => $files['name']);
            }
        }
    }

    /*$imageCount = count($imgs);

    if ($imageCount == 0) {
        echo 'No files uploaded!!  <p><a href="/">Try again</a>';
        return;
    }else{
        print_r($imgs);
    }*/
});

$app->run();
