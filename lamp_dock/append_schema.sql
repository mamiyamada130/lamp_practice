-- 購入履歴テーブル
-- 必要なカラム
-- 注文番号（主キー）・ユーザーID・購入日時・合計金額
CREATE TABLE `orders` (
    `order_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `total_price` int(11) NOT NULL,
    primary key(`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 購入明細テーブル
-- 必要なカラム
-- 明細番号（主キー）・注文番号・アイテムID・購入時の商品価格・購入数
CREATE TABLE `details` (
    `detail_id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` int(11) NOT NULL,
    `item_id` int(11) NOT NULL,
    `order_price` int(11) NOT NULL,
    `order_amount` int(11) NOT NULL,
    primary key(`detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;