<?php
header('Content-Type: application/json');

// Function to sanitize shell command output
function sanitizeOutput($output) {
    return htmlspecialchars(trim($output));
}

// Function to get disk temperature safely
function getDiskTemperature($disk) {
    $diskTemp = @shell_exec("sudo smartctl -A {$disk} 2>&1");
    if (strpos($diskTemp, 'Temperature_Celsius') !== false) {
        preg_match('/Temperature_Celsius\s+\d+\s+\d+\s+\d+\s+Pre-fail\s+Always\s+-\s+(\d+)/', $diskTemp, $matches);
    } elseif (strpos($diskTemp, 'Airflow_Temperature_Cel') !== false) {
        preg_match('/Airflow_Temperature_Cel\s+\d+\s+\d+\s+\d+\s+Old_age\s+Always\s+-\s+(\d+)/', $diskTemp, $matches);
    }
    return isset($matches[1]) ? sanitizeOutput($matches[1]) : 'N/A';
}

// Function to get disk information
function getDiskInfo() {
    $diskInfo = [];
    $diskData = shell_exec("df -h --output=source,pcent,size");
    $lines = explode("\n", $diskData);

    foreach ($lines as $line) {
        if (preg_match('/^\/dev\//', $line)) {
            $parts = preg_split('/\s+/', $line);
            $temperature = getDiskTemperature($parts[0]);
            $diskInfo[] = [
                'name' => sanitizeOutput($parts[0]),
                'usedSpacePercentage' => rtrim($parts[1], '%'),
                'totalSpace' => sanitizeOutput($parts[2]),
                'temperature' => $temperature,
            ];
        }
    }
    return $diskInfo;
}

// Function to get server uptime
function getUptime() {
    $uptime = shell_exec("uptime -p");
    return sanitizeOutput($uptime);
}

// Function to get service information dynamically
function getServicesInfo() {
    $servicesToCheck = ['smb', 'plexmediaserver', 'sshguard', 'rsync', 'ufw', 'apache2'];
    $servicesInfo = [];

    foreach ($servicesToCheck as $service) {
        $status = shell_exec("systemctl is-active {$service}");
        $isActive = trim($status) === 'active';
        $servicesInfo[] = [
            'name' => sanitizeOutput($service),
            'active' => $isActive,
        ];
    }
    usort($servicesInfo, fn($a, $b) => $b['active'] <=> $a['active']);
    return $servicesInfo;
}

function getCpuInfo() {
    // Get CPU name
    $cpuName = shell_exec("lscpu | grep 'Model name' | awk -F ':' '{print $2}'");
    $cpuName = trim($cpuName);

    // Get overall CPU usage
    $cpuUsage = shell_exec("top -bn1 | grep 'Cpu(s)' | awk '{print $2 + $4}'");
    $cpuUsage = round($cpuUsage);

    // Get individual thread usage
    $threadUsage = shell_exec("mpstat -P ALL 1 1 | grep -E '^[ 0-9]+ '");
    $threadLines = explode("\n", trim($threadUsage));
    $threads = [];

    foreach ($threadLines as $line) {
        if (preg_match('/^ *\d+ +\d+\.\d+ +\d+\.\d+ +\d+\.\d+ +\d+\.\d+ +\d+\.\d+ +(\d+\.\d+)/', $line, $matches)) {
            $threads[] = round(100 - $matches[1]); // CPU usage = 100% - idle time
        }
    }

    return [
        'name' => $cpuName,
        'usage' => $cpuUsage,
        'threads' => array_map(function ($usage) {
            return ['usage' => $usage];
        }, $threads),
    ];
}

// Update data array
$data = [
    'disks' => getDiskInfo(),
    'services' => getServicesInfo(),
    'uptime' => getUptime(),
    'cpu' => getCpuInfo(),
];

echo json_encode($data);

?>