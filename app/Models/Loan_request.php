<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan_request extends Model
{
    use HasFactory;

    public const LOAN_STATUS_PENDING = 1;
    public const LOAN_STATUS_APPROVED = 2;
    public const LOAN_STATUS_PAID = 3;
    public const LOAN_STATUS_REJECTED = 4;

    public function get_loan_repaymets()
    {
        return $this->hasMany(Loan_repayment::class);
    }
}
