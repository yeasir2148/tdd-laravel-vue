@extends('layouts.app')

@section('content')
<thread :auth_user_id="{{ \Auth::check() ? auth()->user()->id : 'null' }}" :initial_replies_count="{{ $thread->repliesCount }}"  inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="flex-container">
                            <div class="stretch">
                                <span class="font-weight-bold">{{ $thread->title }}, </span>
                                <br />
                                posted by: <a href="{{ route('user.profile',['user'=>$thread->creator->id]) }}">{{ $thread->creator->name }}</a>
                            </div>
                            
                            @can('update',$thread)
                            <form method="post" action="{{ route('thread.delete',['channel'=>$thread->channel->slug,'thread'=>$thread->id]) }}">
                                {{ method_field('delete') }}
                                {{ csrf_field() }}
                                
                                <button class="btn btn-danger btn-xs">Delete</button>    
                                
                            </form>
                            @endcan
                        </div>
                    </div>

                    <div class="panel-body">
                        {{ $thread->body }}
                        
                    </div>
                </div>
                
                
                <div class="panel panel-primary" id="reply_panel" v-show="replies_count > 0 ? true : false">
                    <div class="panel-heading">
                        <h4>Thread replies</h4>
                    </div>
                    <div class="panel-body">
                        <replies :all_replies="{{ $thread->replies }}" :auth_user_id="auth_user_id" :thread_id="{{ $thread->id }}" @repliescountchanged="updaterepliescount" ></replies>

                        
                    </div>

                </div>
                
                
                
                <new-reply id="reply_textarea" :thread="{{ $thread }}" :is_curr_user_subscribed="{{ json_encode($thread->isCurrentUserSubscribed()) }}" @newreplyadded="updateReplyList">                    
                </new-reply>

                <!-- @if(Auth::check()) -->
                <!-- <div class="panel panel-success">
                    <div class="panel-body">
                        <form method="post" action="{{ route('reply.store',$thread->id) }} ">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <textarea class="form-control" name="body" id="new_reply" rows="3" style="width:100%" placeholder="Have something to say?"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm pull-right">Post Reply</button>
                        </form>
                    </div>
                </div> -->
                <!-- @endif -->
                
                <!--  -->
                <!-- <flash type="alert-success" v-show="{{ session('flash') ? true: false }}">Hello</flash> -->

            </div>

            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-body text-center">
                        <p>This thread was posted by <a href="{{ route('user.profile',['user'=>$thread->creator->id]) }}">{{ $thread->creator->name }}</a>
                            and it currently has <span v-text="replies_count"></span> {{ str_plural('comment', $thread->replies_count) }}
                        </p>

                        <thread-subscribe :subscribed="{{ json_encode($thread->isCurrentUserSubscribed()) }}" :thread="{{ $thread }}"></thread-subscribe>

                    </div>
                </div>
            </div>
        </div>

    </div>
</thread>
@endsection

@section('page_specific_js')
    
    <script>
        
        $('#new_reply').atwho({
            at: "@",
            //data: [ { 'name' : 'Peter' }, { 'name' : 'Tom' }, { 'name' : 'Anne' } ],
            //data: "{{ route('users.all') }}",
            headerTpl: '<h5 class="text-center">select user</h5>',
            insertTpl: "${atwho-at}${name}",
            searchKey: "name",
            delay: 2000,
            callbacks: {
                remoteFilter: function(query, render_callback){
                    axios.get(route('users.all',{ q : query }))
                        .then(serverResponse => {
                            console.log(serverResponse.data);
                            render_callback(serverResponse.data);
                        });
                },
            },
        });

    </script>
@endsection