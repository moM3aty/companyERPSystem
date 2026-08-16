<?php
// Path: resources/lang/en/validation.php

return [
    'required' => 'The :attribute field is required.',
    'email'    => 'The :attribute must be a valid email address.',
    'numeric'  => 'The :attribute must be a number.',
    'string'   => 'The :attribute must be a string.',
    'min'      => [
        'numeric' => 'The :attribute must be at least :min.',
        'string'  => 'The :attribute must be at least :min characters.',
    ],
    'max'      => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'string'  => 'The :attribute may not be greater than :max characters.',
    ],
    'unique'   => 'The :attribute has already been taken.',
    'exists'   => 'The selected :attribute is invalid or does not exist.',
    'date'     => 'The :attribute is not a valid date.',
    'in'       => 'The selected :attribute is invalid.',

    // Custom Attributes Translation for ERP
    'attributes' => [
        'email' => 'email address',
        'password' => 'password',
        'name' => 'name',
        'company_id' => 'company',
        'product_id' => 'product',
        'quantity' => 'quantity',
        'amount' => 'amount',
        'start_date' => 'start date',
        'end_date' => 'end date',
    ],
];