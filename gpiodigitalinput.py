import RPi.GPIO as GPIO
import time
#from time import sleep

INPUTPIN = 10 #GPIO ALARMA1

GPIO.setwarnings(False)
GPIO.setmode(GPIO.BOARD)
GPIO.setup(8,GPIO.OUT)
GPIO.setup(10,GPIO.IN, pull_up_down=GPIO.PUD_DOWN)
#Ahorita se encuentra el configuracion Pull Down
#Si queremos cambiar de configuracion a Pull Up
#sera necesario cambiar a: GPIO.PUD_UP

estado_anterior = None

try:
    while True:
        estado = "ON" if GPIO.input(INPUTPIN) == GPIO.HIGH else "OFF"

        if estado != estado_anterior:
            #client.publish(TOPIC, estado, qos=1)
            print("Estado enviado:", estado)
            estado_anterior = estado

        time.sleep(0.2)

except KeyboardInterrupt:
    GPIO.cleanup()
