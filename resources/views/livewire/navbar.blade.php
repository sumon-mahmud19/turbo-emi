<nav class="bg-base-100 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-primary">MyAdmin</a>
            </div>

            <!-- Desktop Links -->
            <div class="hidden md:flex space-x-2">
                <a wire:navigate href="{{ route('dashboard') }}" wire:click="$set('currentRoute','dashboard')"
                   class="btn btn-sm {{ $currentRoute == 'dashboard' ? 'bg-primary text-white' : 'btn-ghost' }}">
                    Dashboard
                </a>

                @can('customer-list')
                    <a wire:navigate href="{{ route('customers.index') }}" wire:click="$set('currentRoute','customers.index')"
                       class="btn btn-sm {{ $currentRoute == 'customers.index' ? 'bg-primary text-white' : 'btn-ghost' }}">
                        Customers
                    </a>
                @endcan

                @can('location-list')
                    <a wire:navigate href="{{ route('locations.index') }}" wire:click="$set('currentRoute','locations.index')"
                       class="btn btn-sm {{ $currentRoute == 'locations.index' ? 'bg-primary text-white' : 'btn-ghost' }}">
                        Locations
                    </a>
                @endcan

                @can('product-list')
                    <a wire:navigate href="{{ route('products.index') }}" wire:click="$set('currentRoute','products.index')"
                       class="btn btn-sm {{ $currentRoute == 'products.index' ? 'bg-primary text-white' : 'btn-ghost' }}">
                        Products
                    </a>
                @endcan

                @can('product-model-list')
                    <a wire:navigate href="{{ route('products.model') }}" wire:click="$set('currentRoute','products.model')"
                       class="btn btn-sm {{ $currentRoute == 'products.model' ? 'bg-primary text-white' : 'btn-ghost' }}">
                        Product Models
                    </a>
                @endcan

                @can('user-list')
                    <a wire:navigate href="{{ route('users.index') }}" wire:click="$set('currentRoute','users.index')"
                       class="btn btn-sm {{ $currentRoute == 'users.index' ? 'bg-primary text-white' : 'btn-ghost' }}">
                        Users
                    </a>
                @endcan

                @can('role-list')
                    <a wire:navigate href="{{ route('roles.index') }}" wire:click="$set('currentRoute','roles.index')"
                       class="btn btn-sm {{ $currentRoute == 'roles.index' ? 'bg-primary text-white' : 'btn-ghost' }}">
                        User Roles
                    </a>
                @endcan
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="btn btn-square btn-ghost">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu-content" class="hidden md:hidden">
        <ul class="menu p-2 bg-base-100 space-y-1">
            <li>
                <a wire:navigate href="{{ route('dashboard') }}" wire:click="$set('currentRoute','dashboard')"
                   class="{{ $currentRoute == 'dashboard' ? 'bg-primary text-white rounded-lg px-2 py-1' : '' }}">
                   Dashboard
                </a>
            </li>
            @can('customer-list')
                <li>
                    <a wire:navigate href="{{ route('customers.index') }}" wire:click="$set('currentRoute','customers.index')"
                       class="{{ $currentRoute == 'customers.index' ? 'bg-primary text-white rounded-lg px-2 py-1' : '' }}">
                       Customers
                    </a>
                </li>
            @endcan
            @can('location-list')
                <li>
                    <a wire:navigate href="{{ route('locations.index') }}" wire:click="$set('currentRoute','locations.index')"
                       class="{{ $currentRoute == 'locations.index' ? 'bg-primary text-white rounded-lg px-2 py-1' : '' }}">
                       Locations
                    </a>
                </li>
            @endcan
            <!-- add others the same way -->
        </ul>
    </div>
</nav>

<script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenuContent = document.getElementById('mobile-menu-content');
    mobileMenuButton.addEventListener('click', () => {
        mobileMenuContent.classList.toggle('hidden');
    });
</script>
