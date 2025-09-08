<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <i class="bi bi-mortarboard-fill text-maroon" style="font-size: 2rem;"></i>
        </x-slot>

        <div class="text-center mb-4">
            <h4 class="text-maroon fw-bold">Reset Password</h4>
            <p class="text-muted mb-0">We'll send you a reset link</p>
        </div>

        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </div>

        @session('status')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ $value }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endsession

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Oops!</strong> There were some problems with your input.
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-1"></i>Email Address
                </label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="Enter your email address">
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-maroon btn-lg">
                    <i class="bi bi-envelope-arrow-up me-2"></i>Email Password Reset Link
                </button>
            </div>

            <hr class="my-4">
            <div class="text-center">
                <p class="text-muted mb-2">Remember your password?</p>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Login
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
