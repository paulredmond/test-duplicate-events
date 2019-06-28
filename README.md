# Overview of Duplicate Event Handlers When Using Laravel's Event Service Discovery

__Note: using multiple event providers in general feels weird to me; coupled with discovery it is a weird, complex edge-case and I'd probably wouldn't use discovery with multiple event handlers IMHO.__

This is an edge case for projects using both event discovery and multiple event providers. This can be reproduced when multiple providers extend the framework's `\Illuminate\Foundation\Support\Providers\EventServiceProvider` provider and calling the `parent::boot()` method.

To quickly see the registered event listeners caused by multiple event providers run:

```
php artisan debug:listeners
```

## Details

There are a few separate issues here:

1. Event when this feature was released in `v5.8.9` multiple providers extending `\Illuminate\Foundation\Support\Providers\EventServiceProvider` duplicate listeners for me when using discovery if the additional event service provider classes call the `parent::boot()` method. This only affects projects using discovery with multiple event providers.

You can mess around with seeing the duplicated handlers by updating `composer.json` to `"laravel/framework": "5.8.9",` and running `composer update laravel/framework`:

```
# Provided to debug from the CLI
php artisan debug:listeners
```

2. The `event:list` command's output will output the duplicate handlers for each provider using discovery even if the actual event listeners aren't registering duplicate listeners by calling `parent::boot()` (i.e., a second provider class extending `EventServiceProvider` but not calling `parent::boot()` from within it's own `boot()` method). This can be confusing just because although these events listeners are listed in the output, they might not actually register any listeners if omitting the `parent::boot()` method.

3. The `event:cache` method caches the discovered handlers with keys for each provider that extends Laravel's `EventServiceProvider`. That means when the `\Illuminate\Foundation\Support\Providers\EventServiceProvider::boot()` method is called from within the provider, the `getEvents()` method will register duplicated listeners (see [here on GitHub](https://github.com/laravel/framework/blob/0e7f0cfadf1a0fb64c9c4bd0a2bfc345e23f186e/src/Illuminate/Foundation/Support/Providers/EventServiceProvider.php#L62-L65)):

```
return $cache[get_class($this)] ?? [];
```

Even before this change, the additional event providers would register the duplicate listeners without the new array structure introduced in [#28904](https://github.com/laravel/framework/pull/28904).

Further, even if you are not using service discovery you can create duplicate listeners via the `$listen` property if using multiple event providers and should be up to the end-user to avoid duplicates:

```
protected $listen = [
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],
];
```

The onus should be on the end-user not the framework to avoid duplicate events, however, automatic discovery with multiple event providers might not be ideal unless dealing with duplicates is addressed.

## Steps to Reproduce

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
