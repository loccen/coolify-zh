# 2026-05-22 自动更新验证实例快照

## 实例身份

- 服务器 IP：`172.233.75.42`
- SSH：`root@172.233.75.42`
- 登录方式：root 免密

## 当前访问方式

- 控制台访问入口：`http://172.233.75.42:8000`
- 健康检查接口：`http://172.233.75.42:8000/api/health`
- 当前是 IP 直连 HTTP
- 还未配置域名与 HTTPS

## 最近一次已验证自动更新

- 更新前应用镜像：`ghcr.io/loccen/coolify:4.1.1`
- 更新后应用镜像：`ghcr.io/loccen/coolify:4.1.2`
- `coolify-realtime` 镜像：`ghcr.io/loccen/coolify-realtime:1.0.15`
- 健康检查结果：`OK`

## 当前容器状态快照

```text
coolify            ghcr.io/loccen/coolify:4.1.2             healthy
coolify-db         postgres:15-alpine                       healthy
coolify-redis      redis:7-alpine                           healthy
coolify-realtime   ghcr.io/loccen/coolify-realtime:1.0.15   healthy
coolify-sentinel   ghcr.io/coollabsio/sentinel:0.0.21       healthy
coolify-proxy      traefik:v3.6                             healthy
```

## 新会话执行方案里必须保留的验收口径

如果要证明“检测到更新时，对应镜像已就绪”，至少要在这台机器或同等测试实例上复验以下四项：

1. 先记录升级前 `docker ps` 中 `coolify` 的镜像 tag。
2. 再确认 GHCR 对应目标 tag 已能 `docker buildx imagetools inspect`。
3. 再等待 `versions.json` 发布，观察实例出现 `new_version_available=true`。
4. 自动更新完成后，再次核对：
   - `docker ps` 中 `coolify` 的新 tag
   - `/api/health` 返回 `OK`
   - 页面入口仍可访问
