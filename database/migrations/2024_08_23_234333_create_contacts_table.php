<?php

use App\Enums\DateType;
use App\Models\Customer;
use App\Models\Date;
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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class, 'customer_id')->nullable()->constrained();
            $table->foreignIdFor(Date::class, 'date_id')->nullable()->constrained();
            $table->string('type')->default(DateType::CALL->value);
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('subject');
            $table->text('message');
            $table->Timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
