<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ThemeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super_admin,admin']);
    }

    /**
     * Display theme management interface
     */
    public function index()
    {
        $themeSettings = [
            'primary_color' => Configuration::get('theme_primary_color', '#800020'),
            'secondary_color' => Configuration::get('theme_secondary_color', '#FFD700'),
            'accent_color' => Configuration::get('theme_accent_color', '#6C757D'),
            'success_color' => Configuration::get('theme_success_color', '#198754'),
            'warning_color' => Configuration::get('theme_warning_color', '#FFC107'),
            'danger_color' => Configuration::get('theme_danger_color', '#DC3545'),
            'info_color' => Configuration::get('theme_info_color', '#0DCAF0'),
            'dark_mode_enabled' => Configuration::get('theme_dark_mode_enabled', false),
            'custom_css' => Configuration::get('theme_custom_css', ''),
            'theme_mode' => Configuration::get('theme_mode', 'light')
        ];

        return view('admin.theme.index', compact('themeSettings'));
    }

    /**
     * Update theme settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'primary_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondary_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'accent_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'success_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'warning_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'danger_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'info_color' => 'required|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'dark_mode_enabled' => 'boolean',
            'custom_css' => 'nullable|string|max:10000',
            'theme_mode' => 'required|in:light,dark,auto'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update theme configurations
        $themeSettings = [
            'theme_primary_color' => $request->primary_color,
            'theme_secondary_color' => $request->secondary_color,
            'theme_accent_color' => $request->accent_color,
            'theme_success_color' => $request->success_color,
            'theme_warning_color' => $request->warning_color,
            'theme_danger_color' => $request->danger_color,
            'theme_info_color' => $request->info_color,
            'theme_dark_mode_enabled' => $request->boolean('dark_mode_enabled'),
            'theme_custom_css' => $request->custom_css ?? '',
            'theme_mode' => $request->theme_mode
        ];

        foreach ($themeSettings as $key => $value) {
            Configuration::set($key, $value);
        }

        // Clear theme cache
        Cache::forget('theme_settings');
        Cache::forget('compiled_theme_css');

        return redirect()->route('admin.theme.index')
            ->with('success', 'Theme settings updated successfully!');
    }

    /**
     * Reset theme to default
     */
    public function reset()
    {
        $defaultSettings = [
            'theme_primary_color' => '#800020',
            'theme_secondary_color' => '#FFD700',
            'theme_accent_color' => '#6C757D',
            'theme_success_color' => '#198754',
            'theme_warning_color' => '#FFC107',
            'theme_danger_color' => '#DC3545',
            'theme_info_color' => '#0DCAF0',
            'theme_dark_mode_enabled' => false,
            'theme_custom_css' => '',
            'theme_mode' => 'light'
        ];

        foreach ($defaultSettings as $key => $value) {
            Configuration::set($key, $value);
        }

        // Clear theme cache
        Cache::forget('theme_settings');
        Cache::forget('compiled_theme_css');

        return redirect()->route('admin.theme.index')
            ->with('success', 'Theme settings reset to default!');
    }

    /**
     * Generate dynamic CSS based on theme settings
     */
    public function generateCSS()
    {
        $cacheKey = 'compiled_theme_css';
        
        $css = Cache::remember($cacheKey, 3600, function () {
            $settings = Cache::remember('theme_settings', 3600, function () {
                return [
                    'primary_color' => Configuration::get('theme_primary_color', '#800020'),
                    'secondary_color' => Configuration::get('theme_secondary_color', '#FFD700'),
                    'accent_color' => Configuration::get('theme_accent_color', '#6C757D'),
                    'success_color' => Configuration::get('theme_success_color', '#198754'),
                    'warning_color' => Configuration::get('theme_warning_color', '#FFC107'),
                    'danger_color' => Configuration::get('theme_danger_color', '#DC3545'),
                    'info_color' => Configuration::get('theme_info_color', '#0DCAF0'),
                    'custom_css' => Configuration::get('theme_custom_css', ''),
                    'theme_mode' => Configuration::get('theme_mode', 'light')
                ];
            });

            // Generate CSS based on current settings
            $css = $this->generateThemeCSS($settings);
            
            return $css;
        });
        
        return response($css)->header('Content-Type', 'text/css');
    }

    /**
     * Generate theme CSS from settings
     */
    private function generateThemeCSS($settings)
    {
        $css = ":root {\n";
        $css .= "  --bs-primary: {$settings['primary_color']};\n";
        $css .= "  --bs-secondary: {$settings['secondary_color']};\n";
        $css .= "  --bs-success: {$settings['success_color']};\n";
        $css .= "  --bs-warning: {$settings['warning_color']};\n";
        $css .= "  --bs-danger: {$settings['danger_color']};\n";
        $css .= "  --bs-info: {$settings['info_color']};\n";
        $css .= "  --maroon: {$settings['primary_color']};\n";
        $css .= "  --gold: {$settings['secondary_color']};\n";
        $css .= "}\n\n";

        // Add dark mode variables if enabled
        if ($settings['theme_mode'] === 'dark') {
            $css .= "[data-bs-theme='dark'] {\n";
            $css .= "  --bs-body-bg: #212529;\n";
            $css .= "  --bs-body-color: #ffffff;\n";
            $css .= "}\n\n";
        }

        // Add custom CSS
        if (!empty($settings['custom_css'])) {
            $css .= "/* Custom CSS */\n";
            $css .= $settings['custom_css'] . "\n";
        }

        return $css;
    }
}