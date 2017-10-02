#!/usr/bin/env python
# -*- coding: latin-1 -*-
import time
import serial
ser = serial.Serial('/dev/ttyAMA0', 115200)
time.sleep(1)

var = 710
message = "angleMax"+ str(var)

#message="angleMax710"
ser.write(message.encode())
#ser.write("angleMax710".encode())
print(ser.readline())
#time.sleep (1)
#ser.write("Cycle".encode())
print(ser.readline())
print(ser.readline())
print(ser.readline())
