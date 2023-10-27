# 除外リスト生成関数
def addEscapeList(id :str, ll :list):
    #raceIdを分解してlist化
    idAry = [id[0:4], id[4:6], id[6:8], id[8:10],id[10:12]]
    for r in range(1, 13):
        idAry[4] = str(r).zfill(2)
        ll.append(''.join(idAry))
    if idAry[3] == '01':
        for d in range(2,11):
            idAry[3] = str(d).zfill(2)
            #ll = addEscapeList(''.join(idAry), ll)
            ll.extend(addEscapeList(''.join(idAry), []))
    if idAry[2] == '01':
        for t in range(2,11):
            idAry[2] = str(t).zfill(2)
            #ll = addEscapeList(''.join(idAry), ll)
            ll.extend(addEscapeList(''.join(idAry), []))
    return ll
