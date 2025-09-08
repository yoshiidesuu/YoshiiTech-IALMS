<x-guest-layout>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="text-center mb-4">
                <div class="logo-container mx-auto">
                    <i class="bi bi-mortarboard-fill text-maroon" style="font-size: 2rem;"></i>
                </div>
                <h4 class="text-maroon fw-bold">Welcome Back</h4>
                <p class="text-muted mb-0">Sign in to your account</p>
            </div>
            
            <div class="card auth-card">
                <div class="card-body p-4">

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

        @session('status')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ $value }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-1"></i>Email or Username
                </label>
                <input id="email" 
                       type="text" 
                       name="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="Enter your email or username">
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">
                    <i class="bi bi-lock me-1"></i>Password
                </label>
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required 
                       autocomplete="current-password"
                       placeholder="Enter your password">
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                    <label class="form-check-label" for="remember_me">
                        Remember me
                    </label>
                </div>
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-maroon btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </div>

            <div class="text-center">
                @if (Route::has('password.request'))
                    <a class="text-maroon text-decoration-none" href="{{ route('password.request') }}">
                        <i class="bi bi-question-circle me-1"></i>Forgot your password?
                    </a>
                @endif
            </div>

            @if (Route::has('register'))
                <hr class="my-4">
                <div class="text-center">
                    <p class="text-muted mb-2">Don't have an account?</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </a>
                </div>
            @endif
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <p class="text-white-50 mb-0">
                    <small>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</small>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
