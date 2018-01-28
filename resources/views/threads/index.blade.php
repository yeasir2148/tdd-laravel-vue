@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            
            @include('threads._thread_collection')

            {{ $threads->links() }}
        </div>

        <!--  dd($popular_threads)  -->
        
        @if(count($popular_threads))
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading text-center">
                    <h4>Popular threads</h4>
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                    @foreach($popular_threads as $thread)
                        <li class="list-group-item">
                            <a href="{{ url($thread->thread_path) }}">{{ $thread->thread_title }}</a>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection