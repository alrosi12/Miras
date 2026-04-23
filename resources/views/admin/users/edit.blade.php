@extends('layouts.admin')

@section('title', __('Edit user'))

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-slate-900">{{ __('Edit user') }}: {{ $user->name }}</h1>

        <form method="post" action="{{ route('admin.users.update', $user) }}" class="space-y-5 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $user->email)" required />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="password" :value="__('New password (optional)')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" autocomplete="new-password" />
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm password')" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" autocomplete="new-password" />
            </div>

            <div class="flex items-center gap-2">
                {{-- عند إلغاء التأشير يُرسل الحقل المخفي فقط بقيمة 0 --}}
                <input type="hidden" name="is_admin" value="0" />
                <input id="is_admin" name="is_admin" type="checkbox" value="1" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500"
                    @checked(old('is_admin', $user->is_admin ? '1' : '0') == '1') />
                <x-input-label for="is_admin" :value="__('Administrator')" class="!mb-0" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('is_admin')" />

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users.show', $user) }}" class="py-2 text-sm text-slate-600 hover:text-slate-900">{{ __('Cancel') }}</a>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
@endsection
