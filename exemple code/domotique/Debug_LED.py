#!/usr/bin/env python
# -*- coding: latin-1 -*-

import time
import RPi.GPIO as GPIO
# Mode afectation numéro Pin
GPIO.setmode( GPIO.BOARD )
GPIO.setwarnings(False)
# Broche LEDs
LED_Red = 3
LED_Green = 5
LED_Blue = 7

GPIO.setup(LED_Red, GPIO.OUT)
GPIO.setup(LED_Green, GPIO.OUT)
GPIO.setup(LED_Blue, GPIO.OUT)



print("rouge")
GPIO.output(LED_Red, 1)
time.sleep (2)
GPIO.output(LED_Red,0)

print("Vert")
GPIO.output(LED_Green, 1)
time.sleep (2)
GPIO.output(LED_Green,0)

print("bleu")
BLUE=GPIO.PWM(LED_Blue,50)
BLUE.start(100)
time.sleep(2)
BLUE.stop()