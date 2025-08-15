<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$result = [
    'docs_writable' => false,
    'sidebar_writable' => false,
    'php_version' => PHP_VERSION,
    'server_info' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'file_permissions' => []
];

// 检查docs文件夹权限
$docsDir = 'docs';
if (is_dir($docsDir)) {
    $result['docs_writable'] = is_writable($docsDir);
    $result['file_permissions']['docs'] = [
        'exists' => true,
        'writable' => is_writable($docsDir),
        'permissions' => substr(sprintf('%o', fileperms($docsDir)), -4)
    ];
} else {
    $result['docs_writable'] = false;
    $result['file_permissions']['docs'] = [
        'exists' => false,
        'writable' => false,
        'permissions' => 'N/A'
    ];
}

// 检查_sidebar.md文件权限
$sidebarFile = '_sidebar.md';
if (file_exists($sidebarFile)) {
    $result['sidebar_writable'] = is_writable($sidebarFile);
    $result['file_permissions']['sidebar'] = [
        'exists' => true,
        'writable' => is_writable($sidebarFile),
        'permissions' => substr(sprintf('%o', fileperms($sidebarFile)), -4)
    ];
} else {
    $result['sidebar_writable'] = false;
    $result['file_permissions']['sidebar'] = [
        'exists' => false,
        'writable' => false,
        'permissions' => 'N/A'
    ];
}

// 检查PHP扩展
$result['extensions'] = [
    'fileinfo' => extension_loaded('fileinfo'),
    'json' => extension_loaded('json'),
    'mbstring' => extension_loaded('mbstring')
];

// 检查上传目录权限
$uploadDir = sys_get_temp_dir();
$result['upload_dir'] = [
    'path' => $uploadDir,
    'writable' => is_writable($uploadDir),
    'permissions' => substr(sprintf('%o', fileperms($uploadDir)), -4)
];

echo json_encode($result, JSON_PRETTY_PRINT);
?>
