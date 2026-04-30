<style>
    #hasil {
    max-height: 250px;
    overflow-y: auto;
    border-radius: 8px;
}

#hasil .list-group-item:hover {
    background: #f1f5f9;
    cursor: pointer;
}

.table td, .table th {
    vertical-align: middle;
}
</style>
<div class="container-fluid">

<div class="card shadow-sm border-0">
<div class="card-body">

<h4 class="mb-4">📋 Input Resep Dokter</h4>

<div class="row g-4">

<!-- LEFT -->
<div class="col-lg-8">

    <!-- PASIEN + DOKTER -->
    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" id="pasien" class="form-control" placeholder="Nama Pasien">
        </div>
        <div class="col-md-6">
            <input type="text" id="dokter" class="form-control" placeholder="Nama Dokter">
        </div>
    </div>

    <!-- UPLOAD -->
    <div class="mb-3">
        <input type="file" id="gambarResep" class="form-control">
    </div>

    <div class="mb-3 text-center">
        <img id="previewResep" class="img-fluid rounded shadow-sm"
             style="max-height:220px; display:none;">
    </div>

    <!-- SEARCH -->
    <div class="mb-3 position-relative">
        <input type="text" id="search" class="form-control ps-4" placeholder="🔍 Cari obat...">
        <div id="hasil" class="list-group position-absolute w-100 shadow-sm"
             style="z-index:999;"></div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width:30%">Obat</th>
                    <th style="width:20%">Satuan</th>
                    <th style="width:20%">Dosis</th>
                    <th style="width:15%">Qty</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody id="resepList"></tbody>
        </table>
    </div>

</div>

<!-- RIGHT -->
<div class="col-lg-4">

<div class="card shadow-sm border-0">
<div class="card-body">

<h5 class="mb-3">🧾 Ringkasan</h5>

<ul id="preview" class="list-group mb-3 small"></ul>

<button id="btnSimpan" class="btn btn-success w-100">
💾 Simpan Resep
</button>

</div>
</div>

</div>

</div>

</div>
</div>

</div>