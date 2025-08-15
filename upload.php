<?php
header('Content-Type: application/json');

// 检查是否有文件上传
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => '文件上传失败']);
    exit;
}

$file = $_FILES['file'];
$filename = $file['name'];

// 检查文件类型
if (pathinfo($filename, PATHINFO_EXTENSION) !== 'md') {
    echo json_encode(['success' => false, 'message' => '只允许上传.md文件']);
    exit;
}

// 检查docs文件夹是否存在，不存在则创建
$docsDir = 'docs';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
}

// 生成唯一的文件名（避免重名）
$baseName = pathinfo($filename, PATHINFO_FILENAME);
$extension = pathinfo($filename, PATHINFO_EXTENSION);
$counter = 1;
$newFilename = $filename;

while (file_exists($docsDir . '/' . $newFilename)) {
    $newFilename = $baseName . '_' . $counter . '.' . $extension;
    $counter++;
}

// 移动上传的文件到docs文件夹
if (move_uploaded_file($file['tmp_name'], $docsDir . '/' . $newFilename)) {
    // 更新sidebar
    updateSidebar();
    
    echo json_encode(['success' => true, 'message' => '文件上传成功', 'filename' => $newFilename]);
} else {
    echo json_encode(['success' => false, 'message' => '文件保存失败']);
}

// 更新sidebar函数
function updateSidebar() {
    $docsDir = 'docs';
    $sidebarContent = '';
    
    // 获取docs文件夹中的所有.md文件
    $files = glob($docsDir . '/*.md');
    
    // 按修改时间倒序排序
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    // 生成sidebar内容
    foreach ($files as $file) {
        $filename = basename($file, '.md');
        $displayName = str_replace('_', ' ', $filename); // 将下划线替换为空格
        $sidebarContent .= "- [" . ucfirst($displayName) . "](" . $filename . ")\n";
    }
    
    // 写入_sidebar.md文件
    file_put_contents('_sidebar.md', $sidebarContent);
}
?>
