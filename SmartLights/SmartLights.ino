#include <DHTesp.h>         // DHT for ESP32 library
#include <WiFi.h>           // WiFi control for ESP32
#include <ThingsBoard.h>    // ThingsBoard SDK

#define COUNT_OF(x) ((sizeof(x)/sizeof(0[x])) / ((size_t)(!(sizeof(x) % sizeof(0[x])))))

// WiFi access point
#define WIFI_AP_NAME        "Slava"
// WiFi password
#define WIFI_PASSWORD       "20036501"

// ThingsBoard token
#define TOKEN               "7uECCr8b1inZ5umH7zw2"
// ThingsBoard server instance.
#define THINGSBOARD_SERVER  "zaimov.eu"

// Set to true to define Relay as Normally Open (NO)
#define RELAY_NO    true

const int ampsPin = 34;       // pin where the OUT pin from sensor is connected
const int dhtPin = 33;         // pin for DHT11

// Array with relays that should be controlled from ThingsBoard, one by one
uint8_t relay_control[] = {26};
int gpioState[] = {0};

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

// Processes function for RPC call "getStatus"
// RPC_Data is a JSON variant, that can be queried using operator[]
// See https://arduinojson.org/v5/api/jsonvariant/subscript/ for more details
RPC_Response processGetStatus(const RPC_Data &data)
{
  String payload;
  char buff[5];
  StaticJsonDocument<200> doc;

  Serial.println("Received the get status RPC method");

  for (int i = 0; i < COUNT_OF(relay_control); i++) {
    Serial.println("GPIO[");
    Serial.print(i);
    Serial.print("] = ");
    Serial.print(gpioState[i]);
    doc[String(i)] = gpioState[i];
  }

  Voltage = getVPP();
  VRMS = (Voltage / 2.0) * 0.707;   //root 2 is 0.707
  AmpsRMS = ((VRMS * 1000) / mVperAmp)-0.3; //0.3 is the error I got for my sensor
 
  Watt = (AmpsRMS * mains / 1.2);
  TempAndHumidity lastValues = dht.getTempAndHumidity();

  dtostrf(Watt, 2, 2, buff);
  doc["W"] = buff;    
  dtostrf(lastValues.temperature, 2, 2, buff);
  doc["t"] = buff;   
  dtostrf(lastValues.humidity, 2, 2, buff);
  doc["H"] = buff;   
  
  serializeJson(doc, payload);
  Serial.println("Get status: ");
  Serial.print(payload);

  return RPC_Response(NULL, payload.c_str());
}

// Processes function for RPC call "setGpioStatus"
// RPC_Data is a JSON variant, that can be queried using operator[]
// See https://arduinojson.org/v5/api/jsonvariant/subscript/ for more details
RPC_Response processSetGpioStatus(const RPC_Data &data)
{
  Serial.println("Received the set GPIO RPC method");

  int pin = data["pin"];
  int enabled = data["enabled"];

  if (pin < COUNT_OF(relay_control)) {
    Serial.print("Setting rellay ");
    Serial.print(pin);
    Serial.print("(");
    Serial.print(relay_control[pin]);
    Serial.print(")");
    Serial.print(" to state ");
    Serial.println(enabled);

    digitalWrite(relay_control[pin], enabled == 0);
    gpioState[pin] = !enabled;
  }

  return RPC_Response(NULL, (int)data["enabled"]);
}

// RPC handlers
RPC_Callback callbacks[] = {
  { "getStatus",    processGetStatus },
  { "setGpioStatus",    processSetGpioStatus }
};

void setup() {
  // put your setup code here, to run once:
  Serial.begin(115200);
  delay(1000);

  WiFi.begin(WIFI_AP_NAME, WIFI_PASSWORD);
  InitWiFi();  

  pinMode(ampsPin, INPUT_PULLUP);    
  dht.setup(dhtPin, DHTesp::DHT11);
  
  //relay pins
  for (size_t i = 0; i < COUNT_OF(relay_control); ++i) {
    pinMode(relay_control[i], OUTPUT);
    digitalWrite(relay_control[i], gpioState[i]);
  }  
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

  // Subscribe for RPC, if needed
  if (!subscribed) {
    Serial.println("Subscribing for RPC...");
    // Perform a subscription. All consequent data processing will happen in
    // callbacks as denoted by callbacks[] array.
    if (!tb.RPC_Subscribe(callbacks, COUNT_OF(callbacks))) {
      Serial.println("Failed to subscribe for RPC");
      return;
    }
    Serial.println("Subscribe done");
    subscribed = true;
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

// Check if it is a time to send DHT11 temperature and humidity
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