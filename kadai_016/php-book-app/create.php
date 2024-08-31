<?php
$dsn = "mysql:dbname=php_book_app;host=localhost;charset=utf8mb4";
$user = "root";
$password = "";

// 登録ボタンクリック時の処理(submitパラメータの値が存在するとき)
if (isset($_POST['submit'])) {
    try {
        $pdo = new PDO($dsn, $user, $password);

        // 動的な値をプレースホルダに置き換えたINSERT文
        $sql_insert = '
        INSERT INTO books (book_code, book_name, price, stock_quantity, genre_code) VALUES (:book_code, :book_name, :price, :stock_quantity, :genre_code)
        ';
        $stmt_insert = $pdo->prepare($sql_insert);

        // bindValue()で実際値をプレースホルダへバインド
        $stmt_insert->bindValue(':book_code', $_POST['book_code'], PDO::PARAM_INT);
        $stmt_insert->bindValue(':book_name', $_POST['book_name'], PDO::PARAM_STR);
        $stmt_insert->bindValue(':price', $_POST['price'], PDO::PARAM_INT);
        $stmt_insert->bindValue(':stock_quantity', $_POST['stock_quantity'], PDO::PARAM_INT);
        $stmt_insert->bindValue(':genre_code', $_POST['genre_code'], PDO::PARAM_INT);

        $stmt_insert->execute();                            //SQL文を実行
        $count = $stmt_insert->rowCount();                  //追加した件数を取得
        $message = "書籍を{$count}件登録しました。";        //登録メッセージ
        header("Location: read.php?message={$message}");   //書籍一覧ページへリダイレクト・messageパラメータも渡す
    }
    catch (PDOExeption $e) {
        exit($e->getMessage());
    }
}

// ジャンルコードをセレクトボックスの配列から取得
try {
    $pdo = new PDO($dsn, $user, $password);

    $sql_select = 'SELECT genre_code FROM genres';              // genreテーブルからgenre_codeカラムのデータを取得
    $stmt_select = $pdo->query($sql_select);                    // sql文を実行
    $genre_codes = $stmt_select->fetchAll(PDO::FETCH_COLUMN);   // SQL文の実行結果を配列で取得。PDO::FETCH_COLUMNは1次元配列で取得
}
catch (PDOException $e) {
    exit($e->getMessage());
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
            <h1>書籍登録</h1>
            <div class="back">
                <a href="read.php" class="btn">&lt; 戻る</a>
            </div>
            <form action="create.php" method="post" class="registration-form">
                <div>
                    <label for="book_code">ブックコード</label>
                    <input type="number" id="book_code" name="book_code" min="0" max="100000000" require>

                    <label for="book_name">書籍名</label>
                    <input type="text" id="book_name" name="book_name" maxlength="50" require>

                    <label for="price">単価</label>
                    <input type="number" id="price" name="price" min="0" max="100000000" require>

                    <label for="stock_quantity">在庫数</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" min="0" max="100000000" require>

                    <label for="genre_code">ジャンルコード</label>
                    <select id="genre_code" name="genre_code" require>
                        <option disabled selected value>選択してください</option>
                        <?php
                        // 配列の中身を順番に抽出してセレクトボックスの選択肢へ出力
                        foreach ($genre_codes as $genre_code) {
                            echo "<option value='{$genre_code}'>{$genre_code}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn" name="submit" value="create">登録</button>
            </form>
        </article>
    </main>
    <footer>
        <p class="copyright">&copy; 書籍管理アプリ All rights reserved.</p>
    </footer>
</body>
</html>