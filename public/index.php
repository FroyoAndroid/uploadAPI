<?php

require __DIR__.'/../vendor/autoload.php';

$app = new Slim\Slim();

$app->get('/', function() use($app){
    $app->response->headers->set('Content-Type','application/json');
//    $user = new User;
//    $user->username = "Test User2";
//   echo $user->save();
//    $alluser = User::all();
    $alluser = User::where('first_name','niraj')->first();
    echo $alluser->toJson();
});

$app->run();
