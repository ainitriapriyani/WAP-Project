        </main>
        
        <footer class="mt-auto py-3 bg-light text-center">
            <div class="container">
                <span class="text-muted">© <?php echo date('Y'); ?> Cake Shop Management System</span>
            </div>
        </footer>

    </div> <!-- Akhir dari #content -->
</div> <!-- Akhir dari .wrapper -->

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js (untuk dashboard) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    // Script untuk toggle sidebar
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarCollapse = document.getElementById('sidebarCollapse');
        const sidebar = document.getElementById('sidebar');

        if (sidebarCollapse) {
            sidebarCollapse.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
    });
</script>

</body>
</html>
