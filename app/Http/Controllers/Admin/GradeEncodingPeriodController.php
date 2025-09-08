<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeEncodingPeriod;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GradeEncodingPeriodController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:grade-encoding-periods.manage');
    }

    /**
     * Display a listing of grade encoding periods.
     */
    public function index(Request $request)
    {
        $query = GradeEncodingPeriod::with(['academicYear', 'semester', 'creator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('academicYear', function ($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('semester', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by academic year
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        // Filter by semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'upcoming') {
                $query->upcoming();
            } elseif ($request->status === 'expired') {
                $query->expired();
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter by grade type
        if ($request->filled('grade_type')) {
            $query->where('grade_type', $request->grade_type);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $gradeEncodingPeriods = $query->orderBy('start_date', 'desc')
                        ->paginate(15)
                        ->withQueryString();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get(['id', 'name']);
        $semesters = Semester::with('academicYear')
                           ->orderBy('name')
                           ->get(['id', 'name', 'academic_year_id']);

        // Calculate statistics
        $statistics = [
            'total' => GradeEncodingPeriod::count(),
            'active' => GradeEncodingPeriod::where('status', GradeEncodingPeriod::STATUS_ACTIVE)->count(),
            'upcoming' => GradeEncodingPeriod::upcoming()->count(),
            'expired' => GradeEncodingPeriod::expired()->count(),
        ];

        return view('admin.grade_encoding_periods.index', compact('gradeEncodingPeriods', 'academicYears', 'semesters', 'statistics'));
    }

    /**
     * Show the form for creating a new grade encoding period.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get(['id', 'name']);
        $semesters = Semester::with('academicYear')
                           ->orderBy('name')
                           ->get(['id', 'name', 'academic_year_id']);

        return view('admin.grade_encoding_periods.create', compact('academicYears', 'semesters'));
    }

    /**
     * Store a newly created grade encoding period.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'grade_type' => ['required', 'in:' . implode(',', array_keys(GradeEncodingPeriod::getGradeTypes()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_extendable' => ['boolean'],
            'extension_deadline' => ['nullable', 'date', 'after:end_date'],
        ]);

        // Validate semester belongs to academic year if provided
        if ($validated['semester_id']) {
            $semester = Semester::find($validated['semester_id']);
            if ($semester->academic_year_id != $validated['academic_year_id']) {
                return back()->withErrors([
                    'semester_id' => 'Selected semester does not belong to the selected academic year.'
                ])->withInput();
            }
        }

        // Check for overlapping periods of the same type
        $overlapping = GradeEncodingPeriod::where('academic_year_id', $validated['academic_year_id'])
            ->where('grade_type', $validated['grade_type'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                      });
            });

        if ($validated['semester_id']) {
            $overlapping->where('semester_id', $validated['semester_id']);
        } else {
            $overlapping->whereNull('semester_id');
        }

        if ($overlapping->exists()) {
            return back()->withErrors([
                'start_date' => 'A grade encoding period of the same type already exists for this date range.'
            ])->withInput();
        }

        DB::transaction(function () use ($validated) {
            GradeEncodingPeriod::create([
                'name' => $validated['name'],
                'academic_year_id' => $validated['academic_year_id'],
                'semester_id' => $validated['semester_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'grade_type' => $validated['grade_type'],
                'description' => $validated['description'],
                'is_extendable' => $validated['is_extendable'] ?? false,
                'extension_deadline' => $validated['extension_deadline'],
                'status' => GradeEncodingPeriod::STATUS_SCHEDULED,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->route('admin.grade-encoding-periods.index')
                        ->with('success', 'Grade encoding period created successfully.');
    }

    /**
     * Display the specified grade encoding period.
     */
    public function show(GradeEncodingPeriod $gradeEncodingPeriod)
    {
        $gradeEncodingPeriod->load(['academicYear', 'semester', 'creator']);

        return view('admin.grade_encoding_periods.show', compact('gradeEncodingPeriod'));
    }

    /**
     * Show the form for editing the specified grade encoding period.
     */
    public function edit(GradeEncodingPeriod $gradeEncodingPeriod)
    {
        // Prevent editing active or closed periods
        if (in_array($gradeEncodingPeriod->status, [GradeEncodingPeriod::STATUS_ACTIVE, GradeEncodingPeriod::STATUS_CLOSED])) {
            return redirect()->route('admin.grade-encoding-periods.index')
                           ->with('error', 'Cannot edit active or closed grade encoding periods.');
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get(['id', 'name']);
        $semesters = Semester::with('academicYear')
                           ->orderBy('name')
                           ->get(['id', 'name', 'academic_year_id']);

        return view('admin.grade_encoding_periods.edit', compact('gradeEncodingPeriod', 'academicYears', 'semesters'));
    }

    /**
     * Update the specified grade encoding period.
     */
    public function update(Request $request, GradeEncodingPeriod $gradeEncodingPeriod)
    {
        // Prevent updating active or closed periods
        if (in_array($gradeEncodingPeriod->status, [GradeEncodingPeriod::STATUS_ACTIVE, GradeEncodingPeriod::STATUS_CLOSED])) {
            return redirect()->route('admin.grade-encoding-periods.index')
                           ->with('error', 'Cannot update active or closed grade encoding periods.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'grade_type' => ['required', 'in:' . implode(',', array_keys(GradeEncodingPeriod::getGradeTypes()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_extendable' => ['boolean'],
            'extension_deadline' => ['nullable', 'date', 'after:end_date'],
        ]);

        // Validate semester belongs to academic year if provided
        if ($validated['semester_id']) {
            $semester = Semester::find($validated['semester_id']);
            if ($semester->academic_year_id != $validated['academic_year_id']) {
                return back()->withErrors([
                    'semester_id' => 'Selected semester does not belong to the selected academic year.'
                ])->withInput();
            }
        }

        // Check for overlapping periods of the same type (excluding current period)
        $overlapping = GradeEncodingPeriod::where('id', '!=', $gradeEncodingPeriod->id)
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('grade_type', $validated['grade_type'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                      });
            });

        if ($validated['semester_id']) {
            $overlapping->where('semester_id', $validated['semester_id']);
        } else {
            $overlapping->whereNull('semester_id');
        }

        if ($overlapping->exists()) {
            return back()->withErrors([
                'start_date' => 'A grade encoding period of the same type already exists for this date range.'
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $gradeEncodingPeriod) {
            $gradeEncodingPeriod->update([
                'name' => $validated['name'],
                'academic_year_id' => $validated['academic_year_id'],
                'semester_id' => $validated['semester_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'grade_type' => $validated['grade_type'],
                'description' => $validated['description'],
                'is_extendable' => $validated['is_extendable'] ?? false,
                'extension_deadline' => $validated['extension_deadline'],
            ]);
        });

        return redirect()->route('admin.grade-encoding-periods.index')
                        ->with('success', 'Grade encoding period updated successfully.');
    }

    /**
     * Remove the specified grade encoding period.
     */
    public function destroy(GradeEncodingPeriod $gradeEncodingPeriod)
    {
        // Prevent deletion of active or closed periods
        if (in_array($gradeEncodingPeriod->status, [GradeEncodingPeriod::STATUS_ACTIVE, GradeEncodingPeriod::STATUS_CLOSED])) {
            return redirect()->route('admin.grade-encoding-periods.index')
                           ->with('error', 'Cannot delete active or closed grade encoding periods.');
        }

        DB::transaction(function () use ($gradeEncodingPeriod) {
            $gradeEncodingPeriod->delete();
        });

        return redirect()->route('admin.grade-encoding-periods.index')
                        ->with('success', 'Grade encoding period deleted successfully.');
    }

    /**
     * Activate the specified grade encoding period.
     */
    public function activate(GradeEncodingPeriod $gradeEncodingPeriod)
    {
        if ($gradeEncodingPeriod->status !== GradeEncodingPeriod::STATUS_SCHEDULED) {
            return redirect()->route('admin.grade-encoding-periods.index')
                           ->with('error', 'Only scheduled periods can be activated.');
        }

        // Check if current date is within the period
        $now = Carbon::now();
        if ($now->lt($gradeEncodingPeriod->start_date) || $now->gt($gradeEncodingPeriod->end_date)) {
            return redirect()->route('admin.grade-encoding-periods.index')
                           ->with('error', 'Cannot activate period outside its scheduled dates.');
        }

        DB::transaction(function () use ($gradeEncodingPeriod) {
            $gradeEncodingPeriod->activate();
        });

        return redirect()->route('admin.grade-encoding-periods.index')
                        ->with('success', 'Grade encoding period activated successfully.');
    }

    /**
     * Close the specified grade encoding period.
     */
    public function close(GradeEncodingPeriod $gradeEncodingPeriod)
    {
        if ($gradeEncodingPeriod->status !== GradeEncodingPeriod::STATUS_ACTIVE) {
            return redirect()->route('admin.grade-encoding-periods.index')
                           ->with('error', 'Only active periods can be closed.');
        }

        DB::transaction(function () use ($gradeEncodingPeriod) {
            $gradeEncodingPeriod->close();
        });

        return redirect()->route('admin.grade-encoding-periods.index')
                        ->with('success', 'Grade encoding period closed successfully.');
    }

    /**
     * Extend the specified grade encoding period.
     */
    public function extend(Request $request, GradeEncodingPeriod $gradeEncodingPeriod)
    {
        if (!$gradeEncodingPeriod->is_extendable) {
            return redirect()->route('admin.grade-encoding-periods.index')
                           ->with('error', 'This period is not extendable.');
        }

        if ($gradeEncodingPeriod->status !== GradeEncodingPeriod::STATUS_ACTIVE) {
            return redirect()->route('admin.grade-encoding-periods.index')
                           ->with('error', 'Only active periods can be extended.');
        }

        $validated = $request->validate([
            'new_end_date' => [
                'required',
                'date',
                'after:' . $gradeEncodingPeriod->end_date,
                'before_or_equal:' . ($gradeEncodingPeriod->extension_deadline ?? '2099-12-31')
            ],
            'extension_reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $gradeEncodingPeriod) {
            $gradeEncodingPeriod->extend(
                $validated['new_end_date'],
                $validated['extension_reason']
            );
        });

        return redirect()->route('admin.grade-encoding-periods.index')
                        ->with('success', 'Grade encoding period extended successfully.');
    }

    /**
     * Get semesters by academic year for AJAX requests.
     */
    public function getSemestersByAcademicYear(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        
        $semesters = Semester::where('academic_year_id', $academicYearId)
                           ->orderBy('term_number')
                           ->get(['id', 'name', 'term_number']);

        return response()->json($semesters);
    }

    /**
     * Check for period conflicts for AJAX requests.
     */
    public function checkConflicts(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $semesterId = $request->get('semester_id');
        $gradeType = $request->get('grade_type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $excludeId = $request->get('exclude_id');

        $query = GradeEncodingPeriod::where('academic_year_id', $academicYearId)
            ->where('grade_type', $gradeType)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($subQ) use ($startDate, $endDate) {
                      $subQ->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
                  });
            });

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        } else {
            $query->whereNull('semester_id');
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $conflicts = $query->get(['id', 'name', 'start_date', 'end_date']);

        return response()->json([
            'has_conflicts' => $conflicts->count() > 0,
            'conflicts' => $conflicts
        ]);
    }

    /**
     * Get grade encoding period statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => GradeEncodingPeriod::count(),
            'scheduled' => GradeEncodingPeriod::where('status', GradeEncodingPeriod::STATUS_SCHEDULED)->count(),
            'active' => GradeEncodingPeriod::active()->count(),
            'closed' => GradeEncodingPeriod::where('status', GradeEncodingPeriod::STATUS_CLOSED)->count(),
            'upcoming' => GradeEncodingPeriod::upcoming()->count(),
            'expired' => GradeEncodingPeriod::expired()->count(),
            'by_grade_type' => GradeEncodingPeriod::selectRaw('grade_type, COUNT(*) as count')
                                                 ->groupBy('grade_type')
                                                 ->pluck('count', 'grade_type'),
            'extendable' => GradeEncodingPeriod::where('is_extendable', true)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get current active periods for AJAX requests.
     */
    public function getActivePeriods()
    {
        $periods = GradeEncodingPeriod::with(['academicYear', 'semester'])
                                    ->active()
                                    ->orderBy('end_date')
                                    ->get(['id', 'name', 'grade_type', 'end_date', 'academic_year_id', 'semester_id']);

        return response()->json($periods);
    }
}