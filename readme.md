<p align="center">
  <img src="https://github.com/acidjazz/humble/raw/master/logo.jpg"/>
</p>

> Ideal Sessioning and authentication for Laravel

[![Packagist License](https://poser.pugx.org/acidjazz/humble/license.png)](https://choosealicense.com/licenses/apache-2.0/)
[![Latest Stable Version](https://poser.pugx.org/acidjazz/humble/version.png)](https://packagist.org/packages/acidjazz/humble)
[![Total Downloads](https://poser.pugx.org/acidjazz/humble/d/total.png)](https://packagist.org/packages/acidjazz/humble)

## Features
* Passwordless authentication
  * Ability to store and compare a cookie, securing the magic link sent out
  * Ability for magic links to expire
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
composer require acidjazz/humble
```

Add Humble's trait to your user model:

```php
use acidjazz\Humble\Traits\Humble;
...
class User extends Authenticatable
{
  use Humble, Notifiable;
}
```

Publish Humble's migrations (sessions table)
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

If your user class is not `App\User`, we need to tell humble what it is:

Publish Humble's configuration
```
 php artisan vendor:publish --tag="humble.config"
```
Modify `config/humble.php` and specify your user class


