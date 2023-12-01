# 除外race_idを生成してリスト化する関数

def addEscapeList(id :str, ll :list):
            # raceIdを分解してlist化
            idAry = [id[0:4], id[4:6], id[6:8], id[8:10], id[10:12]]
            holdflg = False
            tescflg = True
            for t in range(int(idAry[2]), 13):
                if holdflg:
                    rtime = 1
                else:
                    # ループ1回目だけ開催日の途中から除外リスト化する
                    rtime = int(idAry[3])
                    holdflg = True
                for d in range(rtime, 17):
                    for r in range(1, 13):
                        raceId = idAry[0] + idAry[1] + \
                                str(t).zfill(2) + str(d).zfill(2) + str(r).zfill(2)
                        ll.append(raceId)
                    if d == 1:
                        tescflg = False
                if tescflg:
                    break
                
            return ll
