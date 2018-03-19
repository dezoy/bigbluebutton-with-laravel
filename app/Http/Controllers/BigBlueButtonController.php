<?php
namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use Request;
use Session;
use Illuminate\Support\Facades\Crypt;
use App\Utils\BigBlueButtonClass;
use App\Meeting;
use App\Subscriber;

class BigBlueButtonController extends Controller
{

	public function __construct()
	{
		//
	}


	public function createMeeting($meeting)
	{
		$response = BigBlueButtonClass::createMeeting([
			'meetingID' 			=> $meeting->meetingID,
			'meetingName'			=> $meeting->title,
			'attendee_password'		=> $meeting->attendee_password,
			'moderator_password'	=> $meeting->moderator_password,
			'duration' 				=> $meeting->duration,
			'urlLogout' 			=> $meeting->urlLogout,
			'isRecordingTrue'		=> $meeting->isRecordingTrue
		]);

		$meeting->createTime = $response->getCreateTime();
		$meeting->save();

		return $meeting;
	}


	public function joinMeeting($userHash)
	{
		$query = Subscriber::where('hash', $userHash);
		$subscriber = $query->first();
		$meeting 	= $query->meeting();
		print_r($meeting->toArray());
		die();
		if (empty($meeting->createTime) ){
			// Moderator has not joined yet
			if ($subscriber->isModerator) {
				$meeting = $this->createMeeting($meeting);
			} else {
				return view('bbb.meeting_didnt_degin');
			}
		}

		$url = BigBlueButtonClass::joinMeeting([
			'meetingID' => $meeting->meetingID,
			'fullname'	=> $subscriber->fullname,
			'password'	=> $meeting->moderator_password,
			'createTime'=> $meeting->createTime
		]);

		// Maybe will be change part of url
		// from api/ to html5client/

		return redirect($url);

	 	header("Location:".$url);
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


	public function getMeetings()
	{

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


	public function listMeeting()
	{
		return view('bbb.list_meeting');
	}


	public function addMeeting()
	{
		return view('bbb.create_meeting');
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
