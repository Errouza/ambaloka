@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        @if(isset($transport))
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-4">Detail Transportasi</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Informasi Transportasi</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Maskapai:</label>
                                <span class="text-lg">{{ $transport['description'] }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Kode:</label>
                                <span class="text-lg">{{ $transport['code'] }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Tipe:</label>
                                <span class="text-lg">{{ $transport['transport_type']['description'] }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Kursi Tersedia:</label>
                                <span class="text-lg">{{ $transport['seat'] }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Pesan Tiket</h2>
                        <form action="{{ route('transports.book', $transport['id']) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="seat" class="block text-sm font-medium text-gray-600">Jumlah Penumpang</label>
                                <input type="number" name="seat" id="seat" min="1" max="{{ $transport['seat'] }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                                <p class="mt-1 text-sm text-gray-500">Maksimal {{ $transport['seat'] }} penumpang</p>
                            </div>
                            
                            @auth
                                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                                    Pesan Sekarang
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="block text-center w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                                    Masuk untuk Memesan
                                </a>
                            @endauth
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">Data transportasi tidak ditemukan.</p>
                <a href="{{ route('transports.index') }}" class="mt-4 inline-block bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                    Kembali ke Daftar Transportasi
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
