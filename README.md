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
 ### Dockerインストール

## dockertestを作成
- 作業用ディレクトリdockertestを作成し、作業用ディレクトリに移動します。
```bash
mkdir dockertest
cd dockertest
```

### screen
- screenコマンドをたたいてscreenを起動して作業をします。
　screenは標準で入っています。
```bash
screen
```

