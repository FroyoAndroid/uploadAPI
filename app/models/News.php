<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class News extends Eloquent {

    protected $table = 'amasi_news';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}