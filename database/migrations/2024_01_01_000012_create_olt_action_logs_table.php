<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('olt_action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('olt_device_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action_type');
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->enum('status', ['pending','success','failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->foreignId('executed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('olt_action_logs'); }
};
