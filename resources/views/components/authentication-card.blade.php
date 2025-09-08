<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="text-center mb-4">
            <div class="logo-container mx-auto">
                {{ $logo }}
            </div>
        </div>
        
        <div class="card auth-card">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
        
        <div class="text-center mt-3">
            <p class="text-white-50 mb-0">
                <small>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</small>
            </p>
        </div>
    </div>
</div>
