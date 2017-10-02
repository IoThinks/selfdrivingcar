#!/usr/bin/env python
# -*- coding: latin-1 -*-

import csv
from time import time,sleep
import RPi.GPIO as GPIO
import os
import signal
import sys

####################### Fonction Lecture Analogique ##############################################
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

########################### Action sur la Porte ########################################

def Fermer() :
        #os.system('mpg123 -q /home/domotique/End.mp3 ')
        debug = time()
        MOT=GPIO.PWM(MOTOR_Moins,40000)
        MOT.start(100)
        while readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS ) > (int(Min)+ int(Delta)) and (time()-debug)<2 :
            MOT.ChangeDutyCycle(100)
        while readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS ) > int(Min) and (time()-debug)<2 :
            MOT.ChangeDutyCycle(75)
        MOT.ChangeDutyCycle(100)
        MOT.stop()
        return 0

def Fermer2() :
        #os.system('mpg123 -q /home/domotique/End.mp3 ')
        debug = time()
        MOT=GPIO.PWM(MOTOR_Moins,40000)
        MOT.start(100)
        while readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS ) > (int(Min)+ int(Delta)) and (time()-debug)<2 :
            MOT.ChangeDutyCycle(100)
        while readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS ) > int(Min) and (time()-debug)<2 :
            MOT.ChangeDutyCycle(75)
        MOT.ChangeDutyCycle(100)
        MOT.stop()
        GPIO.output(MOTOR_Plus,True)
        sleep(0.14)
        GPIO.output(MOTOR_Plus,False)
        return 0

def signal_handler(signal, frame):
        print('     Fin de programme')
        GPIO.output(MOTOR_Moins,False)
        GPIO.output(MOTOR_Plus,False)
        GPIO.PWM(MOTOR_Moins,40000).stop()
        GPIO.PWM(MOTOR_Plus,40000).stop()
        sys.exit(0)

#########################################################################################

#detection de signel ctrl+c 
signal.signal(signal.SIGINT, signal_handler)
# Mode afectation numéro Pin
GPIO.setmode( GPIO.BOARD )
GPIO.setwarnings(False)
# Broche Moteur
MOTOR_Plus = 11
MOTOR_Moins = 13
# Broche I2C
SPICLK = 23
SPIMISO = 21
SPIMOSI = 19
SPICS = 22
# Broche ADC
PotPorte = 0


# Initialisation de l'interface SPI
GPIO.setup(SPIMOSI, GPIO.OUT)
GPIO.setup(SPIMISO, GPIO.IN)
GPIO.setup(SPICLK, GPIO.OUT)
GPIO.setup(SPICS, GPIO.OUT)
# Initialisation Moteur
GPIO.setup(MOTOR_Plus, GPIO.OUT)
GPIO.setup(MOTOR_Moins, GPIO.OUT)



# Variables
wait=0
Distance=0
wait=time()
Min=50
Max=600
Delta=100



'''######################################################################################
################################ Programme Principal ####################################
######################################################################################'''


os.system("clear")
print("Bienvenue dans le menue de debug")
print(" ")
print(" 1. Ouverture")
print(" 2. Fermeture")
print(" 3. PWM Fermer")
print(" ")

Choix = input("Choix de la fonction : ")

if Choix == "1" :

    MOT=GPIO.PWM(MOTOR_Moins,80000)
    MOT.start(0)
    MOT.ChangeDutyCycle(100)
    sleep(1)
    #input("Press Enter to continue...")
    MOT.stop()
    #sleep(1.5)
    #GPIO.output(MOTOR_Plus,False)

elif Choix == "2" :

    MOT=GPIO.PWM(MOTOR_Plus,80000)
    MOT.start(100)
    MOT.ChangeDutyCycle(100)
    sleep(1)
    MOT.stop()

elif Choix == "3" :
    Fermer()
    sleep(1)
    Ouvrir()

elif Choix == "4" :
    Fermer2()
    sleep(1)
    Ouvrir()

    #input("Press Enter to continue...")
