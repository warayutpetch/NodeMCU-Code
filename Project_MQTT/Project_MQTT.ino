
#include <ESP8266WiFi.h>
#include <PubSubClient.h>
#include <Wire.h>
#include <DS3231.h>
#include"String.h"
#include <ESP8266HTTPClient.h>
#include <time.h>
#include <EEPROM.h>

/////////////setting database
HTTPClient http;
int timezone = 7 * 3600;                    //ตั้งค่า TimeZone ตามเวลาประเทศไทย
int dst = 0;                                //กำหนดค่า Date Swing Time

///RTC DS3231
DS3231 ck;
RTCDateTime dt;

// Update these with values suitable for your network.
const char* ssid = "CPE_PROJECT";
const char* password = "571733022";

// Config MQTT Server
#define mqtt_server "m21.cloudmqtt.com"
#define mqtt_port 18307
#define mqtt_user "Test"
#define mqtt_password "12345678"

//// Setting Output Solenoid Value
#define LED_PIN D3
#define LED_PIN1 D4
#define LED_PIN2 D5
#define LED_PIN3 D6
#define Status D8
#define F_PIN2 D0


WiFiClient espClient;
PubSubClient client(espClient);

typedef struct  {    // structure of the time record
  int on_hour;      // hour
  int on_min;       // minute
  int on_sec;       // second
  int off_hour;      // hour
  int off_min;       // minute
  int off_sec;       // second
  String onday;
} SETTIME;

int chk_Con = 0;
int chk_Send = 0;
SETTIME time_set[16];
int i, j, k;
float tmp;
char buf[3];
char timeall[] = "07000705080008050900090510001005070607110806081109060911100610110712071708120817091209171012101707180723081808230918092310181023";
char tmp_time[20];
char tmp_char[15];
int slot_No;
char sendtime[20];
String H_on, M_on, S_on,
       H_off, M_off, S_off;

char Smart[10] = "00000000";
int smart_time = 5 , smart_moisture = 50;
String moiture_S, time_S;
int val1, val2, val3, val4;

char statusVal[5] = "1111";

int F1_1 = 0, F1_2 = 0,
    F2_1 = 0, F2_2 = 0,
    F3_1 = 0, F3_2 = 0,
    F4_1 = 0, F4_2 = 0;
int soli_State = 0;
int sensorPin = A0;
int sensorValue = 0;
int Moisture = 0; // variable to store the value coming from the moisture sensor
int status_smart;
int start_count;
int cout_timer = 0;
int onoff_smart = 0;
int onoff_timer = 0;
long interval_A = 5000;
long previousMillis_A = 0;

long interval_flow = 1000;
long previousMillis_flow = 0;
int chk = 0;

//setting flow sensor
volatile int NbTopsFan; //measuring the rising edges of the signal
int Calc;
int hallsensor = D7;    //The pin location of the sensor : D8
char stateTimer[17] = "1111111111111111";
long chkFlowError = 0;
char week[] = "1111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111";
String dayweek;

void setup() {
  EEPROM.begin(512);
  Serial.setDebugOutput(true);
  pinMode(LED_PIN, OUTPUT);
  pinMode(LED_PIN1, OUTPUT);
  pinMode(LED_PIN2, OUTPUT);
  pinMode(LED_PIN3, OUTPUT);
  pinMode(Status, OUTPUT);
  pinMode(hallsensor, INPUT);
  pinMode(sensorPin, INPUT);
  Serial.begin(115200);
  delay(10);


  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());

  /// Update RTC from NTP
  ck.begin();
  configTime(timezone, dst, "pool.ntp.org", "time.nist.gov");
  while (!time(nullptr)) {
    Serial.print(".");
    delay(1000);
  }
  time_t now = time(nullptr);
  struct tm* p_tm = localtime(&now);
  ck.setDateTime(2017, 10, p_tm->tm_mday, p_tm->tm_hour, p_tm->tm_min, p_tm->tm_sec);


  client.setServer(mqtt_server, mqtt_port);
  client.setCallback(callback);
  attachInterrupt(hallsensor, rpm, RISING);

  tmr_set();
  ////
  EEPROM_write(512, "0");

  EEPROM_read(0, 128);
//  for (i = 0; i < 16; i++) {
//    EEPROM.write(130 + i, stateTimer[i]);
//  }
//  EEPROM.commit();
  for (i = 0; i < 16; i++) {
  stateTimer[i] = EEPROM.read(130 + i);
  }
  Serial.println(stateTimer);

}

void loop() {

  connectMQTT();
  checkDevice();


  delay(20);
}

void connectMQTT() {



  if (WiFi.status() == WL_CONNECTED) {
    if (!client.connected()) {
      Serial.print("Attempting MQTT connection...");
      if (client.connect("ESP8266Client", mqtt_user, mqtt_password)) {
        digitalWrite(Status, LOW);
        digitalWrite(LED_PIN, LOW);
        digitalWrite(LED_PIN1, LOW);
        digitalWrite(LED_PIN2, LOW);
        digitalWrite(LED_PIN3, LOW);

        client.subscribe("/ESP/LED");
        client.subscribe("/setdevice1");
        client.subscribe("/setdevice2");
        client.subscribe("/setdevice3");
        client.subscribe("/setdevice4");
        client.subscribe("/ESP/SMART");
        client.subscribe("/ESP/Moisture");
        Serial.println("connected");
        digitalWrite(Status, HIGH);
      } else {
        Serial.print("failed, rc=");
        Serial.print(client.state());
        Serial.println(" try again in 5 seconds");
        delay(5000);
        return;
      }

    }

    client.loop();
  }

}


void checkDevice() {
  dt = ck.getDateTime();
  //  Serial.print(dt.year);   Serial.print(":");
  //  Serial.print(dt.month);   Serial.print(":");
  //  Serial.print(dt.day);   Serial.print(":");
  //  Serial.print(dt.hour);   Serial.print(":");
  //  Serial.print(dt.minute); Serial.print(":");
  //  Serial.print(dt.second); Serial.println("");

  sensorValue = analogRead(sensorPin);
  //Serial.print("Moisture of Soil:  ");
  //Serial.println(sensorValue);
  Moisture = map(sensorValue, 1, 1024, 1, 100);
  Moisture = 100 - Moisture;

  /////send moisture to database every 1 hour
  if (dt.minute == 0 && dt.second == 0 && chk_Send == 0) {
    String date = (String)ck.dateFormat("d-m-Y", dt);
    String tt = (String)ck.dateFormat("H:i:s", dt);
    send_DB((String)Moisture, (String)date, (String)tt);
    chk_Send = 1;
  }
  if (dt.second == 1) {
    chk_Send = 0;

  }

  //////// check wifi connection
  if (dt.minute % 10 == 0) {
    if (WiFi.status() != WL_CONNECTED) {
      chk_Con = 0;
      WiFi.begin(ssid, password);
      while (WiFi.status() != WL_CONNECTED && chk_Con != 10) {
        delay(500);
        Serial.print(".");
        chk_Con++;
      }

      Serial.println(WiFi.localIP());
    }

  }

  ////// check flow when state on
  if (digitalRead(LED_PIN) == 1 || digitalRead(LED_PIN1) == 1 || digitalRead(LED_PIN2) == 1 || digitalRead(LED_PIN3) == 1) {
    flow_sensor();
  }
  if (digitalRead(LED_PIN) == 0 && digitalRead(LED_PIN1) == 0 && digitalRead(LED_PIN2) == 0 && digitalRead(LED_PIN3) == 0) {
    Calc = 0;
  }


  ////send moisture to websocket every 5s
  if (soli_State == 1) {
    if (millis() - previousMillis_A > interval_A) {
      previousMillis_A = millis();
      Serial.println(Moisture);
      char buff[10];
      String st;
      if (Calc >= 10) {
        st += "0";
        st = Calc;
      }
      if (Calc <= 9 ) {

        st = "0";
        st += "0";
        st += Calc;
      }
      if (Calc >= 99 ) {

        st = Calc;
      }
      if (Moisture >= 10) {
        st += Moisture;
      }
      if (Moisture <= 9) {
        st += "0";
        st += Moisture;
      }
      st.toCharArray(buff, 10);
      client.publish("/ESP/temp", buff);
      Calc = 0;

    }
  }


  //// timer mode
  if (onoff_smart == 1) {

    timer_Moisture();
  }

  if (onoff_smart == 0) {
    timer();

  }
  //// smart mode

  unsigned long timeRef;

  if (onoff_smart == 1 && onoff_timer == 0) {
    if (millis() - timeRef > 1000) {
      timeRef = millis();

      if (Moisture < smart_moisture && Moisture > 5 && chk == 0 && dt.minute == 0) {
        start_count = dt.minute;
        int percent;
        percent = (Moisture * 100) / smart_moisture;
        if (percent <= 100 && percent > 75) {
          smart_time = 2;
        }
        if (percent <= 75 && percent > 50) {
          smart_time = 5;
        }
        if (percent <= 50 && percent > 25) {
          smart_time = 10;
        }
        if (percent <= 25 && percent > 4) {
          smart_time = 15;
        }
        digitalWrite(LED_PIN, val1);
        digitalWrite(LED_PIN1, val2);
        digitalWrite(LED_PIN2, val3);
        digitalWrite(LED_PIN3, val4);

        cout_timer = (start_count + smart_time) % 60;

        chk = 1;
      }

      if (cout_timer == dt.minute ) {

        digitalWrite(LED_PIN, LOW);
        digitalWrite(LED_PIN1, LOW);
        digitalWrite(LED_PIN2, LOW);
        digitalWrite(LED_PIN3, LOW);
        cout_timer = 0;


        chk = 0;

      }
    }
  }
}
String EEPROM_read(int index, int length) {
  String text = "";
  char ch = 1;

  for (int i = index; i < (index + length); i++) {
    if (ch = EEPROM.read(i)) {
      text.concat(ch);
    }
  }
  if (length == 128) {
    text.toCharArray(timeall, length + 1);
  }
  if (length == 16 ) {
    text.toCharArray(stateTimer, length + 1);
    Serial.println(text);
  }
  Serial.println(length);
}

int EEPROM_write(int index, String text) {
  for (int i = index; i < text.length(); i++) {
    EEPROM.write(i, text[i]);
  }
  EEPROM.commit();
  Serial.println(text);
}

int EEPROM_write1(int index, String text) {
  for (int i = index; i < text.length() + index + index; i++) {
    EEPROM.write(i, text[i]);
  }
  EEPROM.commit();
  Serial.println(text);
}
