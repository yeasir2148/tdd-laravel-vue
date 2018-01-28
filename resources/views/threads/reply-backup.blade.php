<div class="panel panel-success">
            <div class="panel-heading">
                <div class="flex-container">
                    <h5 class="stretch">
                        {{ $reply->created_at->diffForHumans() }}, 
                        <a href="{{ route('user.profile',['user'=>$reply->owner->id]) }}">{{ $reply->owner->name }}</a> said<b>...</b>:
                    </h5>

                    <favorite :reply="{{ $reply }}" auth_user_id="{{ \Auth::check() ? auth()->user()->id : 'null' }}">
                        
                    </favorite>
                    <!-- <form method="post" action="{{ route('reply.favorite',['id'=>$reply->id]) }}">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-success btn-xs" {{ $reply->isFavoritedByCurrentUser()? 'disabled' :''}}>
                            {{ $reply->favorites_count }} like
                        </button>
                    </form> -->    
                </div>
            </div>
            <div class="panel-body">
                <div v-if="edit">
                    <div class="form-group">
                        <textarea class="form-control" name="edit-reply" rows="2" v-model="reply_body"></textarea>

                    </div>
                    <button type="button" class="btn btn-xs btn-link" @click="updateReply">Update via ajax</button>
                    <button type="button" class="btn btn-xs btn-link" @click="edit = false">Cancel</button>
                    <button type="button" class="btn btn-xs btn-link" @click="updateWithAxios">with axios</button>

                </div>
                <div v-else v-text="reply_body">
                    
                </div>
            </div>
            @can('update',$reply)
            <div class="panel-footer flex-container">
                <button type="button" class="btn btn-info btn-xs mr-1" @click="edit = true">Edit</button>
                <button type="button" class="btn btn-danger btn-xs" @click="deleteReply">Delete</button>
                <!--form method="post" action="{{ route('reply.delete',['reply'=>$reply->id]) }}">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                </form-->
            </div>
            @endcan
        </div>