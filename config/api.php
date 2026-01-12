<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for external API endpoints used by the application.
    | All API URLs are stored here for easy management and environment-specific
    | configuration using .env variables.
    |
    */

    'appraisal' => [
        'base_url' => env('APPRAISAL_API_URL', 'http://192.168.1.203:5123'),
        'timeout' => env('APPRAISAL_API_TIMEOUT', 80),
        'endpoints' => [
            'login' => '/Appraisal/Login',
            'kpi' => '/Appraisal/Kpi',
            'kpi_activation' => '/Appraisal/Kpi/update-activation',  // Note: lowercase 'kpi' per backend
            'batch' => '/Appraisal/Batch',
            'score' => '/Appraisal/Score',
            'grade' => '/Appraisal/Grade',
            'section' => '/Appraisal/Section',
            'metric' => '/Appraisal/Metric',
            'weight' => '/Appraisal/KpiWeight',
            'report' => '/Appraisal/Report',
            'recommendation' => '/Appraisal/Recommendation',
        ]
    ],

    'hrms' => [
        'base_url' => env('HRMS_API_URL', 'http://192.168.1.200:5124'),
        'timeout' => env('HRMS_API_TIMEOUT', 30),
        'endpoints' => [
            'employee' => '/HRMS/Employee',
            'employee_info' => '/HRMS/Employee/GetEmployeeInformation',
            'department' => '/HRMS/Department',
            'emp_role' => '/HRMS/EmpRole',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | In production, this should always be true. Set to false only for
    | development with self-signed certificates.
    |
    */
    'verify_ssl' => env('API_VERIFY_SSL', false),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Automatically retry failed API requests with exponential backoff.
    |
    */
    'retry' => [
        'max_attempts' => env('API_RETRY_MAX_ATTEMPTS', 3),
        'delay_ms' => env('API_RETRY_DELAY_MS', 1000),
    ],
];
