<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan_repayment extends Model
{
    use HasFactory;

    public const LOAN_REPAYMENT_STATUS_PENDING = 1;
    public const LOAN_REPAYMENT_STATUS_PAID = 2;

    protected $fillable = [
        'loan_id',
        'repayment_amount',
        'loan_status'
    ];
}
