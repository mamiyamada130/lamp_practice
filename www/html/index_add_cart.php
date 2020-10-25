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

// PDOを取得
$db = get_db_connect();
// PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);

// POSTされたデータを取得
$item_id = get_post('item_id');

// ログインユーザーのカートに商品を追加
if(add_cart($db,$user['user_id'], $item_id)){
  // 成功メッセージを表示
  set_message('カートに商品を追加しました。');
} else {
  // エラーメッセージを表示
  set_error('カートの更新に失敗しました。');
}

// トップページにリダイレクト
redirect_to(HOME_URL);