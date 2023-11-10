# 除外race_idを生成してリスト化する関数
import pprint

def addEscapeList(id :str, ll :list):
    # raceIdを分解してlist化 2023 01 07 01 01
    #                        2023 01 01 06 12 
    #                        2023 01 02 01 01
    
    idAry = [id[0:4], id[4:6], id[6:8], id[8:10],id[10:12]]
    for d in range(int(idAry[3]), 17):
        for r in range(1, 13):
            raceId = idAry[0] + idAry[1] + \
                    idAry[2] + str(d).zfill(2) + str(r).zfill(2)
            ll.append(raceId)
    # pprint.pprint(ll)
    return ll
