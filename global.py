import csv
import signal
import serial
import sys
import os
from time import sleep

def signal_handler(signal, frame):
        print('Fin de programme')
        ser.write(b's')
        sys.exit(0)

signal.signal(signal.SIGINT, signal_handler)

# variables
ser = serial.Serial('/dev/ttyACM0', 115200)
sleep(2)
Data_Location = "/var/www/html/data/data.csv"

Action=""
LastAction="stop"



data = open(Data_Location, "w")
data.write("Action;stop;")
data.close()
sleep(0.01)
print("Initialisation terminée") 

while True:

    # Lecture de la base de donnée
    with open(Data_Location) as csvfile:
        readCSV = csv.reader(csvfile, delimiter=';')
        for row in readCSV:
            if row[0]=="Action" :
                Action=row[1]
    csvfile.close()

    # Actions
    if LastAction!=Action :
        LastAction=Action

        if (Action=="haut") :
            ser.write(b'h')  
        elif (Action=="bas") :
            ser.write(b'b') 
        elif (Action=="droite") :
            ser.write(b'd') 
        elif (Action=="gauche") :
            ser.write(b'g') 
        elif (Action=="stop") :
            ser.write(b's') 
        print(Action)
    	# ser.write("string".encode()) # methode 1sec delay