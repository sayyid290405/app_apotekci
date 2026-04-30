document.addEventListener('DOMContentLoaded', function(){

    const input = document.getElementById('searchKategori');
    const table = document.getElementById('kategoriTable');

    let debounce;

    if(input){
        input.addEventListener('keyup', function(){

            clearTimeout(debounce);

            debounce = setTimeout(() => {

                fetch(BASE_URL + 'kategori/search?q=' + this.value)
                .then(res => res.json())
                .then(data => {

                    let html = '';

                    data.forEach((k, i) => {
                        html += `
                        <tr>
                            <td>${i+1}</td>
                            <td><strong>${k.nama_kategori}</strong></td>
                            <td>${k.peruntukan_usia ?? '-'}</td>
                            <td>${k.kelas_obat ?? '-'}</td>
                            <td class="text-center">
                                <a href="${BASE_URL}kategori/edit/${k.id_kategori}" class="btn btn-warning btn-sm">✏️</a>
                                <button onclick="hapus('${BASE_URL}kategori/hapus/${k.id_kategori}')" class="btn btn-danger btn-sm">🗑️</button>
                            </td>
                        </tr>`;
                    });

                    table.innerHTML = html;

                });

            }, 300);
        });
    }

});