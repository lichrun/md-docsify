<?php
header('Content-Type: application/json');

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
