import keras
from keras.models import Model
from keras.layers import Input, Dense, Activation, Dropout
from keras.layers import Conv2D, GlobalAveragePooling2D
from keras.layers import BatchNormalization, Add
from keras.callbacks import ModelCheckpoint
from keras.utils import to_categorical
from keras.initializers import he_normal
import keras.backend as K

# redefine target data into one hot vector
classes = 19
Y_train = to_categorical(y_train, classes)
Y_test = to_categorical(y_test, classes)

def cba(inputs, filters, kernel_size, strides):
    x = Conv2D(filters, kernel_size=kernel_size, strides=strides, padding='same', kernel_initializer=he_normal())(inputs)
    #x = BatchNormalization()(x) #バッチノーマライゼーションは入れない方が精度が出る
    x = Activation("relu")(x)
    return x

# define CNN
inputs = Input(shape=(X_train_.shape[1:]))

x = cba(inputs, filters=32, kernel_size=(3,3), strides=(2,2),)
x = Dropout(0.2)(x)
x = cba(x, filters=64, kernel_size=(3,3), strides=(2,2))
x = Dropout(0.2)(x)
x = cba(x, filters=128, kernel_size=(3,3), strides=(2,2))
x = Dropout(0.2)(x)

x = cba(x, filters=128, kernel_size=(3,3), strides=(2,2))
x = Dropout(0.2)(x)
x = cba(x, filters=256, kernel_size=(3,3), strides=(2,2))
x = Dropout(0.2)(x)
x = cba(x, filters=256, kernel_size=(3,3), strides=(2,2))
x = Dropout(0.2)(x)

x = GlobalAveragePooling2D()(x)
#x = keras.layers.Lambda(lambda xx: alpha*(xx)/K.sqrt(K.sum(xx**2)))(x) #metrics learning
x = Dense(classes)(x)
x = Activation("softmax")(x)

model = Model(inputs, x)
#model.summary()

# initiate Adam optimizer
opt = keras.optimizers.adam(lr=0.0001, decay=1e-6, amsgrad=True)

# Let's train the model using Adam with amsgrad
model.compile(loss='categorical_crossentropy',
              optimizer=opt,
              metrics=['accuracy'])

hist = model.fit(X_train_,Y_train,
                 validation_data=(X_test_,Y_test),
                 epochs=400,
                 callbacks=[ ModelCheckpoint(path + 'weights.h5', monitor='val_acc',
                                         verbose=1, mode='auto', save_best_only='true')],
                 verbose=1,
                 batch_size=50)

model_json = model.to_json()
open(path + 'model.json', 'w').write(model_json)

#結果描画
plt.figure()               
plt.title("loss")
plt.plot(hist.history['loss'],label="train_loss")
plt.plot(hist.history['val_loss'],label="val_loss")
plt.legend()
plt.show()

plt.figure()               
plt.title("accuracy")
plt.plot(hist.history['acc'],label="train_acc")
plt.plot(hist.history['val_acc'],label="val_acc")
plt.legend(loc="lower right")
plt.show()
