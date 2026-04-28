<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ setting('company_name', currentTenant()?->name ?? 'ISP Billing') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-item.active { background-color: #4338ca; color: white; }
        .sidebar-item:hover { background-color: #4338ca20; }
        body { font-family: 'Noto Sans Bengali', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

<!-- Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-indigo-900 text-white transform transition-transform duration-200"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <div class="flex items-center justify-between h-16 px-4 bg-indigo-950">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <i class="fas fa-wifi text-indigo-300 text-xl"></i>
            <span class="font-bold text-lg truncate">{{ setting('company_name', currentTenant()?->name ?? 'ISP Billing') }}</span>
        </a>
        <button @click="sidebarOpen=false" class="lg:hidden text-gray-400 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav class="mt-4 px-2 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
        @php
            $nav = [
                ['route' => 'dashboard', 'icon' => 'fas fa-tachometer-alt', 'label' => 'Dashboard'],
                ['route' => 'customers.index', 'icon' => 'fas fa-users', 'label' => 'Customers'],
                ['route' => 'packages.index', 'icon' => 'fas fa-box', 'label' => 'Packages'],
                ['route' => 'areas.index', 'icon' => 'fas fa-map-marker-alt', 'label' => 'Areas & POPs'],
                ['route' => 'services.index', 'icon' => 'fas fa-network-wired', 'label' => 'Services'],
                ['route' => 'olt-devices.index', 'icon' => 'fas fa-server', 'label' => 'OLT Devices'],
                ['route' => 'invoices.index', 'icon' => 'fas fa-file-invoice', 'label' => 'Invoices'],
                ['route' => 'payments.index', 'icon' => 'fas fa-money-bill-wave', 'label' => 'Payments'],
                ['route' => 'dues.index', 'icon' => 'fas fa-exclamation-triangle', 'label' => 'Dues'],
                ['route' => 'reports.collections', 'icon' => 'fas fa-chart-bar', 'label' => 'Reports'],
                ['route' => 'staff.index', 'icon' => 'fas fa-user-tie', 'label' => 'Staff'],
                ['route' => 'sms.templates', 'icon' => 'fas fa-sms', 'label' => 'SMS'],
                ['route' => 'settings.company', 'icon' => 'fas fa-cog', 'label' => 'Settings'],
            ];
        @endphp

        @foreach($nav as $item)
        <a href="{{ route($item['route']) }}"
           class="sidebar-item flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium text-indigo-100 {{ request()->routeIs(rtrim($item['route'], '.index').'*') ? 'active' : '' }}">
            <i class="{{ $item['icon'] }} w-5 text-center"></i>
            <span>{{ $item['label'] }}</span>
        </a>
        @endforeach
    </nav>
</div>

<!-- Main content -->
<div class="transition-all duration-200" :class="sidebarOpen ? 'lg:ml-64' : ''">
    <!-- Top bar -->
    <header class="bg-white shadow-sm h-16 flex items-center justify-between px-4 sticky top-0 z-40">
        <div class="flex items-center space-x-3">
            <button @click="sidebarOpen=!sidebarOpen" class="text-gray-500 hover:text-gray-700 p-1">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <h1 class="text-gray-700 font-semibold text-lg">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600 hidden sm:block">
                <i class="fas fa-building text-indigo-500 mr-1"></i>
                {{ currentTenant()?->name }}
            </span>
            <div x-data="{ open: false }" class="relative">
                <button @click="open=!open" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-indigo-600">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" @click.outside="open=false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border text-sm">
                    <a href="{{ route('settings.company') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-cog mr-2 text-gray-400"></i>Settings
                    </a>
                    <hr>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-red-600 hover:bg-gray-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash messages -->
    <div class="px-6 pt-4">
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-4 flex items-center justify-between" x-data x-init="setTimeout(() => $el.remove(), 5000)">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button @click="$el.remove()" class="text-green-600"><i class="fas fa-times"></i></button>
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4 flex items-center justify-between">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button @click="$el.remove()" class="text-red-600"><i class="fas fa-times"></i></button>
        </div>
        @endif
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <!-- Page content -->
    <main class="p-6">
        @yield('content')
    </main>
</div>

</body>
</html>
