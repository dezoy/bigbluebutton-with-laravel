<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use Validator;

use Illuminate\Support\Facades\Crypt;
use App\Utils\BigBlueButtonClass;
use App\Meeting;

class APIController extends Controller
{
	private $meetingID = 208;
	private $meetingName = "fifth  Meeting";
	private $attendee_password = "123";
	private $moderator_password = "321";
	private $duration = 0;
	private $urlLogout = "http://localhost/bigbluebutton/meeting/list";
	private $isRecordingTrue = 1;

	private $params = [];

	public function __construct(){
		$this->meetingID = BigBlueButtonClass::Uuid($this->meetingID);
	}

	/*
	 *
	 */
	public function distributor()
	{
		$attr  = request()->json()->all();
		$valid = Validator::make($attr, [
            'action' => 'required|max:255',
            'params' => 'required|array'
        ]);
        if ($valid->fails() ) {
            return response()->json([
				'success' => false,
				'message' => 'error',
				'data'    => $valid->errors()->all()
			]);
        }

		$this->params = $attr['params'];

		if ($attr['action'] == 'create'){
			$this->createMeeting();
		}
	}


	public function getMeetings(){

		$response = BigBlueButtonClass::getMeetings();
		echo $response->getMessageKey().'<br>';
		echo $response->getMessage();
		print "<pre>";
		// print_r($response);

		if ($response->getReturnCode() == 'SUCCESS') {
			foreach ($response->getRawXml()->meetings->meeting as $meeting) {
				print_r($meeting);
				// process all meeting
			}
		}

	}

	public function createMeeting()
	{
		if ( ! $user = auth()->user() ){
			return response()->json([
				'success' => false,
				'message' => 'Unauthorized',
				'data' 	  => ''
			]);
		}

		$attr = $this->params;
		$valid = Validator::make($attr, [
            'title'      => 'max:255',
            'user_id' => 'integer'
        ]);
        if ($valid->fails() ) {
            return response()->json([
				'success' => true,
				'message' => 'error',
				'data' 	  => $valid->errors()->all()
			]);
        }

		$nextID 		 	= Meeting::max("id") + 1;
		$this->meetingID 	= BigBlueButtonClass::Uuid($nextID);
		$this->attendee_password  = sha1(rand() . time() . env('APP_KEY'));
		$this->moderator_password = sha1(rand() . time() . env('APP_KEY'));

		if (isset($attr['title']) ){
			$this->meetingName 	= $attr['title'];
		}
		if (isset($attr['duration']) ){
			$this->duration  	= $attr['duration'];
		}
		$param['meetingID'] 			= $this->meetingID;
		$param['meetingName']			= $this->meetingName;
		$param['attendee_password']		= $this->attendee_password;
		$param['moderator_password']	= $this->moderator_password;
		$param['duration'] 				= $this->duration;
		$param['urlLogout'] 			= $this->urlLogout;
		$param['isRecordingTrue']		= $this->isRecordingTrue;

		$response = BigBlueButtonClass::createMeeting($param);
		print_r($response);
		if ($response->getReturnCode() == 'SUCCESS') {
			$meeting = Meeting::create([
				'meeting_id' 		 => $this->meetingID,
				'user_id' 		     => auth()->user()->id,
				'title' 		 	 => $this->meetingName,
				'attendee_password'  => $this->attendee_password,
				'moderator_password' => $this->moderator_password,
				'create_time' 		 => $response->getCreationTime(),
				'duration' 			 => $this->duration,
				'urlLogout' 		 => $this->urlLogout,
				'isRecordingTrue' 	 => $this->isRecordingTrue,
				'record_id'			 => ''
			]);

			return response()->json([
				'success' => true,
				'message' => 'meeting created',
				'data' 	  => $meeting->toArray()
			]);
		}
	}


	public function joinMeeting()
	{
		$attr = request()->only(['meeting_id', 'user_id', 'fullname']);
		$valid = Validator::make($attr, [
            'meeting_id' => 'required',
			'user_id' 	 => 'integer',
            'fullname' 	 => 'string'
        ]);
        if ($valid->fails() ) {
            return response()->json([
				'success' => true,
				'message' => 'error',
				'data' 	  => $valid->errors()->all()
			]);
        }
		$data = Meeting::where('meeting_id', $meetingID)->first();
		Subscriber::create([

		]);
		$param = [
			'meetingID' => $meetingID,
			'fullname'	=> $fullname,
			'password'	=> $password,
		];
		return response()->json([
			'success' => true,
			'message' => 'meeting created',
			'data' 	  => $meeting->toArray()
		]);

	}


	public function closeMeeting($password, $meetingID)
	{
		$param['meetingID'] 		 = $meetingID;
		$param['moderator_password'] = $password;
		$response = BigBlueButtonClass::closeMeeting($param);

	 	return redirect('/meeting/list')->with('status', $response->getMessage());

		echo $response->getReturnCode().'<br>';
		echo $response->getMessageKey().'<br>';
		echo $response->getMessage().'<br>';
		print "<pre>";
		print_r($response);
	}


	public function getMeetingInfo($password, $meetingID)
	{
		$param['meetingID'] 		 = $meetingID;
		$param['moderator_password'] = $password;
		$response = BigBlueButtonClass::getMeetingInfo($param);
		//echo $response->getReturnCode().'<br>';
		//echo $response->getMessageKey().'<br>';
		//echo $response->getMessage().'<br>';
		echo "Meeting Information Response from BBB server";
		print "<pre>";
		print_r($response);

		if ($response->getReturnCode() == 'FAILED') {
			// meeting not found or already closed

		} else {
			print "<pre>";
			//print_r($response);
			// process $response->getRawXml();
		}
	}


	public function getRecordings(){
		$param['meetingID'] = $this->meetingID;
		$response = BigBlueButtonClass::getRecordings($param);
		//echo $response->getReturnCode().'<br>';
		//echo $response->getMessageKey().'<br>';
		//echo $response->getMessage().'<br>';
	 	if ($response->getReturnCode() == 'SUCCESS') {
			return view('bbb.list_recordings', ["response" => $response]);

		 } else {
			echo "Recordings not found";

		}
	}


	public function deleteRecordings($recordId)
	{
		$param['recordingID'] = $recordId;
		$response = BigBlueButtonClass::deleteRecordings($param);
		// echo $response->getReturnCode().'<br>';
		// echo $response->getMessageKey().'<br>';
		// echo $response->getMessage().'<br>';
		// print "<pre>";
		// print_r($response);
		// exit;
		if ($response->getReturnCode() == 'SUCCESS') {
			// return redirect('/meeting/recordings');
			return redirect('/meeting/recordings')->with('status', $response->getMessage() );

			// recording deleted
		} else {
			// something wrong
		}
	}


	public function isMeetingRunning(){
	 	$param['meetingID'] = $this->meetingID;
		$response = BigBlueButtonClass::isMeetingRunning($param);
		echo $response->getReturnCode().'<br>';
		echo $response->isRunning().'<br>';
		print "<pre>";
		print_r($response);
	}
}
