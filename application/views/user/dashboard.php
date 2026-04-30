<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= isset($judul) ? $judul : 'Apotek Online' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body { background-color: #f0f3f7; font-family: sans-serif; }
        .product-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; padding: 10px; }
        @media (min-width: 768px) { .product-grid { grid-template-columns: repeat(4, 1fr); gap: 15px; padding: 15px; } }
        .card-tokped {
            background: #fff; border-radius: 8px; overflow: hidden;
            box-shadow: 0 1px 6px rgba(0,0,0,0.1); text-decoration: none; color: inherit;
            display: flex; flex-direction: column; height: 100%;
        }
        .card-tokped:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); transform: translateY(-2px); }
        .card-tokped img { width: 100%; aspect-ratio: 1/1; object-fit: cover; background: #eee; }
        .product-info { padding: 10px; display: flex; flex-direction: column; flex-grow: 1; }
        .product-name {
            font-size: 14px; line-height: 18px; height: 36px; margin-bottom: 4px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .product-price { font-size: 16px; font-weight: 700; color: #212121; margin-bottom: 4px; }
        .product-meta { font-size: 12px; color: #6d7588; display: flex; align-items: center; gap: 4px; }
        .empty-state { text-align: center; padding: 50px; color: #888; grid-column: 1 / -1; }
    </style>
</head>
<body>

<main class="max-w-[1200px] mx-auto px-4 md:px-6 py-8">
    <h2 class="text-xl font-bold mb-6 text-[#31353B]">Produk Kesehatan Terlaris</h2>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <?php foreach ($produk as $p) : ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition cursor-pointer">
                <div class="w-full h-40 bg-gray-100">
                    <img src="<?= $p['gambar']; ?>" alt="<?= $p['nama_produk']; ?>" class="w-full h-full object-contain p-2">
                </div>
                
                <div class="p-3">
                    <span class="text-[10px] font-bold text-tokped uppercase"><?= $p['nama_kategori']; ?></span>
                    <h3 class="text-sm text-[#31353B] line-clamp-2 h-10 mb-1"><?= $p['nama_produk']; ?></h3>
                    <p class="text-base font-bold text-[#31353B]">Rp <?= number_format($p['harga_jual'], 0, ',', '.'); ?></p>
                    
                    <div class="mt-2 flex items-center gap-1">
                        <div class="flex-1 h-1 bg-gray-200 rounded-full overflow-hidden">
                            <div class="bg-[#EF144A] h-full" style="width: <?= ($p['stok'] > 0) ? '70%' : '0%'; ?>"></div>
                        </div>
                        <span class="text-[10px] text-gray-500">Stok: <?= $p['stok']; ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>