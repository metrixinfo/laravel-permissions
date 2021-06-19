# Fine Grain User and Role Permissions for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/metrixinfo/laravel-permissions.svg?style=flat-square)](https://packagist.org/packages/metrixinfo/laravel-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/metrixinfo/laravel-permissions.svg?style=flat-square)](https://packagist.org/packages/metrixinfo/laravel-permissions)
![GitHub Actions](https://github.com/metrixinfo/laravel-permissions/actions/workflows/main.yml/badge.svg)

Laravel permissions allows you to create permissions and roles.
You can assign one or many roles to a user, and they will inherit the permissions assigned to those roles.
You may also assign specific permissions to a user. 


## Installation

You can install the package via composer:

```bash
composer require metrixinfo/laravel-permissions
```

## Usage

Simply include the traits in your User Model.

```php
use \Metrix\LaravelPermissions\Traits\HasPermissions;
use \Metrix\LaravelPermissions\Traits\HasRoles;
```

You can check for permissions in any area in your code that you like. 
One of the preferred location would be within a Policy or a Gate method.

For a policy it could look something like to allow the auther and 
someone a role of "Moderator" to edit a post:

```php
/**
 * Determine whether the user can edit a post.
 *
 * @param  \App\Models\User  $user
 * @param  \App\Models\Post  $post
 * 
 * @return bool
 */
public function update(User $user, Post $post):bool
{
    return $user->id === $post->user_id || Acl::hasEdit('posts');
}
```

Or it can be used to protect private areas of your site such as to only allow specific 
user to see the Horizon dashboard.

```php

/**
 * Register the Horizon gate.
 *
 * This gate determines who can access Horizon in non-local environments.
 *
 * @return void
 */
protected function gate()
{
    Gate::define('viewHorizon', function ($user) {
        return Acl::hasRead('horizon');
    });
}

```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please use the issue tracker.

## Credits

-   [Michael Love](https://github.com/metrixinfo)
-   [All Contributors](../../contributors)

## License

The GNU GPLv3. Please see [License File](LICENSE.md) for more information.
