<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Layanan KFJM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <!-- Alpine.js untuk Interaksi UI -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased h-full" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen">
        
        <!-- Header / Top Navigation -->
        @include('layouts.navigation')

        <div class="flex">
            <!-- Sidebar Area (Dinamis berdasarkan Route) -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <!-- Close Button Mobile -->
                <div class="flex items-center justify-end p-4 lg:hidden">
                    <button @click="sidebarOpen = false" class="text-gray-500 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Wait for it..."></path></svg>
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Konten Sidebar (Bisa dipindah ke komponen masing-masing) -->
                <div class="h-full overflow-y-auto py-4">
                    @if(Request::is('internal*'))
                        <x-internal-sidebar />
                    @elseif(Request::is('pelatihan*'))
                        <x-pelatihan-sidebar />
                    @elseif(Request::is('uji*'))
                        <x-uji-sidebar />
                    @else
                        <!-- Default Sidebar jika tidak masuk kategori -->
                        <div class="px-6 py-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Main Menu</p>
                            <nav class="mt-4 space-y-2">
                                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg">
                                    Dashboard
                                </a>
                            </nav>
                        </div>
                    @endif
                </div>
            </aside>

            <!-- Backdrop untuk Mobile -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                class="fixed inset-0 z-40 bg-gray-600 bg-opacity-50 lg:hidden"
                x-transition:enter="transition opacity-0 ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition opacity-100 ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            <!-- Main Content Area -->
            <main class="flex-1 w-full min-w-0 bg-gray-50">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white border-b border-gray-200">
                        <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8 flex items-center gap-4">
                            <!-- Toggle Sidebar Mobile Button -->
                            <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-500 rounded-md lg:hidden hover:bg-gray-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                                {{ $header }}
                            </h2>
                        </div>
                    </header>
                @endif

                <div class="py-12">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>