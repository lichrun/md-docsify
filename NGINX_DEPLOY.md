# Nginx 部署说明

## 🚀 快速部署

### 1. 服务器环境要求

- **操作系统**: Ubuntu 18.04+ / CentOS 7+ / Debian 9+
- **Web服务器**: Nginx 1.18+
- **PHP**: 7.4+ (推荐8.0+)
- **PHP扩展**: fileinfo, json, mbstring

### 2. 安装必要软件

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install nginx php-fpm php-mbstring php-json php-fileinfo

# CentOS/RHEL
sudo yum install nginx php-fpm php-mbstring php-json php-fileinfo
```

### 3. 配置Nginx

#### 方法1: 使用提供的配置文件

1. 将项目中的 `nginx.conf` 文件复制到 `/etc/nginx/sites-available/`
2. 修改配置文件中的域名和路径
3. 创建软链接到 `sites-enabled`

```bash
sudo cp nginx.conf /etc/nginx/sites-available/docsify
sudo ln -s /etc/nginx/sites-available/docsify /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default  # 删除默认配置
```

#### 方法2: 手动配置

在 `/etc/nginx/sites-available/` 中创建配置文件：

```nginx
server {
    listen 80;
    server_name your-domain.com;  # 替换为您的域名
    root /var/www/docsify;        # 替换为您的项目路径
    index index.html;

    # 禁用缓存
    add_header Cache-Control "no-cache, no-store, must-revalidate" always;
    add_header Pragma "no-cache" always;
    add_header Expires 0 always;

    # 处理PHP文件
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # 主要路由
    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

### 4. 配置PHP-FPM

编辑 `/etc/php/7.4/fpm/pool.d/www.conf`：

```ini
user = www-data
group = www-data
listen = /run/php/php7.4-fpm.sock
listen.owner = www-data
listen.group = www-data
```

### 5. 设置文件权限

```bash
# 设置项目目录权限
sudo chown -R www-data:www-data /var/www/docsify
sudo chmod -R 755 /var/www/docsify
sudo chmod 644 /var/www/docsify/*.php
sudo chmod 644 /var/www/docsify/*.html
sudo chmod 644 /var/www/docsify/*.md

# 确保docs文件夹可写
sudo chmod 755 /var/www/docsify/docs/
```

### 6. 重启服务

```bash
sudo systemctl restart php7.4-fpm
sudo systemctl restart nginx
sudo systemctl enable nginx
sudo systemctl enable php7.4-fpm
```

## 🔧 故障排除

### 常见问题

#### 1. 404错误
- 检查Nginx配置文件路径是否正确
- 确认 `try_files` 指令配置正确
- 检查文件权限

#### 2. 500错误
- 查看Nginx错误日志: `sudo tail -f /var/log/nginx/error.log`
- 查看PHP-FPM错误日志: `sudo tail -f /var/log/php7.4-fpm.log`
- 检查PHP语法: `php -l filename.php`

#### 3. 权限问题
```bash
# 重新设置权限
sudo chown -R www-data:www-data /var/www/docsify
sudo chmod -R 755 /var/www/docsify
sudo chmod 644 /var/www/docsify/*.php
```

#### 4. 缓存问题
- 清除浏览器缓存
- 检查Nginx缓存配置
- 确认HTTP头设置正确

### 日志位置

- **Nginx访问日志**: `/var/log/nginx/access.log`
- **Nginx错误日志**: `/var/log/nginx/error.log`
- **PHP-FPM日志**: `/var/log/php7.4-fpm.log`

## 📁 项目结构

```
/var/www/docsify/
├── index.html          # 主页面
├── HOME.md             # 首页内容
├── _sidebar.md         # 侧边栏导航
├── upload.php          # 文件上传处理
├── get_files.php       # 获取文件列表
├── delete_file.php     # 删除文件处理
├── docs/               # 文档存储文件夹 (已加入.gitignore)
├── nginx.conf          # Nginx配置模板
└── deploy.sh           # 部署脚本
```

## 🔒 安全建议

1. **HTTPS**: 配置SSL证书，强制HTTPS访问
2. **防火墙**: 只开放必要端口 (80, 443)
3. **文件上传**: 限制文件大小和类型
4. **权限控制**: 最小化文件权限
5. **日志监控**: 定期检查访问和错误日志

## 📚 相关链接

- [Nginx官方文档](https://nginx.org/en/docs/)
- [PHP-FPM配置](https://www.php.net/manual/en/install.fpm.php)
- [Docsify文档](https://docsify.js.org/)
