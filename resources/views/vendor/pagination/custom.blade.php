@if ($paginator->hasPages())
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; flex-wrap: wrap; gap: 12px; padding: 8px 0;">
        
        {{-- Info Teks Jumlah Data --}}
        <div style="font-size: 13px; color: #64748b; font-weight: 500;">
            Menampilkan <span style="font-weight: 600; color: #0f172a;">{{ $paginator->firstItem() ?? 0 }}</span> - <span style="font-weight: 600; color: #0f172a;">{{ $paginator->lastItem() ?? 0 }}</span> dari <span style="font-weight: 600; color: #0f172a;">{{ $paginator->total() }}</span> data
        </div>

        {{-- Tombol Navigasi --}}
        <div style="display: flex; gap: 6px; align-items: center;">
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #e2e8f0; border-radius: 8px; color: #cbd5e1; background-color: #f8fafc; font-size: 13px; font-weight: 600; cursor: not-allowed; user-select: none;">
                    &laquo; Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">
                    &laquo; Previous
                </a>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 6px; color: #94a3b8; font-size: 13px; font-weight: 500;">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border: 1px solid #3b82f6; border-radius: 8px; color: #fff; background-color: #3b82f6; font-size: 13px; font-weight: 600; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25); user-select: none;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 12px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; background-color: #fff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">
                    Next &raquo;
                </a>
            @else
                <span style="display: flex; align-items: center; height: 38px; padding: 0 14px; border: 1px solid #e2e8f0; border-radius: 8px; color: #cbd5e1; background-color: #f8fafc; font-size: 13px; font-weight: 600; cursor: not-allowed; user-select: none;">
                    Next &raquo;
                </span>
            @endif
        </div>

    </div>

    {{-- Tambahkan Style Hover Interaktif (Opsional, letakkan di tag <style> utama halaman) --}}
    <style>
        .pagination-btn:hover {
            border-color: #3b82f6 !important;
            color: #2563eb !important;
            background-color: #f8fafc !important;
        }
    </style>
@endif