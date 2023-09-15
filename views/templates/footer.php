</div>
</div>
<!--end page wrapper -->
<!--start overlay-->
<div class="overlay toggle-icon"></div>
<!--end overlay-->
<!--Start Back To Top Button-->
<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
<!--End Back To Top Button-->
<footer class="page-footer">
    <p class="mb-0">Derechos Reservados © <?php echo date('Y'); ?>. - UES FMP.</p>
</footer>
</div>
<!--end wrapper-->

<!--end switcher-->
<!-- Bootstrap JS -->
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
<!--plugins-->
<script src="<?php echo BASE_URL; ?>assets/js/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/plugins/simplebar/js/simplebar.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="<?php echo BASE_URL; ?>assets/plugins/chartjs/js/Chart.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/plugins/chartjs/js/Chart.extension.js"></script>
<!--app JS-->
<script src="<?php echo BASE_URL; ?>assets/js/app.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/all.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/DataTables/datatables.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/botones-perzonalizados.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/ckeditor.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/funciones.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/jquery-ui.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/plugins/fullcalendar/js/main.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/es.js"></script>
<script>
    const base_url = '<?php echo BASE_URL; ?>';
</script>
<?php if (!empty($data['busqueda'])) { ?>
    <script>
        const nombreKey = '<?php echo $data['carrito']; ?>';
    </script>
    <script src="<?php echo BASE_URL . 'assets/js/' . $data['busqueda']; ?>"></script>
<?php } ?>
<?php if (!empty($data['script'])) { ?>
    <script src="<?php echo BASE_URL . 'assets/js/modulos/' . $data['script']; ?>"></script>
<?php } ?>
</body>

</html>