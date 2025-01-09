            </div> <!-- End of container-fluid -->
        </div> <!-- End of content -->
    </div> <!-- End of wrapper -->

    <footer class="footer bg-dark text-light py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <span>© 2024 Zamfara Crime Reporting System</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../assets/js/admin.js"></script>
    <script src="../../assets/js/activity-feed.js"></script>

    <script>
$(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });
    
    $('#exportForm').on('submit', function(e) {
        let startDate = new Date($('input[name="start_date"]').val());
        let endDate = new Date($('input[name="end_date"]').val());
        
        if(endDate < startDate) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Invalid Date Range',
                text: 'End date must be after start date'
            });
        }
    });
});
</script>

</body>
</html>
