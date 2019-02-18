void send_DB(String message,String dt,String t){
  if (WiFi.status() == WL_CONNECTED) { //Check WiFi connection status
      String url ="http://k00000ko.16mb.com/temp.php?moisture=";
      
      HTTPClient http;  //Declare an object of class HTTPClient
      String date = "&dt=";
      date += dt;
      String tmr ="&t=";
      tmr += t;
      http.begin(url+message+date+tmr);  //Specify request destination
      int httpCode = http.GET();                                                                  //Send the request

      if (httpCode > 0) { //Check the returning code

        String payload = http.getString();   //Get the request response payload
        Serial.println(payload);                     //Print the response payload

      }

      http.end();   //Close connection

    }
    
  }
