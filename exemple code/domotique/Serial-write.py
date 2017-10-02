#!/usr/bin/env python
# -*- coding: utf-8 -*-
 
# Le Raspbery Pi envoie des messages à l'Arduino
 
import serial  # bibliothèque permettant la communication série
import time    # pour le délai d'attente entre les messages
 
ser = serial.Serial('/dev/ttyACM0', 9600)

while True:     # boucle répétée jusqu'à l'interruption du programme
    ser.write("1".encode())
    time.sleep(1)
    ser.write("2".encode())
    time.sleep(1)