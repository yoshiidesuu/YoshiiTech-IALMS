<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <i class="bi bi-mortarboard-fill text-maroon" style="font-size: 2rem;"></i>
        </x-slot>

        <div class="text-center mb-4">
            <h4 class="text-maroon fw-bold">Create Account</h4>
            <p class="text-muted mb-0">Join our student information system</p>
        </div>

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

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">
                    <i class="bi bi-person me-1"></i>Full Name
                </label>
                <input id="name" 
                       type="text" 
                       name="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name') }}" 
                       required 
                       autofocus 
                       autocomplete="name"
                       placeholder="Enter your full name">
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

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
                       autocomplete="username"
                       placeholder="Enter your email address">
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
                       autocomplete="new-password"
                       placeholder="Create a strong password">
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">
                    <i class="bi bi-lock-fill me-1"></i>Confirm Password
                </label>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       class="form-control" 
                       required 
                       autocomplete="new-password"
                       placeholder="Confirm your password">
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input @error('terms') is-invalid @enderror" 
                               type="checkbox" 
                               id="terms" 
                               name="terms" 
                               required>
                        <label class="form-check-label" for="terms">
                            I agree to the 
                            <a href="{{ route('terms.show') }}" class="text-maroon text-decoration-none" target="_blank">
                                Terms of Service
                            </a> 
                            and 
                            <a href="{{ route('policy.show') }}" class="text-maroon text-decoration-none" target="_blank">
                                Privacy Policy
                            </a>
                        </label>
                        @error('terms')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            @endif

            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-maroon btn-lg">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>
            </div>

            <hr class="my-4">
            <div class="text-center">
                <p class="text-muted mb-2">Already have an account?</p>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
