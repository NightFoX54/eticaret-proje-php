                </div><!-- main-content end -->
            </div><!-- main col end -->
        </div><!-- row end -->
    </div><!-- container-fluid end -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Set active class to current page in sidebar
        $(document).ready(function() {
            // Get current path and find target link
            var path = window.location.pathname;
            var page = path.split("/").pop();
            
            // Add active class to target link
            $('.sidebar .nav-link').each(function() {
                var href = $(this).attr('href');
                if (page === href) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
</body>
</html> 