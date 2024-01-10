-- データベース作成
DROP DATABASE IF EXISTS umadan;
CREATE DATABASE umadan;

-- データベース選択
USE umadan;

-- umadan@localhostが存在しているならユーザー削除
DROP USER IF EXISTS 'umadan'@'localhost';

-- ユーザー作成（ユーザー：umadan@localhost、パスワード：umadan）
CREATE USER 'umadan'@'localhost' IDENTIFIED BY 'umadan';

-- ユーザーにinsert操作の特権を付与
GRANT SELECT, INSERT ON umadan.* TO umadan@localhost;

-- なんの特権を付与してるかを確認
SHOW GRANTS FOR 'umadan'@'localhost';

/*---------------テーブル作成----------------------*/

-- 的中
CREATE TABLE `HIT` (
  `RACEDATE` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `COLLECT_PR` int DEFAULT '0',
  `HIT_PR` int DEFAULT '0'
) ;

-- レース
CREATE TABLE `RACE` (
  `RACE_ID` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RNAME` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RACENUMBER` int NOT NULL,
  `HORSE_TOTAL` int NOT NULL,
  `GROUND` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SPIN` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DISTANCE` int NOT NULL,
  `WEATHER` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `SITUATION` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TIME` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RACEDATE` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PLACE` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `GRADE` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LIMIT` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HANDICAP` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RACE_TYPE` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `RACE_ID` (`RACE_ID`)
);

-- 結果情報
CREATE TABLE `RESULT_HORSE` (
  `RACE_ID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RANKING` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HORSEFRAME` int NOT NULL,
  `HORSENUMBER` int NOT NULL,
  `HNAME` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HORSE_ID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `GENDER` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `AGE` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `WEIGHT` float NOT NULL,
  `JOCKEY` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `JOCKEY_ID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TORAINER` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TORAINER_ID` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `BASE` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HORSE_WEIGHT` int NOT NULL,
  `WEIGHT_GAIN_LOSS` int NOT NULL,
  `ODDS` float NOT NULL,
  `POPULAR` int NOT NULL,
  `TIME` float NOT NULL
);

-- 予想情報
CREATE TABLE `PREDICTION_HORSE` (
  `RACE_ID` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HNAME` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HORSENUMBER` int NOT NULL,
  `HORSEFRAME` int NOT NULL,
  `RANKING` int NOT NULL
);

-- 払戻し詳細
CREATE TABLE `HIT_DETAIL` (
  `RACE_ID` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `KINDS` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `HORSENUMBER` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `HORSEFRAME` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `BETBACK` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `POPULAR` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL
);

-- 獲得賞金
CREATE TABLE `PRIZE_MONEY` (
  `RACE_ID` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `RANKING` int(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `PRIZE` int(10) COLLATE utf8mb4_unicode_ci NOT NULL
);

