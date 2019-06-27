# Steps to Reproduce in a New Repo (Laravel 5.8.26)

```console
laravel new test-duplicate-events

cd $_

php artisan make:provider ExampleProvider
# Configure the provider in `config/app.php`
# Update the provider to extend from `Illuminate\Foundation\Support\Providers\EventServiceProvider`
# Enable event discovery in `App\Providers\ExampleProvider and `App\Providers\EventServiceProvider`

php artisan make:listener ExampleEventListener
# Update the `handle()` property typehint to `Illuminate\Auth\Events\Registered`

# Clear cache just in case
php artist event:clear
php artisan event:list
+-----------------------------------+-------------------------------------------------------------+
| Event                             | Listeners                                                   |
+-----------------------------------+-------------------------------------------------------------+
| Illuminate\Auth\Events\Registered | App\Listeners\ExampleEventListener@handle                   |
|                                   | Illuminate\Auth\Listeners\SendEmailVerificationNotification |
|                                   | App\Listeners\ExampleEventListener@handle                   |
+-----------------------------------+-------------------------------------------------------------+
```

Example cache output after steps running `php artisan event:cache`:

```
<?php return array (
  'App\\Providers\\EventServiceProvider' =>
  array (
    'Illuminate\\Auth\\Events\\Registered' =>
    array (
      0 => 'App\\Listeners\\ExampleEventListener@handle',
      1 => 'Illuminate\\Auth\\Listeners\\SendEmailVerificationNotification',
    ),
  ),
  'App\\Providers\\ExampleProvider' =>
  array (
    'Illuminate\\Auth\\Events\\Registered' =>
    array (
      0 => 'App\\Listeners\\ExampleEventListener@handle',
    ),
  ),
);
```
