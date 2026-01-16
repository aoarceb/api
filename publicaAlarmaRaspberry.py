import RPi.GPIO as GPIO
import paho.mqtt.client as mqtt
import time

# ---------- CONFIG ----------
BROKER = "localhost"
TOPIC = "alarmas/carce/puertas/principal/digital"
PIN = 10  # GPIO

GPIO.setmode(GPIO.BOARD)
GPIO.setup(PIN, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

client = mqtt.Client(mqtt.CallbackAPIVersion.VERSION2)
client.connect(BROKER, 1883, 60)

estado_anterior = None

try:
    while True:
        estado = "ON" if GPIO.input(PIN) == GPIO.HIGH else "OFF"

        if estado != estado_anterior:
            client.publish(TOPIC, estado, qos=1)
            print("Estado enviado:", estado)
            estado_anterior = estado

        time.sleep(0.2)

except KeyboardInterrupt:
    GPIO.cleanup()
