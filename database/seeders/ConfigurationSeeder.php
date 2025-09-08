<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            // Application Settings
            [
                'key' => 'app.name',
                'value' => 'Student Information Management System',
                'type' => 'string',
                'group' => 'app',
                'description' => 'Application name displayed throughout the system',
                'is_public' => true,
                'sort_order' => 1
            ],
            [
                'key' => 'app.short_name',
                'value' => 'SIMS',
                'type' => 'string',
                'group' => 'app',
                'description' => 'Short application name for branding',
                'is_public' => true,
                'sort_order' => 2
            ],
            [
                'key' => 'app.version',
                'value' => '1.0.0',
                'type' => 'string',
                'group' => 'app',
                'description' => 'Current application version',
                'is_public' => true,
                'sort_order' => 3
            ],
            [
                'key' => 'app.maintenance_mode',
                'value' => false,
                'type' => 'boolean',
                'group' => 'app',
                'description' => 'Enable maintenance mode to restrict access',
                'is_public' => false,
                'sort_order' => 4
            ],
            [
                'key' => 'app.theme_color',
                'value' => '#800020',
                'type' => 'string',
                'group' => 'app',
                'description' => 'Primary theme color (maroon)',
                'is_public' => true,
                'sort_order' => 5
            ],

            // Institution Settings
            [
                'key' => 'institution.name',
                'value' => 'Sample Educational Institution',
                'type' => 'string',
                'group' => 'institution',
                'description' => 'Name of the educational institution',
                'is_public' => true,
                'sort_order' => 1
            ],
            [
                'key' => 'institution.address',
                'value' => '123 Education Street, Learning City, LC 12345',
                'type' => 'string',
                'group' => 'institution',
                'description' => 'Institution physical address',
                'is_public' => true,
                'sort_order' => 2
            ],
            [
                'key' => 'institution.phone',
                'value' => '+1 (555) 123-4567',
                'type' => 'string',
                'group' => 'institution',
                'description' => 'Institution contact phone number',
                'is_public' => true,
                'sort_order' => 3
            ],
            [
                'key' => 'institution.email',
                'value' => 'info@institution.edu',
                'type' => 'string',
                'group' => 'institution',
                'description' => 'Institution contact email address',
                'is_public' => true,
                'sort_order' => 4
            ],
            [
                'key' => 'institution.website',
                'value' => 'https://www.institution.edu',
                'type' => 'string',
                'group' => 'institution',
                'description' => 'Institution website URL',
                'is_public' => true,
                'sort_order' => 5
            ],

            // Academic Settings
            [
                'key' => 'academic.current_year',
                'value' => '2024-2025',
                'type' => 'string',
                'group' => 'academic',
                'description' => 'Current academic year',
                'is_public' => true,
                'sort_order' => 1
            ],
            [
                'key' => 'academic.current_semester',
                'value' => 'Fall 2024',
                'type' => 'string',
                'group' => 'academic',
                'description' => 'Current academic semester/term',
                'is_public' => true,
                'sort_order' => 2
            ],
            [
                'key' => 'academic.grading_scale',
                'value' => [
                    'A' => ['min' => 90, 'max' => 100],
                    'B' => ['min' => 80, 'max' => 89],
                    'C' => ['min' => 70, 'max' => 79],
                    'D' => ['min' => 60, 'max' => 69],
                    'F' => ['min' => 0, 'max' => 59]
                ],
                'type' => 'json',
                'group' => 'academic',
                'description' => 'Grading scale configuration',
                'is_public' => true,
                'sort_order' => 3
            ],

            // System Settings
            [
                'key' => 'system.timezone',
                'value' => 'America/New_York',
                'type' => 'string',
                'group' => 'system',
                'description' => 'System default timezone',
                'is_public' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'system.date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Default date format for display',
                'is_public' => true,
                'sort_order' => 2
            ],
            [
                'key' => 'system.max_file_upload_size',
                'value' => 10240, // 10MB in KB
                'type' => 'integer',
                'group' => 'system',
                'description' => 'Maximum file upload size in KB',
                'is_public' => false,
                'sort_order' => 3
            ],

            // Security Settings
            [
                'key' => 'security.session_timeout',
                'value' => 120, // 2 hours in minutes
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Session timeout in minutes',
                'is_public' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'security.password_min_length',
                'value' => 8,
                'type' => 'integer',
                'group' => 'security',
                'description' => 'Minimum password length requirement',
                'is_public' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'security.require_password_confirmation',
                'value' => true,
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Require password confirmation for sensitive actions',
                'is_public' => false,
                'sort_order' => 3
            ],

            // Notification Settings
            [
                'key' => 'notifications.email_enabled',
                'value' => true,
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications',
                'is_public' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'notifications.sms_enabled',
                'value' => false,
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable SMS notifications',
                'is_public' => false,
                'sort_order' => 2
            ]
        ];

        foreach ($configurations as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }

        $this->command->info('Configuration settings seeded successfully!');
    }
}
