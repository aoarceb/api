estado_anterior = None

try:
    while True:
        estado = "ON" if GPIO.input(PIN) == GPIO.LOW else "OFF"

        if estado != estado_anterior:
            client.publish(TOPIC, estado, qos=1)
            print("Estado enviado:", estado)
            estado_anterior = estado

        time.sleep(0.2)

except KeyboardInterrupt:
    GPIO.cleanup()
