<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'credits',
        'category',
        'department',
        'year_level',
        'semester_offered',
        'capacity',
        'status',
        'is_laboratory',
        'laboratory_hours',
        'lecture_hours',
        'total_hours'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credits' => 'decimal:2',
        'capacity' => 'integer',
        'is_laboratory' => 'boolean',
        'laboratory_hours' => 'integer',
        'lecture_hours' => 'integer',
        'total_hours' => 'integer',
        'year_level' => 'integer',
        'semester_offered' => 'array'
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Category constants
     */
    const CATEGORY_CORE = 'core';
    const CATEGORY_MAJOR = 'major';
    const CATEGORY_ELECTIVE = 'elective';
    const CATEGORY_GENERAL_EDUCATION = 'general_education';
    const CATEGORY_NSTP = 'nstp';
    const CATEGORY_PE = 'physical_education';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Get all available categories
     *
     * @return array
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_CORE => 'Core Subject',
            self::CATEGORY_MAJOR => 'Major Subject',
            self::CATEGORY_ELECTIVE => 'Elective',
            self::CATEGORY_GENERAL_EDUCATION => 'General Education',
            self::CATEGORY_NSTP => 'NSTP',
            self::CATEGORY_PE => 'Physical Education',
        ];
    }

    /**
     * Get the prerequisites for this subject.
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'subject_id', 'prerequisite_id')
                    ->withTimestamps();
    }

    /**
     * Get the subjects that have this subject as a prerequisite.
     */
    public function dependentSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'prerequisite_id', 'subject_id')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active subjects.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by year level.
     */
    public function scopeByYearLevel(Builder $query, int $yearLevel): Builder
    {
        return $query->where('year_level', $yearLevel);
    }

    /**
     * Scope a query to filter by department.
     */
    public function scopeByDepartment(Builder $query, string $department): Builder
    {
        return $query->where('department', $department);
    }

    /**
     * Scope a query to filter subjects offered in a specific semester.
     */
    public function scopeOfferedInSemester(Builder $query, int $semester): Builder
    {
        return $query->whereJsonContains('semester_offered', $semester);
    }

    /**
     * Scope a query to only include laboratory subjects.
     */
    public function scopeLaboratory(Builder $query): Builder
    {
        return $query->where('is_laboratory', true);
    }

    /**
     * Check if the subject is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the subject is a laboratory subject
     *
     * @return bool
     */
    public function isLaboratory(): bool
    {
        return $this->is_laboratory;
    }

    /**
     * Check if the subject is offered in a specific semester
     *
     * @param int $semester
     * @return bool
     */
    public function isOfferedInSemester(int $semester): bool
    {
        return in_array($semester, $this->semester_offered ?? []);
    }

    /**
     * Get formatted subject code and name
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->code . ' - ' . $this->name;
    }

    /**
     * Get formatted credits display
     *
     * @return string
     */
    public function getCreditsDisplayAttribute(): string
    {
        return number_format($this->credits, 1) . ' ' . ($this->credits == 1 ? 'unit' : 'units');
    }

    /**
     * Get category display name
     *
     * @return string
     */
    public function getCategoryDisplayAttribute(): string
    {
        return self::getCategories()[$this->category] ?? $this->category;
    }

    /**
     * Get status display name
     *
     * @return string
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get formatted hours display
     *
     * @return string
     */
    public function getHoursDisplayAttribute(): string
    {
        if ($this->is_laboratory) {
            return "Lec: {$this->lecture_hours}hrs, Lab: {$this->laboratory_hours}hrs";
        }
        
        return "Lecture: {$this->lecture_hours}hrs";
    }

    /**
     * Get semesters offered as formatted string
     *
     * @return string
     */
    public function getSemestersOfferedDisplayAttribute(): string
    {
        if (empty($this->semester_offered)) {
            return 'Not specified';
        }

        $semesters = [];
        foreach ($this->semester_offered as $sem) {
            switch ($sem) {
                case 1:
                    $semesters[] = '1st Semester';
                    break;
                case 2:
                    $semesters[] = '2nd Semester';
                    break;
                case 3:
                    $semesters[] = 'Summer';
                    break;
                default:
                    $semesters[] = "Semester {$sem}";
            }
        }

        return implode(', ', $semesters);
    }

    /**
     * Check if subject has prerequisites
     *
     * @return bool
     */
    public function hasPrerequisites(): bool
    {
        return $this->prerequisites()->count() > 0;
    }

    /**
     * Get prerequisite codes as comma-separated string
     *
     * @return string
     */
    public function getPrerequisiteCodesAttribute(): string
    {
        return $this->prerequisites->pluck('code')->implode(', ');
    }
}