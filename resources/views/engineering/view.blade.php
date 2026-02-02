@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $document->judul }}</h1>
            <p class="text-gray-600 mt-2">
                <strong>Folder:</strong> {{ $document->folder }} | 
                <strong>Tanggal Upload:</strong> {{ $document->tanggal_upload ? $document->tanggal_upload->format('d/m/Y H:i') : '-' }}
            </p>
        </div>
        <div class="flex gap-2">
            @if ($document->file_path)
                <a href="{{ route('engineering.download', $document->id) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    📥 Download
                </a>
            @endif
            <a href="{{ route('engineering.edit', $document->id) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                ✏️ Edit
            </a>
            <a href="{{ route('engineering.index') }}" 
               class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                ← Kembali
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white rounded shadow p-6">
        <!-- File Info -->
        @if ($document->file_path)
            <div class="border-t pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-3">File</h2>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-gray-700"><strong>Nama File:</strong> {{ $document->file_name }}</p>
                    <p class="text-gray-700"><strong>Tipe File:</strong> {{ $document->file_type }}</p>
                    <p class="text-gray-700"><strong>Ukuran:</strong> {{ number_format($document->file_size / 1024, 2) }} KB</p>
                    <p class="text-gray-700 mt-2">
                        <a href="{{ route('engineering.download', $document->id) }}" 
                           class="text-blue-600 hover:text-blue-900 font-semibold">
                            📥 Download file ini
                        </a>
                    </p>
                </div>
            </div>
        @endif

        <!-- Info -->
        <div class="border-t mt-6 pt-6 text-sm text-gray-500">
            <p><strong>ID:</strong> {{ $document->id }}</p>
            <p><strong>Dibuat:</strong> {{ $document->created_at->format('d/m/Y H:i:s') }}</p>
            <p><strong>Diupdate:</strong> {{ $document->updated_at->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</div>

<style>
    .container {
        max-width: 1200px;
    }
</style>
@endsection
