<?php

require __DIR__.'/../vendor/autoload.php';

$app = new Slim\Slim();

$app->get('/', function() use($app){
    $app->response->headers->set('Content-Type','application/json');
    $user = new User;
    $user->first_name = "Test2";
    $user->last_name = "Test2";
    $user->password = md5('admin');
    $user->email = "tes2t@test.com";
    $user->city = "WEst Asia";
    $user->age = 34;
    $user->role = "AGENT";
    $user->time_stamp = date('Y-m-d H:i:s');
    echo $user->save();
//    $user = new User;
//    $user->username = "Test User2";
//   echo $user->save();
//    $alluser = User::all();
//    $alluser = User::where('first_name','niraj')->first();
//    echo $alluser->toJson();
});

$app->post('/check/user', function() use($app){
    try{
        //$app->response->headers->set('Content-Type', 'application/json');
        $result = array();
        $allPostVars = $app->request->post();
        $data = User::where('email',$allPostVars['email'])->first();
        if($data){
            $result["status"] = "success";
        }else{
            $result["status"] = "success";
            $result["data"] = $data;
        }
        print_r(json_encode($result));

    }catch (Exception $error){
        $result = [
            "status" => "error",
            "message" => print($error->getMessage())
        ];
        print(json_encode($result));
    }

});

$app->post('/create/user', function() use($app){

    try{
        $app->response->headers->set('Content-Type', 'application/json');
        $allPostVars = $app->request->post();
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
            "message" => print($error->getMessage())
        ];
        print(json_encode($result));

    }

//    $matchThese = ['email' => $allPostVars['email'], 'password' => md5($allPostVars['password'])];
//    $results = User::where($matchThese)->get();
//    echo $results->toJson();
//    print_r($allPostVars);
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
