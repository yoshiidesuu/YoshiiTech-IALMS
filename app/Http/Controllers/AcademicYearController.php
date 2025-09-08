<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AcademicYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:academic.manage');
    }

    /**
     * Display a listing of academic years.
     */
    public function index(Request $request)
    {
        $query = AcademicYear::with(['semesters', 'gradeEncodingPeriods']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'archived') {
                $query->archived();
            } elseif ($request->status === 'current') {
                $query->current();
            }
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        $academicYears = $query->orderBy('start_date', 'desc')
                              ->paginate(15)
                              ->withQueryString();

        $years = AcademicYear::selectRaw('YEAR(start_date) as year')
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');

        return view('admin.academic-years.index', compact('academicYears', 'years'));
    }

    /**
     * Show the form for creating a new academic year.
     */
    public function create()
    {
        return view('admin.academic-years.create');
    }

    /**
     * Store a newly created academic year.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:academic_years,name'],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:' . implode(',', array_keys(AcademicYear::getStatuses()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_current' => ['boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            // If setting as current, unset other current academic years
            if ($validated['is_current'] ?? false) {
                AcademicYear::where('is_current', true)->update(['is_current' => false]);
            }

            AcademicYear::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'is_current' => $validated['is_current'] ?? false,
            ]);
        });

        return redirect()->route('admin.academic-years.index')
                        ->with('success', 'Academic year created successfully.');
    }

    /**
     * Display the specified academic year.
     */
    public function show(AcademicYear $academicYear)
    {
        $academicYear->load([
            'semesters' => function ($query) {
                $query->orderBy('term_number');
            },
            'gradeEncodingPeriods' => function ($query) {
                $query->orderBy('start_date');
            }
        ]);

        return view('admin.academic-years.show', compact('academicYear'));
    }

    /**
     * Show the form for editing the specified academic year.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    /**
     * Update the specified academic year.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('academic_years')->ignore($academicYear->id)],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:' . implode(',', array_keys(AcademicYear::getStatuses()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_current' => ['boolean'],
        ]);

        DB::transaction(function () use ($validated, $academicYear) {
            // If setting as current, unset other current academic years
            if ($validated['is_current'] ?? false) {
                AcademicYear::where('is_current', true)
                           ->where('id', '!=', $academicYear->id)
                           ->update(['is_current' => false]);
            }

            $academicYear->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'is_current' => $validated['is_current'] ?? false,
            ]);
        });

        return redirect()->route('admin.academic-years.index')
                        ->with('success', 'Academic year updated successfully.');
    }

    /**
     * Remove the specified academic year.
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Prevent deletion if it's the current academic year
        if ($academicYear->is_current) {
            return redirect()->route('admin.academic-years.index')
                           ->with('error', 'Cannot delete the current academic year.');
        }

        // Check if there are related semesters or grade encoding periods
        if ($academicYear->semesters()->count() > 0 || $academicYear->gradeEncodingPeriods()->count() > 0) {
            return redirect()->route('admin.academic-years.index')
                           ->with('error', 'Cannot delete academic year with existing semesters or grade encoding periods.');
        }

        DB::transaction(function () use ($academicYear) {
            $academicYear->delete();
        });

        return redirect()->route('admin.academic-years.index')
                        ->with('success', 'Academic year deleted successfully.');
    }

    /**
     * Set the specified academic year as current.
     */
    public function setCurrent(AcademicYear $academicYear)
    {
        if ($academicYear->status !== AcademicYear::STATUS_ACTIVE) {
            return redirect()->route('admin.academic-years.index')
                           ->with('error', 'Only active academic years can be set as current.');
        }

        DB::transaction(function () use ($academicYear) {
            // Unset current status from all other academic years
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
            
            // Set this academic year as current
            $academicYear->update(['is_current' => true]);
        });

        return redirect()->route('admin.academic-years.index')
                        ->with('success', 'Academic year set as current successfully.');
    }

    /**
     * Archive the specified academic year.
     */
    public function archive(AcademicYear $academicYear)
    {
        if ($academicYear->is_current) {
            return redirect()->route('admin.academic-years.index')
                           ->with('error', 'Cannot archive the current academic year.');
        }

        DB::transaction(function () use ($academicYear) {
            $academicYear->update([
                'status' => AcademicYear::STATUS_ARCHIVED,
                'archived_at' => Carbon::now(),
            ]);
        });

        return redirect()->route('admin.academic-years.index')
                        ->with('success', 'Academic year archived successfully.');
    }

    /**
     * Restore the specified academic year from archive.
     */
    public function restore(AcademicYear $academicYear)
    {
        if ($academicYear->status !== AcademicYear::STATUS_ARCHIVED) {
            return redirect()->route('admin.academic-years.index')
                           ->with('error', 'Only archived academic years can be restored.');
        }

        DB::transaction(function () use ($academicYear) {
            $academicYear->update([
                'status' => AcademicYear::STATUS_ACTIVE,
                'archived_at' => null,
            ]);
        });

        return redirect()->route('admin.academic-years.index')
                        ->with('success', 'Academic year restored successfully.');
    }

    /**
     * Toggle academic year status between active and inactive.
     */
    public function toggleStatus(AcademicYear $academicYear)
    {
        if ($academicYear->is_current && $academicYear->status === AcademicYear::STATUS_ACTIVE) {
            return redirect()->route('admin.academic-years.index')
                           ->with('error', 'Cannot deactivate the current academic year.');
        }

        $newStatus = $academicYear->status === AcademicYear::STATUS_ACTIVE 
                    ? AcademicYear::STATUS_INACTIVE 
                    : AcademicYear::STATUS_ACTIVE;

        $academicYear->update(['status' => $newStatus]);
        
        $statusText = $newStatus === AcademicYear::STATUS_ACTIVE ? 'activated' : 'deactivated';
        return redirect()->route('admin.academic-years.index')
                        ->with('success', "Academic year {$statusText} successfully.");
    }

    /**
     * Get academic year statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => AcademicYear::count(),
            'active' => AcademicYear::active()->count(),
            'current' => AcademicYear::current()->count(),
            'archived' => AcademicYear::archived()->count(),
        ];

        return response()->json($stats);
    }
}