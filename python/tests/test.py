import requests
from bs4 import BeautifulSoup


race_id = "202301010701"
url = f"https://db.netkeiba.com/race/{race_id}"
res = requests.get(url)
res.encoding = "EUC-JP"
soup = BeautifulSoup(res.text, "html.parser")

print(soup.text.split())