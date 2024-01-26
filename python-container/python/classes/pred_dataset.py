import pandas as pd
import datetime
import pandas as pd
from sklearn import preprocessing
import warnings
import datetime
import pickle

class Main():
    # コンストラクタ dbのインスタンス作成
    def __init__(self):
        #pickleデータロード
        self.dfRaceInfo = pd.read_pickle('python/data/race_info.pkl')
        self.dfHorseResult = pd.read_pickle('python/data/horse_result2.pkl')
        self.dfHorsePed = pd.read_pickle('python/data/horse_ped2.pkl')

        # 学習済みモデルの読み込み
        self.model = pickle.load(open('python/data/model1.pkl','rb'))


    def entryHorse(self, raceinfo, horseinfo):

        #出馬表とレース情報テーブル結合
        dfEntryi = pd.merge(left=horseinfo,right=raceinfo, how='left', on='raceId')
        dfHorseResulti = pd.merge(left=self.dfHorseResult,right=self.dfRaceInfo,
                                how='left', on='raceId',suffixes=['','_i'])
        #戦績テーブルからゴミを除外
        df = dfHorseResulti.copy()
        columns = []
        for cn in df.columns:
            if '_i' not in cn:
                columns.append(cn)
        dfHorseResulti = df[columns]
        #空のデータフレームを生成
        dfEntryH = pd.DataFrame (columns=['horseId','日付'])
        
        indexList = dfEntryi.index
        # #レースを日付降順にソートする
        dfHorseResulti = dfHorseResulti.sort_values(['horseId', '日付'], ascending=[True, False])

        #レース結果のテーブルを1行ずつ取り出す
        for cnt, rowIndex in enumerate(indexList):
            
            try:
                #レース結果から1行分のデータを取り出す
                rowDf = dfEntryi.loc[dfEntryi.index==rowIndex]
                #horseIと日付を取り出す
                horseId = rowDf['horseId'].iloc[0]
                dt = rowDf['日付'].iloc[0]
                
                for idx,tmpDf in \
                    enumerate(dfHorseResulti[(dfHorseResulti['horseId']==horseId)&\
                                            (dfHorseResulti['日付']<dt)].head(9).iterrows()):
                    idxx = idx+1
                    tmpDfx = pd.DataFrame(tmpDf[1]).T
                    #列名にループ回数でサフィックスを付加
                    tmpDfx.columns = tmpDfx.columns + '_' + str(idxx)
                    #結合の為にidexを合わせる
                    tmpDfx.index = rowDf.index
                    #結合の実行
                    rowDf = pd.concat([rowDf, tmpDfx], axis=1)
                #1行分のレコードを結合
                dfEntryH = pd.concat([dfEntryH,rowDf])
            except:
                print('exception catch')
                break

        df = dfEntryH.copy() 
        #スカラー関数の定義
        def CalcInterval(x):
            try:
                interval = datetime.datetime.strptime(x['日付'], '%Y/%m/%d')-\
                            datetime.datetime.strptime(x['日付_1'], '%Y/%m/%d')
                return interval.days
            except:
                return 0
        #apply関数で一括処理
        df['出走間隔'] = df.apply(CalcInterval, axis=1)
        dfEntryp = pd.merge(left=df, right=self.dfHorsePed, how='left', on='horseId')

        #データセットのロード
        #使用する列名を指定
        resultCol = [
            '日付','raceId','枠番','馬番','horseId','性','年齢','斤量',
            'jockeyId','単勝','人気','trainerId','拠点','馬体重','体重増減',
            '出走間隔','ハンデ','着順','R','コース種','コース回り','距離','天気',
            '馬場','開催場所','グレード','制限'
        ]
        recordCol = [
            'R','頭数','枠番','馬番','単勝','人気','着順','jockeyId','斤量',
            'タイム','着差','上り','馬体重','体重増減','出走間隔','コース種',
            'コース回り','距離','天気','馬場','開催場所','グレード','制限','ハンデ'
        ]
        pedCol = ['pedId_' + str(i) for i in range(0,62)]
        #前N走分戦績の列名を生成
        recordCol9 = []
        for i in range(1, 10):
            tmpList = list(map(lambda x: x + '_' + str(i), recordCol))
            recordCol9 += tmpList
        #列名を合体
        COLUMNS = resultCol + recordCol9 + pedCol

        COLUMNS2 = COLUMNS.copy()
        COLUMNS2.remove('着順')
        frame = pd.DataFrame(columns=COLUMNS2)
        targetDataset = pd.concat([frame, dfEntryp])[COLUMNS2]

    #出馬表のラベルエンコーディング
        # #ラベルエンコーディング関数の定義
        def labelEncode(df, target, recflg=False):
            #複数列のラベルエンコーディング関数の定義
            def listEncoder(tdf, le, cols):
                #データフレームのコピー
                tdf_ = tdf.copy()
                #列名から値を取り出す
                encoList = []
                for col in cols:
                    encoList += tdf_[col].unique().tolist()
                #エンコーダーを生成
                le.fit(encoList)
                #複数列分ループ
                for col in cols:
                    #欠損データ以外の列を取り出す
                    notNull = tdf_[col][tdf_[col].notnull()]
                    #エンコード実行してindexをキーにデータフレームに書き込む
                    tdf_[col] = pd.Series(le.transform(notNull), index=notNull.index)
                    #エンコードした列はcategory列に変換
                    tdf_[col] = tdf_[col].astype('category')
                return tdf_, le
            #データフレームのコピー
            tdf = df.copy()
            #ラベルエンコーダーをインスタンス
            le = preprocessing.LabelEncoder()
            #戦績かどうかで分岐
            if not recflg:
                #リストかどうかで分岐
                if type(target) != list:
                    #エンコーダーの生成
                    le.fit(tdf[target])
                    #欠損データ以外の列を取り出す
                    notNull = tdf[target][tdf[target].notnull()]
                    #エンコード実行してindexをキーにデータフレームに書き込む
                    tdf[target] = pd.Series(le.transform(notNull), index=notNull.index)
                    #エンコードした列はcategory列に変換
                    tdf[target] = tdf[target].astype('category')
                else:
                    #戦績以外で複数データだったら複数列エンコードの実行
                    tdf, le = listEncoder(tdf, le, target)
            else:
                #戦績データは列名にサフィックスを付与したリストを生成
                cols9 = [target] + [target + '_' + str(i) for i in range(1, 10)]
                #複数列エンコードの実行
                tdf, le = listEncoder(tdf, le, cols9)
            #データフレームとエンコーダーをreeturn
            return tdf, le

        #データフレームコピー
        df = targetDataset.copy()
        #カテゴリ変数をラベルエンコード
        horseList = ['horseId'] + ['pedId_' + str(i) for i in range(0,62)]

        # print(type(df))
        df, leHorse = labelEncode(df,horseList)
        df, leGender = labelEncode(df,'性')
        df, leTrainer = labelEncode(df,'trainerId')
        df, leHomeBase = labelEncode(df,'拠点')
        df, leJockey = labelEncode(df,'jockeyId',recflg=True)
        df, leHandi = labelEncode(df,'ハンデ',recflg=True)
        df, leType = labelEncode(df,'コース種',recflg=True)
        df, leDir = labelEncode(df,'コース回り',recflg=True)
        df, leWether = labelEncode(df,'天気',recflg=True)
        df, leCondition = labelEncode(df,'馬場',recflg=True)
        df, lePlace = labelEncode(df,'開催場所',recflg=True)
        df, leGrade = labelEncode(df,'グレード',recflg=True)
        df, leRegulation = labelEncode(df,'制限',recflg=True)

        #量的変数の列名を生成
        numericCols = ['年齢']
        cols1 = ['枠番','馬番','単勝','人気','斤量','馬体重',
                '体重増減','出走間隔','R','距離']
        cols2 = ['頭数','着順','タイム','着差','上り']
        numericCols += cols1
        cols3 = cols1 + cols2
        for i in range(1,10):
            numericCols += map(lambda x: x + '_' + str(i),cols3)
        #量的変数に対して片変数を実行
        for col in numericCols:
            df[col] = df[col].astype(float)
        
        #推論
        test = df
        test_x = test.drop(['日付','raceId','単勝'],axis=1)

        #検証データに対して予測実行
        prad = self.model.predict(test_x)
        #結果可視化の為に元のデータセットをコピー
        eval_df = dfEntryi.copy()   #元はdfEntryなってた勘
        #予測結果を列として追加
        eval_df['pred'] = prad
        #1となる確率を列として追加
        eval_df['proba'] = self.model.predict_proba(test_x)[:,1]
        #raceIdでグルーピングで1となる確率が高い順にランク付けを実行
        eval_df['予測着順'] = eval_df.groupby('raceId')['proba'].rank(ascending=False)

        warnings.simplefilter('ignore')

        eval_df['正規化値'] = eval_df.groupby('raceId')['proba']\
            .apply(lambda x: preprocessing.minmax_scale(x)).explode().tolist()
        eval_df[eval_df['raceId']=='202301010301']
        eval_df['標準化分散値'] = eval_df.groupby('raceId')['proba']\
            .apply(lambda x: preprocessing.scale(x)).explode().tolist()
        view_df = eval_df[['raceId','馬名','馬番','枠番','単勝','人気','予測着順',
                        'pred','proba','正規化値','標準化分散値']]

        view_df = view_df.sort_values('予測着順')
        view_df['予測印'] = view_df['pred'].map(lambda x: '◯' if x == 1 else '')

        view_df = view_df[['raceId','馬名','馬番','枠番','予測着順']]
        datas = []
        for dr,dn,db,dw,dy in zip(view_df['raceId'],view_df['馬名'],view_df['馬番'],view_df['枠番'],view_df['予測着順']):
            datas += [dr,dn,int(db),int(dw),int(dy)]

        return datas