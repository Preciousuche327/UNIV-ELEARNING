<?php
// Clear OPcache for the sidebar file
$sidebar_path = realpath(__DIR__ . '/../app/views/partials/sidebar.php');

echo "<h3>OPcache Status</h3>";
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    echo "OPcache enabled: " . ($status['opcache_enabled'] ? 'YES' : 'NO') . "<br>";
    
    // Invalidate the sidebar file
    if (opcache_invalidate($sidebar_path, true)) {
        echo "Sidebar cache invalidated successfully.<br>";
    } else {
        echo "Sidebar was not in cache or invalidation failed.<br>";
    }
    
    // Reset all
    opcache_reset();
    echo "Full OPcache reset done.<br>";
} else {
    echo "OPcache is NOT available.<br>";
}

echo "<h3>Sidebar File Check</h3>";
echo "Path: " . $sidebar_path . "<br>";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($sidebar_path)) . "<br>";

// Read and check line 33 of the sidebar to confirm fix is on disk
$lines = file($sidebar_path);
echo "<h3>Lines 33, 36, 39 content:</h3>";
echo "<pre>";
echo "Line 33: " . htmlspecialchars($lines[32]) . "\n";
echo "Line 36: " . htmlspecialchars($lines[35]) . "\n";
echo "Line 39: " . htmlspecialchars($lines[38]) . "\n";
echo "</pre>";

echo "<h3>PHP Version</h3>";
echo phpversion();
?>
