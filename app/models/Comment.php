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

    public function user(){
        return $this->belongsTo('User');
    }

}