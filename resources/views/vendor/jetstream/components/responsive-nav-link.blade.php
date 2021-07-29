@props(['active', 'icon'])

@php
$classes = 'group flex items-center px-2 py-2 text-base font-medium rounded-md ' . ($active ?? false ? 'bg-lmara text-white' : 'text-tblue hover:bg-dsgreen hover:text-white');
$iconClasses = 'mr-4 flex-shrink-0 h-6 w-6 ' . ($active ?? false ? 'text-white' : 'text-tblue group-hover:text-white');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
  <x-nav-icon name="{{ $icon }}"
    class="{{ $iconClasses }}" />
  {{ $slot }}
</a>
