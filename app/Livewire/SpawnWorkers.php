<?php

namespace App\Livewire;

use App\Events\SampleEvent;
use App\Jobs\EventDispatcherJob;
use App\Jobs\InfinitelyRunningJob;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Laravel\Events\ChildProcess\ErrorReceived;
use Native\Laravel\Events\ChildProcess\MessageReceived;
use Native\Laravel\Events\ChildProcess\ProcessExited;
use Native\Laravel\Events\ChildProcess\ProcessSpawned;
use Native\Laravel\Facades\ChildProcess;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use ReflectionClass;

class SpawnWorkers extends Component
{
    #[On('native:'.SampleEvent::class)]
    public function eventListener(): void
    {
        dd('Event received');
    }

    public function spawnWorker(): void
    {
        ChildProcess::artisan('queue:work', 'queue');
    }

    public function killJsLaunchedWorker(): void
    {
        InfinitelyRunningJob::dispatch();
    }

    public function putEventDispatcherJobOnQueue(): void
    {
        EventDispatcherJob::dispatch();
    }

    public function rectorBroadcastNow(): void
    {
        $classes = [
            MessageReceived::class,
            ErrorReceived::class,
            ProcessExited::class,
            ProcessSpawned::class,
        ];

        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        foreach ($classes as $class) {
            $file = file_get_contents(new ReflectionClass($class)->getFileName());
            $stmts = $parser->parse($file);

            /** @var \PhpParser\Node\Stmt\Class_ $class */
            $classNode = new NodeFinder()->findFirstInstanceOf($stmts, \PhpParser\Node\Stmt\Class_::class);

            $classNode->implements = [];
            $classNode->implements[] = new \PhpParser\Node\Name\FullyQualified(ShouldBroadcastNow::class);

            $printer = new \PhpParser\PrettyPrinter\Standard();

            unlink(new ReflectionClass($class)->getFileName());
            file_put_contents(new ReflectionClass($class)->getFileName(), $printer->prettyPrintFile($stmts));
        }
    }

    public function render()
    {
        return view('livewire.spawn-workers');
    }
}
