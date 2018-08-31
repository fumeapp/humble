<p align="center">
  <img src="https://github.com/acidjazz/humble/raw/master/logo.jpg"/>
</p>

> Ideal Authentication for Laravel

[![Packagist License](https://poser.pugx.org/acidjazz/humble/license.png)](https://choosealicense.com/licenses/apache-2.0/)
[![Latest Stable Version](https://poser.pugx.org/acidjazz/humble/version.png)](https://packagist.org/packages/acidjazz/humble)
[![Total Downloads](https://poser.pugx.org/acidjazz/humble/d/total.png)](https://packagist.org/packages/barryvdh/humble)

## Installation

### Installing humble as in a Laravel app

Install humble with [composer](https://getcomposer.org/doc/00-intro.md):
```
$ composer require acidjazz/humble
```

Add Humble's trait to your user model:

```php
use acidjazz\Humble\Traits\Humble;

class User extends Model
{
  use Humble;
}
```

Publish Humble's migrations of it's sessions table
```
$ php artisan vendor:publish --tag="humble.migrations"
```

Run the migration
```
$ php artisan migrate
```


