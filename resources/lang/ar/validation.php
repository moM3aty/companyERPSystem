<?php
// Path: resources/lang/ar/validation.php

return [
    'required' => 'حقل :attribute مطلوب.',
    'email'    => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صالحاً.',
    'numeric'  => 'يجب أن يكون حقل :attribute رقماً.',
    'string'   => 'يجب أن يكون حقل :attribute نصاً.',
    'min'      => [
        'numeric' => 'يجب أن يكون حقل :attribute على الأقل :min.',
        'string'  => 'يجب أن يكون حقل :attribute على الأقل :min حروف.',
    ],
    'max'      => [
        'numeric' => 'يجب ألا يكون حقل :attribute أكبر من :max.',
        'string'  => 'يجب ألا يكون حقل :attribute أكبر من :max حروف.',
    ],
    'unique'   => 'قيمة :attribute مُستخدمة من قبل.',
    'exists'   => 'القيمة المحددة في :attribute غير صالحة أو غير موجودة في النظام.',
    'date'     => 'حقل :attribute ليس تاريخاً صحيحاً.',
    'in'       => 'الخيار المحدد في :attribute غير صالح.',
    
    // Custom Attributes Translation for ERP
    'attributes' => [
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'name' => 'الاسم',
        'company_id' => 'الشركة',
        'product_id' => 'المنتج',
        'quantity' => 'الكمية',
        'amount' => 'المبلغ',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
    ],
];