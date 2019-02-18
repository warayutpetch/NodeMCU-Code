
void rpm ()     //This is the function that the interupt calls
{
  NbTopsFan++;  //This function measures the rising and falling edge of the hall effect sensors signal
}

void flow_sensor() {
  NbTopsFan = 0;   //Set NbTops to 0 ready for calculations
  sei();      //Enables interrupts
  delay(1000);
  cli();      //Disable interrupts
  Calc = (NbTopsFan / 4.1); //(Pulse frequency x 60) / 7.5Q, = flow rate in L/hour
  Serial.print (Calc, DEC); //Prints the number calculated above
  Serial.println (" L/min");
  chkFlowError++;
  if (chkFlowError%10 == 0) {
    if (Calc > 60) {
      digitalWrite(LED_PIN, 0);
      digitalWrite(LED_PIN1, 0);
      digitalWrite(LED_PIN2, 0);
      digitalWrite(LED_PIN3, 0);
    }
  }
}
