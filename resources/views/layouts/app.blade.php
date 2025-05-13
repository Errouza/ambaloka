<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambaloka - Temani Perjalananmu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-white text-2xl font-bold">AMBALOKA</a>
            <div class="space-x-4">
                <a href="{{ url('/flights') }}" class="text-white hover:text-blue-200">Penerbangan</a>
                <a href="{{ url('/hotels') }}" class="text-white hover:text-blue-200">Hotel</a>
                @auth
                    <a href="{{ url('/bookings') }}" class="text-white hover:text-blue-200">Pesanan Saya</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:text-blue-200">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-blue-200">Masuk</a>
                    <a href="{{ route('register') }}" class="text-white hover:text-blue-200">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto py-8">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Tentang Kami</h3>
                    <p>Partner terpercaya untuk pemesanan tiket pesawat dan hotel Anda.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Menu Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/flights') }}" class="hover:text-blue-400">Penerbangan</a></li>
                        <li><a href="{{ url('/hotels') }}" class="hover:text-blue-400">Hotel</a></li>
                        <li><a href="{{ url('/contact') }}" class="hover:text-blue-400">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Kontak</h3>
                    <p>Email: Aditya.support@ambaloka.com</p>
                    <p>Telepon: +62 123 456 789</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
