#!/usr/bin/env python
# -*- coding: latin-1 -*-

#Execution Auto dans le fichier etc\rc.local


import http.client, urllib.parse
#import urllib.request
#import urllib.parse

import datetime
import csv
from time import time,sleep,strftime
import RPi.GPIO as GPIO
import os
import signal
import sys
import serial



############################ Modification de la valeur ############################################

def ModificationValeur(donnee,categorie,valeur) :
    #print("donnee du debut" + donnee)
    debut=donnee.find(";",(donnee.find(categorie)))+1
    fin=donnee.find(";",debut)
    #print("debut = "+str(debut)+"/rFin = " +str(fin)+"/r")
    donnee=donnee[:debut]+ valeur + donnee[fin:]
    #print("donnee de fin   " + donnee)
    return donnee


############################ Fin de Programme ############################################

def signal_handler(signal, frame):
        print('     Fin de programme')
        sys.exit(0)

#########################################################################################

#detection de signel ctrl+c 
signal.signal(signal.SIGINT, signal_handler)
#Fichier Data
Data_Location = "/var/www/html/data/Data.csv"

ser = serial.Serial('/dev/ttyUSB0', 115200)


'''######################################################################################
################################ Programme Principal ####################################
######################################################################################'''

while True:

################################ lecture des data ############################

    ValEntree = ""
    while ValEntree == "" or ValEntree == None:
        data = open(Data_Location, "r")
        ValEntree = data.read()
        ValSortie = ValEntree
        data.close()

    with open(Data_Location) as csvfile:
        readCSV = csv.reader(csvfile, delimiter=';')
        for row in readCSV:
            if row[0]=="Cycle Porte" :
                Cycle=row[1]
            elif row[0]=="Capteur Exterieur" :
                CaptExt=row[1]
            elif row[0]=="Capteur Interieur" :
                CaptInt=row[1]  
            elif row[0]=="Max" :
                Max=row[1]
            elif row[0]=="Min" :
                Min=row[1]
            elif row[0]=="Delta" :
                Delta=row[1]
            elif row[0]=="Time" :
                Time=row[1]
            elif row[0]=="RGBState" :
                RGBState=row[1]
            elif row[0]=="RGBValue" :
                RGBValue=row[1]
            elif row[0]=="Regulation" :
                Regulation=row[1]
            elif row[0]=="Angle" :
                Angle=int(row[1])*5
            elif row[0]=="AutoOpen" :
                AutoOpen=row[1]
            elif row[0]=="AutoOpenTime" :
                AutoOpenTime=row[1]
            elif row[0]=="AutoClose" :
                AutoClose=row[1]
            elif row[0]=="AutoCloseTime" :
                AutoCloseTime=row[1]
            elif row[0]=="DIOCmd" :
                DIOCmd=row[1]

    #print("DIOCmd : " + DIOCmd)


    csvfile.close()

    BLUE=int(RGBValue[6:9])
    GREEN=int(RGBValue[3:6])
    RED=int(RGBValue[0:3])

############################## Controle DI.O remote ###############################

    if DIOCmd != "WaitData" :
        ser.write(DIOCmd.encode())
        ValSortie=ModificationValeur(ValSortie,"DIOCmd","WaitData")
        print(str(datetime.datetime.now().time())+" - Envoi de la trame : "+ DIOCmd)
        ValSortie


    if ValEntree!=ValSortie :
        data = open(Data_Location, "w")
        data.write(ValSortie)
        data.close()
        sleep(0.01)

    sleep(0.01)