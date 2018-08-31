<?php

namespace acidjazz\Humble\Models;

use Illuminate\Database\Eloquent\Model;

use Jenssegers\Agent\Agent;

class Session extends Model {

  protected $fillable = ['token','user_id','source','cookie','verified', 'to', 'ip','agent'];
  protected $primaryKey = 'token';
  public $incrementing = false;
  public $appends = ['device'];

  public static function hash()
  {
    return hash('sha256', mt_rand());
  }

  public function getDeviceAttribute ()
  {
    $agent = new Agent();
    $agent->setUserAgent($this->agent);
    return [
      'platform' => $agent->platform(),
      'browser' => $agent->browser(),
      'device' => $agent->device(),
      'desktop' => $agent->isDesktop(),
      'mobile' => $agent->isMobile(),
    ];
  }

}


