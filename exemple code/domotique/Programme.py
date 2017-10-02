#!/usr/bin/env python
# -*- coding: latin-1 -*-

#Execution Auto dans le fichier etc\rc.local


import http.client, urllib.parse
#import urllib.request
#import urllib.parse


import csv
from time import time,sleep,strftime
import RPi.GPIO as GPIO
import os
import signal
import sys

######################## Fonction Lecture Analogique ##############################################
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

def Ouvrir() :
        debug = time()
        while readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS ) < int(Max) and (time()-debug)<3 :
            GPIO.output(MOTOR_Plus,True)
        GPIO.output(MOTOR_Plus,False)
        return 0
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

        #Arret Inertie
        GPIO.output(MOTOR_Plus,True)
        sleep(0.14)
        GPIO.output(MOTOR_Plus,False)
        return 0
############################ Capteur ultrason V.1 ############################################  

def Pulse (Var) :
    if (Var=="Ext") :
        GPIO_ECHO=GPIO_ECHO_Ext
    else :
        GPIO_ECHO=GPIO_ECHO_Int

    i=0
    moyenne=0
    Nbloop=4
    for i in range(0,Nbloop) :
        sleep(0.02)
        start=0
        stop=0

        # Send 10us pulse to trigger
        GPIO.output(GPIO_TRIGGER, True)
        sleep(0.00001)
        GPIO.output(GPIO_TRIGGER, False)

        debug = time()
        while GPIO.input(GPIO_ECHO)==0 and (time()-debug)<0.02 :
            start = time()

        debug = time()    
        while GPIO.input(GPIO_ECHO)==1 and (time()-debug)<0.02 :
            stop = time()

        if start != 0 and stop != 0 :
            moyenne+=(stop-start) * 34000
            #elapsed = stop-start
            ##distance = ( elapsed * 34000 )
            #moyenne=moyenne + distance
        else :
            distance = 175
        i=i+1

    moyenne=moyenne/(Nbloop+1)
    
    if Var=="Ext" : 
        print ("moyenne Ext : " + str(moyenne))
    else :
        print ("                                    " + str(moyenne)+ " : moyenne Int")
    
    return moyenne

############################ Capteur ultrason V.2 ############################################  
'''
def Pulse (Var) :
    if (Var=="Ext") :
        GPIO_TRIGGER=GPIO_TRIGGER_Ext
        GPIO_ECHO=GPIO_ECHO_Ext
    else :
        GPIO_TRIGGER=GPIO_TRIGGER_Int
        GPIO_ECHO=GPIO_ECHO_Int

    i=0
    mesures = [0]*3
    Nbloop=3
    for i in range(0,Nbloop) :
        sleep(0.02)

        GPIO.output(GPIO_TRIGGER, True)
        GPIO.output(GPIO_TRIGGER, False)
        Debug=time()
        while True :
            if GPIO.input(GPIO_ECHO)==1 or (time()-Debug)>0.2:
                start = time()
                break

        while True :
            if GPIO.input(GPIO_ECHO)==0 :
                stop = time()
                break

        mesures[i]=stop-start


    moyenne=sorted(mesures)[1]*17000

    if Var=="Ext" : 
        print ("moyenne Ext : " + str(moyenne))
    else :
        print ("                                    " + str(moyenne)+ " : moyenne Int")

    return moyenne
'''

############################ Modification de la valeur ############################################

def ModificationValeur(donnee,categorie,valeur) :
    #print("donnee du debut" + donnee)
    debut=donnee.find(";",(donnee.find(categorie)))+1
    fin=donnee.find(";",debut)
    #print("debut = "+str(debut)+"/rFin = " +str(fin)+"/r")
    donnee=donnee[:debut]+ valeur + donnee[fin:]
    #print("donnee de fin   " + donnee)
    return donnee

############################ Asservissement Porte ############################################

def regulation(CaptAngle) :
    print("debut regulation")
    if (Angle>CaptAngle) :
        print("Commande > Capteur")
        MOT=GPIO.PWM(MOTOR_Plus,40000)
        MOT.start(vitesse())
        while (Angle-int(Delta) >= readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS )) :
            MOT.ChangeDutyCycle(vitesse())
        MOT.stop()
    else :
        print("Capteur > Commande")
        MOT=GPIO.PWM(MOTOR_Moins,40000)
        MOT.start(vitesse())
        while (Angle+int(Delta) <= readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS )) :
            MOT.ChangeDutyCycle(vitesse())
        MOT.stop()
    return 0


def vitesse():
    vitesse=abs((Angle-readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS ))/2)
    print("vitesse = " + str(vitesse))
    if vitesse>100 :
        vitesse = 100
    elif vitesse<0 :
        vitesse = 0
    return vitesse
############################ Fin de Programme ############################################

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
# Broche capteur Ultrason 
GPIO_TRIGGER = 33
GPIO_ECHO_Ext = 35
GPIO_ECHO_Int = 36
# Broche I2C
SPICLK = 23
SPIMISO = 21
SPIMOSI = 19
SPICS = 22
# Broche ADC
PotPorte = 0
# Broche LEDs
LED_Red = 3
LED_Green = 5
LED_Blue = 7
# Broche Capteur Mouvement
CAPTEUR = 11
#Fichier Data
Data_Location = "/var/www/Data.csv"

# Initialisation de l'interface SPI
GPIO.setup(SPIMOSI, GPIO.OUT)
GPIO.setup(SPIMISO, GPIO.IN)
GPIO.setup(SPICLK, GPIO.OUT)
GPIO.setup(SPICS, GPIO.OUT)
# Initialisation Moteur
GPIO.setup(MOTOR_Plus, GPIO.OUT)
GPIO.setup(MOTOR_Moins, GPIO.OUT)
# Initialisation Capteur Ultrason Extreieur
GPIO.setup(GPIO_TRIGGER, GPIO.OUT)
GPIO.setup(GPIO_ECHO_Ext, GPIO.IN)
# Initialisation Capteur Ultrason Interieur
GPIO.setup(GPIO_ECHO_Int, GPIO.IN)
# Initialisation Capteur de Mouvement
GPIO.setup(CAPTEUR, GPIO.IN)
# Initialisation LED Strip
GPIO.setup(LED_Red, GPIO.OUT)
GPIO.setup(LED_Green, GPIO.OUT)
GPIO.setup(LED_Blue, GPIO.OUT)


# Variables

Distance=0
wait=time()
detection=0
position1=0
position2=0
SecureVolet=0

# PWM
PWMRed = GPIO.PWM(LED_Red,50)
PWMGreen = GPIO.PWM(LED_Green,50)
PWMBlue = GPIO.PWM(LED_Blue,50)
REDprevious=0
GREENprevious=0
BLUEprevious=0

position1=readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS )

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

    with open('/var/www/Data.csv') as csvfile:
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
    csvfile.close()

    BLUE=int(RGBValue[6:9])
    GREEN=int(RGBValue[3:6])
    RED=int(RGBValue[0:3])



################################ Affichage des data ############################
    
    '''
    print("Cycle : " + Cycle)
    print("CaptExt : " + CaptExt)
    print("CaptInt : " + CaptInt)
    print("Max : " + Max)
    print("Min : " + Min)
    print("Delta : " + Delta)
    print("Time : " + Time)
    print("Angle : " + Angle)
    print("RGB : " + RGBState)
    print("RGBValue : " + RGBValue)
    print(RED)
    print(GREEN)
    print(BLUE)
    '''

############################## Contole d'angle ###############################

    if (Regulation=="On") :
        CaptAngle = readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS )
        #print("Commande : "+str(Angle))
        #print("Capteur : "+str(CaptAngle))
        if (CaptAngle<Angle-int(Delta) or CaptAngle>Angle+int(Delta)) :
            test=regulation(CaptAngle)
            print("Fin Asservissement")
        sleep(0.05)
        position1=readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS )

############################## Capteur Ultrason ###############################
    
    elif Cycle=="Close" :
        if (time()-wait)>0.5 :
            if (CaptExt=="Activer" and Pulse("Ext")<50) : 
                Cycle="Cycle"
                print ("Ouverture via Capteur Exterieur")

            if (CaptInt=="Activer" and Pulse("Int")<150) :
                Cycle="Cycle"
                print ("Ouverture via Capteur interieur")

####################### Ouverture de porte a la main #########################################
            
            if (CaptInt=="Desactiver" and CaptExt=="Desactiver") :
                position2=readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS )
                #print("position : "  + str(position2-position1))
                if (position2 - position1)>=2 :
                    Cycle="Cycle"
                    print ("Ouverture via une personne")
                position1=position2
            

############################## Passage en porte ouverte ###############################
        '''
        else :
            if readadc( PotPorte, SPICLK, SPIMOSI, SPIMISO, SPICS )>= int(Max) :
                ValSortie=ValEntree.replace('Fermer','Ouvert')
        '''
############################## Traitement commande PHP + anti Spam ###################################

    if Cycle!="Close" :
        
        if Cycle=="Ouvert" :
            if Pulse("Int")<5 :
                Fermer()
                ValSortie=ModificationValeur(ValSortie,"Cycle Porte","Close")
                print ("Fermeture via Capteur interieur")

        elif Cycle=="Cycle" :
            Ouvrir()
            '''
            #prog temporaire en attente de réparation capteur
            sleep(int(Time))
            Fermer()
            ValSortie=ModificationValeur(ValSortie,"Cycle Porte","Close")
            '''
            debug = time()
            dist = Pulse("Int")
            while((time()-debug)<int(Time) and dist>5) :
                dist = Pulse("Int")

            if dist>5 :
                Fermer()
                ValSortie=ModificationValeur(ValSortie,"Cycle Porte","Close")
            else :
                GPIO.output(MOTOR_Plus,True)
                sleep(0.05)
                GPIO.output(MOTOR_Plus,False)
                sleep(2)
                ValSortie=ModificationValeur(ValSortie,"Cycle Porte","Ouvert")
            


    
        elif Cycle=="Ouvrir" :
            Ouvrir()
            ValSortie=ModificationValeur(ValSortie,"Cycle Porte","Ouvert")
            print ("Oureture via WEB")
    
        elif Cycle=="Fermer" :
            Fermer()
            ValSortie=ModificationValeur(ValSortie,"Cycle Porte","Close")
            print ("Fermeture via WEB")
        
        wait = time()


####################### LED Strip #########################################

    if RGBState!="off" :
        if RGBState=="Eteint":
            PWMRed.stop()
            PWMGreen.stop()
            PWMBlue.stop()
            ValSortie=ModificationValeur(ValSortie,"RGBState","off") 
        elif RGBState=="Allumer" :
            PWMRed.start(int(RED/2.55))
            REDprevious=RED
            PWMGreen.start(int(GREEN/2.55))
            GREENprevious=GREEN
            PWMBlue.start(int(BLUE/2.55))
            BLUEprevious=BLUE
            ValSortie=ModificationValeur(ValSortie,"RGBState","on") 
        elif RGBState=="on" :
            if RED != REDprevious :
                PWMRed.ChangeDutyCycle(int(RED/2.55))
                REDprevious=RED
                print("in R")
            if GREEN != GREENprevious :
                PWMGreen.ChangeDutyCycle(int(GREEN/2.55))
                GREENprevious=GREEN
                print("in G")
            if BLUE != BLUEprevious :
                PWMBlue.ChangeDutyCycle(int(BLUE/2.55))
                BLUEprevious=BLUE
                print("in B") 


####################### Gestion Volet #########################################

    if AutoOpen=="Activer" :
        if AutoOpenTime == strftime('%H'+':'+'%M') and SecureVolet == 0 :
            params = urllib.parse.urlencode({'Valeur': 'Volet20'})
            conn = http.client.HTTPConnection("192.168.0.27:80")
            conn.request("POST", "", params)
            SecureVolet = 1
        elif AutoOpenTime != strftime('%H'+':'+'%M') and AutoCloseTime != strftime('%H'+':'+'%M') :
            SecureVolet = 0

    if AutoClose=="Activer" :
        if AutoCloseTime == strftime('%H'+':'+'%M') and SecureVolet == 0 :
            params = urllib.parse.urlencode({'Valeur': 'Volet0'})
            conn = http.client.HTTPConnection("192.168.0.27:80")
            conn.request("POST", "", params)
            SecureVolet = 1
        elif AutoOpenTime != strftime('%H'+':'+'%M') and AutoCloseTime != strftime('%H'+':'+'%M') :
            SecureVolet = 0
        
############################## Ecriture des Data ###################################
    
    #print (ValEntree)
    #print (ValSortie)
    #sleep(0.2)

    if ValEntree!=ValSortie :
        data = open(Data_Location, "w")
        data.write(ValSortie)
        data.close()
        sleep(0.01)

    #input("Press Enter to continue...")
