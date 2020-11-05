<?php 
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// dbデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// 1ページに8個ずつ商品を取得
function get_page_items($db, $start){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE 
      status = 1
    LIMIT
      :start, :max
  ";
  $params = array(':start' => $start, ':max' => PAGE_MAX);
  return fetch_all_query($db, $sql, $params);
}

// 公開されている総商品数を取得
function get_items_num($db){
  $sql = "
    SELECT
    COUNT(*)
    AS
      count
    FROM
      items
    WHERE
      status = 1
  ";
  return fetch_query($db, $sql);
}
