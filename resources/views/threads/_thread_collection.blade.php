@forelse($threads as $thread)
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="flex-container">
                <h4 class="stretch">
                    @if($thread->hasUpdatedSinceUserVisited(auth()->user()))
                        <a href="{{ route('threads.show',['channel'=>$thread->channel->slug,'thread'=>$thread->id]) }}"><strong>{{ $thread->title }}</strong></a>
                    @else
                        <a href="{{ route('threads.show',['channel'=>$thread->channel->slug,'thread'=>$thread->id]) }}">{{ $thread->title }}</a>
                    @endif
                </h4>
                
                <span><a href="{{ route('threads.show',['channel'=>$thread->channel,'thread'=>$thread->id]) }}#reply_panel">{{ $thread->replies_count }} {{ str_plural('reply',$thread->replies_count) }}</a></span>
            </div>
            
                <h5 class="_mar_top_0 _mar_bot_0">By <a href="{{ route('user.profile',$thread->creator) }}">{{ $thread->creator->name }}</a></h5>
            
        </div>
        <div class="panel-body">
            <article>
                <div>{{ $thread->body }}</div>
            </article>
        </div>

        <div class="panel-footer">
            {{ $thread->popularity ? $thread->popularity->visit_count : 0 }} {{ str_plural('Visit', $thread->popularity? $thread->popularity->visit_count : 0) }}
        </div>
    </div>
@empty
    <div class="panel panel-default">
        <div class="panel-body">
            There are no active threads currently!! :(
        </div>
    </div>
@endforelse