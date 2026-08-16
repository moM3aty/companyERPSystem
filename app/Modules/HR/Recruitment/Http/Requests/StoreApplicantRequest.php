<?php
// Path: app/Modules/HR/Recruitment/Http/Requests/StoreApplicantRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Http\Requests;

use App\Core\Validation\ValidatorFactory;
use App\Core\Validation\Rules\Required;
use App\Core\Validation\Rules\StringRule;
use App\Core\Validation\Rules\Email;
use App\Core\Validation\Rules\Exists;
use App\Core\Database\DatabaseManager;

class StoreApplicantRequest
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function validate(array $data, int $companyId): array
    {
        $rules = [
            'job_opening_id' => [new Required(), new Exists($this->db, 'hr_job_openings', 'id', $companyId)],
            'first_name'     => [new Required(), new StringRule()],
            'last_name'      => [new Required(), new StringRule()],
            'email'          => [new Required(), new Email()],
            'phone'          => [new Required(), new StringRule()],
            // في البيئة الفعلية يتم رفع الـ Resume عبر الـ FileManager، هنا نتحقق من المسار النصي
            'resume_path'    => [new StringRule()], 
        ];

        return ValidatorFactory::makeAndValidate($data, $rules);
    }
}