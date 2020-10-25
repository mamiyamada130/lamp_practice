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
$name = get_post('name');
$price = get_post('price');
$status = get_post('status');
$stock = get_post('stock');


$image = get_file('image');

// itemsテーブルに商品を登録
if(regist_item($db, $name, $price, $stock, $status, $image)){
  // 成功メッセージを表示
  set_message('商品を登録しました。');
}else {
  // エラーメッセージを表示
  set_error('商品の登録に失敗しました。');
}

// 商品管理ページにリダイレクト
redirect_to(ADMIN_URL);