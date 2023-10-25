import json
import scrayping as sc



# とりあえず12レース分の情報のみを取得（phpとデータベースデバッグ用）

# print(sc.scrayping())
print(json.dumps(sc.scrayping()))