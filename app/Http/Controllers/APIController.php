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
		$params  = request()->json()->all();
		$valid = Validator::make($params, [
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

		$this->params = $params['params'];

		if (function_exists($params['action'].'Meeting') ){
			$func_name = $params['action'].'Meeting';
			$this->$func_name();
		}

		// switch ($params['action']){
		// 	case 'create':
		// 		$this->createMeeting();
		//
		// 		break;
		// 	case 'delete':
		// 		$this->deleteMeeting();
		//
		// 		break;
		// 	case 'join':
		// 		$this->joinMeeting();
		//
		// 		break;
		// 	case 'unjoin':
		// 		$this->unjoinMeeting();
		//
		// 		break;
		// }
	}


	public function createMeeting()
	{
		$params = $this->params;
		$valid = Validator::make($params, [
            'title'   => 'max:255',
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

		$meeting = Meeting::create([
			'meetingId' 		 => $this->meetingID,
			'user_id' 		     => auth()->user()->id,
			'title' 		 	 => $this->meetingName,
			'attendee_password'  => $this->attendee_password,
			'moderator_password' => $this->moderator_password,
			'duration' 			 => $this->duration,
			'urlLogout' 		 => $this->urlLogout,
			'isRecordingTrue' 	 => $this->isRecordingTrue,
			'recordId'			 => ''
		]);

		return response()->json([
			'success' => true,
			'message' => 'meeting created',
			'data' 	  => $meeting->toArray()
		]);

	}


	public function deleteMeeting()
	{
		$params = $this->params;
		$valid = Validator::make($params, [
            'meeting_id' => 'required'
        ]);

        if ($valid->fails() ) {
            return response()->json([
				'success' => true,
				'message' => 'error',
				'data' 	  => $valid->errors()->all()
			]);
        }

		$meeting = Meeting::where('meetingId', $params['meeting_id'])->ВУДУЕУ();
		return response()->json([
			'success' => true,
			'message' => 'joined',
			'data' 	  => []
		]);
	}


	public function joinMeeting()
	{
		$params = $this->params;
		$valid = Validator::make($params, [
            'meeting_id' => 'required',
			'user_id' 	 => 'integer',
            'fullname' 	 => 'string',
            'moderator'  => 'integer'
        ]);

        if ($valid->fails() ){
            return response()->json([
				'success' => true,
				'message' => 'error',
				'data' 	  => $valid->errors()->all()
			]);
        }

		$meeting = Meeting::where('meetingId', $params['meeting_id'])->first();

		$subscriber = Subscriber::create([
			'meeting_id'  	=> $meeting->id,
			'hash'  		=> hash('sha512', $meeting->meetingId . env('APP_KEY') ),
			'subscriber_id' => $params['user_id'],
			'fullname'    	=> $params['fullname'],
			'isModerator' 	=> $params['moderator']
		]);

		return response()->json([
			'success' => true,
			'message' => 'joined',
			'data' 	  => ['access_url' => route('meeting.join', $subscriber->hash)]
		]);
	}


	public function unjoinMeeting()
	{
		$params = $this->params;
		$valid = Validator::make($params, [
            'meeting_id' => 'required',
			'user_id' 	 => 'required|integer'
        ]);

        if ($valid->fails() ){
            return response()->json([
				'success' => true,
				'message' => 'error',
				'data' 	  => $valid->errors()->all()
			]);
        }

		$meeting = Meeting::where('meetingId', $params['meeting_id'])->first();

		$subscriber = Subscriber::where('meeting_id', $meeting->id)->where('user_id', $params['user_id'])->delete();
		return response()->json([
			'success' => true,
			'message' => 'joined',
			'data' 	  => []
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
