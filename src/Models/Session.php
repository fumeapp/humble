<?php

namespace acidjazz\Humble\Models;

use Illuminate\Database\Eloquent\Model;

use WhichBrowser;

class Session extends Model {

  protected $fillable = ['token','user_id','source','cookie','verified', 'to', 'ip','agent'];
  protected $primaryKey = 'token';
  protected $casts = ['location' => 'array'];
  public $incrementing = false;
  public $appends = ['device'];

  public static function hash()
  {
    return hash('sha256', mt_rand());
  }

  public function getDeviceAttribute ()
  {
    $agent = new WhichBrowser\Parser($this->agent);

    return [
      'string' => $agent->toString(),
      'platform' => $agent->os->toString(),
      'browser' => $agent->browser->toString(),
      'name' => $agent->device->toString(),
      'desktop' => $agent->isType('desktop'),
      'mobile' => $agent->isMobile(),
    ];
  }

}


