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
			$table->string('meeting_id');
			$table->string('title');
			$table->string('attendee_password');
			$table->string('moderator_password');
			$table->string('duration');
			$table->string('urlLogout');
			$table->string('isRecordingTrue');
			$table->string('record_id');

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
