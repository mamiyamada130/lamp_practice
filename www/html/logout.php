<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';

// セッション開始
session_start();
// セッション変数を全て削除
$_SESSION = array();
// sessoinに関連する設定を取得
$params = session_get_cookie_params();
// sessionに利用しているクッキーの有効期限を過去に設定することで無効化
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
// セッションIDを無効化
session_destroy();

// ログインページにリダイレクト
redirect_to(LOGIN_URL);

