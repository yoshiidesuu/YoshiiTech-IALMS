<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailTemplateController extends Controller
{
    use SerializesModels;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:configurations.manage');
    }

    /**
     * Display email template management interface
     */
    public function index()
    {
        $templates = EmailTemplate::active()->orderBy('name')->get();
        $currentTemplate = Configuration::where('key', 'email_template_active')->value('value') ?? 'default';
        
        return view('admin.email-templates.index', compact('templates', 'currentTemplate'));
    }

    /**
     * Get all available email templates
     */
    public function getTemplates()
    {
        $templates = EmailTemplate::active()->get();
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
        
        $currentTemplate = Configuration::where('key', 'email_template_active')->value('value') ?? 'default';
        
        return response()->json([
            'success' => true,
            'templates' => $formattedTemplates,
            'current_template' => $currentTemplate
        ]);
    }

    /**
     * Store new email template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:email_templates,name',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|string|max:50',
            'variables' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $template = EmailTemplate::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Template created successfully',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update email template
     */
    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:email_templates,name,' . $id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'required|string|max:50',
            'variables' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $template->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'template' => $template
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete email template
     */
    public function delete($id)
    {
        try {
            $template = EmailTemplate::findOrFail($id);
            $template->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview email template
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string',
            'preview_type' => 'required|string|in:welcome,password_reset,notification'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid preview parameters'
            ], 422);
        }

        try {
            $templateData = $this->getTemplateData($request->template_name);
            $previewData = $this->getPreviewData($request->preview_type);
            
            $html = view('emails.template-preview', array_merge($templateData, $previewData))->render();
            
            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test email with template
     */
    public function sendTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'template_name' => 'required|string',
            'test_email' => 'required|email',
            'test_type' => 'required|string|in:welcome,password_reset,notification'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $templateData = $this->getTemplateData($request->template_name);
            $testData = $this->getPreviewData($request->test_type);
            
            $mailable = new TestTemplateMail($templateData, $testData, $request->test_type);
            
            Mail::to($request->test_email)->send($mailable);
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->test_email
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get email templates list
     */
    private function getEmailTemplates()
    {
        $templates = [];
        
        // Default template
        $templates['default'] = [
            'name' => 'Default',
            'description' => 'Clean and professional default template',
            'is_default' => true,
            'data' => $this->getDefaultTemplateData()
        ];
        
        // Custom templates from database
        $customTemplates = Configuration::where('key', 'LIKE', 'email_template_%')
            ->where('key', '!=', 'email_template_active')
            ->get();
            
        foreach ($customTemplates as $template) {
            $templateName = str_replace('email_template_', '', $template->key);
            $templateData = json_decode($template->value, true);
            
            $templates[$templateName] = [
                'name' => ucfirst($templateName),
                'description' => 'Custom template',
                'is_default' => false,
                'data' => $templateData
            ];
        }
        
        return $templates;
    }

    /**
     * Get template data
     */
    private function getTemplateData($templateName)
    {
        if ($templateName === 'default') {
            return $this->getDefaultTemplateData();
        }
        
        $template = Configuration::where('key', 'email_template_' . $templateName)->first();
        
        if (!$template) {
            return $this->getDefaultTemplateData();
        }
        
        return json_decode($template->value, true);
    }

    /**
     * Get default template data
     */
    private function getDefaultTemplateData()
    {
        return [
            'subject' => 'Email from ' . config('app.name'),
            'header_text' => 'Welcome to ' . config('app.name'),
            'footer_text' => 'Thank you for using our service.',
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'logo_url' => config('app.logo_path') ? asset('storage/' . config('app.logo_path')) : null,
            'company_name' => config('app.institution_name', config('app.name')),
            'company_address' => null,
            'social_links' => []
        ];
    }

    /**
     * Get preview data for different email types
     */
    private function getPreviewData($type)
    {
        switch ($type) {
            case 'welcome':
                return [
                    'title' => 'Welcome to Our Platform!',
                    'content' => 'Thank you for joining us. We\'re excited to have you on board and look forward to providing you with an excellent experience.',
                    'action_text' => 'Get Started',
                    'action_url' => url('/dashboard'),
                    'user_name' => 'John Doe'
                ];
                
            case 'password_reset':
                return [
                    'title' => 'Password Reset Request',
                    'content' => 'You are receiving this email because we received a password reset request for your account. Click the button below to reset your password.',
                    'action_text' => 'Reset Password',
                    'action_url' => url('/password/reset/token'),
                    'user_name' => 'John Doe'
                ];
                
            case 'notification':
                return [
                    'title' => 'Important Notification',
                    'content' => 'This is a sample notification email to demonstrate how your template will look with different types of content.',
                    'action_text' => 'View Details',
                    'action_url' => url('/notifications'),
                    'user_name' => 'John Doe'
                ];
                
            default:
                return [
                    'title' => 'Sample Email',
                    'content' => 'This is a preview of your email template.',
                    'action_text' => 'Learn More',
                    'action_url' => url('/'),
                    'user_name' => 'Sample User'
                ];
        }
    }
}

/**
 * Test Template Mailable Class
 */
class TestTemplateMail extends Mailable
{
    use SerializesModels;

    public $templateData;
    public $testData;
    public $testType;

    public function __construct($templateData, $testData, $testType)
    {
        $this->templateData = $templateData;
        $this->testData = $testData;
        $this->testType = $testType;
    }

    public function build()
    {
        return $this->subject($this->templateData['subject'] ?? 'Test Email')
                    ->view('emails.template-preview')
                    ->with(array_merge($this->templateData, $this->testData));
    }
}