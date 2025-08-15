<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '无效的请求方法']);
    exit;
}

$filename = $_POST['filename'] ?? '';
if (empty($filename)) {
    echo json_encode(['success' => false, 'message' => '文件名不能为空']);
    exit;
}

// 安全检查：确保文件名只包含字母、数字、下划线和点
if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $filename)) {
    echo json_encode(['success' => false, 'message' => '无效的文件名']);
    exit;
}

$filepath = 'docs/' . $filename;

// 检查文件是否存在
if (!file_exists($filepath)) {
    echo json_encode(['success' => false, 'message' => '文件不存在']);
    exit;
}

// 删除文件
if (unlink($filepath)) {
    // 更新sidebar
    updateSidebar();
    
    echo json_encode(['success' => true, 'message' => '文件删除成功']);
} else {
    echo json_encode(['success' => false, 'message' => '文件删除失败']);
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
    if (file_put_contents('_sidebar.md', $sidebarContent) === false) {
        error_log('无法写入_sidebar.md文件');
    }
    
    // 清除可能的缓存
    if (function_exists('clearstatcache')) {
        clearstatcache();
    }
}
?>
