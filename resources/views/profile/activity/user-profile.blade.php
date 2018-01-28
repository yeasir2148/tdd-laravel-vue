@extends('layouts.app')
@section('content')
<div class="container">
    <div class="page-header">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="flex-container">
                    <div class="stretch">
                        <div class="flex-container">
                            <img src="{{ asset('storage/'.$profile_user->avatar_location) }}" alt="{{ $profile_user->name }}" width="50" height="50" class="mr-1">
                            <div>
                                <h4 class="_mar_bot_0">{{ $profile_user->name }}</h4>
                                <a href="mailto:{{ $profile_user->email }}">{{ $profile_user->email }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="text-blue">Member since: {{ $profile_user->created_at->format('d-m-Y') }}</div>
                </div>

                <br />
                @if(Gate::allows('update-profile',$profile_user))
                <div class="panel panel-success">
                    
                    <div class="panel-body">
                        
                        <form method="POST" action="{{route('user.avatar.store',$profile_user->id)}}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="avatar_image" class="col-form-label">Choose an image</label>
                                <input class="form-control" type="file" name="avatar" id="avatar_image" accept="image">
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Upload</button>
                        </form>                            
                        
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    
        <!-- <div class="row">
            
        </div> -->
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading text-center">
                    <p style="font-size:1.5em"><strong>Recent activity</strong></p>
                </div>
            </div>
            @forelse($user_activities as $date=>$activities)
            <div class="panel panel-success">
                <div class="panel-heading">
                    {{ $date }}
                </div>
                <div class="panel-body">
                    @foreach($activities as $activity)
                        <div class="panel panel-info">
                            <div class="panel-body">
                                @include('profile.activity.activity-'.$activity->object_type)
                            </div>
                        </div>
                    @endforeach
                    
                </div>
            </div>
            @empty
            <div class="page-header">
                <!--div class="row">
                    <div class="col-md-6 col-md-offset-2 text-center"-->
                        <h3>There are no recent activities !!</h3>
                    <!--/div>
                </div-->
                
            </div>
    @endforelse

        </div>
    </div>
    
    
</div>
@endsection