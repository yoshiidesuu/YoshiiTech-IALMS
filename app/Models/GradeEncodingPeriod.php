<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class GradeEncodingPeriod extends Model
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
        'semester_id',
        'start_date',
        'end_date',
        'status',
        'description',
        'grade_type',
        'is_extendable',
        'extension_deadline',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'extension_deadline' => 'datetime',
        'is_extendable' => 'boolean'
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';
    const STATUS_EXTENDED = 'extended';
    const STATUS_ARCHIVED = 'archived';

    /**
     * Grade type constants
     */
    const GRADE_TYPE_MIDTERM = 'midterm';
    const GRADE_TYPE_FINAL = 'final';
    const GRADE_TYPE_COMPLETION = 'completion';
    const GRADE_TYPE_REMOVAL = 'removal';
    const GRADE_TYPE_SPECIAL = 'special';

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
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_EXTENDED => 'Extended',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Get all available grade types
     *
     * @return array
     */
    public static function getGradeTypes(): array
    {
        return [
            self::GRADE_TYPE_MIDTERM => 'Midterm Grades',
            self::GRADE_TYPE_FINAL => 'Final Grades',
            self::GRADE_TYPE_COMPLETION => 'Completion Grades',
            self::GRADE_TYPE_REMOVAL => 'Removal Grades',
            self::GRADE_TYPE_SPECIAL => 'Special Grades',
        ];
    }

    /**
     * Get the academic year that owns the grade encoding period.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the semester that owns the grade encoding period.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the user who created this period.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include active periods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include current periods (within date range).
     */
    public function scopeCurrent(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_EXTENDED]);
    }

    /**
     * Scope a query to filter by grade type.
     */
    public function scopeByGradeType(Builder $query, string $gradeType): Builder
    {
        return $query->where('grade_type', $gradeType);
    }

    /**
     * Scope a query to filter by academic year.
     */
    public function scopeByAcademicYear(Builder $query, int $academicYearId): Builder
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope a query to filter by semester.
     */
    public function scopeBySemester(Builder $query, int $semesterId): Builder
    {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Check if the period is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the period is currently open for grade encoding
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        $now = Carbon::now();
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_EXTENDED]) &&
               $now->between($this->start_date, $this->getEffectiveEndDate());
    }

    /**
     * Check if the period is closed
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED || 
               Carbon::now()->gt($this->getEffectiveEndDate());
    }

    /**
     * Check if the period can be extended
     *
     * @return bool
     */
    public function canBeExtended(): bool
    {
        return $this->is_extendable && 
               $this->status === self::STATUS_ACTIVE &&
               (!$this->extension_deadline || Carbon::now()->lt($this->extension_deadline));
    }

    /**
     * Check if the period is overdue
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        return Carbon::now()->gt($this->getEffectiveEndDate()) && 
               !$this->isClosed();
    }

    /**
     * Get the effective end date (considering extensions)
     *
     * @return Carbon
     */
    public function getEffectiveEndDate(): Carbon
    {
        if ($this->status === self::STATUS_EXTENDED && $this->extension_deadline) {
            return $this->extension_deadline;
        }
        
        return $this->end_date;
    }

    /**
     * Extend the period
     *
     * @param Carbon $newEndDate
     * @param string|null $reason
     * @return bool
     */
    public function extend(Carbon $newEndDate, ?string $reason = null): bool
    {
        if (!$this->canBeExtended()) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_EXTENDED,
            'extension_deadline' => $newEndDate,
            'description' => $this->description . "\n\nExtended until {$newEndDate->format('M d, Y H:i')}" . 
                           ($reason ? ": {$reason}" : '')
        ]);
    }

    /**
     * Close the period
     *
     * @return bool
     */
    public function close(): bool
    {
        return $this->update(['status' => self::STATUS_CLOSED]);
    }

    /**
     * Activate the period
     *
     * @return bool
     */
    public function activate(): bool
    {
        if ($this->status !== self::STATUS_DRAFT) {
            return false;
        }

        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Get formatted period name with dates
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ' (' . $this->start_date->format('M d') . ' - ' . 
               $this->getEffectiveEndDate()->format('M d, Y') . ')';
    }

    /**
     * Get grade type display name
     *
     * @return string
     */
    public function getGradeTypeDisplayAttribute(): string
    {
        return self::getGradeTypes()[$this->grade_type] ?? $this->grade_type;
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
     * Get status badge class for Bootstrap
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'badge-secondary',
            self::STATUS_ACTIVE => 'badge-success',
            self::STATUS_CLOSED => 'badge-danger',
            self::STATUS_EXTENDED => 'badge-warning',
            self::STATUS_ARCHIVED => 'badge-dark',
            default => 'badge-secondary'
        };
    }

    /**
     * Get remaining time until deadline
     *
     * @return string
     */
    public function getRemainingTimeAttribute(): string
    {
        $now = Carbon::now();
        $deadline = $this->getEffectiveEndDate();
        
        if ($now->gt($deadline)) {
            return 'Overdue by ' . $now->diffForHumans($deadline, true);
        }
        
        return 'Ends ' . $deadline->diffForHumans();
    }

    /**
     * Get duration in days
     *
     * @return int
     */
    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->getEffectiveEndDate()) + 1;
    }
}