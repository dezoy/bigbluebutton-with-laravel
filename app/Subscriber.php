<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
	use SoftDeletes;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subscribers';

	protected $fillable = [
		'user_id',
		'meeting_id',
		'hash',
		'isModerator',
		'fullname'
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


	public function meeting()
    {
        return $this->hasOne('App\Meeting', 'meeting_id');
    }

}
