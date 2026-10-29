<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Kasir POS' }} - Minimarket Point of Sale</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 antialiased h-screen overflow-hidden flex flex-col font-sans select-none">
    {{ $slot ?? '' }}
    @yield('content')
</body>
</html>
