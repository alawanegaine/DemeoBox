/* 
 * File:   main.c
 * Author: Quentin
 *
 * Created on 31 mars 2015, 18:09
 */

#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include <delays.h>
#include <string.h>

#include <pic18.h>
#include <i2c.h>

#include <xc.h>

#define FOSC 8000000
#define BUFFER_SIZE 10

#define I2C_ADDRESS 0x29

#define RoueCodeuse1 PORTAbits.RA0
#define RoueCodeuse2 PORTAbits.RA2
#define RoueCodeuse4 PORTAbits.RA1
#define RoueCodeuse8 PORTAbits.RA3

int iRx = 0 ;
int iRxLecture = 0 ;

char idModuleRead ;
char idBrocheRead ;
char valueBrocheRead ;

char RxBuffer[BUFFER_SIZE] ;
char TxBuffer[BUFFER_SIZE] ;

char RxI2CBuffer[BUFFER_SIZE];
char TxI2CBuffer[BUFFER_SIZE] ;

// #pragma config statements should precede project file includes.
// Use project enums instead of #define for ON and OFF.

// CONFIG1H
#pragma config OSC = HS    // Oscillator Selection bits (Internal oscillator block, port function on RA6 and RA7)
#pragma config FCMEN = OFF      // Fail-Safe Clock Monitor Enable bit (Fail-Safe Clock Monitor disabled)
#pragma config IESO = OFF       // Internal/External Oscillator Switchover bit (Oscillator Switchover mode disabled)


// CONFIG2H
#pragma config WDT = OFF        // Watchdog Timer Enable bit (WDT disabled (control is placed on the SWDTEN bit))
#pragma config WDTPS = 32768    // Watchdog Timer Postscale Select bits (1:32768)


// CONFIG2L
#pragma config PWRT = ON       // Power-up Timer Enable bit (PWRT disabled)
#pragma config BOREN = SBORDIS  // Brown-out Reset Enable bits (Brown-out Reset enabled in hardware only (SBOREN is disabled))
#pragma config BORV = 3         // Brown Out Reset Voltage bits (Minimum setting)

// CONFIG3H
#pragma config CCP2MX = PORTBE   // CCP2 MUX bit (CCP2 input/output is multiplexed with RC1)
#pragma config PBADEN = OFF      // PORTB A/D Enable bit (PORTB<4:0> pins are configured as analog input channels on Reset)
#pragma config LPT1OSC = ON     // Low-Power Timer1 Oscillator Enable bit (Timer1 configured for low-power operation)
#pragma config MCLRE = ON       // MCLR Pin Enable bit (MCLR pin enabled; RE3 input pin disabled)

// CONFIG4L
#pragma config STVREN = ON      // Stack Full/Underflow Reset Enable bit (Stack full/underflow will cause Reset)
#pragma config LVP = OFF         // Single-Supply ICSP Enable bit (Single-Supply ICSP enabled)
#pragma config XINST = OFF      // Extended Instruction Set Enable bit (Instruction set extension and Indexed Addressing mode disabled (Legacy mode))

// CONFIG5L
#pragma config CP0 = OFF        // Code Protection bit (Block 0 (000800-001FFFh) not code-protected)
#pragma config CP1 = OFF        // Code Protection bit (Block 1 (002000-003FFFh) not code-protected)
#pragma config CP2 = OFF        // Code Protection bit (Block 2 (004000-005FFFh) not code-protected)
#pragma config CP3 = OFF        // Code Protection bit (Block 3 (006000-007FFFh) not code-protected)

// CONFIG5H
#pragma config CPB = OFF        // Boot Block Code Protection bit (Boot block (000000-0007FFh) not code-protected)
#pragma config CPD = OFF        // Data EEPROM Code Protection bit (Data EEPROM not code-protected)

// CONFIG6L
#pragma config WRT0 = OFF       // Write Protection bit (Block 0 (000800-001FFFh) not write-protected)
#pragma config WRT1 = OFF       // Write Protection bit (Block 1 (002000-003FFFh) not write-protected)
#pragma config WRT2 = OFF       // Write Protection bit (Block 2 (004000-005FFFh) not write-protected)
#pragma config WRT3 = OFF       // Write Protection bit (Block 3 (006000-007FFFh) not write-protected)

// CONFIG6H
#pragma config WRTC = OFF       // Configuration Register Write Protection bit (Configuration registers (300000-3000FFh) not write-protected)
#pragma config WRTB = OFF       // Boot Block Write Protection bit (Boot block (000000-0007FFh) not write-protected)
#pragma config WRTD = OFF       // Data EEPROM Write Protection bit (Data EEPROM not write-protected)

// CONFIG7L
#pragma config EBTR0 = OFF      // Table Read Protection bit (Block 0 (000800-001FFFh) not protected from table reads executed in other blocks)
#pragma config EBTR1 = OFF      // Table Read Protection bit (Block 1 (002000-003FFFh) not protected from table reads executed in other blocks)
#pragma config EBTR2 = OFF      // Table Read Protection bit (Block 2 (004000-005FFFh) not protected from table reads executed in other blocks)
#pragma config EBTR3 = OFF      // Table Read Protection bit (Block 3 (006000-007FFFh) not protected from table reads executed in other blocks)

// CONFIG7H
#pragma config EBTRB = OFF      // Boot Block Table Read Protection bit (Boot block (000000-0007FFh) not protected from table reads executed in other blocks)

// function definitions
void delay_TLS0101(unsigned long delayVal) // delay function
{
    unsigned long i; // define local variable int
    for (i = 0; i < 2*delayVal; i++)
    {
        Nop();
    }
}

void init_USART(){
    TXSTAbits.TXEN = 1 ; // enable transmitter
    TXSTAbits.BRGH = 1 ;
    RCSTAbits.CREN = 1 ; // enable continous receiving


    // confirgure I/O pins
    TRISCbits.RC7 = 1 ; // RX pin is input
    TRISCbits.RC6 = 0 ; // TX pin is output

    SPBRG = 51 ;        // set baud rate to 9600 baud at 8MHz

    PIE1bits.RCIE =  1 ; // enable USART receive interrupt
    RCSTAbits.SPEN = 1 ; // enable USART
}

void init_IOPin(){

    // configuration des pin pour la roue codeuse
    ADCON1bits.PCFG = 0b1111 ; // RA<3:0> to digital input
    TRISAbits.TRISA0 = 1 ;
    TRISAbits.TRISA1 = 1 ;
    TRISAbits.TRISA2 = 1 ;
    TRISAbits.TRISA3 = 1 ;
    
}

void init_I2C(){
    // configure I/O pins
    TRISCbits.RC3 = 0 ;
    TRISCbits.RC4 = 0 ;

    SSPADD = I2C_ADDRESS ;
    
    SSPSTAT = 0 ;

    SSPCON1bits.SSPM = 0b1000 ; // master mode
    SSPCON1bits.SSPEN = 1 ; // enable i2c

    SSPCON2bits.RCEN = 1 ; // enable receive mode

    PIR1bits.SSPIF = 0 ; // clear interrupt flag
    PIE1bits.SSPIE = 1 ; // enable i2C interrupt

    
}

char UART_Read()
{
  while(!RCIF);
  return RCREG;
}

void UART_Write(char data)
{
  while(!TRMT);
  TXREG = data;
}

void UART_Write_Text(char *text)
{
  int i;
  for(i=0;text[i]!='\0';i++)
    UART_Write(text[i]);
}

int read_id_module(){
    int id = 0;
    if(!RoueCodeuse1){
        id += 1 ;
    }
    if(!RoueCodeuse2){
        id += 2 ;
    }
    if(!RoueCodeuse4){
        id += 4 ;
    }
    if(!RoueCodeuse8){
        id += 8 ;
    }
    return id ;
}

void parseMessage(){
    idModuleRead = RxBuffer[iRxLecture%BUFFER_SIZE];
    iRxLecture += 2 ;
    idBrocheRead = RxBuffer[iRxLecture%BUFFER_SIZE];
    iRxLecture += 2 ;
    valueBrocheRead = RxBuffer[iRxLecture%BUFFER_SIZE];
    iRxLecture += 2 ;
    iRx = 0 ;
    iRxLecture = 0 ;
    /*UART_Write('M') ;
    UART_Write(idModule);
    UART_Write('B') ;
    UART_Write(idBroche);
    UART_Write('V') ;
    UART_Write(valueBroche);*/
}

void power_On(){
    IdleI2C() ;
    WriteI2C(I2C_ADDRESS);
    WriteI2C(0x80);
    WriteI2C(0x03);
    StopI2C();
}

void get_light(){
    
}

int main(int argc, char** argv) {

    OSCCONbits.IRCF = 0b111 ;
    OSCTUNEbits.PLLEN = 1 ;
    init_USART();
    init_IOPin();

    INTCONbits.PEIE = 1 ; // enable peripheral interrupts
    INTCONbits.GIE = 1 ; // enable interrupts
    
    while(1)
    {
        power_On();
        get_light() ;
        delay_TLS0101(50000);
    }

    return 0;
}

void interrupt ISR(void){
    //check if the interrupt is caused by RX pin
    if(PIR1bits.RCIF)
    {
        RxBuffer[iRx%BUFFER_SIZE] = UART_Read() ;
        iRx++ ;
        PIR1bits.RCIF = 0; // clear rx flag
    }
    //check if the interrupt is caused by I2C pin
    if(PIR1bits.SSPIF)
    {

        PIR1bits.SSPIF  = 0;
    }
}

