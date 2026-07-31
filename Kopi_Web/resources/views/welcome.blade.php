<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Kopi - Premium Carpooling</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-brand-900 text-gray-100 font-sans antialiased min-h-screen">
        <header class="w-full max-w-5xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="text-2xl font-bold text-primary-500 tracking-tighter">
                KOPI
            </div>
            <nav class="flex items-center gap-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-medium hover:text-primary-500 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium hover:text-primary-500 transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="bg-primary-500 text-black px-5 py-2 rounded-xl text-sm font-semibold hover:bg-primary-400 transition-colors">Sign up</a>
                        @endif
                    @endauth
                @endif
            </nav>
        </header>

        <main class="w-full max-w-5xl mx-auto px-6 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Left Column: Search -->
            <div class="lg:col-span-5 space-y-8">
                <div class="space-y-4">
                    <h1 class="text-5xl font-extrabold tracking-tight">Move with <br><span class="text-primary-500">Premium</span> style.</h1>
                    <p class="text-gray-400 text-lg">Find the perfect ride to your destination in our exclusive carpooling network.</p>
                </div>
                
                <div class="bg-brand-800 p-6 rounded-3xl shadow-2xl border border-gray-800 space-y-4 relative overflow-hidden">
                    <!-- Glassmorphism subtle flare -->
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary-500 opacity-10 blur-3xl rounded-full pointer-events-none"></div>

                    <div class="relative">
                        <div class="absolute left-4 top-4 w-3 h-3 rounded-full bg-primary-500"></div>
                        <input type="text" placeholder="Leaving from..." class="w-full bg-brand-900 border border-gray-800 rounded-xl py-3 pl-12 pr-4 text-gray-200 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all" value="New York, NY">
                    </div>
                    <div class="relative">
                        <div class="absolute left-4 top-4 w-3 h-3 rounded-full bg-accent-500"></div>
                        <input type="text" placeholder="Going to..." class="w-full bg-brand-900 border border-gray-800 rounded-xl py-3 pl-12 pr-4 text-gray-200 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all" value="Boston, MA">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="date" class="w-full bg-brand-900 border border-gray-800 rounded-xl py-3 px-4 text-gray-400 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all" value="2026-07-15">
                        <input type="number" placeholder="Passengers" class="w-full bg-brand-900 border border-gray-800 rounded-xl py-3 px-4 text-gray-200 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 transition-all" value="1">
                    </div>
                    <button class="w-full bg-primary-500 text-black font-bold text-lg py-4 rounded-xl hover:bg-primary-400 transition-transform active:scale-95 shadow-[0_0_20px_var(--color-primary-500)] mt-2">
                        Search Rides
                    </button>
                </div>
            </div>

            <!-- Right Column: Results -->
            <div class="lg:col-span-7 space-y-4">
                <h2 class="text-xl font-semibold mb-6 flex items-center justify-between">
                    <span>Available Rides</span>
                    <span class="text-sm text-gray-400 font-normal">2 results for Jul 15</span>
                </h2>
                
                <!-- Trip Card 1 -->
                <div class="bg-brand-800 rounded-3xl p-5 border border-gray-800 hover:border-primary-500 transition-colors cursor-pointer group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex flex-col relative pl-6 space-y-4">
                            <div class="absolute left-1 top-1 bottom-1 w-0.5 bg-gray-700"></div>
                            <div class="relative">
                                <div class="absolute -left-6 top-1.5 w-2.5 h-2.5 rounded-full bg-primary-500 ring-4 ring-brand-800"></div>
                                <p class="text-lg font-bold">08:00 AM <span class="text-gray-400 font-normal text-base ml-2">New York</span></p>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-6 top-1.5 w-2.5 h-2.5 rounded-full bg-accent-500 ring-4 ring-brand-800"></div>
                                <p class="text-lg font-bold">12:30 PM <span class="text-gray-400 font-normal text-base ml-2">Boston</span></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-extrabold text-primary-500 group-hover:scale-105 transition-transform">$45</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-800 pt-4 mt-2 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-700 overflow-hidden border-2 border-transparent group-hover:border-primary-500 transition-colors">
                                <img src="https://i.pravatar.cc/100?img=33" alt="Driver" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-medium flex items-center gap-1">Marcus <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></p>
                                <p class="text-xs text-gray-400">Lexus ES 300h • ⭐ 4.9</p>
                            </div>
                        </div>
                        <button class="text-sm font-semibold text-gray-300 group-hover:text-primary-500 transition-colors">Details →</button>
                    </div>
                </div>

                <!-- Trip Card 2 -->
                <div class="bg-brand-800 rounded-3xl p-5 border border-gray-800 hover:border-primary-500 transition-colors cursor-pointer group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex flex-col relative pl-6 space-y-4">
                            <div class="absolute left-1 top-1 bottom-1 w-0.5 bg-gray-700"></div>
                            <div class="relative">
                                <div class="absolute -left-6 top-1.5 w-2.5 h-2.5 rounded-full bg-primary-500 ring-4 ring-brand-800"></div>
                                <p class="text-lg font-bold">10:15 AM <span class="text-gray-400 font-normal text-base ml-2">New York</span></p>
                            </div>
                            <div class="relative">
                                <div class="absolute -left-6 top-1.5 w-2.5 h-2.5 rounded-full bg-accent-500 ring-4 ring-brand-800"></div>
                                <p class="text-lg font-bold">02:45 PM <span class="text-gray-400 font-normal text-base ml-2">Boston</span></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-extrabold text-primary-500 group-hover:scale-105 transition-transform">$50</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-800 pt-4 mt-2 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-700 overflow-hidden border-2 border-transparent group-hover:border-primary-500 transition-colors">
                                <img src="https://i.pravatar.cc/100?img=47" alt="Driver" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-medium flex items-center gap-1">Sarah <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg></p>
                                <p class="text-xs text-gray-400">Audi Q5 • ⭐ 5.0</p>
                            </div>
                        </div>
                        <button class="text-sm font-semibold text-gray-300 group-hover:text-primary-500 transition-colors">Details →</button>
                    </div>
                </div>

            </div>
        </main>
    </body>
</html>
