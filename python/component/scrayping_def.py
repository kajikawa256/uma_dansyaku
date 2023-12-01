import requests
from bs4 import BeautifulSoup

# 引数としてurlを渡し、soupをreturnする
def get_soup(url):
    # スクレイピング
    res = requests.get(url)
    res.encoding = "EUC-JP"  # エンコーディング
    soup = BeautifulSoup(res.text, "html.parser")

    return soup