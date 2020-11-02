<?php
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// dbデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// 商品購入履歴を取得
function get_user_orders($db, $user_id){
  $sql = "
    SELECT
      order_id,
      order_date,
      total_price
    FROM
      orders
    WHERE
      user_id = :user_id
    ORDER BY
      order_date DESC
  ";
  $params = array(':user_id' => $user_id);
  return fetch_all_query($db, $sql, $params);
}

// ordersテーブルの全データを取得
// function get_all_orders($db){
//   return get_user_orders($db);
// }
function get_all_orders($db){
  $sql = "
    SELECT
      order_id,
      user_id,
      order_date,
      total_price
    FROM
      orders
    ORDER BY
      order_date DESC
  ";
  return fetch_all_query($db, $sql);
}

// 選択された購入履歴データのみ取得
function get_order($db, $order_id){
  $sql = "
    SELECT
      order_id,
      order_date,
      total_price
    FROM
      orders
    WHERE
      order_id = :order_id
  ";
  $params = array(':order_id' => $order_id);
  return fetch_all_query($db, $sql, $params);
}

// 購入明細を取得
function get_details($db, $order_id){
  $sql = "
    SELECT
      details.detail_id,
      details.order_id,
      details.order_price,
      details.order_amount,
      items.item_id,
      items.name
    FROM
      details
    JOIN
      items
    ON
      details.item_id = items.item_id
    WHERE
      details.order_id = :order_id
  ";
  $params = array(':order_id' => $order_id);
  return fetch_all_query($db, $sql, $params);
}

// ordersテーブルとdetailsテーブルにデータ追加
function add_order_transaction($db, $user_id, $total_price, $carts){
  // トランザクション開始
  $db->beginTransaction();
  // ordersテーブルとdetailsテーブルにデータ追加
  if(insert_order($db, $user_id, $total_price) 
    && detail($db, $carts)){
    // コミット処理
    $db->commit();
    return true;
  }
  // ロールバック処理
  $db->rollback();
  return false;
  
}

// ordersテーブルにデータ追加
function insert_order($db, $user_id, $total_price){
    // SQL文作成
    $sql = "
      INSERT INTO
        orders(
          user_id,
          total_price
        )
      VALUES(:user_id, :total_price)
    ";
    $params = array(':user_id' => $user_id, ':total_price' => $total_price);
    // SQL実行
    return execute_query($db, $sql, $params);
  }

function detail($db, $carts){
  $order_id = $db->lastInsertId();
  foreach($carts as $cart){
    if(insert_detail(
      $db,
      $order_id,
      $cart['item_id'],
      $cart['price'],
      $cart['amount']
    ) === false){
      return false;
    }
  } 
  return true;
}

// detailsテーブルに商品追加
function insert_detail($db, $order_id, $item_id, $price, $amount){
    // SQL文作成
    $sql = "
      INSERT INTO
        details(
          order_id,
          item_id,
          order_price,
          order_amount
        )
      VALUES(:order_id, :item_id, :order_price, :order_amount)
    ";
    $params = array(':order_id' => $order_id, ':item_id' => $item_id, ':order_price' => $price, ':order_amount' => $amount);
    // SQL実行
    return execute_query($db, $sql, $params);
  }

