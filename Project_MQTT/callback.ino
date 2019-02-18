void callback(char* topic, byte* payload, unsigned int length) {
  Serial.println(topic);
  String msg = "";
  int i = 0;
  while (i < length) msg += (char)payload[i++];

  Serial.println(msg);
  if (strcmp(topic, "/ESP/LED") == 0 && msg == "GET-DATA") {
    digitalRead(LED_PIN) ? statusVal[0] = '1' : statusVal[0] = '0';
    digitalRead(LED_PIN1) ? statusVal[1] = '1' : statusVal[1] = '0';
    digitalRead(LED_PIN2) ? statusVal[2] = '1' : statusVal[2] = '0';
    digitalRead(LED_PIN3) ? statusVal[3] = '1' : statusVal[3] = '0';

    client.publish("/ESP/LED", statusVal);

    if (onoff_smart == 1) {
      client.publish("/ESP/SMART", "ON" );
    }
    else {
      client.publish("/ESP/SMART", "OFF" );
    }
    //client.publish("/ESP/LED", week);
    client.publish("/ESP/SMART", Smart);
    client.publish("/ESP/SMART", stateTimer);
    return;
  }

  if (msg == "Soil") {
    soli_State = 1;
    return;
  }
  if (msg == "Closed" ) {
    soli_State = 0;
    return;
  }

  if (strcmp(topic, "/ESP/SMART") == 0 && msg == "O" ) {
    onoff_smart = 1;
    return;
  }
  if (strcmp(topic, "/ESP/SMART") == 0 && msg == "F" ) {
    onoff_smart = 0;
    return;
  }

  if (strcmp(topic, "/setdevice1") == 0 && msg == "GET-TIME") {
    client.publish("/setdevice1", timeall);
    return;
  }

  if (strcmp(topic, "/ESP/SMART") == 0 && length == 8) {
    msg.toCharArray(Smart, 10);
    moiture_S += Smart[0];
    moiture_S += Smart[1];
    time_S += Smart[2];
    time_S += Smart[3];
    smart_moisture = moiture_S.toInt();
    smart_time = time_S.toInt();
    val1 = Smart[4] - '0';
    val2 = Smart[5] - '0';
    val3 = Smart[6] - '0';
    val4 = Smart[7] - '0';

  }
  if (msg == "LEDON") {
    digitalWrite(LED_PIN, HIGH);

  }
  if (msg == "LEDOFF") {
    digitalWrite(LED_PIN, LOW);

  }
  if (msg == "LEDON1") {
    digitalWrite(LED_PIN1, HIGH);

  }
  if (msg == "LEDOFF1") {
    digitalWrite(LED_PIN1, LOW);


  }
  if (msg == "LEDON2") {
    digitalWrite(LED_PIN2, HIGH);


  }
  if (msg == "LEDOFF2") {
    digitalWrite(LED_PIN2, LOW);


  }
  if (msg == "LEDON3") {
    digitalWrite(LED_PIN3, HIGH);

  }
  if (msg == "LEDOFF3") {
    digitalWrite(LED_PIN3, LOW);
  }

  if (strcmp(topic, "/setdevice1") == 0 && msg != "GET-TIME" && length < 20) {
    msg.toCharArray(tmp_time, 16);
    slot_No = tmp_time[8] - '0';
    slot_No = slot_No - 1;
    H_on += tmp_time[0];
    H_on += tmp_time[1];

    M_on += tmp_time[2];
    M_on += tmp_time[3];

    H_off += tmp_time[4];
    H_off += tmp_time[5];

    M_off += tmp_time[6];
    M_off += tmp_time[7];

    dayweek = tmp_time[9];
    dayweek += tmp_time[10];
    dayweek += tmp_time[11];
    dayweek += tmp_time[12];
    dayweek += tmp_time[13];
    dayweek += tmp_time[14];
    dayweek += tmp_time[15];


    if (slot_No == 0) {
      for (i = 0; i < 8; i++) {
        timeall[0 + i] = tmp_time[i];
        EEPROM.write(i, tmp_time[i]);

      }

      EEPROM.commit();
    }

    if (slot_No == 1) {
      for (i = 0; i < 8; i++) {
        timeall[8 + i] = tmp_time[i];
        EEPROM.write(8 + i, tmp_time[i]);

      }
      EEPROM.commit();
    }


    if (slot_No == 2) {
      for (i = 0; i < 8; i++) {
        timeall[16 + i] = tmp_time[i];
        EEPROM.write(16 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }


    if (slot_No == 3) {
      for (i = 0; i < 8; i++) {
        timeall[24 + i] = tmp_time[i];
        EEPROM.write(24 + i, tmp_time[i]);
      }

      EEPROM.commit();

    }




    time_set[slot_No].on_hour = H_on.toInt();
    time_set[slot_No].on_min = M_on.toInt();

    time_set[slot_No].off_hour = H_off.toInt();
    time_set[slot_No].off_min = M_off.toInt();
    time_set[slot_No].onday = dayweek;

    Serial.println("Set!1");
    Serial.print(time_set[slot_No].on_hour);   Serial.print(":");
    Serial.print(time_set[slot_No].on_min); Serial.print(":");

    Serial.print(time_set[slot_No].off_hour);   Serial.print(":");
    Serial.print(time_set[slot_No].off_min); Serial.print(":");


    H_on = "";
    M_on = "";

    H_off = "";
    M_off = "";


  }
  if (strcmp(topic, "/setdevice2") == 0 && msg != "GET-TIME") {
    msg.toCharArray(tmp_time, 15);
    slot_No = tmp_time[8] - '0';
    slot_No = (slot_No - 1) + 4;
    H_on += tmp_time[0];
    H_on += tmp_time[1];

    M_on += tmp_time[2];
    M_on += tmp_time[3];

    H_off += tmp_time[4];
    H_off += tmp_time[5];

    M_off += tmp_time[6];
    M_off += tmp_time[7];

    dayweek = tmp_time[9];
    dayweek += tmp_time[10];
    dayweek += tmp_time[11];
    dayweek += tmp_time[12];
    dayweek += tmp_time[13];
    dayweek += tmp_time[14];
    dayweek += tmp_time[15];

    if (slot_No == 4) {
      for (i = 0; i < 8; i++) {
        timeall[32 + i] = tmp_time[i];
        EEPROM.write(32 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }

    if (slot_No == 5) {
      for (i = 0; i < 8; i++) {
        timeall[40 + i] = tmp_time[i];
        EEPROM.write(40 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }


    if (slot_No == 6) {
      for (i = 0; i < 8; i++) {
        timeall[48 + i] = tmp_time[i];
        EEPROM.write(48 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }


    if (slot_No == 7) {
      for (i = 0; i < 8; i++) {
        timeall[56 + i] = tmp_time[i];
        EEPROM.write(56 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }

    time_set[slot_No].on_hour = H_on.toInt();
    time_set[slot_No].on_min = M_on.toInt();

    time_set[slot_No].off_hour = H_off.toInt();
    time_set[slot_No].off_min = M_off.toInt();
    time_set[slot_No].onday = dayweek;



    Serial.println("Set!2");
    Serial.print(time_set[slot_No].on_hour);   Serial.print(":");
    Serial.print(time_set[slot_No].on_min); Serial.print(":");

    Serial.print(time_set[slot_No].off_hour);   Serial.print(":");
    Serial.print(time_set[slot_No].off_min); Serial.print(":");


    H_on = "";
    M_on = "";

    H_off = "";
    M_off = "";


  }
  if (strcmp(topic, "/setdevice3") == 0 && msg != "GET-TIME") {
    msg.toCharArray(tmp_time, 15);
    slot_No = tmp_time[8] - '0';
    slot_No = (slot_No - 1) + 8;

    H_on += tmp_time[0];
    H_on += tmp_time[1];

    M_on += tmp_time[2];
    M_on += tmp_time[3];

    H_off += tmp_time[4];
    H_off += tmp_time[5];

    M_off += tmp_time[6];
    M_off += tmp_time[7];

    dayweek = tmp_time[9];
    dayweek += tmp_time[10];
    dayweek += tmp_time[11];
    dayweek += tmp_time[12];
    dayweek += tmp_time[13];
    dayweek += tmp_time[14];
    dayweek += tmp_time[15];

    if (slot_No == 8) {
      for (i = 0; i < 8; i++) {
        timeall[64 + i] = tmp_time[i];
        EEPROM.write(64 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }

    if (slot_No == 9) {
      for (i = 0; i < 8; i++) {
        timeall[72 + i] = tmp_time[i];
        EEPROM.write(72 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }


    if (slot_No == 10) {
      for (i = 0; i < 8; i++) {
        timeall[80 + i] = tmp_time[i];
        EEPROM.write(80 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }


    if (slot_No == 11) {
      for (i = 0; i < 8; i++) {
        timeall[88 + i] = tmp_time[i];
        EEPROM.write(88 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }

    time_set[slot_No].on_hour = H_on.toInt();
    time_set[slot_No].on_min = M_on.toInt();

    time_set[slot_No].off_hour = H_off.toInt();
    time_set[slot_No].off_min = M_off.toInt();
    time_set[slot_No].onday = dayweek;

    Serial.println("Set!3");
    Serial.print(time_set[slot_No].on_hour);   Serial.print(":");
    Serial.print(time_set[slot_No].on_min); Serial.print(":");

    Serial.print(time_set[slot_No].off_hour);   Serial.print(":");
    Serial.print(time_set[slot_No].off_min); Serial.print(":");


    H_on = "";
    M_on = "";
    S_on = "";

    H_off = "";
    M_off = "";
    S_off = "";

  }

  if (strcmp(topic, "/setdevice4" ) == 0 && msg != "GET-TIME") {
    msg.toCharArray(tmp_time, 15);
    slot_No = tmp_time[8] - '0';
    slot_No = (slot_No - 1) + 12;

    H_on += tmp_time[0];
    H_on += tmp_time[1];

    M_on += tmp_time[2];
    M_on += tmp_time[3];

    H_off += tmp_time[4];
    H_off += tmp_time[5];

    M_off += tmp_time[6];
    M_off += tmp_time[7];


    dayweek = tmp_time[9];
    dayweek += tmp_time[10];
    dayweek += tmp_time[11];
    dayweek += tmp_time[12];
    dayweek += tmp_time[13];
    dayweek += tmp_time[14];
    dayweek += tmp_time[15];

    if (slot_No == 12) {
      for (i = 0; i < 8; i++) {
        timeall[96 + i] = tmp_time[i];
        EEPROM.write(96 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }

    if (slot_No == 13) {
      for (i = 0; i < 8; i++) {
        timeall[104 + i] = tmp_time[i];
        EEPROM.write(104 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }


    if (slot_No == 14) {
      for (i = 0; i < 8; i++) {
        timeall[112 + i] = tmp_time[i];
        EEPROM.write(112 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }


    if (slot_No == 15) {
      for (i = 0; i < 8; i++) {
        timeall[120 + i] = tmp_time[i];
        EEPROM.write(120 + i, tmp_time[i]);
      }

      EEPROM.commit();
    }

    time_set[slot_No].on_hour = H_on.toInt();
    time_set[slot_No].on_min = M_on.toInt();

    time_set[slot_No].off_hour = H_off.toInt();
    time_set[slot_No].off_min = M_off.toInt();
    time_set[slot_No].onday = dayweek;

    Serial.println("Set!4");
    Serial.print(time_set[slot_No].on_hour);   Serial.print(":");
    Serial.print(time_set[slot_No].on_min); Serial.print(":");
    Serial.print(time_set[slot_No].off_hour);   Serial.print(":");
    Serial.print(time_set[slot_No].off_min); Serial.print(":");

    H_on = "";
    M_on = "";

    H_off = "";
    M_off = "";

  }

  if (msg == "T1ON" || msg == "T1OFF") {
    if (msg == "T1ON") {
      stateTimer[0] = '1';

    }
    else {
      stateTimer[0] = '0';
    }
    EEPROM.write(130, stateTimer[0]);
    EEPROM.commit();
  }

  if (msg == "T2ON" || msg == "T2OFF") {
    if (msg == "T2ON") {
      stateTimer[1] = '1';
    }
    else {
      stateTimer[1] = '0';
    }
    EEPROM.write(131, stateTimer[1]);
    EEPROM.commit();
  }

  if (msg == "T3ON" || msg == "T3OFF") {
    if (msg == "T3ON") {
      stateTimer[2] = '1';
    }
    else {
      stateTimer[2] = '0';
    }
    EEPROM.write(132, stateTimer[2]);
    EEPROM.commit();
  }

  if (msg == "T4ON" || msg == "T4OFF") {
    if (msg == "T4ON") {
      stateTimer[3] = '1';
    }
    else {
      stateTimer[3] = '0';
    }
    EEPROM.write(133, stateTimer[3]);
    EEPROM.commit();
  }
  if (msg == "T5ON" || msg == "T5OFF") {
    if (msg == "T5ON") {
      stateTimer[4] = '1';
    }
    else {
      stateTimer[4] = '0';
    }
    EEPROM.write(134, stateTimer[4]);
    EEPROM.commit();

  }

  if (msg == "T6ON" || msg == "T6OFF") {
    if (msg == "T6ON") {
      stateTimer[5] = '1';
    }
    else {
      stateTimer[5] = '0';
    }

    EEPROM.write(135, stateTimer[5]);
    EEPROM.commit();
  }

  if (msg == "T7ON" || msg == "T7OFF") {
    if (msg == "T7ON") {
      stateTimer[6] = '1';
    }
    else {
      stateTimer[6] = '0';
    }
    EEPROM.write(136, stateTimer[6]);
    EEPROM.commit();
  }

  if (msg == "T8ON" || msg == "T8OFF") {
    if (msg == "T8ON") {
      stateTimer[7] = '1';
    }
    else {
      stateTimer[7] = '0';
    }
    EEPROM.write(137, stateTimer[7]);
    EEPROM.commit();
  }

  if (msg == "T9ON" || msg == "T9OFF") {
    if (msg == "T9ON") {
      stateTimer[8] = '1';
    }
    else {
      stateTimer[8] = '0';
    }
    EEPROM.write(138, stateTimer[8]);
    EEPROM.commit();
  }

  if (msg == "T10ON" || msg == "T10OFF") {
    if (msg == "T10ON") {
      stateTimer[9] = '1';
    }
    else {
      stateTimer[9] = '0';
    }
    EEPROM.write(139, stateTimer[9]);
    EEPROM.commit();
  }
  if (msg == "T11ON" || msg == "T11OFF") {
    if (msg == "T11ON") {
      stateTimer[10] = '1';
    }
    else {
      stateTimer[10] = '0';
    }
    EEPROM.write(140, stateTimer[10]);
    EEPROM.commit();
  }
  if (msg == "T12ON" || msg == "T12OFF") {
    if (msg == "T12ON") {
      stateTimer[11] = '1';
    }
    else {
      stateTimer[11] = '0';
    }
    EEPROM.write(141, stateTimer[11]);
    EEPROM.commit();
  }

  if (msg == "T13ON" || msg == "T13OFF") {
    if (msg == "T13ON") {
      stateTimer[12] = '1';
    }
    else {
      stateTimer[12] = '0';
    }
    EEPROM.write(142, stateTimer[12]);
    EEPROM.commit();
  }
  if (msg == "T14ON" || msg == "T14OFF") {
    if (msg == "T14ON") {
      stateTimer[13] = '1';
    }
    else {
      stateTimer[13] = '0';
    }
    EEPROM.write(143, stateTimer[13]);
    EEPROM.commit();
  }
  if (msg == "T15ON" || msg == "T15OFF") {
    if (msg == "T15ON") {
      stateTimer[14] = '1';
    }
    else {
      stateTimer[14] = '0';
    }

    EEPROM.write(144, stateTimer[14]);
    EEPROM.commit();
  }
  if (msg == "T16ON" || msg == "T16OFF") {
    if (msg == "T16ON") {
      stateTimer[15] = '1';
    }
    else {
      stateTimer[15] = '0';
    }
    EEPROM.write(145, stateTimer[15]);
    EEPROM.commit();
  }
  msg = "";

}
