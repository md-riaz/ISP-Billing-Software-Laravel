<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained();
            $table->decimal('monthly_price', 12, 2);
            $table->enum('status', ['pending','active','suspended','disconnected','terminated'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('olt_device_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pon_port')->nullable();
            $table->string('onu_identifier')->nullable();
            $table->string('onu_serial')->nullable();
            $table->string('onu_name')->nullable();
            $table->string('service_profile')->nullable();
            $table->string('line_profile')->nullable();
            $table->string('remote_reference')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('customer_services'); }
};
