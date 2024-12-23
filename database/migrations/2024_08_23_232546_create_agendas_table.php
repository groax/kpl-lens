<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique()->nullable(); // Store the `id`
            $table->string('recurring_event_id')->nullable(); // Store `recurringEventId` if applicable
            $table->string('ical_uid')->nullable(); // Store `iCalUID`
            $table->string('html_link')->nullable(); // Store `htmlLink`

            $table->string('summary')->nullable(); // Event title
            $table->boolean('in_agenda')->default(false);
            $table->text('description')->nullable();
            $table->string('meet_link')->nullable();
            $table->string('location')->nullable();
            $table->string('type');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
