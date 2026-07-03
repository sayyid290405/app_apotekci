document.addEventListener('DOMContentLoaded', function(){

    let items = [];
    let fileResep = null;

    const search = document.getElementById('search');
    const hasil = document.getElementById('hasil');

    // =====================================================
    // 1. PREVIEW GAMBAR (ANTI-CRASH PARAMETER BLOB)
    // =====================================================
    const gambarResepInput = document.getElementById('gambarResep');
    if (gambarResepInput) {
        gambarResepInput.addEventListener('change', function(e){
            // Proteksi: Jika user membuka galeri lalu klik 'Cancel' (batal memilih foto)
            if (!e.target.files || e.target.files.length === 0) {
                console.log("Pemilihan foto resep dibatalkan oleh pengguna.");
                return; // Berhenti dengan aman tanpa merusak runtime JS
            }

            fileResep = e.target.files[0];

            let reader = new FileReader();
            reader.onload = function(e){
                let img = document.getElementById('previewResep');
                if (img) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
            }
            // Baris ini dijamin aman karena fileResep terbukti ada (bertipe Blob/File)
            reader.readAsDataURL(fileResep);
        });
    }

    // =====================================================
    // 2. LIVE PENCARIAN PRODUK / OBAT
    // =====================================================
    if (search) {
        search.addEventListener('keyup', function(){
            let q = this.value.trim();

            if(q.length < 2){
                if(hasil) hasil.innerHTML = '';
                return;
            }

            fetch(BASE_URL + 'resep/searchProduk?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                let html = '';
                data.forEach(p => {
                    html += `
                    <a href="#" class="list-group-item list-group-item-action"
                       onclick='tambah(${JSON.stringify(p)})'>
                        ${p.nama_produk}
                    </a>`;
                });
                if(hasil) hasil.innerHTML = html;
            })
            .catch(err => console.error('Gagal memuat pencarian:', err));
        });
    }

    // =====================================================
    // 3. TAMBAH OBAT KE KERANJANG & AMBIL SATUAN DB
    // =====================================================
    window.tambah = function(p){
        let exist = items.find(i => i.id == p.id_produk);

        if(exist){
            exist.qty++;
            render();
            return;
        }

        // Mengambil konversi harga dan nama_satuan dari tabel satuan_produk
        fetch(BASE_URL + 'produk/getSatuan/' + p.id_produk)
        .then(r => r.json())
        .then(satuan => {
            items.push({
                id: p.id_produk,
                nama: p.nama_produk,
                qty: 1,
                dosis: '',
                satuan: satuan[0]?.nama_satuan || 'Tablet',
                harga: parseFloat(satuan[0]?.harga) || 0,
                konversi: parseInt(satuan[0]?.konversi) || 1, // Penting untuk potong stok
                satuanList: satuan
            });
            render();
        })
        .catch(err => console.error('Gagal mengambil data satuan obat:', err));

        if(hasil) hasil.innerHTML = '';
        if(search) search.value = '';
    };

    // =====================================================
    // 4. RENDER TABEL TRANSAKSI KASIR
    // =====================================================
    function render(){
        let html = '';
        let preview = '';

        items.forEach((i, index) => {
            let opsi = '';
            i.satuanList.forEach(s => {
                opsi += `
                <option value="${s.nama_satuan}" 
                        data-harga="${s.harga}"
                        data-konversi="${s.konversi || 1}"
                        ${i.satuan == s.nama_satuan ? 'selected' : ''}>
                    ${s.nama_satuan} (Rp ${formatRupiah(s.harga)})
                </option>`;
            });

            html += `
            <tr>
                <td>${i.nama}</td>
                <td>
                    <select class="form-control form-control-sm" onchange="setSatuan(${index}, this)">
                        ${opsi}
                    </select>
                </td>
                <td>
                    <input type="text" value="${i.dosis}" onchange="setDosis(${index}, this.value)"
                           class="form-control form-control-sm" placeholder="Contoh: 3x1 tablet setelah makan">
                </td>
                <td style="width:100px;">
                    <input type="number" min="1" value="${i.qty}" onchange="setQty(${index}, this.value)"
                           class="form-control form-control-sm">
                </td>
                <td>
                    <button type="button" onclick="hapus(${index})" class="btn btn-danger btn-sm">&times;</button>
                </td>
            </tr>`;

            preview += `<li>${i.nama} (${i.qty} ${i.satuan}) - Dosis: ${i.dosis || '-'}</li>`;
        });

        const resepListEl = document.getElementById('resepList');
        const previewEl = document.getElementById('preview');

        if(resepListEl) resepListEl.innerHTML = html;
        if(previewEl) previewEl.innerHTML = preview;
    }

    // =====================================================
    // 5. MANIPULASI QUANTITY, DOSIS, DAN SATUAN
    // =====================================================
    window.setQty = function(i, val){
        let v = parseInt(val);
        items[i].qty = (isNaN(v) || v <= 0) ? 1 : v;
        render();
    };

    window.setDosis = function(i, val){
        items[i].dosis = val;
        render();
    };

    window.setSatuan = function(i, select){
        let selected = select.selectedOptions[0];
        items[i].satuan = selected.value;
        items[i].harga = parseFloat(selected.dataset.harga) || 0;
        items[i].konversi = parseInt(selected.dataset.konversi) || 1;
        render();
    };

    window.hapus = function(i){
        items.splice(i, 1);
        render();
    };

    function formatRupiah(angka){
        return new Intl.NumberFormat('id-ID').format(angka || 0);
    }

    // =====================================================
    // 6. PROSES SIMPAN DATA (SINKRON DENGAN BACKEND & DATABASE)
    // =====================================================
    const btnSimpan = document.getElementById('btnSimpan');
    if (btnSimpan) {
        btnSimpan.addEventListener('click', async function(){
            try {
                // VALIDASI 1: Harus pilih minimal 1 obat
                if(items.length === 0){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Obat Kosong',
                        text: 'Silakan cari dan tambahkan obat terlebih dahulu!'
                    });
                    return;
                }

                // VALIDASI 2: Wajib unggah foto resep fisik
                if(!fileResep){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Foto Resep Wajib',
                        text: 'Silakan pilih atau ambil foto lembar resep dokter terlebih dahulu!'
                    });
                    return;
                }

                // Pengumpulan Data dari Form Elemen HTML (Menggunakan Operator Opsional '?')
                const pasien = document.getElementById('pasien')?.value.trim() || 'Umum';
                const dokter = document.getElementById('dokter')?.value.trim() || 'Mandiri';
                const metode = document.getElementById('metode')?.value || 'tunai';
                const bayar  = document.getElementById('bayar')?.value || 0;
                const diskon = document.getElementById('diskon')?.value || 0;
                const ppn    = document.getElementById('ppn')?.checked ? '1' : '';

                // FormData Builder
                const formData = new FormData();
                formData.append('nama_pasien', pasien);
                formData.append('nama_dokter', dokter);
                formData.append('metode_bayar', metode);
                formData.append('bayar', bayar);
                formData.append('diskon', diskon);
                formData.append('ppn', ppn);

                // Sanitize Data Items sebelum di-JSON agar sesuai tipe di database (Decimal/Int)
                const cleanItems = items.map(i => ({
                    id: i.id,
                    qty: Number(i.qty) > 0 ? Number(i.qty) : 1,
                    dosis: i.dosis || '',
                    satuan: i.satuan || 'Tablet',
                    harga: i.harga || 0,
                    konversi: i.konversi || 1
                }));

                // Gunakan key 'produk' agar dibaca oleh fungsi Controller PHP Anda
                formData.append('produk', JSON.stringify(cleanItems));

                // Jika pembayaran transfer, sisipkan file bukti transaksi jika tersedia
                if (metode === 'transfer') {
                    const buktiFile = document.getElementById('bukti_pembayaran')?.files[0];
                    if (buktiFile) {
                        formData.append('bukti_transfer', buktiFile);
                    }
                }

                // File Lembar Resep Medis Fisik
                formData.append('gambar_resep', fileResep);

                // Keamanan CSRF Token CodeIgniter 3
                if(typeof CSRF_NAME !== 'undefined' && typeof CSRF_HASH !== 'undefined'){
                    formData.append(CSRF_NAME, CSRF_HASH);
                }

                // Tampilkan Animasi Loading UI
                Swal.fire({
                    title: 'Memproses Transaksi...',
                    text: 'Sedang menyimpan data ke server, mohon tunggu.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                // Eksekusi Pengiriman HTTP POST request
                const response = await fetch(BASE_URL + 'resep/simpan', {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();

                // Validasi struktur balikan dari server (Harus Murni JSON)
                let json;
                try {
                    json = JSON.parse(text);
                } catch (e) {
                    console.error('Bukan format JSON resmi dari server:', text);
                    throw new Error('Sistem Back-End Error atau mengembalikan teks HTML.');
                }

                // Aksi Berdasarkan Status Response Backend
                if(json.status === 'success' || json.status === 'ok'){
                    await Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil',
                        text: 'Resep dan rincian transaksi kasir berhasil tersimpan!',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Redirect ke halaman detail atau nota/struk cetak
                    window.location.href = json.redirect;
                } else {
                    throw new Error(json.message || 'Gagal menyimpan transaksi resep.');
                }

            } catch (err) {
                console.error('Runtime Error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: err.message || 'Koneksi terputus atau server sedang dalam perbaikan.'
                });
            }
        });
    }
});