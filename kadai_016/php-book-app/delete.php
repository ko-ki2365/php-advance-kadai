<?php
$dsn = "mysql:dbname=php_book_app;host=localhost;charset=utf8mb4";
$user = "root";
$password = "";

try {
    $pdo = new PDO($dsn, $user, $password);

    // idカラム値をプレースホルダへ置き換え
    $sql_delete = 'DELETE FROM books WHERE id = :id';
    $stmt_delete = $pdo->prepare($sql_delete);

    $stmt_delete->bindValue(':id', $_GET['id'], PDO::PARAM_INT);        // idカラムの実際値をプレースホルダへバインド
    $stmt_delete->execute();                                            // SQL文を実行
    $count = $stmt_delete->rowCount();                                  // 削除件数を取得
    $message = "商品を{$count}件削除しました。";                        // 削除メッセージ
    header("Location: read.php?message={$message}");                   // 書籍一覧ページへリダイレクト(同時にメッセージも渡す)
}
catch (PDOExeption $e) {
    exit($e->getMessage());
}