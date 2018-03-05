<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('user_id');
			$table->string('meetingId');
			$table->string('title');
			$table->string('attendee_password');
			$table->string('moderator_password');
			$table->integer('createTime')->default(0);
			$table->integer('duration');
			$table->string('urlLogout');
			$table->tinyInteger('isRecordingTrue');
			$table->string('recordId');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meetings');
    }
}
