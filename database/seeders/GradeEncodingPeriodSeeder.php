<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GradeEncodingPeriod;
use App\Models\AcademicYear;
use App\Models\Semester;
use Carbon\Carbon;

class GradeEncodingPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get academic years and semesters
        $currentYear = AcademicYear::where('is_current', true)->first();
        $previousYear = AcademicYear::where('name', '2023-2024')->first();
        
        if (!$currentYear || !$previousYear) {
            $this->command->warn('Academic years not found. Please run AcademicDataSeeder first.');
            return;
        }
        
        $currentSemesters = Semester::where('academic_year_id', $currentYear->id)->get();
        $previousSemesters = Semester::where('academic_year_id', $previousYear->id)->get();
        
        if ($currentSemesters->isEmpty() || $previousSemesters->isEmpty()) {
            $this->command->warn('Semesters not found. Please run AcademicDataSeeder first.');
            return;
        }
        
        $periods = [
            // Previous Year - First Semester (Completed)
            [
                'name' => 'Midterm Grades - First Semester 2023-2024',
                'grade_type' => 'midterm',
                'academic_year_id' => $previousYear->id,
                'semester_id' => $previousSemesters->where('term_number', 1)->first()->id,
                'start_date' => '2023-10-01 00:00:00',
                'end_date' => '2023-10-15 23:59:59',
                'status' => 'closed',
                'description' => 'Midterm grade encoding period for first semester 2023-2024',
                'is_extendable' => true,
                'extension_deadline' => null,
                'created_by' => 1,
                'created_at' => now()->subMonths(4),
                'updated_at' => now()->subMonths(3),
            ],
            [
                'name' => 'Final Grades - First Semester 2023-2024',
                'grade_type' => 'final',
                'academic_year_id' => $previousYear->id,
                'semester_id' => $previousSemesters->where('term_number', 1)->first()->id,
                'start_date' => '2023-12-10 00:00:00',
                'end_date' => '2023-12-20 23:59:59',
                'status' => 'closed',
                'description' => 'Final grade encoding period for first semester 2023-2024',
                'is_extendable' => false,
                'extension_deadline' => null,
                'created_by' => 1,
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subMonths(2),
            ],
            // Previous Year - Second Semester (Completed)
            [
                'name' => 'Midterm Grades - Second Semester 2023-2024',
                'grade_type' => 'midterm',
                'academic_year_id' => $previousYear->id,
                'semester_id' => $previousSemesters->where('term_number', 2)->first()->id,
                'start_date' => '2024-03-01 00:00:00',
                'end_date' => '2024-03-15 23:59:59',
                'status' => 'closed',
                'description' => 'Midterm grade encoding period for second semester 2023-2024',
                'is_extendable' => true,
                'extension_deadline' => null,
                'created_by' => 1,
                'created_at' => now()->subMonths(2),
                'updated_at' => now()->subMonth(),
            ],
            [
                'name' => 'Final Grades - Second Semester 2023-2024',
                'grade_type' => 'final',
                'academic_year_id' => $previousYear->id,
                'semester_id' => $previousSemesters->where('term_number', 2)->first()->id,
                'start_date' => '2024-05-20 00:00:00',
                'end_date' => '2024-05-30 23:59:59',
                'status' => 'closed',
                'description' => 'Final grade encoding period for second semester 2023-2024',
                'is_extendable' => false,
                'extension_deadline' => null,
                'created_by' => 1,
                'created_at' => now()->subMonth(),
                'updated_at' => now()->subWeeks(3),
            ],
            // Current Year - First Semester (Completed)
            [
                'name' => 'Midterm Grades - First Semester 2024-2025',
                'grade_type' => 'midterm',
                'academic_year_id' => $currentYear->id,
                'semester_id' => $currentSemesters->where('term_number', 1)->first()->id,
                'start_date' => '2024-10-01 00:00:00',
                'end_date' => '2024-10-15 23:59:59',
                'status' => 'closed',
                'description' => 'Midterm grade encoding period for first semester 2024-2025',
                'is_extendable' => true,
                'extension_deadline' => null,
                'created_by' => 1,
                'created_at' => now()->subWeeks(12),
                'updated_at' => now()->subWeeks(10),
            ],
            [
                'name' => 'Final Grades - First Semester 2024-2025',
                'grade_type' => 'final',
                'academic_year_id' => $currentYear->id,
                'semester_id' => $currentSemesters->where('term_number', 1)->first()->id,
                'start_date' => '2024-12-10 00:00:00',
                'end_date' => '2024-12-20 23:59:59',
                'status' => 'closed',
                'description' => 'Final grade encoding period for first semester 2024-2025',
                'is_extendable' => false,
                'extension_deadline' => null,
                'created_by' => 1,
                'created_at' => now()->subWeeks(8),
                'updated_at' => now()->subWeeks(6),
            ],
            // Current Year - Second Semester (Active)
            [
                'name' => 'Midterm Grades - Second Semester 2024-2025',
                'grade_type' => 'midterm',
                'academic_year_id' => $currentYear->id,
                'semester_id' => $currentSemesters->where('term_number', 2)->first()->id,
                'start_date' => now()->addDays(30)->format('Y-m-d H:i:s'),
                'end_date' => now()->addDays(45)->format('Y-m-d H:i:s'),
                'status' => 'draft',
                'description' => 'Midterm grade encoding period for second semester 2024-2025',
                'is_extendable' => true,
                'extension_deadline' => null,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Final Grades - Second Semester 2024-2025',
                'grade_type' => 'final',
                'academic_year_id' => $currentYear->id,
                'semester_id' => $currentSemesters->where('term_number', 2)->first()->id,
                'start_date' => now()->addDays(120)->format('Y-m-d H:i:s'),
                'end_date' => now()->addDays(135)->format('Y-m-d H:i:s'),
                'status' => 'draft',
                'description' => 'Final grade encoding period for second semester 2024-2025',
                'is_extendable' => true,
                'extension_deadline' => null,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($periods as $period) {
            // Check if semester exists before creating the period
            if ($period['semester_id']) {
                GradeEncodingPeriod::updateOrCreate(
                    [
                        'academic_year_id' => $period['academic_year_id'],
                        'semester_id' => $period['semester_id'],
                        'grade_type' => $period['grade_type']
                    ],
                    $period
                );
            }
        }
        
        $this->command->info('Grade encoding periods seeded successfully.');
    }
}