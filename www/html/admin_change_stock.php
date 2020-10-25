<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';

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

// 管理者でログインされていない場合、ログインページにリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// POSTされたデータを取得
$item_id = get_post('item_id');
$stock = get_post('stock');

// itemsテーブルの商品在庫を変更
if(update_item_stock($db, $item_id, $stock)){
  // 成功メッセージを表示
  set_message('在庫数を変更しました。');
} else {
  // エラーメッセージ表示
  set_error('在庫数の変更に失敗しました。');
}

// 商品管理ページにリダイレクト
redirect_to(ADMIN_URL);