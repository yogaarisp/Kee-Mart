    </div>
    <script>
        // Auto-hide flash messages after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.getElementsByClassName('alert');
                for (let alert of alerts) {
                    alert.style.display = 'none';
                }
            }, 3000);
        });
    </script>
</body>
</html>
