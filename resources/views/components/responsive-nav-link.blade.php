@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-lg border-l-4 border-indigo-400 bg-indigo-50 py-2.5 ps-3 pe-4 text-start text-base font-medium text-indigo-700 transition duration-200 ease-out focus:border-indigo-700 focus:bg-indigo-100 focus:text-indigo-800 focus:outline-none'
            : 'block w-full rounded-lg border-l-4 border-transparent py-2.5 ps-3 pe-4 text-start text-base font-medium text-gray-600 transition duration-200 ease-out hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800 focus:border-gray-300 focus:bg-gray-50 focus:text-gray-800 focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
