<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class Comment extends Eloquent{
    protected $table = 'comment_news';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public function User(){
        return $this->belongsTo('User');
    }

    public function getUser(){
        return User::select('first_name', 'last_name' , 'profile')->where('id',$this->user_id)->get();
    }

}