<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SmtpController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:configurations.manage']);
    }

    /**
     * Display SMTP configuration page
     */
    public function index()
    {
        $smtpConfig = [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'default_mailer' => config('mail.default')
        ];

        return view('admin.smtp.index', compact('smtpConfig'));
    }

    /**
     * Update SMTP configuration
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|in:tls,ssl',
            'from_address' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'default_mailer' => 'required|in:smtp,log,array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update environment variables
            $this->updateEnvFile([
                'MAIL_MAILER' => $request->default_mailer,
                'MAIL_HOST' => $request->host,
                'MAIL_PORT' => $request->port,
                'MAIL_USERNAME' => $request->username ?? '',
                'MAIL_PASSWORD' => $request->password ?? '',
                'MAIL_ENCRYPTION' => $request->encryption ?? 'null',
                'MAIL_FROM_ADDRESS' => $request->from_address,
                'MAIL_FROM_NAME' => '"' . $request->from_name . '"'
            ]);

            // Clear config cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');

            return response()->json([
                'success' => true,
                'message' => 'SMTP configuration updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update SMTP configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test SMTP connection
     */
    public function testConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email',
            'test_subject' => 'nullable|string|max:255',
            'test_message' => 'nullable|string|max:1000',
            'use_template' => 'boolean',
            'template_name' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $testEmail = $request->test_email;
            $subject = $request->test_subject ?: 'SMTP Configuration Test';
            $message = $request->test_message ?: 'This is a test email to verify your SMTP configuration.';

            // Send test email with or without template
            if ($request->use_template && $request->template_name) {
                $templateData = $this->getTemplateData($request->template_name);
                $testData = [
                    'title' => 'SMTP Configuration Test',
                    'content' => $message,
                    'action_text' => 'Visit Dashboard',
                    'action_url' => url('/admin/dashboard'),
                    'user_name' => 'Administrator'
                ];
                
                $mailable = new \App\Http\Controllers\Admin\TestTemplateMail($templateData, $testData, 'notification');
                $mailable->subject($subject);
                
                Mail::to($testEmail)->send($mailable);
            } else {
                // Send using existing SMTP test template
                Mail::to($testEmail)->send(new SmtpTestMail($subject, $message));
            }

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $testEmail
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available email templates
     */
    public function getEmailTemplates()
    {
        try {
            $templates = \App\Models\EmailTemplate::active()->get();
            $formattedTemplates = [];
            
            foreach ($templates as $template) {
                $formattedTemplates[$template->id] = [
                    'id' => $template->id,
                    'name' => $template->name,
                    'description' => $template->description,
                    'subject' => $template->subject,
                    'body' => $template->body,
                    'variables' => $template->variables ?? [],
                    'type' => $template->type
                ];
            }

            return response()->json([
                'success' => true,
                'templates' => $formattedTemplates
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load templates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get template data for email rendering
     */
    private function getTemplateData($templateId)
    {
        try {
            $template = \App\Models\EmailTemplate::find($templateId);
            
            if (!$template) {
                return $this->getDefaultTemplateData();
            }
            
            return [
                'subject' => $template->subject,
                'body' => $template->body,
                'variables' => $template->variables ?? []
            ];
        } catch (\Exception $e) {
            return $this->getDefaultTemplateData();
        }
    }

    /**
     * Get default template data
     */
    private function getDefaultTemplateData()
    {
        return [
            'subject' => 'Email from ' . config('app.name'),
            'header_text' => 'SMTP Test Email',
            'footer_text' => 'This is a test email from your SMTP configuration.',
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'logo_url' => config('app.logo_path') ? asset('storage/' . config('app.logo_path')) : null,
            'company_name' => config('app.institution_name', config('app.name')),
            'company_address' => null,
            'social_links' => []
        ];
    }

    /**
     * Get current SMTP settings
     */
    public function getSettings()
    {
        try {
            $settings = [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'default_mailer' => config('mail.default')
            ];

            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve SMTP settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update environment file
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }
}

/**
 * Test Email Mailable Class
 */
class TestEmail extends Mailable
{
    use SerializesModels;

    public function build()
    {
        return $this->subject('SMTP Configuration Test Email')
                    ->view('emails.smtp-test');
    }
}