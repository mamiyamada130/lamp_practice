<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';
// cartデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'cart.php';

// ログインチェックを行うため、セッションを開始
session_start();

// ログインチェック用関数を利用
if(is_logined() === false){
  // ログインしていない場合はログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// PDO取得
$db = get_db_connect();
// PDOを利用して、ログインユーザーのデータを取得
$user = get_login_user($db);

$token = get_post('token');

// post送信されたトークンのチェック
if(is_valid_csrf_token($token) === false) {
  redirect_to(LOGIN_URL);
}

// トークンの破棄
delete_session();

// POSTされたcart_idを取得
$cart_id = get_post('cart_id');

// カートの削除に成功した場合、メッセージを表示
if(delete_cart($db, $cart_id)){
  set_message('カートを削除しました。');
} else {
  // エラーメッセージを表示
  set_error('カートの削除に失敗しました。');
}

// カートページにリダイレクト
redirect_to(CART_URL);