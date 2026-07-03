</div> <!-- END CONTENT -->

<!-- CORE -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- BASE URL -->
<script>
const BASE_URL = "<?= base_url() ?>";
const CSRF_NAME = "<?= $this->security->get_csrf_token_name(); ?>";
const CSRF_HASH = "<?= $this->security->get_csrf_hash(); ?>";
</script>


<!-- DYNAMIC JS -->
<?php if(isset($js)): ?>
<script src="<?= base_url('assets/js/'.$js) ?>"></script>
<?php endif; ?>


<!-- GLOBAL -->
<script src="<?= base_url('assets/js/global.js') ?>"></script>


<!-- SWEET ALERT FLASH -->
<?php if($this->session->flashdata('success')): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= $this->session->flashdata('success') ?>',
    timer: 2000,
    showConfirmButton: false
});
</script>
<?php endif; ?>

<?php if($this->session->flashdata('error')): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= $this->session->flashdata('error') ?>'
});
</script>
<?php endif; ?>

</body>
</html>