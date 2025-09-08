<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class ConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('admin-access')) {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Configuration::query();
        
        // Filter by group if specified
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('group', 'like', "%{$search}%");
            });
        }
        
        $configurations = $query->orderBy('group')
                               ->orderBy('sort_order')
                               ->orderBy('key')
                               ->paginate(20);
        
        $groups = Configuration::distinct('group')
                              ->whereNotNull('group')
                              ->pluck('group')
                              ->sort();
        
        return view('admin.configurations.index', compact('configurations', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Configuration::distinct('group')
                              ->whereNotNull('group')
                              ->pluck('group')
                              ->sort();
        
        return view('admin.configurations.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:configurations,key',
            'value' => 'nullable',
            'type' => 'required|in:string,integer,float,boolean,json,array',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'is_encrypted' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        $data = $request->all();
        
        // Process value based on type
        $data['value'] = $this->processValue($request->value, $request->type);
        
        Configuration::create($data);
        
        return redirect()->route('admin.configurations.index')
                        ->with('success', 'Configuration created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Configuration $configuration)
    {
        return view('admin.configurations.show', compact('configuration'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Configuration $configuration)
    {
        $groups = Configuration::distinct('group')
                              ->whereNotNull('group')
                              ->pluck('group')
                              ->sort();
        
        return view('admin.configurations.edit', compact('configuration', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Configuration $configuration)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:configurations,key,' . $configuration->id,
            'value' => 'nullable',
            'type' => 'required|in:string,integer,float,boolean,json,array',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'is_encrypted' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        $data = $request->all();
        
        // Process value based on type
        $data['value'] = $this->processValue($request->value, $request->type);
        
        $configuration->update($data);
        
        return redirect()->route('admin.configurations.index')
                        ->with('success', 'Configuration updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Configuration $configuration)
    {
        $configuration->delete();
        
        return redirect()->route('admin.configurations.index')
                        ->with('success', 'Configuration deleted successfully.');
    }
    
    /**
     * Clear configuration cache
     */
    public function clearCache()
    {
        Configuration::clearCache();
        
        return redirect()->route('admin.configurations.index')
                        ->with('success', 'Configuration cache cleared successfully.');
    }
    
    /**
     * Export configurations as JSON
     */
    public function export()
    {
        $configurations = Configuration::all();
        
        $filename = 'configurations_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->json($configurations)
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Process value based on type
     */
    private function processValue($value, $type)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json', 'array' => is_string($value) ? json_decode($value, true) : $value,
            default => $value
        };
    }
}
