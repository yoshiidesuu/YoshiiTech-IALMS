<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Subject;
use Carbon\Carbon;

class AcademicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Academic Years
        $this->createAcademicYears();
        
        // Create Semesters
        $this->createSemesters();
        
        // Create Subjects
        $this->createSubjects();
    }
    
    /**
     * Create sample academic years
     */
    private function createAcademicYears(): void
    {
        $academicYears = [
            [
                'name' => '2023-2024',
                'start_date' => '2023-08-15',
                'end_date' => '2024-05-30',
                'status' => 'completed',
                'is_current' => false,
                'archived_at' => null,
                'description' => 'Academic Year 2023-2024',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2024-2025',
                'start_date' => '2024-08-15',
                'end_date' => '2025-05-30',
                'status' => 'active',
                'is_current' => true,
                'archived_at' => null,
                'description' => 'Current Academic Year 2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2025-2026',
                'start_date' => '2025-08-15',
                'end_date' => '2026-05-30',
                'status' => 'draft',
                'is_current' => false,
                'archived_at' => null,
                'description' => 'Planned Academic Year 2025-2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($academicYears as $year) {
            AcademicYear::firstOrCreate(
                ['name' => $year['name']],
                $year
            );
        }
        
        $this->command->info('Academic Years seeded successfully.');
    }
    
    /**
     * Create sample semesters
     */
    private function createSemesters(): void
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $previousYear = AcademicYear::where('name', '2023-2024')->first();
        $nextYear = AcademicYear::where('name', '2025-2026')->first();
        
        $semesters = [
            // Previous Year Semesters
            [
                'name' => 'First Semester',
                'academic_year_id' => $previousYear->id,
                'start_date' => '2023-08-15',
                'end_date' => '2023-12-15',
                'enrollment_start' => '2023-07-01',
                'enrollment_end' => '2023-08-10',
                'status' => 'completed',
                'is_current' => false,
                'term_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Second Semester',
                'academic_year_id' => $previousYear->id,
                'start_date' => '2024-01-08',
                'end_date' => '2024-05-30',
                'enrollment_start' => '2023-12-01',
                'enrollment_end' => '2024-01-05',
                'status' => 'completed',
                'is_current' => false,
                'term_number' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Current Year Semesters
            [
                'name' => 'First Semester',
                'academic_year_id' => $currentYear->id,
                'start_date' => '2024-08-15',
                'end_date' => '2024-12-15',
                'enrollment_start' => '2024-07-01',
                'enrollment_end' => '2024-08-10',
                'status' => 'completed',
                'is_current' => false,
                'term_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Second Semester',
                'academic_year_id' => $currentYear->id,
                'start_date' => '2025-01-08',
                'end_date' => '2025-05-30',
                'enrollment_start' => '2024-12-01',
                'enrollment_end' => '2025-01-05',
                'status' => 'active',
                'is_current' => true,
                'term_number' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Next Year Semesters
            [
                'name' => 'First Semester',
                'academic_year_id' => $nextYear->id,
                'start_date' => '2025-08-15',
                'end_date' => '2025-12-15',
                'enrollment_start' => '2025-07-01',
                'enrollment_end' => '2025-08-10',
                'status' => 'draft',
                'is_current' => false,
                'term_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Second Semester',
                'academic_year_id' => $nextYear->id,
                'start_date' => '2026-01-08',
                'end_date' => '2026-05-30',
                'enrollment_start' => '2025-12-01',
                'enrollment_end' => '2026-01-05',
                'status' => 'draft',
                'is_current' => false,
                'term_number' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($semesters as $semester) {
            Semester::firstOrCreate(
                ['name' => $semester['name'], 'academic_year_id' => $semester['academic_year_id']],
                $semester
            );
        }
        
        $this->command->info('Semesters seeded successfully.');
    }
    
    /**
     * Create sample subjects
     */
    private function createSubjects(): void
    {
        $subjects = [
            // Core Subjects
            [
                'code' => 'MATH101',
                'name' => 'College Algebra',
                'description' => 'Fundamental concepts of algebra including linear equations, quadratic equations, and functions.',
                'credits' => 3,
                'category' => 'core',

                'capacity' => 40,
                'status' => 'active',
                'department' => 'Mathematics Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MATH102',
                'name' => 'Calculus I',
                'description' => 'Introduction to differential calculus including limits, derivatives, and applications.',
                'credits' => 4,
                'category' => 'core',

                'capacity' => 35,
                'status' => 'active',
                'department' => 'Mathematics Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ENG101',
                'name' => 'English Composition I',
                'description' => 'Development of writing skills through practice in various forms of composition.',
                'credits' => 3,
                'category' => 'general_education',

                'capacity' => 25,
                'status' => 'active',
                'department' => 'English Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ENG102',
                'name' => 'English Composition II',
                'description' => 'Advanced composition with emphasis on research and argumentative writing.',
                'credits' => 3,
                'category' => 'general_education',

                'capacity' => 25,
                'status' => 'active',
                'department' => 'English Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Science Subjects
            [
                'code' => 'PHYS101',
                'name' => 'General Physics I',
                'description' => 'Introduction to mechanics, heat, and sound with laboratory component.',
                'credits' => 4,
                'category' => 'core',

                'capacity' => 30,
                'status' => 'active',
                'department' => 'Physics Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CHEM101',
                'name' => 'General Chemistry I',
                'description' => 'Fundamental principles of chemistry including atomic structure, bonding, and reactions.',
                'credits' => 4,
                'category' => 'core',
                'capacity' => 32,
                'status' => 'active',
                'department' => 'Chemistry Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'BIO101',
                'name' => 'General Biology I',
                'description' => 'Introduction to biological principles including cell structure, genetics, and evolution.',
                'credits' => 4,
                'category' => 'core',
                'capacity' => 28,
                'status' => 'active',
                'department' => 'Biology Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Computer Science Subjects
            [
                'code' => 'CS101',
                'name' => 'Introduction to Computer Science',
                'description' => 'Fundamental concepts of computer science and programming using Python.',
                'credits' => 3,
                'category' => 'major',
                'capacity' => 35,
                'status' => 'active',
                'department' => 'Computer Science Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CS102',
                'name' => 'Data Structures and Algorithms',
                'description' => 'Study of fundamental data structures and algorithms with implementation in Java.',
                'credits' => 4,
                'category' => 'major',

                'capacity' => 30,
                'status' => 'active',
                'department' => 'Computer Science Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Social Sciences
            [
                'code' => 'HIST101',
                'name' => 'World History I',
                'description' => 'Survey of world history from ancient civilizations to the Renaissance.',
                'credits' => 3,
                'category' => 'general_education',
                'capacity' => 40,
                'status' => 'active',
                'department' => 'History Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PSYC101',
                'name' => 'Introduction to Psychology',
                'description' => 'Overview of psychological principles, theories, and research methods.',
                'credits' => 3,
                'category' => 'elective',
                'capacity' => 45,
                'status' => 'active',
                'department' => 'Psychology Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ECON101',
                'name' => 'Principles of Microeconomics',
                'description' => 'Introduction to microeconomic theory including supply and demand, market structures.',
                'credits' => 3,
                'category' => 'elective',
                'capacity' => 38,
                'status' => 'active',
                'department' => 'Economics Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Advanced Subjects
            [
                'code' => 'MATH201',
                'name' => 'Calculus II',
                'description' => 'Integral calculus, sequences, series, and applications.',
                'credits' => 4,
                'category' => 'core',

                'capacity' => 30,
                'status' => 'active',
                'department' => 'Mathematics Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CS201',
                'name' => 'Database Systems',
                'description' => 'Design and implementation of database systems including SQL and normalization.',
                'credits' => 3,
                'category' => 'major',

                'capacity' => 25,
                'status' => 'active',
                'department' => 'Computer Science Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PHYS201',
                'name' => 'General Physics II',
                'description' => 'Electricity, magnetism, and optics with laboratory component.',
                'credits' => 4,
                'category' => 'core',

                'capacity' => 28,
                'status' => 'active',
                'department' => 'Physics Department',
                'year_level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }
        
        $this->command->info('Subjects seeded successfully.');
    }
}