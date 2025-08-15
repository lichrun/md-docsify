<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>部署状态检查</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 文档系统部署状态检查</h1>
        
        <div class="section">
            <h2>📋 系统信息</h2>
            <div class="status info">
                <strong>PHP版本:</strong> <?php echo PHP_VERSION; ?><br>
                <strong>服务器软件:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
                <strong>操作系统:</strong> <?php echo PHP_OS; ?><br>
                <strong>当前时间:</strong> <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>

        <div class="section">
            <h2>🔧 PHP扩展检查</h2>
            <?php
            $required_extensions = ['fileinfo', 'json', 'mbstring'];
            foreach ($required_extensions as $ext) {
                if (extension_loaded($ext)) {
                    echo "<div class='status success'>✓ {$ext} 扩展已加载</div>";
                } else {
                    echo "<div class='status error'>✗ {$ext} 扩展未加载</div>";
                }
            }
            ?>
        </div>

        <div class="section">
            <h2>📁 文件权限检查</h2>
            <?php
            $paths_to_check = [
                'docs' => 'docs文件夹',
                '_sidebar.md' => '侧边栏文件',
                'upload.php' => '上传脚本',
                'get_files.php' => '文件列表脚本',
                'delete_file.php' => '删除脚本'
            ];

            foreach ($paths_to_check as $path => $description) {
                if (file_exists($path)) {
                    if (is_writable($path)) {
                        echo "<div class='status success'>✓ {$description} ({$path}) - 可写</div>";
                    } else {
                        echo "<div class='status warning'>⚠ {$description} ({$path}) - 不可写</div>";
                    }
                } else {
                    echo "<div class='status error'>✗ {$description} ({$path}) - 不存在</div>";
                }
            }
            ?>
        </div>

        <div class="section">
            <h2>🌐 网络功能测试</h2>
            <?php
            // 测试文件上传功能
            if (is_writable('docs')) {
                echo "<div class='status success'>✓ docs文件夹可写，文件上传功能正常</div>";
            } else {
                echo "<div class='status error'>✗ docs文件夹不可写，文件上传功能异常</div>";
            }

            // 测试sidebar更新功能
            if (is_writable('_sidebar.md')) {
                echo "<div class='status success'>✓ 侧边栏文件可写，自动更新功能正常</div>";
            } else {
                echo "<div class='status error'>✗ 侧边栏文件不可写，自动更新功能异常</div>";
            }
            ?>
        </div>

        <div class="section">
            <h2>📊 当前文档统计</h2>
            <?php
            if (is_dir('docs')) {
                $files = glob('docs/*.md');
                $count = count($files);
                if ($count > 0) {
                    echo "<div class='status info'>📚 当前共有 {$count} 个文档文件</div>";
                    echo "<div class='code'>";
                    foreach ($files as $file) {
                        $filename = basename($file);
                        $size = filesize($file);
                        $time = date('Y-m-d H:i:s', filemtime($file));
                        echo "• {$filename} ({$size} bytes, 修改时间: {$time})<br>";
                    }
                    echo "</div>";
                } else {
                    echo "<div class='status info'>📚 docs文件夹为空，暂无文档</div>";
                }
            } else {
                echo "<div class='status error'>✗ docs文件夹不存在</div>";
            }
            ?>
        </div>

        <div class="section">
            <h2>🔍 调试信息</h2>
            <div class="code">
                <strong>当前工作目录:</strong> <?php echo getcwd(); ?><br>
                <strong>脚本路径:</strong> <?php echo __FILE__; ?><br>
                <strong>内存限制:</strong> <?php echo ini_get('memory_limit'); ?><br>
                <strong>上传最大文件大小:</strong> <?php echo ini_get('upload_max_filesize'); ?><br>
                <strong>POST最大大小:</strong> <?php echo ini_get('post_max_size'); ?><br>
                <strong>最大执行时间:</strong> <?php echo ini_get('max_execution_time'); ?>秒
            </div>
        </div>

        <div class="section">
            <h2>📝 部署建议</h2>
            <?php
            $issues = [];
            
            if (!is_writable('docs')) {
                $issues[] = "docs文件夹权限不足，需要设置可写权限";
            }
            
            if (!is_writable('_sidebar.md')) {
                $issues[] = "_sidebar.md文件权限不足，需要设置可写权限";
            }
            
            if (!extension_loaded('fileinfo')) {
                $issues[] = "fileinfo扩展未加载，可能影响文件类型检测";
            }
            
            if (empty($issues)) {
                echo "<div class='status success'>🎉 系统配置正常，可以正常使用！</div>";
            } else {
                echo "<div class='status warning'>⚠️ 发现以下问题需要解决：</div>";
                echo "<ul>";
                foreach ($issues as $issue) {
                    echo "<li>{$issue}</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>

        <div class="section">
            <h2>🔗 快速链接</h2>
            <div class="code">
                <a href="index.html" target="_blank">📖 主页面</a> | 
                <a href="test_upload.html" target="_blank">🧪 功能测试</a> | 
                <a href="check_permissions.php" target="_blank">🔐 权限检查</a>
            </div>
        </div>
    </div>
</body>
</html>
