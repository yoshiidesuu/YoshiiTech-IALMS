<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:policies.manage');
    }

    /**
     * Display a listing of policies.
     */
    public function index(Request $request)
    {
        $query = Policy::with(['creator', 'approver', 'parentPolicy']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhereJsonContains('tags', $search);
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by effective date
        if ($request->filled('effective_date')) {
            $query->whereDate('effective_date', $request->effective_date);
        }

        $policies = $query->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        $categories = Policy::select('category')
                           ->distinct()
                           ->whereNotNull('category')
                           ->orderBy('category')
                           ->pluck('category');

        return view('admin.policies.index', compact('policies', 'categories'));
    }

    /**
     * Show the form for creating a new policy.
     */
    public function create()
    {
        $approvers = User::whereHas('roles', function ($query) {
            $query->whereHas('permissions', function ($pq) {
                $pq->where('name', 'policies.approve');
            });
        })->get(['id', 'name']);

        return view('admin.policies.create', compact('approvers'));
    }

    /**
     * Store a newly created policy.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'in:' . implode(',', array_keys(Policy::getCategories()))],
            'summary' => ['nullable', 'string', 'max:500'],
            'effective_date' => ['nullable', 'date', 'after_or_equal:today'],
            'expiry_date' => ['nullable', 'date', 'after:effective_date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'status' => ['required', 'in:' . implode(',', array_keys(Policy::getStatuses()))],
        ]);

        DB::transaction(function () use ($validated) {
            Policy::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                'summary' => $validated['summary'],
                'effective_date' => $validated['effective_date'],
                'expiry_date' => $validated['expiry_date'],
                'tags' => $validated['tags'] ?? [],
                'status' => $validated['status'],
                'version' => '1.0',
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.policies.index')
                        ->with('success', 'Policy created successfully.');
    }

    /**
     * Display the specified policy.
     */
    public function show(Policy $policy)
    {
        $policy->load([
            'creator',
            'approver',
            'parentPolicy',
            'childPolicies' => function ($query) {
                $query->orderBy('version', 'desc');
            }
        ]);

        return view('admin.policies.show', compact('policy'));
    }

    /**
     * Show the form for editing the specified policy.
     */
    public function edit(Policy $policy)
    {
        $approvers = User::whereHas('roles', function ($query) {
            $query->whereHas('permissions', function ($pq) {
                $pq->where('name', 'policies.approve');
            });
        })->get(['id', 'name']);

        return view('admin.policies.edit', compact('policy', 'approvers'));
    }

    /**
     * Update the specified policy.
     */
    public function update(Request $request, Policy $policy)
    {
        // Prevent editing published policies
        if ($policy->status === Policy::STATUS_PUBLISHED) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'Cannot edit published policies. Create a new version instead.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'in:' . implode(',', array_keys(Policy::getCategories()))],
            'summary' => ['nullable', 'string', 'max:500'],
            'effective_date' => ['nullable', 'date', 'after_or_equal:today'],
            'expiry_date' => ['nullable', 'date', 'after:effective_date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'status' => ['required', 'in:' . implode(',', array_keys(Policy::getStatuses()))],
        ]);

        DB::transaction(function () use ($validated, $policy) {
            $policy->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'category' => $validated['category'],
                'summary' => $validated['summary'],
                'effective_date' => $validated['effective_date'],
                'expiry_date' => $validated['expiry_date'],
                'tags' => $validated['tags'] ?? [],
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('admin.policies.index')
                        ->with('success', 'Policy updated successfully.');
    }

    /**
     * Remove the specified policy.
     */
    public function destroy(Policy $policy)
    {
        // Prevent deletion of published policies
        if ($policy->status === Policy::STATUS_PUBLISHED) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'Cannot delete published policies.');
        }

        // Check if policy has child versions
        if ($policy->childPolicies()->count() > 0) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'Cannot delete policy with existing versions.');
        }

        DB::transaction(function () use ($policy) {
            $policy->delete();
        });

        return redirect()->route('admin.policies.index')
                        ->with('success', 'Policy deleted successfully.');
    }

    /**
     * Publish the specified policy.
     */
    public function publish(Policy $policy)
    {
        if ($policy->status !== Policy::STATUS_APPROVED) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'Only approved policies can be published.');
        }

        DB::transaction(function () use ($policy) {
            $policy->update([
                'status' => Policy::STATUS_PUBLISHED,
                'published_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('admin.policies.index')
                        ->with('success', 'Policy published successfully.');
    }

    /**
     * Approve the specified policy.
     */
    public function approve(Policy $policy)
    {
        if ($policy->status !== Policy::STATUS_REVIEW) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'Only policies under review can be approved.');
        }

        // Check if user has approval permission
        if (!Auth::user()->can('policies.approve')) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'You do not have permission to approve policies.');
        }

        DB::transaction(function () use ($policy) {
            $policy->update([
                'status' => Policy::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('admin.policies.index')
                        ->with('success', 'Policy approved successfully.');
    }

    /**
     * Submit policy for review.
     */
    public function submitForReview(Policy $policy)
    {
        if ($policy->status !== Policy::STATUS_DRAFT) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'Only draft policies can be submitted for review.');
        }

        DB::transaction(function () use ($policy) {
            $policy->update(['status' => Policy::STATUS_REVIEW]);
        });

        return redirect()->route('admin.policies.index')
                        ->with('success', 'Policy submitted for review successfully.');
    }

    /**
     * Archive the specified policy.
     */
    public function archive(Policy $policy)
    {
        if (!in_array($policy->status, [Policy::STATUS_PUBLISHED, Policy::STATUS_EXPIRED])) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'Only published or expired policies can be archived.');
        }

        DB::transaction(function () use ($policy) {
            $policy->update(['status' => Policy::STATUS_ARCHIVED]);
        });

        return redirect()->route('admin.policies.index')
                        ->with('success', 'Policy archived successfully.');
    }

    /**
     * Create a new version of the policy.
     */
    public function createVersion(Policy $policy)
    {
        if ($policy->status !== Policy::STATUS_PUBLISHED) {
            return redirect()->route('admin.policies.index')
                           ->with('error', 'Only published policies can have new versions created.');
        }

        // Get the next version number
        $latestVersion = Policy::where('parent_policy_id', $policy->id)
                              ->orWhere('id', $policy->id)
                              ->orderBy('version', 'desc')
                              ->first();
        
        $versionParts = explode('.', $latestVersion->version);
        $majorVersion = (int) $versionParts[0];
        $minorVersion = isset($versionParts[1]) ? (int) $versionParts[1] : 0;
        $newVersion = $majorVersion . '.' . ($minorVersion + 1);

        DB::transaction(function () use ($policy, $newVersion) {
            $newPolicy = Policy::create([
                'title' => $policy->title,
                'content' => $policy->content,
                'category' => $policy->category,
                'summary' => $policy->summary,
                'effective_date' => $policy->effective_date,
                'expiry_date' => $policy->expiry_date,
                'tags' => $policy->tags,
                'status' => Policy::STATUS_DRAFT,
                'version' => $newVersion,
                'parent_policy_id' => $policy->parent_policy_id ?? $policy->id,
                'created_by' => Auth::id(),
            ]);

            return $newPolicy;
        });

        return redirect()->route('admin.policies.index')
                        ->with('success', 'New policy version created successfully.');
    }

    /**
     * Get policy versions for AJAX requests.
     */
    public function getVersions(Policy $policy)
    {
        $versions = Policy::where('parent_policy_id', $policy->parent_policy_id ?? $policy->id)
                         ->orWhere('id', $policy->parent_policy_id ?? $policy->id)
                         ->orderBy('version', 'desc')
                         ->get(['id', 'version', 'status', 'created_at']);

        return response()->json($versions);
    }

    /**
     * Preview policy content.
     */
    public function preview(Policy $policy)
    {
        return view('admin.policies.preview', compact('policy'));
    }

    /**
     * Get policy statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => Policy::count(),
            'draft' => Policy::where('status', Policy::STATUS_DRAFT)->count(),
            'review' => Policy::where('status', Policy::STATUS_REVIEW)->count(),
            'approved' => Policy::where('status', Policy::STATUS_APPROVED)->count(),
            'published' => Policy::published()->count(),
            'archived' => Policy::where('status', Policy::STATUS_ARCHIVED)->count(),
            'by_category' => Policy::selectRaw('category, COUNT(*) as count')
                                  ->groupBy('category')
                                  ->pluck('count', 'category'),
        ];

        return response()->json($stats);
    }

    /**
     * Search policies by tags for AJAX requests.
     */
    public function searchByTags(Request $request)
    {
        $tag = $request->get('tag');
        
        $policies = Policy::whereJsonContains('tags', $tag)
                         ->published()
                         ->orderBy('published_at', 'desc')
                         ->get(['id', 'title', 'category', 'published_at']);

        return response()->json($policies);
    }
}