<?php
$dsn = "mysql:dbname=php_book_app;host=localhost;charset=utf8mb4";
$user = "root";
$password = "";

// 更新ボタンクリック時の処理(submitパラメータの値が存在するとき)
if (isset($_POST['submit'])) {
    try {
        $pdo = new PDO($dsn, $user, $password);

        // 動的な値をプレースホルダに置き換えたUPDATE文
        $sql_update = '
        UPDATE books
        SET book_code = :book_code,
        book_name = :book_name,
        price = :price,
        stock_quantity = :stock_quantity,
        genre_code = :genre_code
        WHERE id = :id
        ';
        $stmt_update = $pdo->prepare($sql_update);

        // 実際値をプレースホルダへバインド
        $stmt_update->bindValue(':book_code', $_POST['book_code'], PDO::PARAM_INT);
        $stmt_update->bindValue(':book_name', $_POST['book_name'], PDO::PARAM_STR);
        $stmt_update->bindValue(':price', $_POST['price'], PDO::PARAM_INT);
        $stmt_update->bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
        $stmt_update->bindValue(':genre_code', $_POST['genre_code'], PDO::PARAM_INT);
        $stmt_update->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

        $stmt_update->execute();                             // SQL文を実行
        $count = $stmt_update->rowCount();                   // 更新件数を取得
        $message = "商品を{$count}件更新しました。";         // 更新完了時メッセージ
        header("Location: read.php?message={$message}");    // 書籍一覧ページへリダイレクト・$messageも渡す
    }
    catch (PDOException $e) {
        exit ($e->getMessage());
    }
}

// idパラメータの値がある場合の処理
if (isset($_GET['id'])) {
    try {
        $pdo = new PDO($dsn, $user, $password);

        // idカラムの実際値をプレースホルダへ置き換えたSQL文
        $sql_select_book = 'SELECT * FROM books WHERE id = :id';
        $stmt_select_book = $pdo->prepare($sql_select_book);

        $stmt_select_book->bindValue(':id', $_GET['id'], PDO::PARAM_INT);       // 実際値をプレースホルダへバインド
        $stmt_select_book->execute();                                           // SQL文を実行
        $book = $stmt_select_book->fetch(PDO::FETCH_ASSOC);                     // SQL文の実行結果を配列で取得(fetchAll(PDO::FETCH_COLUMN)で１次元配列で取得)

        // idパラメータの値と同一idのデータが存在しない場合はエラーメッセージを出力&処理終了
        if ($book === FALSE) {
            exit('idパラメータの値が不正です。');
        }

        $sql_select_genre_codes = 'SELECT genre_code FROM genres';               // genresテーブルからgenre_codeのデータを取得するSQL文
        $stmt_select_genre_codes = $pdo->query($sql_select_genre_codes);         // SQL文を実行
        $genre_codes = $stmt_select_genre_codes->fetchAll(PDO::FETCH_COLUMN);    // SQL文の実行結果を配列で取得(fetchAll(PDO::FETCH_COLUMN)で１次元配列で取得)
    }
    catch (PDOException $e) {
        exit($e->getMessage());
    }
}
else {
    exit('idパラメータの値が存在しません。');                                    // idパラメータの値が存在しない場合の処理(エラーメッセージを返して処理停止)
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <title>書籍管理アプリ</title>
</head>
<body>
    <header>
        <nav>
            <a href="index.php">書籍管理アプリ</a>
        </nav>
    </header>
    <main>
        <article class="registration">
            <h1>書籍編集</h1>
            <div class="back">
                <a href="read.php" class="btn">&lt; 戻る</a>
            </div>
            <form action="update.php?id=<?= $_GET['id'] ?>" method="post" class="registration-form">
                <div>
                    <label for="book_code">ブックコード</label>
                    <input type="number" id="book_code" name="book_code" value="<?= $book['book_code'] ?>" min="0" max="100000000" require>

                    <label for="book_code">書籍名</label>
                    <input type="text" id="book_name" name="book_name" value="<?= $book['book_name'] ?>" maxlength="50" require>

                    <label for="book_code">単価</label>
                    <input type="number" id="price" name="price" value="<?= $book['price'] ?>" min="0" max="100000000" require>

                    <label for="book_code">在庫数</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="<?= $book['stock_quantity'] ?>" min="0" max="100000000" require>

                    <label for="genre_code">ジャンルコード</label>
                    <select id="genre_code" name="genre_code" require>
                        <?php
                        // 配列を順番に抽出&セレクトボックスの選択肢へ出力
                        foreach ($genre_codes as $genre_code) {
                            // $genre_codeがジャンルコードと一致している場合はselected属性をつけて初期値とする
                            if ($genre_codes === $genre_code) {
                                echo "<option value='{$genre_code}' selected>{$genre_code}</option>";
                            }
                            else {
                                echo "<option value='{$genre_code}'>{$genre_code}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn" name="submit">更新</button>
            </form>
        </article>
    </main>
    <footer>
        <p class="copyright">&copy; 書籍管理アプリ All rights reserved.</p>
    </footer>
</body>
</html>

























?>