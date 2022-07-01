<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="_tmp/logo.svg" height="100px">
    </a>
    <h1 align="center">VT-Admin 服务端模板</h1>
    <br>
</p>

这个模板用于 `vt-admin-app` 的接口服务，基于 [Yii 2](http://www.yiiframework.com/) 框架。

## Docker下使用

**复制配置**

```bash
cp .env.docker .env
```


**修改配置**

将 `.env` 中出现的配置换成自己的实际参数，默认不用改


**启动**

直接在根目录下执行如下命令进行启动

```bash
docker-compose up -d
```


**导入SQL**

在启动完成后，需要初始化数据，数据库连接信息如下，端口可自行修改 `docker-compose.yml` 文件

```txt
地址：127.0.0.1
端口：53306
用户：root
密码：空
```

连接成功后请新建一个编码为 `utf8mb4` 名为 `vt-admin` 的数据库，然将 `_tmp/init.sql` 中的内容用数据库管理工具运行一下


**浏览**

打开 [http://127.0.0.1:50080](http://127.0.0.1:50080) 


## 非Docker下使用

注意：此方式仅适用于熟悉PHP相关人员


**安装依赖**


```bash
php composer.phar install -v
```


**复制配置**

```bash
cp .env.example .env
```


**修改配置**

将 `.env` 中出现的配置换成自己的实际参数


**导入SQL**

新建一个数据库，并将 `_tmp/init.sql` 中的内容用数据库管理工具运行一下
