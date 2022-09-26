<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanRepayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->decimal('repayment_amount', 11, 2);
            $table->decimal('paid_amount', 11, 2)->default(0);            
            $table->tinyInteger('loan_status')->comment('1: pending,2: paid');
            $table->date('emi_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->timestamps();
            $table->foreign('loan_id')->references('id')->on('loan_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_repayment');
    }
}
