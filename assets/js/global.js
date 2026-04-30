// ==========================
// FORMAT RUPIAH
// ==========================
function rupiah(angka){
    return new Intl.NumberFormat('id-ID').format(angka);
}

// ==========================
// SWEET ALERT HAPUS
// ==========================
function hapus(url){
    Swal.fire({
        title: 'Hapus Data?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href = url;
        }
    });
}