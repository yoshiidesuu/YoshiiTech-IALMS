<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BrandingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin,admin']);
    }

    /**
     * Display the branding management interface
     */
    public function index()
    {
        $settings = [
            'institution_name' => config('app.institution_name', 'Institution Name'),
            'system_title' => config('app.system_title', 'Management System'),
            'logo_path' => config('app.logo_path', null),
            'favicon_path' => config('app.favicon_path', null),
            'footer_text' => config('app.footer_text', '© 2024 Institution. All rights reserved.'),
            'contact_email' => config('app.contact_email', 'contact@institution.edu'),
            'contact_phone' => config('app.contact_phone', '+1 (555) 123-4567')
        ];

        return view('admin.branding.index', compact('settings'));
    }

    /**
     * Update branding settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'institution_name' => 'required|string|max:255',
            'system_title' => 'required|string|max:255',
            'footer_text' => 'nullable|string|max:500',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = [];

        // Handle text settings
        $textFields = ['institution_name', 'system_title', 'footer_text', 'contact_email', 'contact_phone'];
        foreach ($textFields as $field) {
            if ($request->filled($field)) {
                $settings[$field] = $request->input($field);
            }
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo_' . time() . '.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('public/branding', $logoName);
            $settings['logo_path'] = Storage::url($logoPath);

            // Delete old logo if exists
            $oldLogo = config('app.logo_path');
            if ($oldLogo && Storage::exists('public' . str_replace('/storage', '', $oldLogo))) {
                Storage::delete('public' . str_replace('/storage', '', $oldLogo));
            }
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $faviconName = 'favicon_' . time() . '.' . $favicon->getClientOriginalExtension();
            $faviconPath = $favicon->storeAs('public/branding', $faviconName);
            $settings['favicon_path'] = Storage::url($faviconPath);

            // Delete old favicon if exists
            $oldFavicon = config('app.favicon_path');
            if ($oldFavicon && Storage::exists('public' . str_replace('/storage', '', $oldFavicon))) {
                Storage::delete('public' . str_replace('/storage', '', $oldFavicon));
            }
        }

        // Update configuration file
        $this->updateConfigFile($settings);

        // Clear cache
        Cache::flush();

        return back()->with('success', 'Branding settings updated successfully!');
    }

    /**
     * Reset branding to defaults
     */
    public function reset()
    {
        $defaultSettings = [
            'institution_name' => 'Institution Name',
            'system_title' => 'Management System',
            'logo_path' => null,
            'favicon_path' => null,
            'footer_text' => '© 2024 Institution. All rights reserved.',
            'contact_email' => 'contact@institution.edu',
            'contact_phone' => '+1 (555) 123-4567'
        ];

        // Delete uploaded files
        $currentLogo = config('app.logo_path');
        $currentFavicon = config('app.favicon_path');
        
        if ($currentLogo && Storage::exists('public' . str_replace('/storage', '', $currentLogo))) {
            Storage::delete('public' . str_replace('/storage', '', $currentLogo));
        }
        
        if ($currentFavicon && Storage::exists('public' . str_replace('/storage', '', $currentFavicon))) {
            Storage::delete('public' . str_replace('/storage', '', $currentFavicon));
        }

        // Update configuration file
        $this->updateConfigFile($defaultSettings);

        // Clear cache
        Cache::flush();

        return back()->with('success', 'Branding settings reset to defaults!');
    }

    /**
     * Delete uploaded file
     */
    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:logo,favicon'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid file type']);
        }

        $type = $request->input('type');
        $configKey = $type . '_path';
        $currentFile = config('app.' . $configKey);

        if ($currentFile && Storage::exists('public' . str_replace('/storage', '', $currentFile))) {
            Storage::delete('public' . str_replace('/storage', '', $currentFile));
            
            // Update config
            $this->updateConfigFile([$configKey => null]);
            
            // Clear cache
            Cache::flush();
            
            return response()->json(['success' => true, 'message' => ucfirst($type) . ' deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'File not found']);
    }

    /**
     * Update configuration file with new settings
     */
    private function updateConfigFile(array $settings)
    {
        $configPath = config_path('app.php');
        $config = include $configPath;

        foreach ($settings as $key => $value) {
            $config[$key] = $value;
        }

        $configContent = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents($configPath, $configContent);
    }

    /**
     * Get current branding settings as JSON
     */
    public function getSettings()
    {
        $settings = [
            'institution_name' => config('app.institution_name', 'Institution Name'),
            'system_title' => config('app.system_title', 'Management System'),
            'logo_path' => config('app.logo_path', null),
            'favicon_path' => config('app.favicon_path', null),
            'footer_text' => config('app.footer_text', '© 2024 Institution. All rights reserved.'),
            'contact_email' => config('app.contact_email', 'contact@institution.edu'),
            'contact_phone' => config('app.contact_phone', '+1 (555) 123-4567')
        ];

        return response()->json($settings);
    }
}