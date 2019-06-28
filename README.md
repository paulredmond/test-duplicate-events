# Overview of Duplicate Event Handlers When Using Laravel's Event Service Discovery

__Note: using multiple event providers in general feels weird to me; coupled with discovery it is a weird, complex edge-case and I'd probably wouldn't use discovery with multiple event handlers IMHO.__

This is mostly to demonstrate how to `event:list` can output duplicate events if multiple event providers are discovering the same paths. It's not necessarily anything wrong with the framework code but perhaps confusing to unravel.

It's not very clear that if you are using multiple providers that your additional providers will list out duplicate events unless you are discovering a different path than the `EventServiceProvider`.

## Details

There are a few separate considerations:

1. Although PRs and issues have been made to address using multiple event providers, it still may not be clear why people are seeing duplicate events in `event:list` even if the provider doesn't subscribe any events.

2. The `event:cache` and `event:list` now use providers as array keys, but if using discovery in multiple providers `event:list` doesn't necessarily mean that the events are subscribed appropriately. This can be confusing just because although these event listeners are listed in the output, they might not actually register any listeners. I guess that's probably more onus on those wanting to use mutiple event providers.

3. The `event:cache` method caches the discovered handlers with keys for each provider that extends Laravel's `EventServiceProvider`. The implementation is fine but wanted to point out that again, the file doesn't guarantee that a listener is ever created in the other service providers besides `App\Providers\EventServiceProvider`. Again, this might be more confusion than any fix needed.

## Steps to Reproduce / Experiment

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
