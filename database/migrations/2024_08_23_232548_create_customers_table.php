<?php

use App\Enums\DateType;
use App\Models\Date;
use App\Models\Fair;
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Fair::class, 'fair_id')->nullable()->constrained();
            $table->foreignIdFor(Date::class, 'date_id')->nullable()->constrained();
            $table->enum('type_contact', ['lead', 'customer'])->default('lead');
            $table->string('type')->nullable()->default(DateType::WEDDING->value);
            $table->json('names')->nullable();
            $table->json('emails')->nullable();
            $table->json('phones')->nullable();
            $table->json('address')->nullable();

            $table->text('description')->nullable();
            $table->boolean('is_date_event_set')->default(false);
            $table->date('date_event');
            $table->timestamp('is_accepted_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
