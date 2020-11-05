<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';
// pageデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'page.php';

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

// 商品一覧用の商品データを取得
// $items = get_open_items($db);

// getされた現在のページ数を取得
if(get_get('page') === ''){
  $page = 1;
} else {
  $page = (int)get_get('page');
}

// 1ページ目は0、それ以外は現在のページ数-1に8かける
$start = ($page - 1) * PAGE_MAX;

// 8個ずつ商品を取得
$items = get_page_items($db, $start);

// 公開されている商品数
$items_num = get_items_num($db);

// 総ページ数を取得
$total_pages = ceil($items_num['count'] / PAGE_MAX);



// トークンを生成
$token = get_csrf_token();

// ビューの読み込み
include_once VIEW_PATH . 'index_view.php';