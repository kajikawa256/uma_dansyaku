import requests
from bs4 import BeautifulSoup
import component.pay_list as pl

# race_id = "201005030211" # １位が同着のレース（例外）
# race_id = "202301020112"
race_id = "202301010101" #通常のレース
url = f"https://db.netkeiba.com/race/{race_id}"
res = requests.get(url)
res.encoding = "EUC-JP"
soup = BeautifulSoup(res.text, "html.parser")

pl.insert_hit_detail(soup, race_id)
