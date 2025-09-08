<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Semester extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'academic_year_id',
        'start_date',
        'end_date',
        'enrollment_start',
        'enrollment_end',
        'status',
        'description',
        'term_number',
        'is_current'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'enrollment_start' => 'date',
        'enrollment_end' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Get the academic year that owns the semester.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the grade encoding periods for the semester.
     */
    public function gradeEncodingPeriods(): HasMany
    {
        return $this->hasMany(GradeEncodingPeriod::class);
    }

    /**
     * Scope a query to only include active semesters.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include current semester.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope a query to only include semesters within enrollment period.
     */
    public function scopeEnrollmentOpen($query)
    {
        $today = Carbon::today();
        return $query->where('enrollment_start', '<=', $today)
                    ->where('enrollment_end', '>=', $today);
    }

    /**
     * Check if the semester is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the semester is current
     *
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->is_current;
    }

    /**
     * Check if enrollment is open
     *
     * @return bool
     */
    public function isEnrollmentOpen(): bool
    {
        $today = Carbon::today();
        return $today->between($this->enrollment_start, $this->enrollment_end);
    }

    /**
     * Set this semester as current
     *
     * @return bool
     */
    public function setAsCurrent(): bool
    {
        // Remove current status from other semesters in the same academic year
        self::where('academic_year_id', $this->academic_year_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        // Set this semester as current
        return $this->update(['is_current' => true]);
    }

    /**
     * Get formatted semester name with academic year
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->academicYear->name . ')';
    }

    /**
     * Get enrollment status
     *
     * @return string
     */
    public function getEnrollmentStatusAttribute(): string
    {
        $today = Carbon::today();
        
        if ($today->lt($this->enrollment_start)) {
            return 'Not Started';
        } elseif ($today->gt($this->enrollment_end)) {
            return 'Closed';
        } else {
            return 'Open';
        }
    }

    /**
     * Get semester duration in days
     *
     * @return int
     */
    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Get enrollment duration in days
     *
     * @return int
     */
    public function getEnrollmentDurationAttribute(): int
    {
        return $this->enrollment_start->diffInDays($this->enrollment_end) + 1;
    }
}