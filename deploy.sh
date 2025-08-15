#!/bin/bash

# 文档系统部署脚本
# 使用方法: ./deploy.sh [环境]

ENVIRONMENT=${1:-production}
PROJECT_NAME="docsify-docs-system"

echo "🚀 开始部署 $PROJECT_NAME 到 $ENVIRONMENT 环境..."

# 检查Git状态
if [ -n "$(git status --porcelain)" ]; then
    echo "⚠️  检测到未提交的更改，请先提交或暂存更改"
    git status --short
    exit 1
fi

# 获取当前分支
CURRENT_BRANCH=$(git branch --show-current)
echo "📍 当前分支: $CURRENT_BRANCH"

# 拉取最新代码
echo "📥 拉取最新代码..."
git pull origin $CURRENT_BRANCH

# 检查是否有新提交
if [ "$(git rev-list HEAD...origin/$CURRENT_BRANCH --count)" -eq 0 ]; then
    echo "✅ 代码已是最新版本"
else
    echo "🔄 代码已更新"
fi

# 创建docs文件夹（如果不存在）
if [ ! -d "docs" ]; then
    echo "📁 创建docs文件夹..."
    mkdir -p docs
    chmod 755 docs
fi

# 设置文件权限
echo "🔐 设置文件权限..."
chmod 644 *.php
chmod 644 *.html
chmod 644 *.md
chmod 755 docs/

# 检查PHP配置
echo "🔍 检查PHP配置..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n1 | cut -d " " -f 2 | cut -d "." -f 1,2)
    echo "✅ PHP版本: $PHP_VERSION"
else
    echo "❌ PHP未安装或不在PATH中"
fi

# 检查Nginx配置
echo "🔍 检查Nginx配置..."
if command -v nginx &> /dev/null; then
    echo "✅ Nginx已安装"
    echo "📝 请确保已配置nginx.conf文件"
else
    echo "❌ Nginx未安装或不在PATH中"
fi

# 显示部署信息
echo ""
echo "🎉 部署完成！"
echo ""
echo "📋 下一步操作："
echo "1. 将nginx.conf配置添加到您的Nginx站点配置中"
echo "2. 重启Nginx服务: sudo systemctl reload nginx"
echo "3. 访问您的网站测试功能"
echo ""
echo "🔧 常用命令："
echo "- 查看Nginx状态: sudo systemctl status nginx"
echo "- 查看Nginx错误日志: sudo tail -f /var/log/nginx/error.log"
echo "- 查看PHP-FPM状态: sudo systemctl status php7.4-fpm"
echo ""
echo "📁 项目结构："
echo "- docs/ (上传的文档，已加入.gitignore)"
echo "- *.php (PHP后端脚本)"
echo "- *.md (Markdown文档)"
echo "- index.html (主页面)"
echo "- nginx.conf (Nginx配置模板)"
