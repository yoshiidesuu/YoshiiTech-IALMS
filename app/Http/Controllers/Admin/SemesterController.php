<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SemesterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:academic.manage');
    }

    /**
     * Display a listing of semesters.
     */
    public function index(Request $request)
    {
        $query = Semester::with(['academicYear', 'gradeEncodingPeriods']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('academicYear', function ($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year_id', $request->academic_year);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'current') {
                $query->current();
            } elseif ($request->status === 'enrollment_open') {
                $query->enrollmentOpen();
            }
        }

        // Filter by term number
        if ($request->filled('term')) {
            $query->where('term_number', $request->term);
        }

        $semesters = $query->orderBy('academic_year_id', 'desc')
                          ->orderBy('term_number')
                          ->paginate(15)
                          ->withQueryString();

        $academicYears = AcademicYear::active()->orderBy('start_date', 'desc')->get();

        return view('admin.semesters.index', compact('semesters', 'academicYears'));
    }

    /**
     * Show the form for creating a new semester.
     */
    public function create()
    {
        $academicYears = AcademicYear::active()->orderBy('start_date', 'desc')->get();
        return view('admin.semesters.create', compact('academicYears'));
    }

    /**
     * Store a newly created semester.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'enrollment_start' => ['required', 'date', 'before_or_equal:enrollment_end'],
            'enrollment_end' => ['required', 'date', 'after_or_equal:enrollment_start'],
            'term_number' => ['required', 'integer', 'min:1', 'max:4'],
            'status' => ['required', 'in:' . implode(',', array_keys(Semester::getStatuses()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_current' => ['boolean'],
        ]);

        // Additional validation: check for duplicate semester name within the same academic year
        $existingSemester = Semester::where('academic_year_id', $validated['academic_year_id'])
                                   ->where('name', $validated['name'])
                                   ->first();
        
        if ($existingSemester) {
            return back()->withErrors([
                'name' => 'A semester with this name already exists in the selected academic year.'
            ])->withInput();
        }

        // Additional validation: check for duplicate term number within the same academic year
        $existingTerm = Semester::where('academic_year_id', $validated['academic_year_id'])
                               ->where('term_number', $validated['term_number'])
                               ->first();
        
        if ($existingTerm) {
            return back()->withErrors([
                'term_number' => 'A semester with this term number already exists in the selected academic year.'
            ])->withInput();
        }

        DB::transaction(function () use ($validated) {
            // If setting as current, unset other current semesters in the same academic year
            if ($validated['is_current'] ?? false) {
                Semester::where('academic_year_id', $validated['academic_year_id'])
                       ->where('is_current', true)
                       ->update(['is_current' => false]);
            }

            Semester::create([
                'name' => $validated['name'],
                'academic_year_id' => $validated['academic_year_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'enrollment_start' => $validated['enrollment_start'],
                'enrollment_end' => $validated['enrollment_end'],
                'term_number' => $validated['term_number'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'is_current' => $validated['is_current'] ?? false,
            ]);
        });

        return redirect()->route('admin.semesters.index')
                        ->with('success', 'Semester created successfully.');
    }

    /**
     * Display the specified semester.
     */
    public function show(Semester $semester)
    {
        $semester->load([
            'academicYear',
            'gradeEncodingPeriods' => function ($query) {
                $query->orderBy('start_date');
            }
        ]);

        return view('admin.semesters.show', compact('semester'));
    }

    /**
     * Show the form for editing the specified semester.
     */
    public function edit(Semester $semester)
    {
        $academicYears = AcademicYear::active()->orderBy('start_date', 'desc')->get();
        return view('admin.semesters.edit', compact('semester', 'academicYears'));
    }

    /**
     * Update the specified semester.
     */
    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'enrollment_start' => ['required', 'date', 'before_or_equal:enrollment_end'],
            'enrollment_end' => ['required', 'date', 'after_or_equal:enrollment_start'],
            'term_number' => ['required', 'integer', 'min:1', 'max:4'],
            'status' => ['required', 'in:' . implode(',', array_keys(Semester::getStatuses()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_current' => ['boolean'],
        ]);

        // Additional validation: check for duplicate semester name within the same academic year
        $existingSemester = Semester::where('academic_year_id', $validated['academic_year_id'])
                                   ->where('name', $validated['name'])
                                   ->where('id', '!=', $semester->id)
                                   ->first();
        
        if ($existingSemester) {
            return back()->withErrors([
                'name' => 'A semester with this name already exists in the selected academic year.'
            ])->withInput();
        }

        // Additional validation: check for duplicate term number within the same academic year
        $existingTerm = Semester::where('academic_year_id', $validated['academic_year_id'])
                               ->where('term_number', $validated['term_number'])
                               ->where('id', '!=', $semester->id)
                               ->first();
        
        if ($existingTerm) {
            return back()->withErrors([
                'term_number' => 'A semester with this term number already exists in the selected academic year.'
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $semester) {
            // If setting as current, unset other current semesters in the same academic year
            if ($validated['is_current'] ?? false) {
                Semester::where('academic_year_id', $validated['academic_year_id'])
                       ->where('is_current', true)
                       ->where('id', '!=', $semester->id)
                       ->update(['is_current' => false]);
            }

            $semester->update([
                'name' => $validated['name'],
                'academic_year_id' => $validated['academic_year_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'enrollment_start' => $validated['enrollment_start'],
                'enrollment_end' => $validated['enrollment_end'],
                'term_number' => $validated['term_number'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'is_current' => $validated['is_current'] ?? false,
            ]);
        });

        return redirect()->route('admin.semesters.index')
                        ->with('success', 'Semester updated successfully.');
    }

    /**
     * Remove the specified semester.
     */
    public function destroy(Semester $semester)
    {
        // Prevent deletion if it's the current semester
        if ($semester->is_current) {
            return redirect()->route('admin.semesters.index')
                           ->with('error', 'Cannot delete the current semester.');
        }

        // Check if there are related grade encoding periods
        if ($semester->gradeEncodingPeriods()->count() > 0) {
            return redirect()->route('admin.semesters.index')
                           ->with('error', 'Cannot delete semester with existing grade encoding periods.');
        }

        DB::transaction(function () use ($semester) {
            $semester->delete();
        });

        return redirect()->route('admin.semesters.index')
                        ->with('success', 'Semester deleted successfully.');
    }

    /**
     * Set the specified semester as current.
     */
    public function setCurrent(Semester $semester)
    {
        if ($semester->status !== Semester::STATUS_ACTIVE) {
            return redirect()->route('admin.semesters.index')
                           ->with('error', 'Only active semesters can be set as current.');
        }

        DB::transaction(function () use ($semester) {
            // Unset current status from all other semesters in the same academic year
            Semester::where('academic_year_id', $semester->academic_year_id)
                   ->where('is_current', true)
                   ->update(['is_current' => false]);
            
            // Set this semester as current
            $semester->update(['is_current' => true]);
        });

        return redirect()->route('admin.semesters.index')
                        ->with('success', 'Semester set as current successfully.');
    }

    /**
     * Toggle semester status between active and inactive.
     */
    public function toggleStatus(Semester $semester)
    {
        if ($semester->is_current && $semester->status === Semester::STATUS_ACTIVE) {
            return redirect()->route('admin.semesters.index')
                           ->with('error', 'Cannot deactivate the current semester.');
        }

        $newStatus = $semester->status === Semester::STATUS_ACTIVE 
                    ? Semester::STATUS_INACTIVE 
                    : Semester::STATUS_ACTIVE;

        $semester->update(['status' => $newStatus]);
        
        $statusText = $newStatus === Semester::STATUS_ACTIVE ? 'activated' : 'deactivated';
        return redirect()->route('admin.semesters.index')
                        ->with('success', "Semester {$statusText} successfully.");
    }

    /**
     * Open enrollment for the semester.
     */
    public function openEnrollment(Semester $semester)
    {
        if ($semester->status !== Semester::STATUS_ACTIVE) {
            return redirect()->route('admin.semesters.index')
                           ->with('error', 'Only active semesters can have enrollment opened.');
        }

        $now = Carbon::now();
        if ($now->lt($semester->enrollment_start) || $now->gt($semester->enrollment_end)) {
            return redirect()->route('admin.semesters.index')
                           ->with('error', 'Current date is outside the enrollment period.');
        }

        $semester->update(['enrollment_status' => 'open']);

        return redirect()->route('admin.semesters.index')
                        ->with('success', 'Enrollment opened successfully.');
    }

    /**
     * Close enrollment for the semester.
     */
    public function closeEnrollment(Semester $semester)
    {
        $semester->update(['enrollment_status' => 'closed']);

        return redirect()->route('admin.semesters.index')
                        ->with('success', 'Enrollment closed successfully.');
    }

    /**
     * Get semester statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => Semester::count(),
            'active' => Semester::active()->count(),
            'current' => Semester::current()->count(),
            'enrollment_open' => Semester::enrollmentOpen()->count(),
        ];

        return response()->json($stats);
    }
}