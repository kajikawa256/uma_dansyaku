import requests
from bs4 import BeautifulSoup
import re
import time
from datetime import datetime, timedelta

url = "https://race.netkeiba.com/race/result.html?race_id=202301010101&rf=race_list#lnkga2jn7aqrt3i3hpt"
res = requests.get(url)
res.encoding = "EUC-JP"
soup = BeautifulSoup(res.text, 'html.parser')
list = soup.find_all("title")
list = list[0].text

print(list)

# list = list[0].text
# list = list.split()

# #上位５馬表示
# z = 14
# for x in range (5):
#   for y in range (2):
#     if x == 0:
#       print(list[3 + z + y],end=' ')
#     else:
#       print(list[2 + z + y],end=' ')
#   z+=15
  
