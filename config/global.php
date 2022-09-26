<?php
return [
    'status'=>[
        'unauthorized'=>401,
        'validation'=>401,
        'success'=>200,
        'forbidden'=>403,
        'bad_request'=>400
    ],
    'message'=>[
        'error'=>'Something Went Wrong',
        'no_data_found'=>'No Records Found',
        'invalid_data'=>'Data provided is invalid',
        'not_added'=>'Record not added',
        'added'=>'Record added successfully',
        'not_updated'=>'Record not updated',
        'updated'=>'Record updated successfully',
        'loan_paid'=>'Loan already paid',
        'loan_not_approved'=>'Loan request is not approved yet or loan is rejected'
    ],
    'loan_term'=>7
]
?>