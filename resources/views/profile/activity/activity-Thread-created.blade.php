@if(! is_null($activity->object )) <!-- if thread has already been deleted after cretation then $activity->object will return null
    If not deleted, then this condition overall will return true, so show thread name and link to the thread -->
    {{ ucfirst($activity->activity_type) }} thread <a href="{{ route('threads.show',['channel'=>$activity->object->channel->slug,'thread'=>$activity->object->id]) }}">{{ $activity->object->title }}</a>
@else       <!-- Otherwise, thread has been delted already, so just mention thread id, as it is available in activities table -->
    {{ ucfirst($activity->activity_type) }} thread having ID of {{ $activity->object_id }}
@endif