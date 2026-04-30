document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("searchUser");
    const tableBody  = document.getElementById("tableUsers");

    if (!searchInput) return;

    searchInput.addEventListener("keyup", function () {
        let keyword = this.value;

        fetch(baseUrl + "users/search?q=" + keyword)
            .then(res => res.json())
            .then(data => {
                let html = "";
                let no = 1;

                data.forEach(user => {
                    html += `
                        <tr>
                            <td>${no++}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.role_id}</td>
                            <td>
                                <a href="${baseUrl}users/edit/${user.id}" class="btn btn-warning btn-sm">Edit</a>
                                <a href="${baseUrl}users/delete/${user.id}" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin hapus?')">Hapus</a>
                            </td>
                        </tr>
                    `;
                });

                tableBody.innerHTML = html;
            });
    });

});