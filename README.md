# Fine Grain User and Role Permissions for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/metrixinfo/laravel-permissions.svg?style=flat-square)](https://packagist.org/packages/metrixinfo/laravel-permissions)
[![Total Downloads](https://img.shields.io/packagist/dt/metrixinfo/laravel-permissions.svg?style=flat-square)](https://packagist.org/packages/metrixinfo/laravel-permissions)

**Laravel Permissions** allows you to create fine grain permissions and assign them to users and roles.

You may assign zero or many roles to a user, and they will inherit the permissions assigned to those roles.
You may also assign specific permissions to a specific user. 

A user's permissions are cached for increased performance. 

## Requirements
- PHP 7.4|8.0
- Laravel 7.x|8.x

## Installation

You can install the package via composer:
```bash
composer require metrixinfo/laravel-permissions
```

Publish the configuration by running the following artisan command:
```bash
php artisan vendor:publish --provider="Metrix\LaravelPermissions\LaravelPermissionsServiceProvider" --tag="permissions"
```

Run the migrations to create the required tables.
```bash
php artisan migrate
```
The migrations will create the following tables:
- permissions
- roles
- role_user
- permission_user
- permission_role

Foreign Key constraints are enforced to guarantee data integrity. 
If you will be refreshing your database in your local environment 
you will need to disable them in the ```down()``` method of your ```users``` table migration.

*database/migrations/02014_10_12_000000_create_users_table.php*

```php 
 /**
  * Reverse the migrations.
  *
  * @return void
  */
 public function down(): void
 {
     Schema::disableForeignKeyConstraints();
     Schema::dropIfExists('users');
     Schema::enableForeignKeyConstraints();
 }
```

## Usage

Include the following two traits in your User Model.

```php
use \Metrix\LaravelPermissions\Traits\HasPermissions;
use \Metrix\LaravelPermissions\Traits\HasRoles;
```

### Permissions
A permission is described by an 'area' defined by you.
Examples of areas could be 'blog.post' and 'blog.comment'.
It is up to you to create the permissions your app requires by inserting them into the permissions table.

Each assigned permission can have a combination of these 4 actions: 
- Read
- Write 
- Edit
- Delete

You can check for permissions in any area of your code that you like. 
Some preferred locations would be within a Policy or a Gate method.

For a policy it could look something like this to allow the author and 
someone with a role of "Moderator" to edit a post:

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
users to see the Horizon dashboard.

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

## Console Commands
Console commands are provided to help manage your permissions. 

You can flush all the cached permissions:
```bash
php artisan acl:clear
```
or only the permissions belonging to a specific user.
```bash
php artisan acl:clear -u 212
```

You may manage permissions using the acl:permissions artisan command.
```bash
php artisan acl:permissions
```

You may manage roles using the acl:roles artisan command.
```bash
php artisan acl:roles
```

### Changelog

Please see [CHANGELOG](CHANGELOG) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please use the issue tracker.

## Credits

-   [Michael Love](https://github.com/metrixinfo)
-   [All Contributors](../../contributors)

This package is inspired by the work done by Harro Verton (WanWizard) for [FuelPHP's](https://fuelphp.com/docs/packages/auth/ormauth/intro.html) OrmAuth package.

## License

The GNU GPLv3. Please see [License File](LICENSE.md) for more information.
