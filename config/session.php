<?php
// Session timeout configuration (seconds)
define('SESSION_TIMEOUT_SECONDS', 20 * 60); // 20 menit
define('SESSION_WARNING_SECONDS', 60); // Tampilkan peringatan 1 menit sebelum logout

// Throttle interval (detik) untuk keepalive dari client agar tidak terlalu sering
define('SESSION_KEEPALIVE_THROTTLE_SECONDS', 30);
