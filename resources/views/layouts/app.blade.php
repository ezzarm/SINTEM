{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SINTEM')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Lato', sans-serif;
            background: #f9f9fb;
            min-height: 100vh;
            display: flex;
        }

        .layout-wrap {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Main content area */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
        }

        /* Page header bar (title + subtitle per page) */
        .page-header {
            padding: 24px 32px 20px;
            border-bottom: 1px solid #f0f0f5;
            background: #ffffff;
        }
        .page-header-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a2e;
            letter-spacing: -0.2px;
        }
        .page-header-sub {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* Scrollable content slot */
        .page-body {
            flex: 1;
            padding: 28px 32px;
            overflow-y: auto;
        }
    </style>

    {{-- Page-specific styles --}}
    @stack('styles')
</head>

<body>
<div class="layout-wrap">

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Main area --}}
    <div class="main-content">

        {{-- Topbar slot — optional, only shows if page defines it --}}
        @yield('topbar')

        {{-- Page header (title + subtitle injected per page) --}}
        @hasSection('header')
        <div class="page-header">
            <h1 class="page-header-title">@yield('header')</h1>
            @hasSection('subheader')
            <p class="page-header-sub">@yield('subheader')</p>
            @endif
        </div>
        @endif

        {{-- Page content --}}
        <div class="page-body">
            @yield('content')
        </div>

    </div>
</div>

{{-- Page-specific scripts --}}
@stack('scripts')
</body>
</html>