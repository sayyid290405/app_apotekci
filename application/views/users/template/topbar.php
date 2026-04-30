<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Tokopedia - Beneran Bener</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        tokped: '#03AC0E', // Warna hijau Tokopedia
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F3F4F5]">

<header class="w-full sticky top-0 z-50 bg-white border-b shadow-sm" style="border-color: #E5E7E9;">
    <!-- <div class="hidden lg:block bg-[#F3F4F5] py-1.5 px-6 md:px-16">
        <div class="max-w-[1200px] mx-auto flex justify-between items-center text-[11px] text-[#6D7588]">
            <div class="flex gap-5">
                <a href="#" class="hover:text-tokped">Tentang Toko</a>
                <a href="#" class="hover:text-tokped">Mitra Toko</a>
                <a href="#" class="hover:text-tokped">Mulai Berjualan</a>
                <a href="#" class="hover:text-tokped">Bantuan</a>
            </div>
            <a href="#" class="flex items-center gap-1.5 hover:text-tokped">
                <i data-lucide="smartphone" class="w-3 h-3"></i> Download App
            </a>
        </div>
    </div> -->


    <div class="w-full px-4 md:px-16 py-3 bg-white">
        <div class="max-w-[1200px] mx-auto flex items-center gap-6">
            
            <a href="#" class="flex-shrink-0 flex items-center gap-2 group decoration-none">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm" style="background-color: #03AC0E;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <path d="M16 10a4 4 0 0 1-8 0" />
                    </svg>
                </div>
                <div class="hidden xl:block leading-tight">
                    <span class="block text-lg font-extrabold tracking-tight" style="color: #03AC0E;">Bayur Farma</span>
                </div>
            </a>

            <button class="hidden lg:block text-sm font-medium text-[#6D7588] hover:text-tokped transition">
                Kategori
            </button>

            <div class="flex-1 max-w-2xl relative group">
                <div class="flex items-center border rounded-lg h-10 px-3 bg-white transition-all focus-within:border-tokped overflow-hidden shadow-inner" style="border-color: #E5E7E9;">
                    <i data-lucide="search" class="text-gray-400 group-focus-within:text-tokped" style="width:18px;height:18px;"></i>
                    <input type="text" placeholder="Cari obat, vitamin, atau alat kesehatan..." class="w-full px-3 text-sm outline-none text-[#31353B] placeholder-gray-400 bg-transparent">
                    <button class="hidden md:block px-4 py-1.5 text-xs font-bold text-white rounded-md ml-2" style="background-color: #03AC0E;">Cari</button>
                </div>
                <div class="hidden lg:flex gap-4 mt-1.5 px-1.5">
                    <a href="#" class="text-[11px] text-[#6D7588] hover:text-tokped">Masker Medis</a>
                    <a href="#" class="text-[11px] text-[#6D7588] hover:text-tokped">Vitamin C</a>
                    <a href="#" class="text-[11px] text-[#6D7588] hover:text-tokped">Imboost</a>
                    <a href="#" class="text-[11px] text-[#6D7588] hover:text-tokped">Sanitizer</a>
                </div>
            </div>

            <div class="flex items-center gap-2 md:gap-4">
    <div class="flex items-center gap-1 border-r pr-3" style="border-color: #E5E7E9;">
        <div class="relative p-2 cursor-pointer rounded-lg hover:bg-gray-100 transition group">
            <i data-lucide="shopping-cart" class="text-gray-600 w-5 h-5 group-hover:text-tokped"></i>
            <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 rounded-full text-white text-[9px] font-bold flex items-center justify-center shadow-sm" style="background-color: #EF144A;">3</span>
        </div>
        
        <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-tokped transition">
            <i data-lucide="bell" class="w-5 h-5"></i>
        </button>

        <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-tokped transition">
            <i data-lucide="mail" class="w-5 h-5"></i>
        </button>
    </div>

    <a href="#" class="flex items-center gap-2 pl-2 group no-underline">
        <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200 shadow-sm">
            <img src="https://ui-avatars.com/api/?name=Sayyid+Ahmad&background=E8F5E9&color=03AC0E" alt="User Profile" class="w-full h-full object-cover">
        </div>
        <div class="hidden lg:block">
            <span class="block text-sm font-bold text-[#31353B] group-hover:text-tokped transition"></span>
        </div>
    </a>
</div>

        </div>
    </div>
</header>
<script src="https://unpkg.com/lucide@latest"></script>
    <script>
      lucide.createIcons();
    </script>
</body>
</html>