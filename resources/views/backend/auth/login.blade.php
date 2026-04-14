@extends('backend.auth.layouts.app')

@section('title')
    {{ __('Sign In') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div>
    <div class="mb-5 sm:mb-8">
      <h1 class="mb-2 font-semibold text-gray-700 text-title-sm dark:text-white/90 sm:text-title-md">
        {{ __('Sign In') }}
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-300">
        {{ __('Enter your email and password to sign in!') }}
      </p>
    </div>
    <div>
      <form action="{{ route('admin.login.submit') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        <div class="space-y-5">
          <x-messages />

          <div>
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input
              autofocus
              type="text"
              id="email"
              name="email"
              autocomplete="username"
              placeholder="{{ __('Enter your email') }}"
              class="form-control"
              value="{{ old('email') }}"
              required
            />
          </div>

          <x-inputs.password 
            name="password" 
            label="{{ __('Password') }}"
            placeholder="{{ __('Enter your password') }}" 
            required
          />

          <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center justify-center gap-2 text-sm font-medium has-checked:text-gray-900 dark:has-checked:text-white has-disabled:cursor-not-allowed">
                <span class="relative flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="before:content[''] peer relative size-4 appearance-none overflow-hidden rounded-sm border border-outline bg-surface-alt before:absolute before:inset-0 checked:border-primary checked:before:bg-primary focus:outline-2 focus:outline-offset-2 focus:outline-outline-strong checked:focus:outline-primary active:outline-offset-0 disabled:cursor-not-allowed dark:border-outline-dark dark:bg-surface-dark-alt dark:checked:border-primary-dark dark:checked:before:bg-primary-dark dark:focus:outline-outline-dark-strong dark:checked:focus:outline-primary-dark" checked/>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" stroke="currentColor" fill="none" stroke-width="4" class="pointer-events-none invisible absolute left-1/2 top-1/2 size-3 -translate-x-1/2 -translate-y-1/2 text-white peer-checked:visible">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </span>
                <span class="form-label mb-0">{{ __('Remember me') }}</span>
            </label>
            <a href="{{ route('admin.password.request') }}" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">{{ __('Forgot password?') }}</a>
          </div>

          <x-recaptcha page="login" />

          <div>
            <button type="submit" class="btn-primary w-full" :disabled="loading">
              <span x-text="loading ? '' : '{{ __('Sign In') }}'">{{ __('Sign In') }}</span>
              <iconify-icon :icon="loading ? 'lucide:loader-circle' : 'lucide:log-in'" :class="{ 'animate-spin': loading, 'ml-2': !loading }" />
            </button>
          </div>
        </div>
      </form>
    </div>
</div>
@endsection
