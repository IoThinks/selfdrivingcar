

##Lecture du potentiometre porte


#!/usr/bin/env python
# -*- coding: latin-1 -*-

import time
import RPi.GPIO as GPIO

GPIO.setmode( GPIO.BOARD )
DEBUG = 1

# Lit les données SPI d'une puce MCP3008, 8 canaux disponibles (adcnum de 0 à 7)
def readadc( adcnum, clockpin, mosipin, misopin, cspin ):
        if( (adcnum > 7) or (adcnum < 0)):
                return -1

        GPIO.output( cspin, True )

        GPIO.output( clockpin, False ) # met Clock à Low
        GPIO.output( cspin, False )    # met CS à Low (active le module MCP3008)

        commandout = adcnum # numéro de channel
        commandout |= 0x18  # OR pour ajouter Start bit + signle-ended bit
                            # 0x18 = 24d = 00011000b
        commandout <<=3     # décalage de 3 bits à gauche

        # Envoi des Bits sur le bus SPI
        for i in range(5):
                # faire un AND pour determiner l'état du bit de poids le plus 
                # fort (0x80 = 128d = 10000000b)
                if( commandout & 0x80 ): # faire un AND pour déterminer l'état du bit
                        GPIO.output( mosipin, True )
                else:
                        GPIO.output( mosipin, False )
                commandout <<= 1 # décalage de 1 bit sur la gauche

                # Envoi du bit mosipin avec signal d'horloge
                GPIO.output( clockpin, True )
                GPIO.output( clockpin, False )

        # lecture des bits renvoyés par le MCP3008
        # Lecture de 1  bit vide, 10 bits de données et un bit null
        adcout = 0
        for i in range(12):
                # Signal d'horloge pour que le MCP3008 place un bit
                GPIO.output( clockpin, True )
                GPIO.output( clockpin, False )
                # décalage de 1 bit vers la gauche
                adcout <<= 1
                # stockage du bit en fonction de la broche miso
                if( GPIO.input(misopin)):
                        adcout |= 0x1 # active le bit avec une opération OR

        # Mettre Chip Select à High (désactive le MCP3008)
        GPIO.output( cspin, True )

        # Le tout premier bit (celui de poids le plus faible, le dernier lut)
        # est null. Donc on l'elimine ce dernier bit en décalant vers la droite
        adcout >>= 1

        return adcout

# Broches connectées sur l'interface SPI du MCP3008 depuis le Cobbler
# (changer selon vos besoins)
# Broche I2C
SPICLK = 23
SPIMISO = 21
SPIMOSI = 19
SPICS = 22
# Broche ADC
potentiometer_adc = 0

# Initialisation de l'interface SPI
GPIO.setup(SPIMOSI, GPIO.OUT)
GPIO.setup(SPIMISO, GPIO.IN)
GPIO.setup(SPICLK, GPIO.OUT)
GPIO.setup(SPICS, GPIO.OUT)

while True:
        # Lecture analogique, retourne une valeur entre 0 et 1023 
        # pour une valeur de tension entre 0 et VRef (3.3v)
        trim_pot = readadc( potentiometer_adc, SPICLK, SPIMOSI, SPIMISO, SPICS )

        print( "Valeur: " + str( trim_pot ) )

        # convertir en tension
        print( "tension: "+ str( (5*trim_pot)/1024 ) )

        # attendre une demi-seconde
        time.sleep(0.2)
