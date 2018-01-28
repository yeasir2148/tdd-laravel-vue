<div class="panel panel-info">
    <div class="panel-heading">
        <div class="flex-container">
            <h5 class="stretch">
                <a href="{{ route('threads.show',['channel'=>$thread->channel,'thread'=>$thread->id]) }}">{{ $thread->title }}</a> 
            </h5>
            <span>{{ $thread->created_at->diffForHumans() }}</span> 
        </div>
    </div>
    <div class="panel-body">
        {{ $thread->body }}
    </div>
</div>