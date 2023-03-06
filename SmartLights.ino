#include <DHTesp.h>         // DHT for ESP32 library
#include <WiFi.h>           // WiFi control for ESP32
#include <ThingsBoard.h>    // ThingsBoard SDK

#define COUNT_OF(x) ((sizeof(x)/sizeof(0[x])) / ((size_t)(!(sizeof(x) % sizeof(0[x])))))

// WiFi access point
#define WIFI_AP_NAME        "Mi 10 Pro"
// WiFi password
#define WIFI_PASSWORD       "killemall"

// ThingsBoard token
#define TOKEN               "7uECCr8b1inZ5umH7zw2"
// ThingsBoard server instance.
#define THINGSBOARD_SERVER  "zaimov.eu"

const int ampsPin = 34;       // pin where the OUT pin from sensor is connected
const int relayPin = 26;      // pin where relay is connected
const int dhtPin = 33;         // pin for DHT11

const int mains = 240;        // mains voltage

// Initialize ThingsBoard client
WiFiClient espClient;
// Initialize ThingsBoard instance
ThingsBoard tb(espClient);
// the Wifi radio's status
int status = WL_IDLE_STATUS;

// Main application loop delay
int quant = 20;

// Period of sending a temperature/humidity data.
int send_delay = 2000;

// Time passed after LED was turned ON, milliseconds.
int led_passed = 0;
// Time passed after temperature/humidity data was sent, milliseconds.
int send_passed = 0;

// Set to true if application is subscribed for the RPC messages.
bool subscribed = false;
// LED number that is currenlty ON.
int current_led = 0;

int mVperAmp = 185;           // this the 5A version of the ACS712 -use 100 for 20A Module and 66 for 30A Module

int Watt = 0;
double Voltage = 0;
double VRMS = 0;
double AmpsRMS = 0;

// DHT object
DHTesp dht;

void setup() {
  // put your setup code here, to run once:
  Serial.begin(115200);
  delay(1000);

  WiFi.begin(WIFI_AP_NAME, WIFI_PASSWORD);
  InitWiFi();  

  pinMode(ampsPin, INPUT_PULLUP);  
  pinMode(relayPin, OUTPUT);
  dht.setup(dhtPin, DHTesp::DHT11);
}

void loop() {
  delay(quant);
  send_passed += quant;
  // Reconnect to WiFi, if needed
  if (WiFi.status() != WL_CONNECTED) {
    reconnect();
    return;
  }  

  // Reconnect to ThingsBoard, if needed
  if (!tb.connected()) {
    subscribed = false;

    // Connect to the ThingsBoard
    Serial.print("Connecting to: ");
    Serial.print(THINGSBOARD_SERVER);
    Serial.print(" with token ");
    Serial.println(TOKEN);
    if (!tb.connect(THINGSBOARD_SERVER, TOKEN)) {
      Serial.println("Failed to connect");
      return;
    }
  }

  Serial.println (""); 
  Voltage = getVPP();
  VRMS = (Voltage/2.0) * 0.707;   //root 2 is 0.707
  AmpsRMS = ((VRMS * 1000)/mVperAmp)-0.3; //0.3 is the error I got for my sensor
 
  Serial.print(AmpsRMS);
  Serial.print(" Amps RMS  ---  ");
  Watt = (AmpsRMS * mains / 1.2);
  // note: 1.2 is my own empirically established calibration factor
  // as the voltage measured at D34 depends on the length of the OUT-to-D34 wire
  // 240 is the main AC power voltage – this parameter changes locally
  Serial.print(Watt);
  Serial.println(" Watts");  

  TempAndHumidity lastValues = dht.getTempAndHumidity();
  Serial.print("H ");
  Serial.println(lastValues.humidity);
  Serial.print("t ");
  Serial.println(lastValues.temperature);

// Check if it is a time to send DHT22 temperature and humidity
  if (send_passed > send_delay) {
    Serial.println("Sending data...");

    // Uploads new telemetry to ThingsBoard using MQTT. 
    // See https://thingsboard.io/docs/reference/mqtt-api/#telemetry-upload-api 
    // for more details
    
    if (isnan(lastValues.humidity) || isnan(lastValues.temperature)) {
      Serial.println("Failed to read from DHT sensor!");
    } else {
      tb.sendTelemetryFloat("t", lastValues.temperature);
      tb.sendTelemetryFloat("h", lastValues.humidity);
      tb.sendTelemetryFloat("W", Watt);      
    }

    send_passed = 0;
  }

  /*digitalWrite(relayPin, HIGH);
  Serial.println("Current Flowing");
  delay(5000);

  digitalWrite(relayPin, LOW);
  Serial.println("Current not Flowing");
  delay(5000);*/

  // Process messages
  tb.loop();
}

// ***** function calls ******
float getVPP()
{
  float result;
  int readValue;                // value read from the sensor
  int maxValue = 0;             // store max value here
  int minValue = 4096;          // store min value here ESP32 ADC resolution
  
   uint32_t start_time = millis();
   while((millis()-start_time) < 1000) //sample for 1 Sec
   {
       readValue = analogRead(ampsPin);
       // see if you have a new maxValue
       if (readValue > maxValue) 
       {
           /*record the maximum sensor value*/
           maxValue = readValue;
       }
       if (readValue < minValue) 
       {
           /*record the minimum sensor value*/
           minValue = readValue;
       }
   }
   
   // Subtract min from max
   result = ((maxValue - minValue) * 3.3)/4096.0; //ESP32 ADC resolution 4096
      
   return result;
 }

 void InitWiFi()
{
  Serial.println("Connecting to AP ...");
  // attempt to connect to WiFi network

  WiFi.begin(WIFI_AP_NAME, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("Connected to AP");
}

void reconnect() {
  // Loop until we're reconnected
  status = WiFi.status();
  if ( status != WL_CONNECTED) {
    WiFi.begin(WIFI_AP_NAME, WIFI_PASSWORD);
    while (WiFi.status() != WL_CONNECTED) {
      delay(500);
      Serial.print(".");
    }
    Serial.println("Connected to AP");
  }
}