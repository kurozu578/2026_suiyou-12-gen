<?php
$dbh = new PDO('mysql:host=mysql;dbname=example_db', 'root', '');

if (isset($_POST['body'])) {
  // POSTで送られてくるフォームパラメータ body がある場合

  $image_filename = null;
  if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
    // アップロードされた画像がある場合

    // 一時ファイル(tmp_name)の「本当の中身」をチェックする
    $mime_type = mime_content_type($_FILES['image']['tmp_name']);

    //取得した「本当のMIMEタイプ」がimage/から始まっているかチェック
    if (preg_match('/^image\//', $mime_type) !== 1) {
      // アップロードされたものが画像ではなかった場合処理を強制的に終了
      header("HTTP/1.1 302 Found");
      header("Location: ./bbsimagetest.php");
      return;
    }

    // 元のファイル名から拡張子を取得
    $pathinfo = pathinfo($_FILES['image']['name']);
    $extension = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
    // 新しいファイル名を決める。他の投稿の画像ファイルと重複しないように時間+乱数で決める。
    $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.' . $extension;
    $filepath =  '/var/www/upload/image/' . $image_filename;
    move_uploaded_file($_FILES['image']['tmp_name'], $filepath);
  }

  // insertする
  $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body, image_filename) VALUES (:body, :image_filename)");
  $insert_sth->execute([
    ':body' => $_POST['body'],
    ':image_filename' => $image_filename,
  ]);

  // 処理が終わったらリダイレクトする
  // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
  header("HTTP/1.1 302 Found");
  header("Location: ./bbsimagetest.php");
  return;
}

// いままで保存してきたものを取得
$select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');
$select_sth->execute();
?>

<head>
  <title>画像投稿できる掲示板</title>
</head> 

<!-- フォームのPOST先はこのファイル自身にする -->
<form method="POST" action="./bbsimagetest.php" enctype="multipart/form-data">
  <textarea name="body" required></textarea>
  <div style="margin: 1em 0;">
    <input type="file" accept="image/*" name="image" id="image-input">
  </div>
  <button type="submit">送信</button>
</form>

<hr>

<?php foreach($select_sth as $entry): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt>ID</dt>
    <dd><?= $entry['id'] ?></dd>
    <dt>日時</dt>
    <dd><?= $entry['created_at'] ?></dd>
    <dt>内容</dt>
    <dd>
      <?= nl2br(htmlspecialchars($entry['body'])) // 必ず htmlspecialchars() すること ?>
      <?php if(!empty($entry['image_filename'])): // 画像がある場合は img 要素を使って表示 ?>
      <div>
        <img src="/image/<?= $entry['image_filename'] ?>" style="max-height: 10em;">
      </div>
      <?php endif; ?>
    </dd>
  </dl>
<?php endforeach ?>

<script>
// ファイル選択のパーツ（inputタグ）を取得
const imageInput = document.getElementById('image-input');

// ファイルが選択された（変更された）ときに動くイベントを設定
imageInput.addEventListener('change', function() {
  // 選択されたファイルが存在するかチェック
  if (this.files && this.files[0]) {
    const file = this.files[0];
    
    // 5MBをバイト単位に計算
    // 5 × 1024 (KB) × 1024 (Byte) = 5,242,880 Byte
    const maxSize = 5 * 1024 * 1024; 

    // 選択されたファイルのサイズが5MBを超えている場合
    if (file.size > maxSize) {
      alert('ファイルサイズが大きすぎます。5MB以下の画像を選択してください。');
      
      // 選択されたファイルを強制的にクリア（キャンセル）する
      this.value = '';
    }
  }
});
</script>
