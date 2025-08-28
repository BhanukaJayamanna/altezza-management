@props(['title'])

@php
    $appName = config('app.name', 'Altezza Property Management');
    $pageTitle = $title ? $title . ' - ' . $appName : $appName;
@endphp

<title>{{ $pageTitle }}</title>
