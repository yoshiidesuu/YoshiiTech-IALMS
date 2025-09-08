<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Email Template' }}</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, {{ $primary_color ?? '#007bff' }}, {{ $secondary_color ?? '#6c757d' }});
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        
        .email-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 20px;
        }
        
        .email-header h1 {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .email-header p {
            font-size: 16px;
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .email-body {
            padding: 40px;
        }
        
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        
        .email-title {
            font-size: 24px;
            font-weight: 600;
            color: {{ $primary_color ?? '#007bff' }};
            margin-bottom: 20px;
            line-height: 1.3;
        }
        
        .email-content {
            font-size: 16px;
            color: #555555;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        
        .action-button {
            display: inline-block;
            background-color: {{ $primary_color ?? '#007bff' }};
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }
        
        .action-button:hover {
            background-color: {{ $secondary_color ?? '#6c757d' }};
            color: white;
            text-decoration: none;
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 30px 0;
        }
        
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .company-info {
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: 600;
            color: #333333;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 14px;
            color: #666666;
            line-height: 1.5;
        }
        
        .social-links {
            margin: 20px 0;
        }
        
        .social-link {
            display: inline-block;
            margin: 0 10px;
            text-decoration: none;
            color: {{ $primary_color ?? '#007bff' }};
            font-size: 20px;
            transition: color 0.3s ease;
        }
        
        .social-link:hover {
            color: {{ $secondary_color ?? '#6c757d' }};
        }
        
        .footer-text {
            font-size: 14px;
            color: #666666;
            margin-top: 20px;
            line-height: 1.5;
        }
        
        .unsubscribe {
            font-size: 12px;
            color: #999999;
            margin-top: 15px;
        }
        
        .unsubscribe a {
            color: #999999;
            text-decoration: underline;
        }
        
        /* Responsive design */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .email-header,
            .email-body,
            .email-footer {
                padding: 20px;
            }
            
            .email-header h1 {
                font-size: 24px;
            }
            
            .email-title {
                font-size: 20px;
            }
            
            .action-button {
                display: block;
                width: 100%;
                padding: 15px;
            }
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background-color: #1a1a1a;
            }
            
            .email-body {
                background-color: #1a1a1a;
            }
            
            .greeting,
            .email-content {
                color: #e9ecef;
            }
            
            .email-footer {
                background-color: #2d2d2d;
                border-top-color: #404040;
            }
            
            .company-name {
                color: #ffffff;
            }
            
            .company-address,
            .footer-text {
                color: #cccccc;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            @if(!empty($logo_url))
                <img src="{{ $logo_url }}" alt="{{ $company_name ?? config('app.name') }}" class="email-logo">
            @endif
            
            <h1>{{ $header_text ?? 'Welcome!' }}</h1>
            @if(!empty($company_name))
                <p>{{ $company_name }}</p>
            @endif
        </div>
        
        <!-- Body -->
        <div class="email-body">
            @if(!empty($user_name))
                <div class="greeting">
                    Hello {{ $user_name }},
                </div>
            @endif
            
            @if(!empty($title))
                <h2 class="email-title">{{ $title }}</h2>
            @endif
            
            @if(!empty($content))
                <div class="email-content">
                    {!! nl2br(e($content)) !!}
                </div>
            @endif
            
            @if(!empty($action_text) && !empty($action_url))
                <div class="button-container">
                    <a href="{{ $action_url }}" class="action-button">{{ $action_text }}</a>
                </div>
            @endif
            
            @if(!empty($additional_content))
                <div class="divider"></div>
                <div class="email-content">
                    {!! nl2br(e($additional_content)) !!}
                </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            @if(!empty($company_name) || !empty($company_address))
                <div class="company-info">
                    @if(!empty($company_name))
                        <div class="company-name">{{ $company_name }}</div>
                    @endif
                    @if(!empty($company_address))
                        <div class="company-address">{!! nl2br(e($company_address)) !!}</div>
                    @endif
                </div>
            @endif
            
            @if(!empty($social_links) && is_array($social_links))
                <div class="social-links">
                    @foreach($social_links as $social)
                        @if(!empty($social['platform']) && !empty($social['url']))
                            <a href="{{ $social['url'] }}" class="social-link" target="_blank" rel="noopener">
                                @switch($social['platform'])
                                    @case('facebook')
                                        📘
                                        @break
                                    @case('twitter')
                                        🐦
                                        @break
                                    @case('linkedin')
                                        💼
                                        @break
                                    @case('instagram')
                                        📷
                                        @break
                                    @case('youtube')
                                        📺
                                        @break
                                    @default
                                        🔗
                                @endswitch
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
            
            @if(!empty($footer_text))
                <div class="footer-text">
                    {!! nl2br(e($footer_text)) !!}
                </div>
            @endif
            
            <div class="unsubscribe">
                <p>
                    This email was sent to you by {{ $company_name ?? config('app.name') }}.
                    <br>
                    If you no longer wish to receive these emails, you can 
                    <a href="#">unsubscribe here</a>.
                </p>
            </div>
        </div>
    </div>
</body>
</html>