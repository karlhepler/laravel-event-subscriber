<?php

namespace OldTimeGuitarGuy\LaravelEventSubscriber;

use File;
use Illuminate\Console\Command;

class MakeEventSubscriber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:event-subscriber
                            {name : The name of your event subscriber.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new event subscriber.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->arguments('name');

        if ( $this->subscriberExists($name) ) {
            return $this->error($this->path($name) . ' already exists!');
        }

        $this->createSubscriber($name);

        $this->info("New event subscriber created in " . $this->path($name));
        $this->info("Don't forget to register the subscriber in app/Providers/EventServiceProvider.php");
    }

    ///////////////////////
    // PROTECTED METHODS //
    ///////////////////////

    /**
     * Create the event subscriber
     *
     * @param  string $name
     * @return void
     */
    protected function createSubscriber($name)
    {
        File::put($this->path($name), $this->populateStub($name, $this->stub()));
    }

    /**
     * Determine if the subscriber already exists
     *
     * @param  string $name
     * @return boolean
     */
    protected function subscriberExists($name)
    {
        return File::exists($this->path($name));
    }

    /////////////////////
    // PRIVATE METHODS //
    /////////////////////

    /**
     * Get the file stub
     *
     * @return string
     */
    private function stub()
    {
        return File::get(__DIR__.'/EventSubscriber.stub');
    }

    /**
     * Populate the stub with a class name
     *
     * @param  string $name
     * @param  string $stub
     * @return string
     */
    private function populateStub($name, $stub)
    {
        return str_replace('DummyClass', $name, $stub);
    }

    /**
     * Get the output path for the subscriber
     *
     * @param  string $name
     * @return string
     */
    private function path($name)
    {
        return app_path("Subscribers/{$name}.php");
    }
}
