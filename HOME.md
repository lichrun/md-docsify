# Markdown 文档系统

> 可以上传和解析MD文件

## 文件上传

<form action="upload.php" method="post" enctype="multipart/form-data" style="margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
  <div style="margin-bottom: 15px;">
    <label for="file" style="display: block; margin-bottom: 5px; font-weight: bold;">选择Markdown文件：</label>
    <input type="file" id="file" name="file" accept=".md" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px;">
  </div>
  <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer;">上传文件</button>
</form>

## 最近上传的文件

<div id="fileList">
  <!-- 文件列表将通过JavaScript动态加载 -->
</div>

<script>
// 加载文件列表
function loadFileList() {
  fetch('get_files.php')
    .then(response => response.json())
    .then(files => {
      const fileList = document.getElementById('fileList');
      if (files.length === 0) {
        fileList.innerHTML = '<p>暂无文件</p>';
        return;
      }
      
      let html = '<div style="margin-top: 20px;">';
      files.forEach(file => {
        const date = new Date(file.upload_time).toLocaleString('zh-CN');
        html += `
          <div style="border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center;">
            <div>
              <strong>${file.name}</strong><br>
              <small style="color: #666;">上传时间: ${date}</small>
            </div>
            <button onclick="deleteFile('${file.name}')" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">删除</button>
          </div>
        `;
      });
      html += '</div>';
      fileList.innerHTML = html;
    })
    .catch(error => {
      console.error('加载文件列表失败:', error);
      document.getElementById('fileList').innerHTML = '<p style="color: red;">加载文件列表失败</p>';
    });
}

// 删除文件
function deleteFile(filename) {
  if (confirm('确定要删除文件 ' + filename + ' 吗？')) {
    fetch('delete_file.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'filename=' + encodeURIComponent(filename)
    })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('文件删除成功');
        loadFileList();
        // 刷新页面以更新sidebar
        location.reload();
      } else {
        alert('删除失败: ' + result.message);
      }
    })
    .catch(error => {
      console.error('删除文件失败:', error);
      alert('删除文件失败');
    });
  }
}

// 页面加载完成后加载文件列表
document.addEventListener('DOMContentLoaded', loadFileList);
</script>

