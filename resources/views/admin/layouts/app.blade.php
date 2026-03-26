{{-- resources/views/admin/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin – SINTEM')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; }
        body { font-family: 'Lato', sans-serif; background: #ffffff; display: flex; }

        .layout-wrap { display: flex; width: 100%; height: 100vh; overflow: hidden; }

        .main-content {
            flex: 1; min-width: 0; height: 100vh;
            overflow: hidden; display: flex; flex-direction: column;
            background: #ffffff;
        }

        .page-topbar { flex-shrink: 0; }

        .page-header {
            flex-shrink: 0;
            padding: 16px 32px 14px;
            border-bottom: 1px solid #f0f0f5;
            background: #ffffff;
        }
        .page-header-title { font-size: 20px; font-weight: 400; color: #1a1a2e; letter-spacing: -0.2px; }
        .page-header-sub   { font-size: 13px; color: #9ca3af; margin-top: 2px; }

        .page-body {
            flex: 1; min-height: 0;
            padding: 0;
            background: #ffffff;
            overflow: hidden;
            width: 100%;
            display: flex;
            flex-direction: column;
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="layout-wrap">

    @include('admin.components.sidebar')

    <div class="main-content">

        @hasSection('topbar')
        <div class="page-topbar">@yield('topbar')</div>
        @endif

        @hasSection('header')
        <div class="page-header">
            <h1 class="page-header-title">@yield('header')</h1>
            @hasSection('subheader')
            <p class="page-header-sub">@yield('subheader')</p>
            @endif
        </div>
        @endif

        <div class="page-body">
            @yield('content')
        </div>

    </div>
</div>

@stack('scripts')
</body>
</html>