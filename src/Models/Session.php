<?php

namespace acidjazz\Humble\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquent;

use WhichBrowser;

/**
 * Class Session
 * @package acidjazz\Humble\Models
 * @mixin Eloquent
 */
class Session extends Model {

  protected $guarded = [];
  protected $primaryKey = 'token';
  protected $casts = ['location' => 'array'];
  public $incrementing = false;
  public $appends = ['device', 'current'];

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

  public function getCurrentAttribute ()
  {
    $token = request()->get('token') ?: request()->bearerToken() ?:  request()->cookie('token') ?: false;
    return $this->token === $token;
  }

  public function user()
  {
    return $this->belongsTo(config('humble.user'));
  }

}


