## AWS EC2インスタンスにSSHでログイン
```bash
 ssh ec2-user@{IPアドレス} -i {秘密鍵ファイルのパス}　
```

## vimのインストール
```bash
sudo yum install vim -y
```

- vimをインストールしたのち、設定ファイルを書く
```bash
vim ~/.vimrc
 ```
中身は授業資料第2回のvimの設定のおすすめ設定などを追加するなどします。

 ## Docker
 ### Dockerインストールと自動起動化
```bash
sudo yum install -y docker
sudo systemctl start docker
sudo systemctl enable docker
```

sudoをつけずにDockerコマンドを叩けるように、docekrグループに追加する
```bash
sudo usermod -a -G docker ec2-user
```

一度ログアウトして、再起動する
```bash
exit logout
ssh ec2-user@{IPアドレス} -i {秘密鍵ファイルのパス}
 ```

### Docker Composeのインストール方法
```bash
DOCKER_CONFIG=${DOCKER_CONFIG:-$HOME/.docker}
mkdir -p $DOCKER_CONFIG/cli-plugins
curl -SL https://github.com/docker/compose/releases/download/v5.1.2/docker-compose-linux-x86_64 -o $DOCKER_CONFIG/cli-plugins/docker-compose
chmod +x $DOCKER_CONFIG/cli-plugins/docker-compose
```

インストールできたか確認
バージョンが表示されたらインストールできています
```bash
docker compose version
```

## dockertestを作成
- 作業用ディレクトリdockertestを作成し、作業用ディレクトリに移動します

```bash
mkdir dockertest
cd dockertest
```

## screen
screenコマンドをたたいてscreenを起動して作業をします。
screenは標準で入っています。
```bash
screen
```

### .screenrc
設定ファイルを編集してカスタマイズします。内容は第3回の授業を参考にします。
```bash
vim ~/.screenrc
```


# 起動するための準備
## Gitインストール
```bash
sudo yum install git -y
```

## git clone
```bash
git clone https://github.com/kurozu578/2026_suiyou-12-gen.git
```

## clone出来たら
`docker compose build`する

- build中にエラーが発生した場合
```bash
mkdir -p ~/.docker/cli-plugins
ARCH=$(uname -m | sed 's/x86_64/amd64/;s/aarch64/arm64/')
BUILDX_URL=$(curl -s https://api.github.com/repos/docker/buildx/releases/latest | grep "browser_download_url.*linux-$ARCH" | cut -d '"' -f 4)
curl -L $BUILDX_URL -o ~/.docker/cli-plugins/docker-buildx
chmod +x ~/.docker/cli-plugins/docker-buildx
```
これらを行ったのち、`docker compose build`した後に、`docker compose up`をする

## mysql
```bash
 docker compose exec mysql mysql example_db
```
これで起動します。

### テーブルの作成
```bash
CREATE TABLE `bbs_entries` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `body` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);
```
- データベースの掲示板用テーブルに画像を保存するパスを入れておく
```bash
ALTER TABLE `bbs_entries` ADD COLUMN image_filename TEXT DEFAULT NULL;
```

これらを行ったのち、`docker compose build`した後に、`docker compose up`をする

## ブラウザから確認
`http://EC2インスタンスのIPアドレス/bbsimagetest.php`にブラウザからアクセスして動作を確認

うまくいってたら完成

