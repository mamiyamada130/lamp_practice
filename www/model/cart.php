<?php 
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// dbデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// ログインユーザーのカートデータを取得
function get_user_carts($db, $user_id){
  // SQL文作成
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
  ";
  $params = array(':user_id' => $user_id);
  return fetch_all_query($db, $sql, $params);
}

// ログインユーザーのカート内の特定の商品データを取得
function get_user_cart($db, $user_id, $item_id){
  // SQL文作成
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
    AND
      items.item_id = :item_id
  ";
  $params = array(':user_id' => $user_id, 'item_id' => $item_id);
  return fetch_query($db, $sql, $params);

}

// カートに商品を追加
function add_cart($db, $user_id, $item_id ) {
  // 商品データを取得
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    // カート内に商品がない場合、カートに商品を追加
    return insert_cart($db, $user_id, $item_id);
  }
  // カート内に商品がある場合、数量を1追加
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

// カートに商品を追加
function insert_cart($db, $user_id, $item_id, $amount = 1){
  // SQL文作成
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(:item_id, :user_id, :amount)
  ";
  // SQL文実行
  $params = array(':item_id' => $item_id, ':user_id' => $user_id, ':amount' => $amount);
  return execute_query($db, $sql, $params);
}

// カート内の商品の数量を変更
function update_cart_amount($db, $cart_id, $amount){
  // SQL文作成
  $sql = "
    UPDATE
      carts
    SET
      amount = :amount
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  // SQL実行
  $params = array(':amount' => $amount, ':cart_id' => $cart_id);
  return execute_query($db, $sql, $params);
}

// カートを削除
function delete_cart($db, $cart_id){
  // SQL文作成
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  $params = array(':cart_id' => $cart_id);
  // SQL実行
  return execute_query($db, $sql, $params);
}

// 商品の購入
function purchase_carts($db, $carts){
  // 商品が購入可能でない場合
  if(validate_cart_purchase($carts) === false){
    // 購入失敗
    return false;
  }
  // カート内の商品データを配列で取得
  foreach($carts as $cart){
    // itemsテーブルの商品在庫から購入数を引く
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      // エラーメッセージを表示
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  // 購入したカートを削除
  delete_user_carts($db, $carts[0]['user_id']);
}

// 購入したカートを削除
function delete_user_carts($db, $user_id){
  // SQL文作成
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = :user_id
  ";
  $params = array('user_id' => $user_id);
  // SQL実行
  execute_query($db, $sql, $params);
}

// カート内の合計金額を取得
function sum_carts($carts){
  // 初期値を設定
  $total_price = 0;
  // カート内の商品データを配列で取得
  foreach($carts as $cart){
    // 合計金額を取得
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

// カート内の商品が購入可能かチェック
function validate_cart_purchase($carts){
  // カートがない場合
  if(count($carts) === 0){
    // エラーメッセージを表示
    set_error('カートに商品が入っていません。');
    return false;
  }
  // カート内の商品データを配列で取得
  foreach($carts as $cart){
    // 商品が公開されていない場合
    if(is_open($cart) === false){
      // エラーメッセージを表示
      set_error($cart['name'] . 'は現在購入できません。');
    }
    // 商品在庫が購入数より少ない場合
    if($cart['stock'] - $cart['amount'] < 0){
      // エラーメッセージを表示
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  // エラーが存在している場合、購入失敗
  if(has_error() === true){
    return false;
  }
  return true;
}

