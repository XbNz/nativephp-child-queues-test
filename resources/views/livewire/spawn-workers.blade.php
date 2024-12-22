<div>
    <div class="flex flex-col space-y-4">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" wire:click="killJsLaunchedWorker">1. Kill JS worker</button>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" wire:click="spawnWorker">2. Spawn ChildProcess Worker</button>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" wire:click="rectorBroadcastNow">3. Make NativePHP ChildProcess events BroadcastNow</button>
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full" wire:click="putEventDispatcherJobOnQueue">4. Put EventDispatcher job on queue</button>
    </div>

    @foreach($implementStmts as $implementStmt)
        <span>
            <pre>{{ $implementStmt }}</pre>
        </span>
    @endforeach
</div>
