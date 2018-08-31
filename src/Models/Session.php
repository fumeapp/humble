<?php

namespace acidjazz\Humble\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model {

  protected $fillable = ['token','user_id','source','cookie','verified', 'to', 'ip','agent'];
  protected $primaryKey = 'token';
  public $incrementing = false;

  public static function hash()
  {
    return hash('sha256', mt_rand());
  }

}


