@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Register</h2>
        
        <form method="POST" action="{{ route('register.submit') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" name="name" type="text" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       value="{{ old('name') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Register
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Login here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
