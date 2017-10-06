import serial
import sys
import signal
import os
 
def signal_handler(signal, frame):
        print('     Fin de programme')
        sys.exit(0)

signal.signal(signal.SIGINT, signal_handler)
ser = serial.Serial('/dev/ttyUSB0', 115200)


os.system("clear")
print("Bienvenue dans le menue de debug liaison Serie")
print(" ")
print("    Telecomande")
print("1ON      1OFF")
print("2ON      2OFF")
print("3ON      3OFF")
print(" ")

while 1 : 
    Choix = input("Choix de la fonction : ")

    ser.write(Choix.encode())
    print("envoi de la trame : "+ Choix)
    print(" ")
