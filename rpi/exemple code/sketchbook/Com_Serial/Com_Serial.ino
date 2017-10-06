String val;

void setup() {
  Serial.begin(115200);
  pinMode(13, OUTPUT);
}
 
void loop() {
  //Serial.println("Hello");
  if (Serial.available() > 0) {
    val = Serial.readStringUntil('\n');
    Serial.println(val);
    if (val == "1ON") {
          Serial.println("1 On");
          digitalWrite(13, HIGH);
      }
      else if (val=="1OFF"){
          Serial.println("1 Off");
          digitalWrite(13, LOW);
      }
  }
  delay(10);
}
