<!-- resources/js/Layouts/AppLayout.vue -->
<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo / Brand -->
                        <div class="flex-shrink-0 flex items-center">
                            <Link href="/dashboard" class="text-xl font-bold text-gray-800">
                                {{ appName }}
                            </Link>
                        </div>
                        
                        <!-- Desktop Navigation -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <Link 
                                href="/dashboard" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200"
                                :class="{
                                    'border-indigo-500 text-gray-900': $page.url.startsWith('/dashboard'),
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page.url.startsWith('/dashboard')
                                }"
                            >
                                Dashboard
                            </Link>
                            
                            <Link 
                                href="/products" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200"
                                :class="{
                                    'border-indigo-500 text-gray-900': $page.url.startsWith('/products'),
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page.url.startsWith('/products')
                                }"
                            >
                                Products
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Right side -->
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <div class="sm:hidden">
                            <button 
                                @click="toggleMobileMenu"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path 
                                        v-if="!mobileMenuOpen"
                                        stroke-linecap="round" 
                                        stroke-linejoin="round" 
                                        stroke-width="2" 
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path 
                                        v-else
                                        stroke-linecap="round" 
                                        stroke-linejoin="round" 
                                        stroke-width="2" 
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- User menu -->
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <!-- User dropdown -->
                                <div class="relative">
                                    <button 
                                        @click="toggleUserDropdown"
                                        class="flex items-center text-sm rounded-full focus:outline-none"
                                    >
                                        <span class="sr-only">Open user menu</span>
                                        <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ $page.props.auth?.user?.name?.charAt(0) || 'U' }}
                                            </span>
                                        </div>
                                    </button>
                                    
                                    <!-- Dropdown menu -->
                                    <div 
                                        v-if="userDropdownOpen"
                                        v-click-outside="closeUserDropdown"
                                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 z-50"
                                    >
                                        <Link 
                                            href="/profile" 
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                            @click="closeUserDropdown"
                                        >
                                            Your Profile
                                        </Link>
                                        <form @submit.prevent="logout">
                                            <button 
                                                type="submit"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                            >
                                                Sign out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile menu -->
                <div v-if="mobileMenuOpen" class="sm:hidden border-t">
                    <div class="pt-2 pb-3 space-y-1">
                        <Link 
                            href="/dashboard" 
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                            :class="{
                                'border-indigo-500 bg-indigo-50 text-indigo-700': $page.url.startsWith('/dashboard'),
                                'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800': !$page.url.startsWith('/dashboard')
                            }"
                            @click="mobileMenuOpen = false"
                        >
                            Dashboard
                        </Link>
                        
                        <Link 
                            href="/products" 
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                            :class="{
                                'border-indigo-500 bg-indigo-50 text-indigo-700': $page.url.startsWith('/products'),
                                'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800': !$page.url.startsWith('/products')
                            }"
                            @click="mobileMenuOpen = false"
                        >
                            Products
                        </Link>

                        <Link 
                            href="/categories" 
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                            :class="{
                                'border-indigo-500 bg-indigo-50 text-indigo-700': $page.url.startsWith('/categories'),
                                'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800': !$page.url.startsWith('/categories')
                            }"
                            @click="mobileMenuOpen = false"
                        >
                            Categories
                        </Link>
                        
                        <Link 
                            href="/sources" 
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                            :class="{
                                'border-indigo-500 bg-indigo-50 text-indigo-700': $page.url.startsWith('/sources'),
                                'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800': !$page.url.startsWith('/sources')
                            }"
                            @click="mobileMenuOpen = false"
                        >
                            Sources
                        </Link>
                        
                        <Link 
                            href="/receivers" 
                            class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                            :class="{
                                'border-indigo-500 bg-indigo-50 text-indigo-700': $page.url.startsWith('/receivers'),
                                'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800': !$page.url.startsWith('/receivers')
                            }"
                            @click="mobileMenuOpen = false"
                        >
                            Receivers
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex">
            <!-- Sidebar -->
            <aside 
                :class="{
                    'w-64': sidebarOpen,
                    'w-0': !sidebarOpen,
                    'hidden': !sidebarOpen
                }"
                class="bg-gray-800 text-white h-[calc(100vh-4rem)] overflow-y-auto transition-all duration-300 ease-in-out md:block"
            >
                <div class="p-4">
                    <!-- Sidebar Toggle (Desktop) -->
                    <div class="hidden md:flex justify-end mb-4">
                        <button 
                            @click="toggleSidebar"
                            class="p-2 rounded-lg bg-gray-700 hover:bg-gray-600 transition-colors"
                        >
                            <svg 
                                class="w-5 h-5" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path 
                                    v-if="sidebarOpen"
                                    stroke-linecap="round" 
                                    stroke-linejoin="round" 
                                    stroke-width="2" 
                                    d="M15 19l-7-7 7-7"
                                />
                                <path 
                                    v-else
                                    stroke-linecap="round" 
                                    stroke-linejoin="round" 
                                    stroke-width="2" 
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation Menu -->
                    <nav class="space-y-2">
                        <Link 
                            href="/dashboard" 
                            class="flex items-center px-3 py-2 rounded-lg transition-colors"
                            :class="{
                                'bg-gray-900': $page.url.startsWith('/dashboard'),
                                'hover:bg-gray-700': !$page.url.startsWith('/dashboard')
                            }"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="ml-3">Dashboard</span>
                        </Link>

                        <Link 
                            href="/products" 
                            class="flex items-center px-3 py-2 rounded-lg transition-colors"
                            :class="{
                                'bg-gray-900': $page.url.startsWith('/products'),
                                'hover:bg-gray-700': !$page.url.startsWith('/products')
                            }"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <span class="ml-3">Products</span>
                        </Link>
                        
                        <Link 
                            href="/categories" 
                            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors"
                            :class="{
                                'bg-gray-900': $page.url.startsWith('/categories'),
                                'hover:bg-gray-700': !$page.url.startsWith('/categories')
                            }"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span class="ml-3">Categories</span>
                        </Link>
                        
                        <Link 
                            href="/sources" 
                            class="flex items-center px-3 py-2 rounded-lg transition-colors"
                            :class="{
                                'bg-gray-900': $page.url.startsWith('/sources'),
                                'hover:bg-gray-700': !$page.url.startsWith('/sources')
                            }"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <span class="ml-3">Sources</span>
                        </Link>

                        <Link 
                            href="/receivers" 
                            class="flex items-center px-3 py-2 rounded-lg transition-colors"
                            :class="{
                                'bg-gray-900': $page.url.startsWith('/receivers'),
                                'hover:bg-gray-700': !$page.url.startsWith('/receivers')
                            }"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <span class="ml-3">Receivers</span>
                        </Link>

                        <!-- <Link 
                            href="#" 
                            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="ml-3">Orders</span>
                        </Link> -->

                        <!-- <Link 
                            href="#" 
                            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="ml-3">Customers</span>
                        </Link> -->

                        <Link 
                            href="/profile" 
                            class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="ml-3">Settings</span>
                        </Link>
                    </nav>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main 
                :class="{
                    'md:ml-64': sidebarOpen,
                    'md:ml-0': !sidebarOpen
                }"
                class="flex-1 p-4 md:p-6 transition-all duration-300 ease-in-out min-h-[calc(100vh-4rem)]"
            >
                <!-- Mobile Sidebar Toggle Button -->
                <button 
                    v-if="!sidebarOpen"
                    @click="toggleSidebar"
                    class="md:hidden mb-4 p-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <!-- Page Content -->
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';

const sidebarOpen = ref(true);
const userDropdownOpen = ref(false);
const mobileMenuOpen = ref(false);

const appName = computed(() => {
    return import.meta.env.VITE_APP_NAME || 'Laravel';
});

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleUserDropdown = () => {
    userDropdownOpen.value = !userDropdownOpen.value;
};

const closeUserDropdown = () => {
    userDropdownOpen.value = false;
};

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<style scoped>
aside::-webkit-scrollbar {
    width: 6px;
}

aside::-webkit-scrollbar-track {
    background: #374151;
}

aside::-webkit-scrollbar-thumb {
    background: #6b7280;
    border-radius: 3px;
}

aside::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>