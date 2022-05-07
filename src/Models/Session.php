<?php

namespace acidjazz\Humble\Models;

use acidjazz\Humble\Guards\HumbleGuard;
use Illuminate\Database\Eloquent\Model;
use Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

  public static function hash(): string
  {
    return hash('sha256', mt_rand());
  }

  public function getDeviceAttribute (): array
  {
      return HumbleGuard::device($this->agent);
  }

  public function getCurrentAttribute (): bool
  {
    $token = request()->get('token') ?: request()->bearerToken() ?:  request()->cookie('token') ?: false;
    return $this->token === $token;
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(config('humble.user'));
  }

}


