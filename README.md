Laravel Event Subscriber
=========================

Installation
-------------

1. `composer require oldtimeguitarguy/laravel-event-subscriber`
2. Add `OldTimeGuitarGuy\LaravelEventSubscriber\EventSubscriberProvider::class,` to the providers array in `config/app.php`
3. Run `php artisan vendor:publish` to copy the config file to `config/event_subscriber.php`





Usage
------

1. Create subscribers with `php artisan make:event-subscriber SubscriberName`
2. Add custom event classes in `config/event_subscriber.php`





Description
------------

The basic premise of this evolved from [here](https://laravel.com/docs/5.2/events#event-subscribers).

I love the idea, but I don't like how you have to define that `subscribe` method.

This class eliminates that.

Basically create your event subscriber class just like the documentation says,
but now, if you extend from this class, you never have to write the `subscribe` method.

Instead, just prefix all of your event names with `on` as public methods.

So you would do something like this:

```php
class MyEventSubscriber extends EventSubscriber
{
    public function onUserLogin($event)
    {
        // do stuff
    }

    public function onUserLogout($event)
    {
        // do stuff
    }
}
```

That's it. There are a couple of caveats:
------------------------------------------

1. It looks for the events in Laravel's `app/Events` directory. (or any class you add to the classmap in the config file)
2. You can have a maximum of **one** subdirectory under `app/Events/`
3. Be careful about name collisions, even if the event classes exist in different subdirectories under `app/Events`
