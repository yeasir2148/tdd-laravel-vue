@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Create New Thread</b></div>
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <ul id="val_errors">
                            <li class="validation-error">{{ $error }}</li>
                        </ul>
                    @endforeach
                @endif
                <div class="panel-body">
                    <form method="post" action="{{ route('thread.store') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="title" class="col-form-label">Thread title</label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ old('title') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="body" class="col-form-label">Thread content</label>
                            <textarea class="form-control" id="body" name="body" row="5" style="width:100%" required>{{ old('body') }} </textarea>
                        </div>

                        <div class="form-group">
                            <label for="channel_id" class="col-form-label">Select channel</label>
                            <select name="channel_id" id="channel_id" class="form-control" required>
                                    <option value="" selected></option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}" @if(old('channel_id') == $channel->id) selected @endif>{{ $channel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                    </form>
                        
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
