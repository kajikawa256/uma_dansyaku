from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
import classes.db_operation_class as db

class Main():
  # コンストラクタ
  def __init__(self):
    # インスタンスの作成
    self.db_instans = db.Main()

    # chromedriverの設定
    chrome_options = Options()
    chrome_options.add_argument('--headless=new')                                   # chromedriverをヘッドレスに
    chrome_options.add_argument('--blink-settings=imagesEnabled=false')             # 画像を読み込まない
    chrome_options.add_argument('--disable-background-networking')                  # 様々なバックグラウンドネットワークサービスを無効にする
    chrome_options.add_experimental_option('excludeSwitches', ['enable-logging'])   # chromedriveのログ非表示

    self.chrome_options = chrome_options
    self.path = "./python/data/chromedriver"                                        # chromedriceのパス


  # 出馬表をスクレイピング
  def scrayping_running_list(self,race_id):
    # chromedriverの起動
    service = webdriver.chrome.service.Service(self.path)
    driver = webdriver.Chrome(service=service, options=self.chrome_options)

    # url
    url = f"https://race.netkeiba.com/race/shutuba.html?race_id={race_id}"

    driver.get(url)

    # クラス名で指定して要素を取得
    tr = driver.find_elements(By.CLASS_NAME, "HorseList")

    result_list = []

    # レコード数分ループ
    for x in range(0, len(tr)):
      # 出走取消馬などはスクレイピングせずにスキップ
      if "取消" in tr[x].text:
        continue
      record = tr[x].text.replace("--","").split()
      horse_id = driver.find_elements(By.CLASS_NAME, "HorseName")[x+1].find_element(By.TAG_NAME,"a").get_attribute("href").split("horse/")[1]
      jockey_id = driver.find_elements(By.CLASS_NAME, "Jockey")[x+1].find_element(By.TAG_NAME,"a").get_attribute("href").split("recent/")[1].replace("/","")
      trainer_id = driver.find_elements(By.CLASS_NAME, "Trainer")[x+1].find_element(By.TAG_NAME,"a").get_attribute("href").split("recent/")[1].replace("/","")


      # 馬体重が発表されていない場合馬体重と体重増減を0にする
      if len(record) == 9:
        record.insert(7,0)
        record.insert(8,0)
      else:
        weigh_info = record.pop(7)
        record.insert(7,int(weigh_info.split("(")[0]))
        record.insert(8,weigh_info.replace(weigh_info.split("(")[0],""))
        record[8] = int(record[8].replace("(","").replace(")",""))

      # リストに各情報を入れていく
      record.insert(0,race_id)
      record.insert(1,"未定")      # 着順は未確定なので未定
      record[2] = int(record[2])
      record[3] = int(record[3])
      record.insert(5,horse_id)
      gender_and_age = record.pop(6)
      record.insert(6,gender_and_age[0])
      record.insert(7,gender_and_age.replace(gender_and_age[0],""))
      record.insert(10,jockey_id)
      trainder_and_base = record.pop(11)
      record.insert(11,trainder_and_base[2:])
      record.insert(12,trainer_id)
      record.insert(13,trainder_and_base.replace(trainder_and_base[2:],""))
      record[8] = 0 if record[8] == "未定" else float(record[8])            # 未定の場合は0を挿入

      record[16] =  0 if record[16] == "-.-" else float(record[16])
      record[17] =  0 if record[17] == "**" else int(record[17])

      # 全リストを結合
      result_list.extend(record)

    # driverを終了
    driver.quit()

    return result_list
      