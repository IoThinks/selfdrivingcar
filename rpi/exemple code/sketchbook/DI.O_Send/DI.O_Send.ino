
//see below detail of the protocol.
byte rcPin = 8, led = 13;

unsigned long shiftedCode;
const unsigned int THIGH = 220, TSHORT = 350, TLONG=1400;
String val;
int nloop = 3;

void setup()
{
  Serial.begin(115200);
  pinMode(rcPin, OUTPUT);
  pinMode(led, OUTPUT);
  //Serial.println("    Telecomande");
  //Serial.println("1 - On      2 - Off");
  //Serial.println("3 - On      4 - Off");
  //Serial.println("5 - On      6 - Off");
}

void loop() {
  if (Serial.available() > 0) {
    val = Serial.readStringUntil('\n');
    
    if (val == "ModeProg") {
      nloop = 20;
      digitalWrite(led, HIGH);
    }
    else if (val == "ModeNormal") {
      nloop = 3;
      digitalWrite(led, LOW);
    }
    else {
      for (int i = 0; i < nloop; i++) { // 20 cycles pour init. d'une prise
      //on: 756963216, off: 756963200 button 1
      //on: 756963217, off: 756963201 button 2
      //on: 756963218, off: 756963202 button 3
      
        if (val == "DIO1ON") {
            shiftedCode = 756963216;
        }
        else if (val=="DIO1OFF"){
            shiftedCode = 756963200;
        }
        else if (val=="DIO2ON") {
            shiftedCode = 756963217;
        }
        else if (val=="DIO2OFF"){
            shiftedCode = 756963201;
        }
        else if (val=="DIO3ON") {
            shiftedCode = 756963218;
        }
        else if (val=="DIO3OFF"){
            shiftedCode = 756963202;
        }
        
        //Sequence de verrou anoncant le départ du signal au recepeteur
        digitalWrite(rcPin, HIGH);
        delayMicroseconds(THIGH);
        digitalWrite(rcPin, LOW); 
        delayMicroseconds(2675);
        for (int i = 0; i < 32; i++) {
          if (shiftedCode & 0x80000000L) {
            digitalWrite(rcPin, HIGH);
            delayMicroseconds(THIGH);
            digitalWrite(rcPin, LOW);
            delayMicroseconds(TLONG);
            digitalWrite(rcPin, HIGH);
            delayMicroseconds(THIGH);
            digitalWrite(rcPin, LOW);
            delayMicroseconds(TSHORT);
          } else {
            digitalWrite(rcPin, HIGH);
            delayMicroseconds(THIGH);
            digitalWrite(rcPin, LOW);
            delayMicroseconds(TSHORT);
            digitalWrite(rcPin, HIGH);
            delayMicroseconds(THIGH);
            digitalWrite(rcPin, LOW);
            delayMicroseconds(TLONG);
          }
          shiftedCode <<= 1;
        }
        digitalWrite(rcPin, HIGH);
        delayMicroseconds(THIGH);
        digitalWrite(rcPin, LOW);
        delayMicroseconds(10600);
        digitalWrite(rcPin, HIGH);
        delayMicroseconds(THIGH);
      }
    }
  } 
}

//http://homeeasyhacking.wikia.com/wiki
//
//Device Addressing Edit
//
//To send a dimming level a special modified bit is placed
//at bit 27 (See Specification)
//
//Encoding Edit
//
//Manchester coding is used:
//Data 0 = Manchester 01          Data 1 = Manchester 10
//A Manchester 0 is a High for 275uS and Low for 275uS
//A Manchester 1 is a High for 275uS and Low for 1225uS
//So.......
//0 = High 275uS, Low 275uS, High 275uS, Low 1225uS
//1 = High 275uS, Low 1225uS, High 275uS, Low 275uS
//A preamble is sent before each command which is High 275uS, Low 2675uS
//
//When sending a dim level a special bit is placed in bit 27
//Dim bit 27 = High 275uS, Low 275uS, High 275uS, Low 275uS.
//This seems a bit odd, and goes against the manchester coding
//specification !
//
//XXXXXXXXXXXXXXXXXXXXXXXXXXX01100  (32 bits)
//         bit 26, on / off
//      bit 27-31, button number
