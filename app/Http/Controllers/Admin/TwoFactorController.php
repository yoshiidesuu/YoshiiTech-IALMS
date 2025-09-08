<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Models\User;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->middleware(['auth', 'can:users.manage']);
        $this->google2fa = new Google2FA();
    }

    /**
     * Display two-factor authentication management page
     */
    public function index()
    {
        $user = Auth::user();
        $isEnabled = !empty($user->two_factor_secret);
        
        return view('admin.two-factor.index', compact('isEnabled'));
    }

    /**
     * Enable two-factor authentication
     */
    public function enable(Request $request)
    {
        $user = Auth::user();
        
        // Generate secret key
        $secret = $this->google2fa->generateSecretKey();
        
        // Store secret temporarily (not confirmed yet)
        $user->two_factor_secret = encrypt($secret);
        $user->two_factor_confirmed_at = null;
        $user->save();
        
        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        
        $qrCodeImage = $this->generateQrCode($qrCodeUrl);
        
        return response()->json([
            'success' => true,
            'secret' => $secret,
            'qr_code' => $qrCodeImage,
            'backup_codes' => $this->createBackupCodes($user)
        ]);
    }

    /**
     * Confirm two-factor authentication setup
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password provided.'
            ], 422);
        }

        // Verify the code
        $secret = decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid authentication code. Please try again.'
            ], 422);
        }

        // Confirm 2FA setup
        $user->two_factor_confirmed_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication has been enabled successfully!'
        ]);
    }

    /**
     * Disable two-factor authentication
     */
    public function disable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'code' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password provided.'
            ], 422);
        }

        // Verify the code
        $secret = decrypt($user->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid authentication code. Please try again.'
            ], 422);
        }

        // Disable 2FA
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication has been disabled successfully.'
        ]);
    }

    /**
     * Generate new backup codes
     */
    public function generateBackupCodes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        
        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password provided.'
            ], 422);
        }

        $backupCodes = $this->createBackupCodes($user);

        return response()->json([
            'success' => true,
            'backup_codes' => $backupCodes,
            'message' => 'New backup codes generated successfully. Please save them securely.'
        ]);
    }

    /**
     * Get current 2FA status
     */
    public function getStatus()
    {
        $user = Auth::user();
        $isEnabled = !empty($user->two_factor_secret) && !empty($user->two_factor_confirmed_at);
        
        return response()->json([
            'success' => true,
            'enabled' => $isEnabled,
            'confirmed' => !empty($user->two_factor_confirmed_at),
            'has_backup_codes' => !empty($user->two_factor_recovery_codes)
        ]);
    }

    /**
     * Generate QR code image
     */
    private function generateQrCode($url)
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new ImagickImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($url);
            
            return 'data:image/png;base64,' . base64_encode($qrCode);
        } catch (\Exception $e) {
            // Fallback to simple URL if QR generation fails
            return $url;
        }
    }

    /**
     * Generate backup recovery codes
     */
    private function createBackupCodes(User $user)
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        }
        
        // Store encrypted backup codes
        $user->two_factor_recovery_codes = encrypt(json_encode($codes));
        $user->save();
        
        return $codes;
    }
}