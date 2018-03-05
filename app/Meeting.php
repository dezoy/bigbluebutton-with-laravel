<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
	use SoftDeletes;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'meetings';

	protected $fillable = [
		'user_id',
		'meetingId',
		'title',
		'attendee_password',
		'moderator_password',
		'createTime',
		'duration',
		'urlLogout',
		'isRecordingTrue',
		'recordId',
	];

	/**
     * The accessor to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];


	public function user()
    {
        return $this->belongsTo('App\User', 'id', 'user_id');
    }

}
