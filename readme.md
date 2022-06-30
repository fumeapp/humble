<p align="center">
  <img src="https://github.com/fumeapp/humble/raw/master/logo.jpg"/>
</p>

> Ideal Sessioning and authentication for Laravel

[![Packagist License](https://poser.pugx.org/fumeapp/humble/license.png)](https://choosealicense.com/licenses/apache-2.0/)
[![Latest Stable Version](https://poser.pugx.org/fumeapp/humble/version.png)](https://packagist.org/packages/fumeapp/humble)
[![Total Downloads](https://poser.pugx.org/fumeapp/humble/d/total.png)](https://packagist.org/packages/fumeapp/humble)

## Features
* Passwordless authentication
  * Ability to store and compare a cookie, securing the magic link sent out
  * Link expiration
  * Able to store "action" objects passed through for completing tasks the user was doing before prompted
* Detailed sessions using [whichbrowser](https://github.com/WhichBrowser/Parser-PHP)

```json
"device": {
  "string": "Chrome 68 on a Google Pixel 2 XL running Android 9",
  "platform": "Android 9",
  "browser": "Chrome 68",
  "name": "Google Pixel 2 XL",
  "desktop": false,
  "mobile": true
}
```

* Detailed location using [lyften](https://github.com/Torann/laravel-geoip)'s adapter for [GeoIP2](https://github.com/maxmind/GeoIP2-php)

```json
"location": {
  "ip": "86.222.88.167",
  "country": "France",
  "city": "Lons",
  "state": "NAQ",
  "postal_code": "64140",
  "lat": 43.3167,
  "lon": -0.4,
  "timezone": "Europe\/Paris",
  "currency": "EUR"
}
```

## Installation

Install humble with [composer](https://getcomposer.org/doc/00-intro.md):
```bash
composer require fumeapp/humble
```

Add Humble's trait to your user model:

```php
use Fumeapp\Humble\Traits\Humble;
...
class User extends Authenticatable
{
  use Humble, Notifiable;
}
```

### Publish Humble's migrations (sessions table)
```bash
php artisan vendor:publish --tag="humble.migrations"
```

Run the migration
```bash
php artisan migrate
```

Change your guard in your config, to the 'humble' guard in `config/auth.php`, in my case since I mainly use Laravel as an API
```php
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'humble',
            'provider' => 'users',
        ],
    ],
```
> Check your defaults as well, if it's not api, you'll need to change that

If your user class is not `App\Models\User`, we need to tell humble what it is:

### Publish Humble's configuration
```
 php artisan vendor:publish --tag="humble.config"
```
Modify `config/humble.php` and specify your user class

## Usage

Humble is similar to [Laravel Sanctum](https://laravel.com/docs/9.x/sanctum#introduction) in the way you can also assign abilties to sessions.

Primarily humble sessions are stored for user sessions which by default would inherit all session abilities. You can also think of Humble Session as Personal Access Tokens, inside your application your users could create multiple session tokens for various services and integrations like: GitHub actions, CLI Applications, Slack Tokens, etc...

With these addtional session tokens your users can create, you still have access to the session `source` where you can attach certain Gates & Policy behaviors based on that. If your are fine with leaving these session tokens as-is with same access as the user you can stick with that. If you need more granularity you can also assign abilites.

With abilites you can addtionally set specific rules these session tokens can have. For example you might want to have a session tokens for a GitHub action that only has the abilty to peform READ events and not WRITE.

These abilites can be user defined in your app, meaing its up to you to declare these rules in your app and check/valdiate them. The default ability is set to 
`["*"]` which means full access, but when creating a session you can pass a parameter to only set this to READ, and which your database record would show as 
`["READ"]`

For valdiating these abilites we provide a few helpers and middlewares to make this easier.

To validate the middleware level you first need to add the following to your `$routeMiddleware` inside of `app/Http/Kernel.php`

```php
// ...
'abilities' => \Fumeapp\Humble\Http\Middleware\CheckAbilities::class,
'ability' => \Fumeap\Humble\Http\Middleware\CheckForAnyAbility::class,
// ...
```

Once you add those, you can apply them in your routes middleware like so:

```php
Route::get('admin', function () {
    return response()->json([
        'success' => true
    ]);
})->middleware(['auth:api', 'ability:admin']);
```

> The example would pass if that session token had the abilites as: `["*"]` or `["admin"]`

You can also use `abilities` as middleware which is a strict check that the token must have all the given abilites.

Other ways of checking/valdiating abilites inside of your app in areas like Gates & Polciies

1. `auth()->user()->tokenCan('write')`
2. `auth()->session()->can('write')`
3. `$user()->tokenCan('write')`
4. `$request->user()->tokenCan('write')`


We also provide an easy way to create new session tokens with the following method

```php
 $user->createToken('action', ['write'])
```

> This would also work with either `auth()->user()->createToken(...)` or `$request->user()->createToken(...)`
