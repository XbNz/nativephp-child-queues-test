<?php

namespace App\Livewire;

use App\Events\SampleEvent;
use App\Events\SampleEventB;
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
    public array $targetClasses = [
        ProcessSpawned::class,
        ErrorReceived::class,
        MessageReceived::class,
        ProcessExited::class,
    ];

    #[On('native:'.SampleEvent::class)]
    public function eventListener(): void
    {
        $this->js('alert("SampleEvent received on Livewire listener")');
    }

    #[On('native:'.SampleEventB::class)]
    public function eventListenerB(): void
    {
        $this->js('alert("SampleEvent received on Laravel listener as well")');
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
        $parser = (new ParserFactory())->createForNewestSupportedVersion();

        foreach ($this->targetClasses as $class) {
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
        return view('livewire.spawn-workers', [
            'implementStmts' => array_map(fn(string $class) => new NodeFinder()->findFirstInstanceOf(
                (new ParserFactory())->createForNewestSupportedVersion()->parse(file_get_contents((new ReflectionClass($class))->getFileName())),
                \PhpParser\Node\Stmt\Class_::class,
            )->implements[0]->toString(), $this->targetClasses),
        ]);
    }
}
