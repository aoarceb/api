import json
import requests
import paho.mqtt.client as mqtt
from datetime import datetime

MQTT_BROKER = "localhost"
MQTT_PORT = 1883
MQTT_TOPIC = "alarmas/carce/+/+/+"

API_URL = "http://localhost/apiGuardaAlarma.php"
#API_KEY = "MI_CLAVE_SECRETA"

def on_connect(client, userdata, flags, rc):
    print("Conectado a MQTT")
    client.subscribe(MQTT_TOPIC, qos=1)

def on_message(client, userdata, msg):
    try:
        topic_parts = msg.topic.split("/")

        usuario = topic_parts[1]
        dispositivo = topic_parts[2] #ejem puertas
        ubicacion = topic_parts[3] #ejem puerta principal
        tipo = topic_parts[4]  # digital | analog

        payload = msg.payload.decode().strip()

        data = {
            "usuario": usuario,
            "nombre_alarma": dispositivo,
            "ubicacion_alarma": ubicacion,
            "tipo": tipo,
            "fecha_evento": datetime.now().isoformat()
        }

        if tipo == "digital":
            data["estado"] = payload  # ON / OFF

        elif tipo == "analog":
            analog_data = json.loads(payload)
            data.update(analog_data)

        else:
            print("Tipo desconocido:", tipo)
            return

        requests.post(
            API_URL,
            json=data,
            #headers={"X-API-KEY": API_KEY},
            headers={"Content-Type": "application/json"},
            timeout=5
        )

        print(f"Evento {tipo} recibido de {dispositivo}")

    except Exception as e:
        print("Error:", e)

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

client.connect(MQTT_BROKER, MQTT_PORT, 60)
client.loop_forever()
