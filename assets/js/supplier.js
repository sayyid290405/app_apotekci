const searchInput = document.querySelector('input[name="q"]');
const tableBody = document.getElementById('supplierTable');

let debounceTimer;

searchInput.addEventListener('keyup', function(){

    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {

        fetch(BASE_URL + 'supplier/search?q=' + this.value)
        .then(res => res.json())
        .then(data => {

            let html = '';

            if(data.length === 0){
                html = `<tr><td colspan="6" class="text-center">Data tidak ditemukan</td></tr>`;
            }

            data.forEach((s, index) => {
                html += `
                <tr>
                    <td>${index+1}</td>
                    <td><strong>${s.nama_supplier}</strong></td>
                    <td>${s.legalitas ?? '-'}</td>
                    <td>${s.alamat ?? '-'}</td>
                    <td>${s.kontak ?? '-'}</td>
                    <td>
                        <a href="${BASE_URL}supplier/detail/${s.id_supplier}" class="btn btn-info btn-sm">👁️</a>
                        <a href="${BASE_URL}supplier/edit/${s.id_supplier}" class="btn btn-warning btn-sm">✏️</a>
                        <button onclick="hapus('${BASE_URL}supplier/hapus/${s.id_supplier}')" class="btn btn-danger btn-sm">🗑️</button>
                    </td>
                </tr>
                `;
            });

            tableBody.innerHTML = html;

        });

    }, 400); // debounce biar tidak spam server

});