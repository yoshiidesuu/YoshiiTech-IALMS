<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:academic.manage');
    }

    /**
     * Display a listing of subjects.
     */
    public function index(Request $request)
    {
        $query = Subject::with(['prerequisites']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by year level
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        // Filter by laboratory
        if ($request->filled('laboratory')) {
            if ($request->status === 'yes') {
                $query->where('has_laboratory', true);
            } elseif ($request->status === 'no') {
                $query->where('has_laboratory', false);
            }
        }

        $subjects = $query->orderBy('code')
                         ->paginate(15)
                         ->withQueryString();

        $categories = Subject::select('category')
                            ->distinct()
                            ->whereNotNull('category')
                            ->orderBy('category')
                            ->pluck('category');

        $departments = Subject::select('department')
                             ->distinct()
                             ->whereNotNull('department')
                             ->orderBy('department')
                             ->pluck('department');

        return view('admin.subjects.index', compact('subjects', 'categories', 'departments'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $availableSubjects = Subject::active()
                                   ->orderBy('code')
                                   ->get(['id', 'code', 'name']);

        return view('admin.subjects.create', compact('availableSubjects'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:subjects,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credits' => ['required', 'numeric', 'min:0', 'max:10'],
            'category' => ['required', 'in:' . implode(',', array_keys(Subject::getCategories()))],
            'department' => ['required', 'string', 'max:100'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'semester_offered' => ['required', 'array'],
            'semester_offered.*' => ['in:1,2,3,summer'],
            'capacity' => ['required', 'integer', 'min:1', 'max:200'],
            'status' => ['required', 'in:' . implode(',', array_keys(Subject::getStatuses()))],
            'has_laboratory' => ['boolean'],
            'lecture_hours' => ['required', 'numeric', 'min:0', 'max:10'],
            'laboratory_hours' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['exists:subjects,id'],
        ]);

        // Validate laboratory hours if has_laboratory is true
        if ($validated['has_laboratory'] && empty($validated['laboratory_hours'])) {
            return back()->withErrors([
                'laboratory_hours' => 'Laboratory hours are required when subject has laboratory.'
            ])->withInput();
        }

        DB::transaction(function () use ($validated) {
            $subject = Subject::create([
                'code' => strtoupper($validated['code']),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'credits' => $validated['credits'],
                'category' => $validated['category'],
                'department' => $validated['department'],
                'year_level' => $validated['year_level'],
                'semester_offered' => $validated['semester_offered'],
                'capacity' => $validated['capacity'],
                'status' => $validated['status'],
                'has_laboratory' => $validated['has_laboratory'] ?? false,
                'lecture_hours' => $validated['lecture_hours'],
                'laboratory_hours' => $validated['laboratory_hours'] ?? 0,
            ]);

            // Attach prerequisites if provided
            if (!empty($validated['prerequisites'])) {
                $subject->prerequisites()->attach($validated['prerequisites']);
            }
        });

        return redirect()->route('admin.subjects.index')
                        ->with('success', 'Subject created successfully.');
    }

    /**
     * Display the specified subject.
     */
    public function show(Subject $subject)
    {
        $subject->load([
            'prerequisites',
            'dependentSubjects' => function ($query) {
                $query->orderBy('code');
            }
        ]);

        return view('admin.subjects.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        $availableSubjects = Subject::active()
                                   ->where('id', '!=', $subject->id)
                                   ->orderBy('code')
                                   ->get(['id', 'code', 'name']);

        $subject->load('prerequisites');

        return view('admin.subjects.edit', compact('subject', 'availableSubjects'));
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('subjects')->ignore($subject->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credits' => ['required', 'numeric', 'min:0', 'max:10'],
            'category' => ['required', 'in:' . implode(',', array_keys(Subject::getCategories()))],
            'department' => ['required', 'string', 'max:100'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'semester_offered' => ['required', 'array'],
            'semester_offered.*' => ['in:1,2,3,summer'],
            'capacity' => ['required', 'integer', 'min:1', 'max:200'],
            'status' => ['required', 'in:' . implode(',', array_keys(Subject::getStatuses()))],
            'has_laboratory' => ['boolean'],
            'lecture_hours' => ['required', 'numeric', 'min:0', 'max:10'],
            'laboratory_hours' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['exists:subjects,id', 'not_in:' . $subject->id],
        ]);

        // Validate laboratory hours if has_laboratory is true
        if ($validated['has_laboratory'] && empty($validated['laboratory_hours'])) {
            return back()->withErrors([
                'laboratory_hours' => 'Laboratory hours are required when subject has laboratory.'
            ])->withInput();
        }

        // Check for circular dependencies in prerequisites
        if (!empty($validated['prerequisites'])) {
            foreach ($validated['prerequisites'] as $prerequisiteId) {
                if ($this->hasCircularDependency($subject->id, $prerequisiteId)) {
                    return back()->withErrors([
                        'prerequisites' => 'Circular dependency detected. A prerequisite cannot depend on this subject.'
                    ])->withInput();
                }
            }
        }

        DB::transaction(function () use ($validated, $subject) {
            $subject->update([
                'code' => strtoupper($validated['code']),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'credits' => $validated['credits'],
                'category' => $validated['category'],
                'department' => $validated['department'],
                'year_level' => $validated['year_level'],
                'semester_offered' => $validated['semester_offered'],
                'capacity' => $validated['capacity'],
                'status' => $validated['status'],
                'has_laboratory' => $validated['has_laboratory'] ?? false,
                'lecture_hours' => $validated['lecture_hours'],
                'laboratory_hours' => $validated['laboratory_hours'] ?? 0,
            ]);

            // Sync prerequisites
            $subject->prerequisites()->sync($validated['prerequisites'] ?? []);
        });

        return redirect()->route('admin.subjects.index')
                        ->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        // Check if subject is used as a prerequisite
        if ($subject->dependentSubjects()->count() > 0) {
            return redirect()->route('admin.subjects.index')
                           ->with('error', 'Cannot delete subject that is used as a prerequisite for other subjects.');
        }

        DB::transaction(function () use ($subject) {
            // Remove all prerequisite relationships
            $subject->prerequisites()->detach();
            
            // Delete the subject
            $subject->delete();
        });

        return redirect()->route('admin.subjects.index')
                        ->with('success', 'Subject deleted successfully.');
    }

    /**
     * Toggle subject status between active and inactive.
     */
    public function toggleStatus(Subject $subject)
    {
        $newStatus = $subject->status === Subject::STATUS_ACTIVE 
                    ? Subject::STATUS_INACTIVE 
                    : Subject::STATUS_ACTIVE;

        $subject->update(['status' => $newStatus]);
        
        $statusText = $newStatus === Subject::STATUS_ACTIVE ? 'activated' : 'deactivated';
        return redirect()->route('admin.subjects.index')
                        ->with('success', "Subject {$statusText} successfully.");
    }

    /**
     * Get subjects by department for AJAX requests.
     */
    public function getByDepartment(Request $request)
    {
        $department = $request->get('department');
        
        $subjects = Subject::active()
                          ->where('department', $department)
                          ->orderBy('code')
                          ->get(['id', 'code', 'name']);

        return response()->json($subjects);
    }

    /**
     * Get subjects by year level for AJAX requests.
     */
    public function getByYearLevel(Request $request)
    {
        $yearLevel = $request->get('year_level');
        
        $subjects = Subject::active()
                          ->where('year_level', $yearLevel)
                          ->orderBy('code')
                          ->get(['id', 'code', 'name']);

        return response()->json($subjects);
    }

    /**
     * Get subject prerequisites for AJAX requests.
     */
    public function getPrerequisites(Subject $subject)
    {
        $prerequisites = $subject->prerequisites()
                               ->orderBy('code')
                               ->get(['id', 'code', 'name']);

        return response()->json($prerequisites);
    }

    /**
     * Check for circular dependency in prerequisites.
     */
    private function hasCircularDependency($subjectId, $prerequisiteId, $visited = [])
    {
        if (in_array($prerequisiteId, $visited)) {
            return true;
        }

        $visited[] = $prerequisiteId;

        $prerequisiteSubject = Subject::find($prerequisiteId);
        if (!$prerequisiteSubject) {
            return false;
        }

        foreach ($prerequisiteSubject->prerequisites as $prereq) {
            if ($prereq->id == $subjectId) {
                return true;
            }
            
            if ($this->hasCircularDependency($subjectId, $prereq->id, $visited)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get subject statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => Subject::count(),
            'active' => Subject::active()->count(),
            'inactive' => Subject::inactive()->count(),
            'with_laboratory' => Subject::where('has_laboratory', true)->count(),
            'by_category' => Subject::selectRaw('category, COUNT(*) as count')
                                   ->groupBy('category')
                                   ->pluck('count', 'category'),
            'by_department' => Subject::selectRaw('department, COUNT(*) as count')
                                     ->groupBy('department')
                                     ->pluck('count', 'department'),
        ];

        return response()->json($stats);
    }
}