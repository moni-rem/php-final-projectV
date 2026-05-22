<footer class="bg-slate-950 text-white">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-5 py-10 sm:grid-cols-2 sm:px-8 lg:grid-cols-4">
        <div>
            <a href="{{ url('/') }}" class="text-lg font-black tracking-wide">Refined Travel</a>
            <p class="mt-3 max-w-md text-sm leading-6 text-white/70">
                Discover local events, book tickets, and keep your travel plans organized in one simple place.
            </p>
        </div>

        <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-300">Explore</p>
            <div class="mt-4 grid gap-3 text-sm font-semibold text-white/75">
                <a href="{{ route('events.index') }}" class="hover:text-white">Events</a>
                <a href="{{ route('about') }}" class="hover:text-white">About us</a>
                <a href="{{ route('bookings.history') }}" class="hover:text-white">Booking History</a>
            </div>
        </div>

        <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-300">Account</p>
            <div class="mt-4 grid gap-3 text-sm font-semibold text-white/75">
                @auth
                    <a href="{{ route('user.dashboard') }}" class="hover:text-white">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-left font-semibold hover:text-white">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-white">Login</a>
                    <a href="{{ route('register') }}" class="hover:text-white">Register</a>
                @endauth
            </div>
        </div>

        <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-300">Contact</p>
            <div class="mt-4 grid gap-3 text-sm font-semibold text-white/75">
                <p>Phnom Penh, Cambodia</p>
                <a href="mailto:support@refinedtravel.test" class="hover:text-white">support@refinedtravel.test</a>
                <p>Open daily for event booking</p>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-2 px-5 py-5 text-xs font-semibold text-white/55 sm:grid-cols-2 sm:px-8">
            <p>&copy; {{ date('Y') }} Refined Travel. All rights reserved.</p>
            <p class="sm:text-right">Built for easier event booking.</p>
        </div>
    </div>
</footer>
