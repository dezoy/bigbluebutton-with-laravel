@extends('bbb.layouts_default')
@section('content')

  <h1>Meetings List</h1>

	<table class="table table-bordered">
		<thead>
		  <tr>
			<th>Meeting ID </th>
			<th>Meeting Name</th>
			<th>Attendee Password</th>
			<th>Moderator Password</th>
			<th>Duration</th>
			<th>Info</th>
			<th>Moderator</th>
			<th>Attendee</th>
			<th>Close</th>
		  </tr>
		</thead>
		<tbody>
        @php
			$meetingsList = DB::table("meetings")->get();
			$i=1;
        @endphp
		@foreach($meetingsList as $key => $meeting){
		  <tr>
			<td><a href="{{ url('/meeting/info/') }}/{{ $meeting->moderator_password;?>/{{ $meeting->meetingID;?>"  target="_blank" >{{ $meeting -> meetingID }}</a></td>
			<td>{{ $meeting->meetingName }}</td>
			<td>{{ $meeting->moderator_password }}</td>
			<td>{{ $meeting->attendee_password }}</td>
			<td>{{ $meeting->duration }} Min</td>
			<td><a href="{{ url('/meeting/info/') }}/{{ $meeting->moderator_password;?>/{{ $meeting->meetingID }}"  target="_blank" >info</a></td>
			<td><a href="{{ url('/meeting/join/') }}/Moderator {{ $i }}/{{ $meeting->moderator_password;?>/{{ $meeting->meetingID }}"  target="_blank" >join</a></td>
			<td><a href="{{ url('/meeting/join/') }}/Demo {{ $i }}/{{ $meeting->attendee_password }}/{{ $meeting->meetingID }}" target="_blank" >join</a></td>
			  <td><a href="{{ url('/meeting/close/') }}/{{ $meeting->moderator_password }}/{{ $meeting->meetingID }}"  target="_blank" >close</a></td>
		  </tr>
			$i++;
        @endforeach
		</tbody>
	  </table>
@endsection
