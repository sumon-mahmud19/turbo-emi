<nav class="bg-base-100 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Left: Brand / Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-primary">MyAdmin</a>
            </div>

            <!-- Center: Desktop Links -->
            <div class="hidden md:flex space-x-4">
                <a wire:navigate href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm">Dashboard</a>
                <a wire:navigate href="{{ route('customers.index') }}" class="btn btn-ghost btn-sm">Customers</a>
                <a wire:navigate href="{{ route('locations.index') }}" class="btn btn-ghost btn-sm">Locations</a>
                <a href="{{ route('purchases.index') }}" class="btn btn-ghost btn-sm">Purchases</a>
                <a wire:navigate href="{{ route('products.index') }}" class="btn btn-ghost btn-sm">Products</a>
                <a wire:navigate href="{{ route('products.model') }}" class="btn btn-ghost btn-sm">Products Model</a>
                <a wire:navigate href="{{ route('users.index') }}" class="btn btn-ghost btn-sm">Users</a>
                <a wire:navigate href="{{ route('roles.index') }}" class="btn btn-ghost btn-sm">Roles</a>
            </div>



            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="btn btn-square btn-ghost">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu-content" class="hidden md:hidden">
        <ul class="menu p-2 bg-base-100 space-y-1">
            <li><a wire:navigate href="{{ route('dashboard') }}">Dashboard</a></li>
            <li><a wire:navigate href="{{ route('customers.index') }}">Customers</a></li>
            <li><a wire:navigate href="{{ route('locations.index') }}">Locations</a></li>
            <li><a href="{{ route('purchases.index') }}">Purchases</a></li>
            <li><a wire:navigate href="{{ route('products.index') }}">Products</a></li>
            <li><a wire:navigate href="{{ route('products.model') }}">Products Model</a></li>
            <li><a wire:navigate href="{{ route('users.index') }}">Users</a></li>
            <li><a wire:navigate href="{{ route('roles.index') }}">Roles</a></li>

        </ul>
    </div>
</nav>

<script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenuContent = document.getElementById('mobile-menu-content');

    mobileMenuButton.addEventListener('click', () => {
        mobileMenuContent.classList.toggle('hidden');
    });
</script>
