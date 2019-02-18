void tmr_set() {
  int k = 0, j = 0;
  char st[] = "07000705080008050900090510001005070607110806081109060911100610110712071708120817091209171012101707180723081808230918092310181023";
  for (k = 0; k < 128; k += 8) {

    H_on += st[k];
    H_on += st[k + 1];

    M_on += st[k + 2];
    M_on += st[k + 3];

    H_off += st[k + 4];
    H_off += st[k + 5];

    M_off += st[k + 6];
    M_off += st[k + 7];

    time_set[j].on_hour = H_on.toInt();
    time_set[j].on_min = M_on.toInt();

    time_set[j].off_hour = H_off.toInt();
    time_set[j].off_min = M_off.toInt();

/*
    Serial.print(time_set[j].on_hour);
    Serial.println(time_set[j].on_min);

    Serial.print(time_set[j].off_hour);
    Serial.println(time_set[j].off_min);*/
    H_on = "";
    M_on = "";
    H_off = "";
    M_off = "";
    j++;
  }

}
