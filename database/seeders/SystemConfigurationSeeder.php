<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            // System Information
            [
                'key' => 'system.name',
                'display_name' => 'System Name',
                'description' => 'The name of the institution management system',
                'value' => 'Institution Management System',
                'type' => 'text',
                'group' => 'system',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'string', 'max:255']),
                'sort_order' => 1,
            ],
            [
                'key' => 'system.version',
                'display_name' => 'System Version',
                'description' => 'Current version of the system',
                'value' => '1.0.0',
                'type' => 'text',
                'group' => 'system',
                'is_public' => true,
                'is_editable' => false,
                'validation_rules' => json_encode(['required', 'string']),
                'sort_order' => 2,
            ],
            [
                'key' => 'system.logo',
                'display_name' => 'System Logo',
                'description' => 'Path to the system logo image',
                'value' => '/images/logo.png',
                'type' => 'file',
                'group' => 'system',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['nullable', 'string']),
                'sort_order' => 3,
            ],
            
            // Institution Information
            [
                'key' => 'institution.name',
                'display_name' => 'Institution Name',
                'description' => 'Official name of the educational institution',
                'value' => 'Sample Educational Institution',
                'type' => 'text',
                'group' => 'institution',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'string', 'max:255']),
                'sort_order' => 1,
            ],
            [
                'key' => 'institution.address',
                'display_name' => 'Institution Address',
                'description' => 'Physical address of the institution',
                'value' => '123 Education Street, Learning City, State 12345',
                'type' => 'textarea',
                'group' => 'institution',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'string']),
                'sort_order' => 2,
            ],
            [
                'key' => 'institution.phone',
                'display_name' => 'Institution Phone',
                'description' => 'Primary contact phone number',
                'value' => '+1 (555) 123-4567',
                'type' => 'text',
                'group' => 'institution',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'string', 'max:20']),
                'sort_order' => 3,
            ],
            [
                'key' => 'institution.email',
                'display_name' => 'Institution Email',
                'description' => 'Primary contact email address',
                'value' => 'info@institution.edu',
                'type' => 'email',
                'group' => 'institution',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'email', 'max:255']),
                'sort_order' => 4,
            ],
            
            // Academic Settings
            [
                'key' => 'academic.current_year',
                'display_name' => 'Current Academic Year',
                'description' => 'The current academic year',
                'value' => '2024-2025',
                'type' => 'text',
                'group' => 'academic',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'string', 'max:20']),
                'sort_order' => 1,
            ],
            [
                'key' => 'academic.grading_system',
                'display_name' => 'Grading System',
                'description' => 'The grading system used by the institution',
                'value' => 'letter',
                'type' => 'select',
                'group' => 'academic',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'in:letter,numeric,percentage']),
                'options' => json_encode([
                    'letter' => 'Letter Grades (A, B, C, D, F)',
                    'numeric' => 'Numeric Grades (4.0 Scale)',
                    'percentage' => 'Percentage Grades (0-100)'
                ]),
                'sort_order' => 2,
            ],
            
            // Security Settings
            [
                'key' => 'security.password_min_length',
                'display_name' => 'Minimum Password Length',
                'description' => 'Minimum required password length',
                'value' => '8',
                'type' => 'number',
                'group' => 'security',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'integer', 'min:6', 'max:50']),
                'sort_order' => 1,
            ],
            [
                'key' => 'security.session_timeout',
                'display_name' => 'Session Timeout (minutes)',
                'description' => 'User session timeout in minutes',
                'value' => '120',
                'type' => 'number',
                'group' => 'security',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'integer', 'min:15', 'max:1440']),
                'sort_order' => 2,
            ],
            
            // Email Settings
            [
                'key' => 'email.from_name',
                'display_name' => 'Email From Name',
                'description' => 'Default sender name for system emails',
                'value' => 'Institution Management System',
                'type' => 'text',
                'group' => 'email',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'string', 'max:255']),
                'sort_order' => 1,
            ],
            [
                'key' => 'email.from_address',
                'display_name' => 'Email From Address',
                'description' => 'Default sender email address for system emails',
                'value' => 'noreply@institution.edu',
                'type' => 'email',
                'group' => 'email',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'email', 'max:255']),
                'sort_order' => 2,
            ],
            
            // Theme Settings
            [
                'key' => 'theme.primary_color',
                'display_name' => 'Primary Theme Color',
                'description' => 'Primary color for the system theme',
                'value' => '#800000',
                'type' => 'color',
                'group' => 'theme',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/']),
                'sort_order' => 1,
            ],
            [
                'key' => 'theme.secondary_color',
                'display_name' => 'Secondary Theme Color',
                'description' => 'Secondary color for the system theme',
                'value' => '#6c757d',
                'type' => 'color',
                'group' => 'theme',
                'is_public' => true,
                'is_editable' => true,
                'validation_rules' => json_encode(['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/']),
                'sort_order' => 2,
            ],
        ];

        foreach ($configurations as $config) {
            DB::table('configurations')->updateOrInsert(
                ['key' => $config['key']],
                array_merge($config, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
