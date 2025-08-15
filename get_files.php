<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$docsDir = 'docs';
$files = [];

if (is_dir($docsDir)) {
    // 获取所有.md文件
    $mdFiles = glob($docsDir . '/*.md');
    
    // 按修改时间倒序排序
    usort($mdFiles, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    foreach ($mdFiles as $file) {
        $files[] = [
            'name' => basename($file),
            'upload_time' => date('Y-m-d H:i:s', filemtime($file)),
            'size' => filesize($file)
        ];
    }
}

echo json_encode($files);
?>
