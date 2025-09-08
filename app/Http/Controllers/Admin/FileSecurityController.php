<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileSecurityController extends Controller
{
    /**
     * Display the file security management page
     */
    public function index()
    {
        try {
            $config = $this->getFileSecurityConfig();
            return view('admin.file-security.index', compact('config'));
        } catch (\Exception $e) {
            Log::error('Error loading file security page: ' . $e->getMessage());
            return back()->with('error', 'Failed to load file security settings.');
        }
    }

    /**
     * Update file security configuration
     */
    public function updateConfig(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'max_file_size' => 'required|integer|min:1|max:102400', // Max 100MB
                'allowed_extensions' => 'required|string',
                'blocked_extensions' => 'nullable|string',
                'scan_uploads' => 'boolean',
                'quarantine_suspicious' => 'boolean',
                'auto_delete_quarantine' => 'boolean',
                'quarantine_days' => 'nullable|integer|min:1|max:365',
                'max_uploads_per_user' => 'nullable|integer|min:1',
                'max_storage_per_user' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $config = [
                'max_file_size' => $request->max_file_size,
                'allowed_extensions' => array_map('trim', explode(',', $request->allowed_extensions)),
                'blocked_extensions' => $request->blocked_extensions ? array_map('trim', explode(',', $request->blocked_extensions)) : [],
                'scan_uploads' => $request->boolean('scan_uploads'),
                'quarantine_suspicious' => $request->boolean('quarantine_suspicious'),
                'auto_delete_quarantine' => $request->boolean('auto_delete_quarantine'),
                'quarantine_days' => $request->quarantine_days ?? 30,
                'max_uploads_per_user' => $request->max_uploads_per_user,
                'max_storage_per_user' => $request->max_storage_per_user,
                'updated_at' => now()->toISOString()
            ];

            Configuration::updateOrCreate(
                ['key' => 'file_security_config'],
                ['value' => json_encode($config)]
            );

            return response()->json([
                'success' => true,
                'message' => 'File security configuration updated successfully',
                'config' => $config
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating file security config: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update file security configuration'
            ], 500);
        }
    }

    /**
     * Get current file security status
     */
    public function getStatus()
    {
        try {
            $config = $this->getFileSecurityConfig();
            $stats = $this->getSecurityStats();
            
            return response()->json([
                'success' => true,
                'config' => $config,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting file security status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get file security status'
            ], 500);
        }
    }

    /**
     * Test file upload validation
     */
    public function testValidation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'test_file' => 'required|file'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file provided for testing'
                ], 422);
            }

            $file = $request->file('test_file');
            $config = $this->getFileSecurityConfig();
            $result = $this->validateFile($file, $config);

            return response()->json([
                'success' => true,
                'validation_result' => $result,
                'file_info' => [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'extension' => $file->getClientOriginalExtension(),
                    'mime_type' => $file->getMimeType()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error testing file validation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to test file validation'
            ], 500);
        }
    }

    /**
     * Get quarantined files
     */
    public function getQuarantinedFiles()
    {
        try {
            $quarantinePath = storage_path('app/quarantine');
            $files = [];

            if (is_dir($quarantinePath)) {
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($quarantinePath)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $files[] = [
                            'name' => $file->getFilename(),
                            'path' => $file->getPathname(),
                            'size' => $file->getSize(),
                            'quarantined_at' => date('Y-m-d H:i:s', $file->getMTime()),
                            'days_in_quarantine' => floor((time() - $file->getMTime()) / 86400)
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'files' => $files
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting quarantined files: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get quarantined files'
            ], 500);
        }
    }

    /**
     * Delete quarantined file
     */
    public function deleteQuarantinedFile(Request $request)
    {
        try {
            $filePath = $request->input('file_path');
            
            if (!$filePath || !str_contains($filePath, 'quarantine')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file path'
                ], 400);
            }

            if (file_exists($filePath)) {
                unlink($filePath);
                return response()->json([
                    'success' => true,
                    'message' => 'Quarantined file deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error deleting quarantined file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete quarantined file'
            ], 500);
        }
    }

    /**
     * Get file security configuration
     */
    private function getFileSecurityConfig()
    {
        $config = Configuration::where('key', 'file_security_config')->first();
        
        if ($config) {
            return json_decode($config->value, true);
        }

        // Default configuration
        return [
            'max_file_size' => 10240, // 10MB in KB
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'],
            'blocked_extensions' => ['exe', 'bat', 'cmd', 'scr', 'pif', 'com'],
            'scan_uploads' => true,
            'quarantine_suspicious' => true,
            'auto_delete_quarantine' => true,
            'quarantine_days' => 30,
            'max_uploads_per_user' => 100,
            'max_storage_per_user' => 1048576, // 1GB in KB
        ];
    }

    /**
     * Get security statistics
     */
    private function getSecurityStats()
    {
        $quarantinePath = storage_path('app/quarantine');
        $quarantinedCount = 0;
        $totalQuarantineSize = 0;

        if (is_dir($quarantinePath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($quarantinePath)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $quarantinedCount++;
                    $totalQuarantineSize += $file->getSize();
                }
            }
        }

        return [
            'quarantined_files' => $quarantinedCount,
            'quarantine_size' => $totalQuarantineSize,
            'last_scan' => now()->toISOString(),
            'threats_blocked_today' => rand(0, 5), // Placeholder - would be from actual logs
            'total_uploads_today' => rand(10, 100) // Placeholder - would be from actual logs
        ];
    }

    /**
     * Validate file against security rules
     */
    private function validateFile($file, $config)
    {
        $result = [
            'valid' => true,
            'errors' => [],
            'warnings' => []
        ];

        // Check file size
        $fileSizeKB = $file->getSize() / 1024;
        if ($fileSizeKB > $config['max_file_size']) {
            $result['valid'] = false;
            $result['errors'][] = "File size ({$fileSizeKB}KB) exceeds maximum allowed ({$config['max_file_size']}KB)";
        }

        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $config['blocked_extensions'])) {
            $result['valid'] = false;
            $result['errors'][] = "File extension '{$extension}' is blocked";
        }

        if (!in_array($extension, $config['allowed_extensions'])) {
            $result['warnings'][] = "File extension '{$extension}' is not in the allowed list";
        }

        // Basic mime type check
        $mimeType = $file->getMimeType();
        $suspiciousMimes = ['application/x-executable', 'application/x-msdownload'];
        if (in_array($mimeType, $suspiciousMimes)) {
            $result['valid'] = false;
            $result['errors'][] = "Suspicious file type detected: {$mimeType}";
        }

        return $result;
    }
}