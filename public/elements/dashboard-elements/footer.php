<?php

/**
 * THIS IS THE DASHBOARD FOOTER
 */
?>

<!-- Custom scrollbar library -->
<script src="https://unpkg.com/simplebar@latest/dist/simplebar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>


<!-- Global custom javascript -->
<script src="../scripts/dashboard.js"></script>

<!-- Custom JavaScript files -->
<?php
// Use this variable to add specific JS files (e.g. trips.js, events.js,...)
echo !empty($insideFooter) ? $insideFooter : null;
?>



</body>

</html>