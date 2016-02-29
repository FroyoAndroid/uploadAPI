<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use Aws\S3\S3Client;

require realpath(__DIR__ . '/../vendor/autoload.php');

$s3_config = require('../app/s3_config.php');

/*print($s3_config['s3']['key']);
print($s3_config['s3']['secret']);
print($s3_config['s3']['bucket']);*/

$s3 = S3Client::factory([
    'key' => $s3_config['s3']['key'],
    'secret' => $s3_config['s3']['secret']
]);

$app = new Slim\Slim();

$app->response()->headers->set('Access-Control-Allow-Headers', 'Content-Type');
$app->response()->headers->set('Access-Control-Allow-Methods', 'GET, POST');
$app->response()->headers->set('Access-Control-Allow-Origin', '*');


$app->get('/', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $alluser = User::all();

        if ($alluser) {
            $result = [
                "status" => "success",
                "data" => json_decode($alluser->toJson())
            ];

        } else {
            $result = [
                "status" => "success",
                "data" => "No Data"
            ];
        }
        print(json_encode($result));
    } catch (Exception $error) {
        print_r($error);
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }

//        $user = new User;
//    $user->username = "Test User2";
//   echo $user->save();
//    $alluser = User::all();
//    $alluser = User::where('first_name','niraj')->first();
//    echo $alluser->toJson();
});

$app->get('/news', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $allnews = News::orderBy('updated_at', 'desc')->get();

        if ($allnews) {
            $result = [
                "status" => "success",
                "data" => json_decode($allnews->toJson())
            ];

        } else {
            $result = [
                "status" => "success",
                "data" => "No Data"
            ];
        }
        print(json_encode($result));
    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->get('/news/:newsId', function ($newsId) use ($app) {
    try {
       $app->response->headers->set('Content-Type', 'application/json');
        $allnews = News::where('id',$newsId)->first();
        if ($allnews) {
            $comments = News::find($newsId)->getComments();
            $result = [
                "status" => "success",
                "data" => array( 'news' => json_decode($allnews->toJson()) , 'comments' =>json_decode($comments->toJson()) )
            ];

        } else {
            $result = [
                "status" => "error",
                "message" => "No Data"
            ];
        }
        print(json_encode($result));
    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->post('/create/news', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true); // parse the JSON into an associative array
        $news = new News;
        $news->news_title = $allPostVars['news_title'];
        $news->news_content = $allPostVars['news_content'];
        $news->news_img = $allPostVars['img_name'];
        $news->likes = 0;
        if (($news->save()) > 0) {
            $result = [
                "status" => "success",
                "message" => "News Created"
            ];
            print(json_encode($result));
        };
    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->post('/create/news/comment', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true); // parse the JSON into an associative array
        $comment = new Comment;
        $comment->user_id = $allPostVars['user_id'];
        $comment->news_id = $allPostVars['news_id'];
        $comment->comment = $allPostVars['comment'];
        if (($comment->save()) > 0) {
            $result = [
                "status" => "success",
                "message" => "Comment Added"
            ];
            print(json_encode($result));
        };
    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->get('/events', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $events = Event::all();

        if ($events) {
            $result = [
                "status" => "success",
                "data" => json_decode($events->toJson())
            ];

        } else {
            $result = [
                "status" => "error",
                "message" => "No Data"
            ];
        }
        print(json_encode($result));
    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->get('/upcoming/events', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        //print_r(date("Y-m-d"));
        $events = Event::where('schedule_start','>',date("Y-m-d"))->get();

        if ($events) {
            $result = [
                "status" => "success",
                "data" => json_decode($events->toJson())
            ];

        } else {
            $result = [
                "status" => "error",
                "message" => "No Data"
            ];
        }
        print(json_encode($result));
    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->get('/live/events', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        //print_r(date("Y-m-d"));
        $events = Event::where('schedule_start','=',date("Y-m-d"))->get();

        if ($events) {
            $result = [
                "status" => "success",
                "data" => json_decode($events->toJson())
            ];

        } else {
            $result = [
                "status" => "error",
                "message" => "No Data"
            ];
        }
        print(json_encode($result));
    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->post('/create/event', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true); // parse the JSON into an associative array
        $event = new Event;
        $event->event_name = $allPostVars['event_name'];
        $event->event_details = $allPostVars['event_details'];
        $event->event_venue = $allPostVars['venue'];
        $event->schedule_start = date('Y-m-d', strtotime($allPostVars['start_date'])); //'10/16/2003'
        $event->schedule_end = date('Y-m-d', strtotime($allPostVars['end_date']));
        if (($event->save()) > 0) {
            $result = [
                "status" => "success",
                "message" => "Event Created"
            ];
            print(json_encode($result));
        };

    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->post('/check/user', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $result = array();
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true);
        $data = User::where('email', $allPostVars['email'])->where('password', md5($allPostVars['password']))->first();
        if ($data) {
            $result["status"] = "success";
            $result["data"] = json_decode($data->toJson());
        } else {
            $result["status"] = "error";
            $result["message"] = "Incorrect Credential.";
        }
        print_r(json_encode($result));

    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => "Incorrect Credential.",
            "error" => $error->getMessage()
        ];
        print(json_encode($result));
    }

});

$app->post('/create/user', function () use ($app) {

    try {
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
        if (($user->save()) > 0) {
            $result = [
                "status" => "success",
                "message" => "User Created"
            ];
            print(json_encode($result));
        };
    } catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }
});

$app->post('/upload/image', function () use ($app) {
    //echo ini_get('upload_max_filesize');

    //echo intval(ini_get('upload_max_filesize'))*1024*1024;
    $app->response()->header("Content-Type", "application/json");
    //print_r($_FILES['uploads']);

    //echo($_FILES['uploads']['size']);

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
            "message" => "Sorry can't upload such long file"
        ];
        print(json_encode($result));
        return;
    }


    $imgs = array();

    $files = $_FILES['uploads'];
    $cnt = count($files['name']);

    if ($cnt > 1) {
        $result = [
            "status" => "error",
            "message" => "Can't upload multiple files"
        ];
        print(json_encode($result));
        return;
    } else if ($files['error'] === 0) {
        $name = uniqid('img-' . date('Ymd') . '-');
        $ext = explode(".", $files["name"]);
        if (move_uploaded_file($files['tmp_name'], 'uploads/images/' . $name . '.' . $ext[1]) === true) {
            $imgs[] = array('url' => '/uploads/images/' . $name . '.' . $ext[1], 'name' => $files['name']);
            $result = [
                "status" => "success",
                "data" => $imgs
            ];
            print(json_encode($result));
        }
    }else{
        $result = [
            "status" => "error",
            "message" => "Files not uploaded!! Try Again"
        ];
        print(json_encode($result));
    }


});

$app->post('/upload/video', function () use ($app, $s3, $s3_config) {

    $app->response()->header("Content-Type", "application/json");

    //echo($_FILES['uploads']['size']);

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
            "message" => "Sorry can't upload such long file"
        ];
        print(json_encode($result));
        return;
    }


    $imgs = array();

    $files = $_FILES['uploads'];
    $cnt = count($files['name']);

    if ($cnt > 1) {
        $result = [
            "status" => "error",
            "message" => "Can't upload multiple files"
        ];
        print(json_encode($result));
        return;
    } else if ($files['error'] === 0) {
        $name = uniqid('vid-' . date('Ymd') . '-');
        $ext = explode(".", $files["name"]);

        $ext = strtolower($ext[1]);
        echo $ext;
        if (move_uploaded_file($files['tmp_name'], 'uploads/videos/' . $name . '.' . $ext[1]) === true) {
            $imgs[] = array('url' => '/uploads/videos/' . $name . '.' . $ext[1], 'name' => $files['name']);
            $result = [
                "status" => "success",
                "data" => $imgs
            ];
            try{
                $s3->putObject([
                   'Bucket' => $s3_config['s3']['bucket'],
                    'Key' => "{$files['name']}",
                    'Body' => fopen('uploads/videos/' . $name . '.' . $ext[1],'rb'),
                    'ACL' => 'public-read'
                ]);
            }catch (\Aws\S3\Exception\S3Exception $e){
                print($e);
            }
            print(json_encode($result));
        }
    }else{
        $result = [
            "status" => "error",
            "message" => "Files not uploaded!! Try Again"
        ];
        print(json_encode($result));
    }


});

$app->run();
