<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price_monthly', 12, 2)->default(0);
            $table->decimal('price_yearly', 12, 2)->default(0);
            $table->integer('max_customers')->default(100);
            $table->integer('max_staff')->default(5);
            $table->integer('max_olt_devices')->default(1);
            $table->integer('max_sms_monthly')->default(100);
            $table->boolean('has_reports')->default(true);
            $table->boolean('has_api')->default(false);
            $table->boolean('has_branding')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('subscription_plans'); }
};
