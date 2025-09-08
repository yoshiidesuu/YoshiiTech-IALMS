<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'is_current',
        'description',
        'archived_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'archived_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_DRAFT = 'draft';

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
            self::STATUS_DRAFT => 'Draft',
        ];
    }

    /**
     * Get the semesters for this academic year.
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    /**
     * Get the grade encoding periods for this academic year.
     */
    public function gradeEncodingPeriods(): HasMany
    {
        return $this->hasMany(GradeEncodingPeriod::class);
    }

    /**
     * Scope to get only active academic years.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to get the current academic year.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope to get non-archived academic years.
     */
    public function scopeNotArchived($query)
    {
        return $query->where('status', '!=', self::STATUS_ARCHIVED);
    }

    /**
     * Check if the academic year is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the academic year is current.
     *
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->is_current;
    }

    /**
     * Check if the academic year is archived.
     *
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Archive the academic year.
     *
     * @return bool
     */
    public function archive(): bool
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->archived_at = Carbon::now();
        $this->is_current = false;
        
        return $this->save();
    }

    /**
     * Set as current academic year.
     *
     * @return bool
     */
    public function setCurrent(): bool
    {
        // First, remove current status from all other academic years
        self::where('id', '!=', $this->id)->update(['is_current' => false]);
        
        // Set this academic year as current
        $this->is_current = true;
        $this->status = self::STATUS_ACTIVE;
        
        return $this->save();
    }

    /**
     * Get the formatted date range.
     *
     * @return string
     */
    public function getDateRangeAttribute(): string
    {
        return $this->start_date->format('M d, Y') . ' - ' . $this->end_date->format('M d, Y');
    }

    /**
     * Get the status badge class for Bootstrap.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-success',
            self::STATUS_INACTIVE => 'bg-warning',
            self::STATUS_ARCHIVED => 'bg-secondary',
            self::STATUS_DRAFT => 'bg-info',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the formatted status.
     *
     * @return string
     */
    public function getFormattedStatusAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    /**
     * Check if the academic year can be deleted.
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete if it has semesters or is current
        return !$this->is_current && $this->semesters()->count() === 0;
    }

    /**
     * Get the duration in days.
     *
     * @return int
     */
    public function getDurationInDays(): int
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Check if the academic year is currently ongoing.
     *
     * @return bool
     */
    public function isOngoing(): bool
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date) && $this->isActive();
    }
}