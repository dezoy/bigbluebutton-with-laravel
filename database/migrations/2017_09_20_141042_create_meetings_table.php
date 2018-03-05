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
			$table->string('meetingId')->default('');
			$table->string('title')->default('');
			$table->string('attendee_password')->default('');
			$table->string('moderator_password')->default('');
			$table->integer('createTime')->default(0);
			$table->integer('duration');
			$table->string('urlLogout')->default('');
			$table->tinyInteger('isRecordingTrue');
			$table->string('recordId')->default('');

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
