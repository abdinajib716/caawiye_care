@props(['breadcrumbs' => []])

@extends('backend.layouts.app')

@section('title')
    @if ($pageTitle ?? false)
        {{ $pageTitle }}
    @else
        @php
            // Get the last breadcrumb item's label as the page title
            $pageTitle = '';
            if (is_array($breadcrumbs) && !empty($breadcrumbs)) {
                if (isset($breadcrumbs['title'])) {
                    $pageTitle = $breadcrumbs['title'];
                } elseif (isset($breadcrumbs[0])) {
                    $lastBreadcrumb = end($breadcrumbs);
                    $pageTitle = $lastBreadcrumb['label'] ?? '';
                }
            }
        @endphp
        {{ $pageTitle }} | {{ config('app.name') }}
    @endif
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        @if ($breadcrumbsData ?? false)
            {!! $breadcrumbsData !!}
        @else
            <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
        @endif

        {{ $slot }}
    </div>
@endsection
