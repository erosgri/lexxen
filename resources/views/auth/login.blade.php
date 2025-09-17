@extends('layouts.app')

@section('title', 'Login - ' . config('app.name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center">{{ __('Login') }}</h3>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Senha') }}</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Entrar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


