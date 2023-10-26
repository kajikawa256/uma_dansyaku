def get_id(exclusionIDList,acquiredIDList):
  #レースIDリスト生成
  #開催年(2022と2023)
  years  = [str(i).zfill(4) for i in range(2023, 2024)]
  #開催場所(MAX10) 01札幌、02函館、03福島、04新潟、05東京、
  #                06中山、07中京、08京都、09阪神、10小倉
  place = [str(i).zfill(2) for i in range(1, 11)]
  #開催回(MAX10)
  times = [str(i).zfill(2) for i in range(1, 11)]
  #開催日(MAX10)
  days  = [str(i).zfill(2) for i in range(1, 11)]
  #レース12(MAX12)
  races = [str(i).zfill(2) for i in range(1, 13)] 

  raceIdList = []

  for y in years:
      for p in place:
          for t in times:
              for d in days:
                  for r in races:
                      raceIdList.append(y + p + t + d + r)
  
  
  # 除外リストと取得済みのレースIDは除外
  compact1_raceIdList = [i for i in raceIdList if i not in acquiredIDList]
  compact2_raceIdList = [i for i in compact1_raceIdList if i not in exclusionIDList]

  return compact2_raceIdList