    void timer_Moisture() {


    for (i = 0 ; i < 16; i++) {
     if (dt.hour == time_set[i].on_hour && dt.minute == time_set[i].on_min && Moisture < smart_moisture && stateTimer[i]=='1' ) {
       if (i < 4) {
         digitalWrite(LED_PIN, HIGH);
         client.publish("/ESP/LED", "LEDON");
         onoff_timer = 1;
       }

       if (i < 8 && i >= 4) {
         digitalWrite(LED_PIN1, HIGH);
         client.publish("/ESP/LED", "LEDON1");
         onoff_timer = 1;
       }

       if (i < 12 && i >= 8) {
         digitalWrite(LED_PIN2, HIGH);
         client.publish("/ESP/LED", "LEDON2");
         onoff_timer = 1;
       }

       if (i < 16 && i >= 12) {
         digitalWrite(LED_PIN3, HIGH);
         client.publish("/ESP/LED", "LEDON3");
         onoff_timer = 1;
       }

     }


     if (dt.hour == time_set[i].off_hour && dt.minute == time_set[i].off_min) {
       if (i < 4) {
         digitalWrite(LED_PIN, LOW);
         client.publish("/ESP/LED", "LEDOFF");
         onoff_timer = 0;
       }

       if (i < 8 && i >= 4) {
         digitalWrite(LED_PIN1, LOW);
         client.publish("/ESP/LED", "LEDOFF1");
         onoff_timer = 0;
       }

       if (i < 12 && i >= 8) {
         digitalWrite(LED_PIN2, LOW);
         client.publish("/ESP/LED", "LEDOFF2");
         onoff_timer = 0;
       }

       if (i < 16 && i >= 12) {
         digitalWrite(LED_PIN3, LOW);
         client.publish("/ESP/LED", "LEDOFF3");
         onoff_timer = 0;
       }

     }

    }


    }
