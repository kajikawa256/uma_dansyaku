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
x_train,x_test,y_train,y_test = train_test_split(x,y,test_size=0.3,random_state=1)

# モデル構造の定義
inputs = keras.layers.Input(shape=(2,))
x = keras.layers.Flatten()(inputs)
x = keras.layers.Dense(128, activation='relu')(x)
x = keras.layers.Dropout(0.2)(x)
predictions = keras.layers.Dense(10, activation='softmax')(x)

# 入出力の定義
model = keras.Model(inputs=inputs, outputs=predictions)

# モデルをcompileします
model.compile(optimizer='adam',
              loss='sparse_categorical_crossentropy',
              metrics=['accuracy'])
print(model.summary())

# 学習します
hist = model.fit(x_train, y_train, validation_split=0.1, epochs=5)

# テストデータの予測精度を計算します
print(model.evaluate(x_test, y_test))