<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Policy extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'category',
        'version',
        'status',
        'published_at',
        'effective_date',
        'expiry_date',
        'created_by',
        'approved_by',
        'approved_at',
        'parent_policy_id',
        'summary',
        'tags'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'approved_at' => 'datetime',
        'tags' => 'array'
    ];

    /**
     * Status constants
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_REVIEW = 'review';
    const STATUS_APPROVED = 'approved';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_EXPIRED = 'expired';

    /**
     * Category constants
     */
    const CATEGORY_ACADEMIC = 'academic';
    const CATEGORY_ADMINISTRATIVE = 'administrative';
    const CATEGORY_STUDENT_AFFAIRS = 'student_affairs';
    const CATEGORY_FACULTY = 'faculty';
    const CATEGORY_FINANCIAL = 'financial';
    const CATEGORY_DISCIPLINARY = 'disciplinary';
    const CATEGORY_GENERAL = 'general';

    /**
     * Get all available statuses
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_REVIEW => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_EXPIRED => 'Expired',
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
            self::CATEGORY_ACADEMIC => 'Academic Policies',
            self::CATEGORY_ADMINISTRATIVE => 'Administrative Policies',
            self::CATEGORY_STUDENT_AFFAIRS => 'Student Affairs',
            self::CATEGORY_FACULTY => 'Faculty Policies',
            self::CATEGORY_FINANCIAL => 'Financial Policies',
            self::CATEGORY_DISCIPLINARY => 'Disciplinary Policies',
            self::CATEGORY_GENERAL => 'General Policies',
        ];
    }

    /**
     * Get the user who created this policy.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this policy.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the parent policy (for versioning).
     */
    public function parentPolicy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'parent_policy_id');
    }

    /**
     * Get the child policies (versions).
     */
    public function childPolicies(): HasMany
    {
        return $this->hasMany(Policy::class, 'parent_policy_id');
    }

    /**
     * Get policy acknowledgments.
     */
    public function acknowledgments(): HasMany
    {
        return $this->hasMany(PolicyAcknowledgment::class);
    }

    /**
     * Scope a query to only include published policies.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
                    ->where('published_at', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', now());
                    });
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include current versions.
     */
    public function scopeCurrentVersion(Builder $query): Builder
    {
        return $query->whereNull('parent_policy_id')
                    ->orWhereNotExists(function ($q) {
                        $q->select('id')
                          ->from('policies as p2')
                          ->whereColumn('p2.parent_policy_id', 'policies.id')
                          ->where('p2.status', '!=', self::STATUS_ARCHIVED);
                    });
    }

    /**
     * Scope a query to only include effective policies.
     */
    public function scopeEffective(Builder $query): Builder
    {
        $today = Carbon::today();
        return $query->where('effective_date', '<=', $today)
                    ->where(function ($q) use ($today) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', $today);
                    });
    }

    /**
     * Check if the policy is published
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && 
               $this->published_at && 
               $this->published_at->isPast();
    }

    /**
     * Check if the policy is effective
     *
     * @return bool
     */
    public function isEffective(): bool
    {
        $today = Carbon::today();
        return $this->effective_date <= $today && 
               (!$this->expiry_date || $this->expiry_date > $today);
    }

    /**
     * Check if the policy is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if the policy can be edited
     *
     * @return bool
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REVIEW]);
    }

    /**
     * Publish the policy
     *
     * @return bool
     */
    public function publish(): bool
    {
        if ($this->status !== self::STATUS_APPROVED) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now()
        ]);
    }

    /**
     * Archive the policy
     *
     * @return bool
     */
    public function archive(): bool
    {
        return $this->update(['status' => self::STATUS_ARCHIVED]);
    }

    /**
     * Create a new version of this policy
     *
     * @param array $attributes
     * @return Policy
     */
    public function createNewVersion(array $attributes): Policy
    {
        $newVersion = new static(array_merge(
            $this->toArray(),
            $attributes,
            [
                'parent_policy_id' => $this->id,
                'version' => $this->getNextVersionNumber(),
                'status' => self::STATUS_DRAFT,
                'published_at' => null,
                'approved_at' => null,
                'approved_by' => null
            ]
        ));

        $newVersion->save();
        return $newVersion;
    }

    /**
     * Get the next version number
     *
     * @return string
     */
    protected function getNextVersionNumber(): string
    {
        $basePolicy = $this->parentPolicy ?? $this;
        $latestVersion = static::where('parent_policy_id', $basePolicy->id)
                              ->orWhere('id', $basePolicy->id)
                              ->orderBy('version', 'desc')
                              ->first();

        if (!$latestVersion) {
            return '1.0';
        }

        $versionParts = explode('.', $latestVersion->version);
        $major = (int) ($versionParts[0] ?? 1);
        $minor = (int) ($versionParts[1] ?? 0);

        return $major . '.' . ($minor + 1);
    }

    /**
     * Get formatted version display
     *
     * @return string
     */
    public function getVersionDisplayAttribute(): string
    {
        return 'v' . $this->version;
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
     * Get status badge class for Bootstrap
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_DRAFT => 'badge-secondary',
            self::STATUS_REVIEW => 'badge-warning',
            self::STATUS_APPROVED => 'badge-info',
            self::STATUS_PUBLISHED => 'badge-success',
            self::STATUS_ARCHIVED => 'badge-dark',
            self::STATUS_EXPIRED => 'badge-danger',
            default => 'badge-secondary'
        };
    }
}