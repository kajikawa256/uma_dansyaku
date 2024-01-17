import component.scrayping_def as sc
import classes.create_scrayping_list as create_list
import classes.scrayping_running_list as running_list

#任意の年数分スクレイピングしてデータベースにinsertする
insert_instans = create_list.Main()             # インスタンスの作成
running_list_instans = running_list.Main()      # 出馬表をスクレイピングするためのseleniumインスタンスクラス

# race_idのリストを基にスクレイピングを行う
race_id = "202309050411"

# URLを作成
url = f"https://race.netkeiba.com/race/result.html?race_id={race_id}"

# URLを基にスクレイピング
soup = sc.get_soup(url)

if "出馬表" in soup.text:
  print("s")

# create_list_instans = create_list.Main()
# create_list_instans.test(soup,race_id)
