<p align="center">
  <img src="https://github.com/acidjazz/humble/raw/master/logo.jpg"/>
</p>

> Ideal Sessioning and Authentication for Laravel

[![Packagist License](https://poser.pugx.org/acidjazz/humble/license.png)](https://choosealicense.com/licenses/apache-2.0/)
[![Latest Stable Version](https://poser.pugx.org/acidjazz/humble/version.png)](https://packagist.org/packages/acidjazz/humble)
[![Total Downloads](https://poser.pugx.org/acidjazz/humble/d/total.png)](https://packagist.org/packages/barryvdh/humble)

## Features
* Passwordless Authentication
  * Ability to store and compare a cookie, securing the magic link sent out
* Detailed sessions using Jenssegers [Agent](https://github.com/jenssegers/agent)

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

Publish Humble's migrations of it's sessions table
```bash
php artisan vendor:publish --tag="humble.migrations"
```

Run the migration
```bash
php artisan migrate
```


