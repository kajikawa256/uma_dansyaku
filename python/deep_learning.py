import keras
from keras.models import Model
from keras.layers import Input, Dense, Activation, Dropout
from keras.layers import Conv2D, GlobalAveragePooling2D
from keras.layers import BatchNormalization, Add
from keras.callbacks import ModelCheckpoint
from keras.utils import to_categorical
from keras.initializers import he_normal
import keras.backend as K
import sqlalchemy as sa
import pandas as pd
import data.constant as con
from tqdm import tqdm
import component.create_raceID as cr
import classes.db_operation_class as db
import classes.pred_dataset as pred
import classes.convert_df as convert
from sklearn.model_selection import train_test_split

# インスタンスの作成
db_instans = db.Main()
pred_instans = pred.Main()
convert_instans = convert.Main()

# DBを基にDFを作成
race_id = ""
df = convert_instans.convert_df("RESULT_HORSE",race_id)

# 目的変数と説明変数に分割
x = df.iloc[:,1:] # 説明変数
y = df.iloc[:,0]  # 目的変数

# 訓練用データとテスト用データに分割
X_train,X_test,y_train,y_test = train_test_split(x,y,test_size=0.3,random_state=1)

print(df)

