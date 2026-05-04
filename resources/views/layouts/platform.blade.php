<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Platform') - ISP Billing Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>[x-cloak] { display: none !important; } body { font-family: 'Noto Sans Bengali', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: true }">

<div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-200"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <div class="flex items-center justify-between h-16 px-4 bg-black">
        <span class="font-bold text-lg flex items-center space-x-2">
            <i class="fas fa-shield-alt text-yellow-400"></i>
            <span>Platform Admin</span>
        </span>
    </div>

    <nav class="mt-4 px-2 space-y-1">
        <a href="{{ route('platform.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-100 hover:bg-gray-700 {{ request()->routeIs('platform.dashboard') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-tachometer-alt w-5 text-center"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('platform.tenants.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-100 hover:bg-gray-700 {{ request()->routeIs('platform.tenants*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-building w-5 text-center"></i><span>Tenants (ISPs)</span>
        </a>
        <a href="{{ route('platform.plans.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-100 hover:bg-gray-700 {{ request()->routeIs('platform.plans*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-tags w-5 text-center"></i><span>Subscription Plans</span>
        </a>
    </nav>
</div>

<div class="transition-all duration-200" :class="sidebarOpen ? 'lg:ml-64' : ''">
    <header class="bg-white shadow-sm h-16 flex items-center justify-between px-4 sticky top-0 z-40">
        <div class="flex items-center space-x-3">
            <button @click="sidebarOpen=!sidebarOpen" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <h1 class="text-gray-700 font-semibold">@yield('page-title', 'Platform Admin')</h1>
        </div>
        <div x-data="{ open: false }" class="relative">
            <button @click="open=!open" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900">
                <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div x-show="open" @click.outside="open=false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border text-sm">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-red-600 hover:bg-gray-50">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="px-6 pt-4">
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-4" x-data x-init="setTimeout(() => $el.remove(), 5000)">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
        @endif
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4">
            <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
    </div>

    <main class="p-6">@yield('content')</main>
</div>
</body>
</html>
