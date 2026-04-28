<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('customer_code');
            $table->string('full_name');
            $table->string('company_name')->nullable();
            $table->enum('customer_type', ['home','business','corporate'])->default('home');
            $table->string('primary_phone');
            $table->string('secondary_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('nid')->nullable();
            $table->string('address_line')->nullable();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pop_id')->nullable()->constrained()->nullOnDelete();
            $table->string('thana')->nullable();
            $table->string('district')->nullable();
            $table->string('postal_code')->nullable();
            $table->date('connection_date')->nullable();
            $table->date('activation_date')->nullable();
            $table->enum('status', ['pending_installation','active','temporary_hold','suspended_due','suspended_manual','disconnected','terminated'])->default('pending_installation');
            $table->enum('discount_type', ['none','fixed','percent'])->default('none');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('opening_due', 12, 2)->default(0);
            $table->decimal('installation_charge', 12, 2)->default(0);
            $table->text('billing_note')->nullable();
            $table->foreignId('assigned_collector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('customers'); }
};
