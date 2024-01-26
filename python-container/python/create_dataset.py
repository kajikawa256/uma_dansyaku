import pandas as pd
import numpy as np
from tqdm import tqdm
# import os
import datetime
# import re
from tqdm._tqdm_notebook import tqdm_notebook
from sklearn import preprocessing
import classes.convert_df as con

# データセットクラス
class Dataset:
    
    # コンストラクタ
    def __init__(self, load=True):

        self.__columnsDict = self.__generateColumns()
        self.__encodeEnvs = self.__generateEncodeEnvs()
        if load:
            self.__dataset = pd.read_pickle('./data/dataset.pkl')
    @property
    def dataset(self):
        return self.__dataset[self.columnsDict['all']].copy()
    @property
    def columnsDict(self):
        return self.__columnsDict.copy()
    # エンコード実行メソッド
    def encoding(self, src, fit=False):
        df = src.copy()
        for key, env in tqdm(self.__encodeEnvs.items()):
            cols = env['cols']
            le = env['encoder']
            if fit:
                # fitの指示があったらデータフレームから対象の値を取り出して実行
                na = df[cols].to_numpy()
                tg = na.reshape(-1).tolist()
                le.fit(tg)
            for col in cols:
                # 欠損データ以外の列を取り出す
                notNull = df[col][df[col].notnull()]
                # エンコード実行してindexをキーにデータフレームに書き込む
                df[col] = pd.Series(le.transform(notNull), index=notNull.index)
                # エンコードした列はcategory列に変換
                df[col] = df[col].astype('category')
        cols = self.__columnsDict['numeric']
        for col in cols:
            df[col] = (df[col].replace('中止',np.nan).replace('除外',np.nan)
                       .replace('取消',np.nan).replace('失格',np.nan).replace('未定',np.nan))
            df[col] = df[col].astype(float)

        return df
    # 出馬表用前処理メソッド
    def preprocessingEntryTable(self, entryDf): 
        df = self.addHistrical(entryDf)
        # df = self.addInterval(df)
        df = df[self.__columnsDict['entry_table']]
        df = self.encoding(df)
        return df
    # データセット一括更新メソッド
    def update(self):
        print('更新します')
        # import cron.scrayping
        #import cron.prediction

        self.__dataset = self.__updateDataset()
    # 列名生成メソッド
    def __generateColumns(self):
        # 使用する列名を指定
        resultCol = [
            'RACEDATE', 'RACE_ID', 'HORSEFRAME', 'HORSENUMBER', 'HORSE_ID', 'GENDER', 'AGE',
            'WEIGHT', 'JOCKEY_ID', 'ODDS', 'POPULAR', 'TORAINER_ID', 'BASE',
            'HORSE_WEIGHT', 'WEIGHT_GAIN_LOSS', 'HANDICAP', 'RANKING','RACENUMBER',# 'interval', 
            'GROUND', 'SPIN', 'DISTANCE', 'WEATHER', 'SITUATION', 'PLACE',
            'GRADE', 'LIMIT','HORSE_TOTAL','TIME'
        ]
        # recordCol = [
        #     'RACENUMBER', 'HORSE_TOTAL', 'HORSEFRAME', 'HORSENUMBER', 'ODDS', 'POPULAR', 'RANKING',
        #     'JOCKEY_ID', 'WEIGHT', 'TIME', 'HORSE_WEIGHT',
        #     'WEIGHT_GAIN_LOSS', 'GROUND', 'SPIN', 'DISTANCE', #interval,
        #     'WEATHER', 'SITUATION', 'PLACE', 'GRADE', 'LIMIT', 'HANDICAP',
        # ]
        # # 前N走分戦績の列名を生成
        # recordCol9 = []
        # for i in range(1, 10):
        #     tmpList = list(map(lambda x: x + '_' + str(i), recordCol))
        #     recordCol9 += tmpList

        # 生成した列名を辞書に格納
        columnsDict = {}
        columnsDict['all'] = resultCol# + recordCol9
        # columnsDict['histrical'] = recordCol9
        l = columnsDict['all'].copy()
        l.remove('RANKING')
        columnsDict['entry_table'] = l

        # 量的変数の列名を生成
        numericCols = ['AGE']
        cols1 = ['HORSEFRAME', 'HORSENUMBER', 'ODDS', 'POPULAR', 'WEIGHT',
                'HORSE_WEIGHT', 'WEIGHT_GAIN_LOSS', 'RACENUMBER', 'GROUND',]# 'interval',]
        cols2 = ['HORSE_TOTAL', 'RANKING', 'TIME']
        numericCols += cols1
        cols3 = cols1 + cols2
        for i in range(1, 10):
            numericCols += map(lambda x: x + '_' + str(i), cols3)
        columnsDict['numeric'] = numericCols

        # カテゴリ変数の列名を生成
        sr = pd.Series(columnsDict['all'])
        categoryCol = sr[~sr.isin(numericCols)].to_list()
        columnsDict['categorical'] = categoryCol

        return columnsDict
    # エンコーダー生成メソッド
    def __generateEncodeEnvs(self):
        encodeEnvs = {}
        # horseIdだけは勝手が違うので個別に環境を生成
        dd = {}
        dd['cols'] = ['HORSE_ID']
        dd['encoder'] = preprocessing.LabelEncoder()
        encodeEnvs['HORSE_ID'] = dd
        # それ以外の対象はリスト化してfor文で処理
        cols = ['GENDER', 'TORAINER_ID', 'BASE', 'JOCKEY_ID', 'HANDICAP', 'GROUND',
                'SPIN', 'WEATHER', 'SITUATION', 'PLACE', 'GRADE', 'LIMIT']
        for col in cols:
            dd = {}
            dd['cols'] = [s for s in self.__columnsDict['all'] if col in s]
            dd['encoder'] = preprocessing.LabelEncoder()
            encodeEnvs[col] = dd
        return encodeEnvs
    # 過去データセットの更新
    def __updateDataset(self):
        print('レース結果のデータをロードします')
        # 保存したデータを読み込みレース結果とレース情報を結合
        dataDf = self.__generateRaceResult()
        loadDf = pd.DataFrame()

        # 加工対象があったら加工実行
        if len(dataDf) > 0:
            print('レース結果に戦績データを付与します')
            # 上記のデータに戦績データを付与
            dataAddHis = Dataset.addHistrical(dataDf)
            if len(loadDf) >= 0:
                dataAddHis = pd.concat([loadDf, dataAddHis], axis=0)
        else:
            dataAddHis = loadDf
        
        # print('interval(出走間隔)を計算し列に追加します')
        # dataAddInterval = Dataset.addInterval(dataAddHis)
        # dataset = (dataAddInterval
        #             .sort_values(['RACEDATE', 'RACE_ID', 'HORSENUMBER'], ascending=[True, True, True])
        #             .reset_index(drop=True))
        dataset = dataAddHis
        print('データセットをファイルに保存します')
        dataset.to_pickle('./data/dataset_no.pkl')
        print('データセットの更新が完了しました')

        return dataset
    # レース結果とレース情報をファイルからロード
    def __generateRaceResult(self):
        gift = con.Main()
        # DBからデータ取得
        df = gift.convert_df('RESULT_HORSE','')
        infoDf = gift.convert_df('RACE','')
        # レース結果と競走馬戦績にレース情報を結合
        result = pd.merge(left=df, right=infoDf,
                    how='left', on='RACE_ID', suffixes=['', '_i'])
        return result
    # レース結果または出馬表データに戦績データ付与するメソッド
    @staticmethod
    def addHistrical(srcdf):
        tqdm_notebook.pandas()
        gift = con.Main()
        # DBからデータ取得
        dfRaceInfo = gift.convert_df('RACE','')
        dfHorseResult = gift.convert_df('RESULT_HORSE','')
        # 競走馬戦績にレース情報を結合
        dfHorseResulti = pd.merge(left=dfHorseResult, right=dfRaceInfo,
                                how='left', on='RACE_ID', suffixes=['', '_i'])
        # 戦績テーブルからゴミを除去
        df = dfHorseResulti.copy()
        columns = []
        for cn in df.columns:
            if '_i' not in cn:
                columns.append(cn)
        dfHorseResulti = df[columns]
        # 引数のデータフレームをコピー
        df = srcdf.copy()
        # 戦績データとする列を_1～_9のサフィックスを付与して一度numpy配列にする
        cols = np.array([dfHorseResulti.add_suffix(f'_{i}').columns for i in range(1, 10)])
        # 列名を1次元配列にreshape
        cols = cols.reshape(-1)

        # 戦績データ生成関数の定義
        def generateHistoricalData(row):
            # 日付とhorseIdの抽出
            dt = row['RACEDATE']
            horseId = row['HORSE_ID']
            # 戦績データを日付とhorseIdで絞り込み
            na = (
                dfHorseResulti[(dfHorseResulti['RACEDATE']<dt)&
                               (dfHorseResulti['HORSE_ID']==horseId)]
                    .head(9)        # 直近9レース分に限定
                    .to_numpy()     # numpy配列に変換
                    .reshape(-1)    # 1次元配列に変換
            )
            # 戦績データのサイズが9レース分無かったら足りないサイズ分nanで埋める
            if na.size < cols.size:
                naNan = np.array([np.nan for i in range(cols.size - na.size)])
                na = np.concatenate([na, naNan])
            return na
        # 戦績データを生成してapplyで関数実行
        df[cols] = df.progress_apply(generateHistoricalData, axis=1, result_type='expand')

        return df
    # レース結果または出馬表データのinterval(出走間隔)を計算するメソッド
    # @staticmethod
    # def addInterval(srcdf):
    #     df = srcdf.copy()
    #     # スカラー関数の定義
    #     def calcInterval(x):
    #         try:
    #             interval = (datetime.datetime.strptime(x['RACEDATE'], '%Y年%m月%d日') - 
    #                         datetime.datetime.strptime(x['RACEDATE_1'], '%Y年%m月%d日'))
    #             return interval.days
    #         except:
    #             return 0
    #     # apply関数で一括処理
    #     df['interval'] = df.progress_apply(calcInterval, axis=1)
    #     return df
def version():
    mejor = 0
    minor = 1
    print(f'競馬予測プログラム Ver.{mejor}.{minor}')
    comment = (
'''

Release note
Ver0.1
レース結果とレース情報内のスキップ処理のバグを修正
'''
)
    print(comment)

if __name__ == '__main__':
    version()