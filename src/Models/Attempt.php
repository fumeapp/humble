<?php

namespace acidjazz\Humble\Models;

use Illuminate\Database\Eloquent\Model;
use Eloquent;


/**
 * Class Attempt
 * @package acidjazz\Humble\Models
 * @mixin Eloquent;
 */
class Attempt extends Model {

    protected $guarded = [];
    protected $primaryKey = 'token';
    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(config('humble.user'));
    }
}
