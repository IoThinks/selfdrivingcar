#!/usr/bin/env python
# -*- coding: latin-1 -*-

import csv
import time
import RPi.GPIO as GPIO
import os

############################ Capteur ultrason ############################################  

def Pulse (Var) :
    if (Var=="Ext") :
        GPIO_ECHO=GPIO_ECHO_Ext
    else :
        GPIO_ECHO=GPIO_ECHO_Int

    i=0
    moyenne=0
    Nbloop=4
    for i in range(0,Nbloop) :
        time.sleep(0.02)
        start=0
        stop=0

        # Send 10us pulse to trigger
        GPIO.output(GPIO_TRIGGER, True)
        time.sleep(0.00001)
        GPIO.output(GPIO_TRIGGER, False)

        debug = time.time()
        while GPIO.input(GPIO_ECHO)==0 and (time.time()-debug)<0.02 :
            start = time.time()

        debug = time.time()    
        while GPIO.input(GPIO_ECHO)==1 and (time.time()-debug)<0.02 :
            stop = time.time()

        #print("Start : " + str(start))
        #print("Stop : "+ str(stop))

        if start != 0 and stop != 0 :
            elapsed = stop-start
            distance = ( elapsed * 34000 )
            moyenne=moyenne + distance
        else :
            distance = 175
        i=i+1

    moyenne=moyenne/Nbloop
    #print (moyenne)
    return moyenne


#########################################################################################

# Mode afectation numéro Pin
GPIO.setmode( GPIO.BOARD )
GPIO.setwarnings(False)
# Broche capteur Ultrason Exterieur
GPIO_TRIGGER = 33
GPIO_ECHO_Ext = 36
# Broche capteur Ultrason Interieur
GPIO_ECHO_Int = 35
# Fichier Data
Data_Location = "/var/www/Data.csv"
# Led
LED_Porte = 37

# Initialisation Capteur Ultrason Extreieur
GPIO.setup(GPIO_TRIGGER, GPIO.OUT)
GPIO.setup(GPIO_ECHO_Ext, GPIO.IN)
# Initialisation Capteur Ultrason Interieur
GPIO.setup(GPIO_ECHO_Int, GPIO.IN)
# Initialisation Led
GPIO.setup(LED_Porte, GPIO.OUT)

# Variables
wait=0
Distance=0
wait=time.time()
detection=0
DistTest= 50 # distance de test


'''######################################################################################
################################ Programme Principal ####################################
######################################################################################'''

os.system("clear")
print("Bienvenue dans le menue de debug")
print(" ")
print(" 1. Capteur Interieur")
print(" 2. Capteur Exterieur")
print(" ")

Choix = input("Choix de la fonction : ")

if Choix == "1" :
    print("Mesure capteur interieur : ")
    while True :

        Variable = Pulse("Int")
        if (Variable < DistTest) :
            GPIO.output( LED_Porte, True )
            print(Variable)
        else :
            GPIO.output( LED_Porte, False )


elif Choix == "2" :

    print("Mesure capteur Exterieur : ")
    while True :

        Variable = Pulse("Ext")
        if (Variable < DistTest) :
            GPIO.output( LED_Porte, True )
            print(Variable)
        else :
            GPIO.output( LED_Porte, False )

elif Choix == "3" :

    while True :
        L=GPIO.PWM(LED_Porte,0.5)
        L.start(5)
        #sleep(20000)

        input("Press Enter to continue...")




