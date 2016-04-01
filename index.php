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
$app->response->header('charset', 'utf-8');

$app->get('/users', function () use ($app) {
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

/*This will get all the news based on dates descending*/
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

/*This will get all the news details related to the news/id along with comments on that news*/
$app->get('/news/:newsId', function ($newsId) use ($app) {
    try {
        $commentsArray = array();
        $app->response->headers->set('Content-Type', 'application/json');
        $allnews = News::where('id', $newsId)->first();
        if ($allnews) {
            $comments = News::find($newsId)->getComments();
            foreach($comments as $comment){
                array_push($commentsArray,array(
                    'comment'=> $comment->comment,
                    //'username' => $comment->User->first_name . '' .$comment->User->last_name,
                    'updated_at' => $comment->updated_at,
                    'user' =>(object) $comment->getUser()->first()
                ));

            }

            $result = [
                "status" => "success",
                "data" => array('news' => json_decode($allnews->toJson()), 'comments' => json_decode(json_encode($commentsArray)))
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

$app->get('/video/:eventId', function ($eventId) use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $allVideo = Post::select('id','post_title','likes')->where('event_id', $eventId)->get();
        if ($allVideo) {
            $result = [
                "status" => "success",
                "data" => json_decode($allVideo->toJson())
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


$app->get('/video/detail/:videoId', function ($videoId) use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $allVideo = Post::select('id','post_title','video_src','likes')->where('id', $videoId)->get();
        $commentsArray = array();
        if ($allVideo) {
            $comments = Post::find($videoId)->getComments();
            foreach($comments as $comment){
                array_push($commentsArray,array(
                    'comment'=> $comment->comment,
                    'updated_at' => $comment->updated_at,
                    'user' =>(object) $comment->getUser()->first()
                ));

            }
            $result = [
                "status" => "success",
                "data" => array('video' => json_decode($allVideo->toJson()), 'comments' => json_decode(json_encode($commentsArray)))
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


$app->get('/video', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $approved = "APPROVED" ;
        $allVideo = Post::select('id','post_title', 'video_src')->where('post_status',$approved)->orderBy('updated_at', 'desc')->get();
        if ($allVideo) {
            $result = [
                "status" => "success",
                "data" => json_decode($allVideo->toJson())
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

$app->get('/pending/video', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $approved = "PENDING";
        $allVideo = Post::select('id','post_title', 'video_src')->where('post_status',$approved)->orderBy('updated_at', 'desc')->get();
        if ($allVideo) {
            $result = [
                "status" => "success",
                "data" => json_decode($allVideo->toJson())
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

$app->post('/create/post/comment', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true); // parse the JSON into an associative array
        $comment = new PostComment;
        $comment->user_id = $allPostVars['user_id'];
        $comment->post_id = $allPostVars['post_id'];
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
        $events = Event::where('schedule_start', '>', date("Y-m-d",time()))->get();

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
        $events = Event::where('schedule_start', '=', date("Y-m-d",time()))->get();

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

$app->post('/create/post', function () use ($app) {
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true); // parse the JSON into an associative array
        $post = new Post;
        $post->post_title = $allPostVars['post_title'];
        $post->video_src = $allPostVars['video_src'];
        $post->likes = 0; //@TODO user_id need to be included too here.
        $post->post_status = "PENDING";
        if(isset($allPostVars['category'])){
            $post->category = $allPostVars['category'];
        }else{
            $post->category = "EVENT";
        }
        $post->event_id = $allPostVars['event_id'];
        $post->updated_at = date('Y-m-d', time()); //'10/16/2003'
        $post->created_at = date('Y-m-d', time());
        if (($post->save()) > 0) {
            $result = [
                "status" => "success",
                "message" => $post
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
        $data = User::select('id','first_name', 'last_name', 'email', 'city', 'role', 'age', 'profile')->where('email', $allPostVars['email'])->where('password', md5($allPostVars['password']))->first();
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

$app->get('/user/exist/:email',function($email) use($app){
    try {
        $app->response->headers->set('Content-Type', 'application/json');
        if (User::where('email', '=', $email)->exists()) {
            // user found
            $result = [
                "status" => "success",
                "data" => (object) User::where('email', '=', $email)->get()->first()
            ];
        } else {
            $result = [
                "status" => "error",
                "message" => "No User Found!!!"
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

$app->post('/edit/role/user', function () use ($app) {

    try{
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true);
        $user = User::where('id',$allPostVars['id'])->get()->first();
        if($user){
            $user->role = $allPostVars['role'];
            if($user->save()>0){
                $result = [
                    "status" => "success",
                    "data" => json_decode($user->toJson())
                ];
                print(json_encode($result));
            }

        }
    }catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
        ];
        print(json_encode($result));
    }

});

$app->post('/edit/user', function () use ($app) {

    try{
        $app->response->headers->set('Content-Type', 'application/json');
        $json = $app->request->getBody();
        $allPostVars = json_decode($json, true);
        $user = User::where('id',$allPostVars['id'])->get()->first();
        if($user){
            $user->first_name = $allPostVars['first_name'];
            $user->last_name = $allPostVars['last_name'];
            $user->city = $allPostVars['city'];
            $user->age = $allPostVars['age'];
            if(isset($allPostVars['profile'])){
                $user->profile = $allPostVars['profile'];
            }
            if($user->save()>0){
                $result = [
                    "status" => "success",
                    "data" => json_decode($user->toJson())
                ];
                print(json_encode($result));
            }

        }
    }catch (Exception $error) {
        $result = [
            "status" => "error",
            "message" => $error->getMessage()
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
        $user->profile = $allPostVars["profile"];
        $user->role = "GENERAL";
        $user->time_stamp = date('Y-m-d H:i:s');
        if (($user->save()) > 0) {
            $result = [
                "status" => "success",
                "data" => json_decode($user->toJson())
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
    } else {
        $result = [
            "status" => "error",
            "message" => "Files not uploaded!! Try Again"
        ];
        print(json_encode($result));
    }


});

$app->post('/upload/video', function () use ($app, $s3, $s3_config) {

    //$app->response()->header("Content-Type", "application/json");

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
        if (move_uploaded_file($files['tmp_name'], 'uploads/videos/' . $name . '.' . $ext) === true) {
            $imgs[] = array('url' => '/uploads/videos/' . $name . '.' . $ext, 'name' => $files['name']);
            $result = [
                "status" => "success",
                "data" => $imgs
            ];
            try {
                $s3->putObject([
                    'Bucket' => $s3_config['s3']['bucket'],
                    'Key' => "{$name}.{$ext}",
                    'Body' => fopen('uploads/videos/' . $name . '.' . $ext, 'rb'),
                    'ACL' => 'public-read'
                ]);
            } catch (\Aws\S3\Exception\S3Exception $e) {
                print($e);
            }
            print(json_encode($result));
        }
    } else {
        $result = [
            "status" => "error",
            "message" => "Files not uploaded!! Try Again"
        ];
        print(json_encode($result));
    }


});

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
