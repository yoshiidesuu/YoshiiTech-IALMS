<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to {{app_name}}!',
                'body' => '<h1>Welcome {{user_name}}!</h1><p>Thank you for joining {{app_name}}. We\'re excited to have you on board!</p><p>Get started by <a href="{{login_url}}">logging into your account</a>.</p><p>Best regards,<br>The {{app_name}} Team</p>',
                'description' => 'Welcome new users to the platform',
                'variables' => ['app_name', 'user_name', 'login_url'],
                'type' => 'welcome',
                'is_active' => true
            ],
            [
                'name' => 'Notification Email',
                'subject' => 'Notification from {{app_name}}',
                'body' => '<h2>Hello {{user_name}}</h2><p>You have a new notification:</p><div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 15px 0;">{{message}}</div><p>{{#action_url}}<a href="{{action_url}}" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Take Action</a>{{/action_url}}</p>',
                'description' => 'General notification template',
                'variables' => ['app_name', 'user_name', 'message', 'action_url'],
                'type' => 'notification',
                'is_active' => true
            ],
            [
                'name' => 'Test Email',
                'subject' => 'Test Email from {{app_name}}',
                'body' => '<h2>Test Email</h2><p>This is a test email sent from <strong>{{app_name}}</strong> on {{timestamp}}.</p><p>If you received this email, your SMTP configuration is working correctly!</p><hr><p><small>Sent at: {{timestamp}}</small></p>',
                'description' => 'Simple test email template for SMTP testing',
                'variables' => ['app_name', 'timestamp'],
                'type' => 'test',
                'is_active' => true
            ],
            [
                'name' => 'Password Reset',
                'subject' => 'Reset Your Password - {{app_name}}',
                'body' => '<h2>Password Reset Request</h2><p>Hello {{user_name}},</p><p>You requested a password reset for your {{app_name}} account. Click the button below to reset your password:</p><p><a href="{{reset_url}}" style="background: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a></p><p>This link will expire in {{expiry_time}} minutes.</p><p>If you didn\'t request this reset, please ignore this email.</p>',
                'description' => 'Password reset email template',
                'variables' => ['app_name', 'user_name', 'reset_url', 'expiry_time'],
                'type' => 'security',
                'is_active' => true
            ],
            [
                'name' => 'System Maintenance',
                'subject' => 'Scheduled Maintenance - {{app_name}}',
                'body' => '<h2>Scheduled System Maintenance</h2><p>Dear {{user_name}},</p><p>We will be performing scheduled maintenance on {{app_name}} on:</p><div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0;"><strong>Date:</strong> {{maintenance_date}}<br><strong>Time:</strong> {{maintenance_time}}<br><strong>Duration:</strong> {{maintenance_duration}}</div><p>During this time, the system may be temporarily unavailable. We apologize for any inconvenience.</p>',
                'description' => 'System maintenance notification template',
                'variables' => ['app_name', 'user_name', 'maintenance_date', 'maintenance_time', 'maintenance_duration'],
                'type' => 'system',
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}
