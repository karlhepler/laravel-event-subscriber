<?php

namespace OldTimeGuitarGuy\LaravelEventSubscriber;

use ReflectionClass;
use ReflectionMethod;
use DirectoryIterator;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;

/**
 * Just extend your event subscribers from this
 * and subscribing to events will be clean & easy.
 * https://laravel.com/docs/5.2/events#event-subscribers
 */
class EventSubscriber
{
    private $eventSubDirectories;

    /**
     * Subscribe to all events referenced in the class
     *
     * @param  \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe(Dispatcher $events)
    {
        // Get the reflection class instance
        $subscriber = new ReflectionClass($this);

        // Go through each public method & listen to all event methods
        foreach ($subscriber->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Continue if the method doesn't start with "on"
            if ( strpos($method, 'on') === false ) continue;

            // Get the event
            $event = $this->getEvent($method);

            // If there is no event, then just continue
            if ( is_null($event) ) continue;

            // Listen for the event!
            $events->listen($event, $subscriber->getName().'@'.$method->name);
        }
    }

    /**
     * Get the event name
     *
     * @param  ReflectionMethod $method
     * @return string|null
     */
    private function getEvent(ReflectionMethod $method)
    {
        // The method name starts with "on", so remove that
        $eventName = substr($method->name, 2);

        // First check custom event classmap
        $event = $this->checkCustomEventClassmap($eventName);

        // If an event is returned, then just return that!
        if (! is_null($event) ) {
            return $event;
        }

        // Return the first match or null
        $path = array_first(
            $this->getEventSubDirectories(),
            function($key, $path) use ($eventName) {
                return class_exists("App\\Events\\{$path}\\{$eventName}");
            }, null);

        // Return null if path is null
        if ( is_null($path) ) return null;

        // All's good! Return the full event classname
        return "App\\Events\\{$path}\\{$eventName}";
    }

    /**
     * Check the custom event classmap
     * & return the fully-qualified classname
     * if the event exists
     *
     * @param  string $eventName
     * @return string|null
     */
    private function checkCustomEventClassmap($eventName)
    {
        $classmap = config("event_subscriber.custom_event_classmap", []);

        return array_first($classmap, function($key, $path) use ($eventName) {
            $isMatch = preg_match('/'.$eventName.'$/', $path, $matches) === 1;
            return $isMatch && class_exists(array_get($matches, 1));
        }, null);
    }

    /**
     * Get the subdirectories under app/Events
     *
     * @return array
     */
    private function getEventSubDirectories()
    {
        // Quick return if it already exists
        if ( isset($this->eventSubDirectories) ) {
            return $this->eventSubDirectories;
        }

        $output = [];

        // Add only directories to the output
        foreach (new DirectoryIterator(app_path('Events')) as $path) {
            if ( $path->isDir() && strpos($path->getFilename(), '.') === false ) {
                $output[] = $path->getFilename();
            }
        }

        return $output;
    }
}
