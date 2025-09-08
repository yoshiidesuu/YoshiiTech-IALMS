<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\Configuration;

class MaintenanceModeController extends Controller
{
    /**
     * Display maintenance mode management page
     */
    public function index()
    {
        $isMaintenanceMode = app()->isDownForMaintenance();
        $maintenanceConfig = $this->getMaintenanceConfig();
        
        return view('admin.maintenance.index', compact('isMaintenanceMode', 'maintenanceConfig'));
    }

    /**
     * Enable maintenance mode
     */
    public function enable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:1000',
            'allowed_ips' => 'nullable|string',
            'retry_after' => 'nullable|integer|min:60|max:86400',
            'redirect_url' => 'nullable|url',
            'show_progress' => 'boolean',
            'estimated_time' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Save maintenance configuration
            $config = [
                'message' => $request->message ?? 'We are currently performing scheduled maintenance. Please check back soon.',
                'allowed_ips' => $this->parseAllowedIps($request->allowed_ips),
                'retry_after' => $request->retry_after ?? 3600,
                'redirect_url' => $request->redirect_url,
                'show_progress' => $request->boolean('show_progress'),
                'estimated_time' => $request->estimated_time,
                'enabled_at' => now()->toISOString()
            ];

            Configuration::updateOrCreate(
                ['key' => 'maintenance_mode_config'],
                ['value' => json_encode($config)]
            );

            // Build artisan command
            $command = ['down'];
            
            if ($request->message) {
                $command[] = '--message=' . escapeshellarg($request->message);
            }
            
            if ($request->retry_after) {
                $command[] = '--retry=' . $request->retry_after;
            }
            
            if ($request->redirect_url) {
                $command[] = '--redirect=' . escapeshellarg($request->redirect_url);
            }
            
            if ($request->allowed_ips) {
                $allowedIps = $this->parseAllowedIps($request->allowed_ips);
                foreach ($allowedIps as $ip) {
                    $command[] = '--allow=' . escapeshellarg($ip);
                }
            }

            // Execute maintenance mode
            Artisan::call('down', array_slice($command, 1));

            return response()->json([
                'success' => true,
                'message' => 'Maintenance mode enabled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enable maintenance mode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disable maintenance mode
     */
    public function disable()
    {
        try {
            Artisan::call('up');
            
            // Update configuration
            $config = $this->getMaintenanceConfig();
            $config['disabled_at'] = now()->toISOString();
            
            Configuration::updateOrCreate(
                ['key' => 'maintenance_mode_config'],
                ['value' => json_encode($config)]
            );

            return response()->json([
                'success' => true,
                'message' => 'Maintenance mode disabled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to disable maintenance mode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get maintenance mode status
     */
    public function status()
    {
        $isMaintenanceMode = app()->isDownForMaintenance();
        $config = $this->getMaintenanceConfig();
        
        return response()->json([
            'success' => true,
            'is_maintenance_mode' => $isMaintenanceMode,
            'config' => $config
        ]);
    }

    /**
     * Update maintenance configuration without changing mode
     */
    public function updateConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:1000',
            'allowed_ips' => 'nullable|string',
            'retry_after' => 'nullable|integer|min:60|max:86400',
            'redirect_url' => 'nullable|url',
            'show_progress' => 'boolean',
            'estimated_time' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $existingConfig = $this->getMaintenanceConfig();
            
            $config = array_merge($existingConfig, [
                'message' => $request->message ?? $existingConfig['message'],
                'allowed_ips' => $this->parseAllowedIps($request->allowed_ips),
                'retry_after' => $request->retry_after ?? $existingConfig['retry_after'],
                'redirect_url' => $request->redirect_url,
                'show_progress' => $request->boolean('show_progress'),
                'estimated_time' => $request->estimated_time,
                'updated_at' => now()->toISOString()
            ]);

            Configuration::updateOrCreate(
                ['key' => 'maintenance_mode_config'],
                ['value' => json_encode($config)]
            );

            return response()->json([
                'success' => true,
                'message' => 'Maintenance configuration updated successfully',
                'config' => $config
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current maintenance configuration
     */
    private function getMaintenanceConfig()
    {
        $config = Configuration::where('key', 'maintenance_mode_config')->first();
        
        if (!$config) {
            return [
                'message' => 'We are currently performing scheduled maintenance. Please check back soon.',
                'allowed_ips' => [],
                'retry_after' => 3600,
                'redirect_url' => null,
                'show_progress' => false,
                'estimated_time' => null
            ];
        }
        
        return json_decode($config->value, true);
    }

    /**
     * Parse allowed IPs from string
     */
    private function parseAllowedIps($ipsString)
    {
        if (empty($ipsString)) {
            return [];
        }
        
        $ips = array_map('trim', explode(',', $ipsString));
        return array_filter($ips, function($ip) {
            return filter_var($ip, FILTER_VALIDATE_IP) !== false;
        });
    }
}