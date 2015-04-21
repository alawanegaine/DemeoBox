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

#include <pic18.h>

#include <xc.h>

#define FOSC 8000000
#define BUFFER_SIZE 200

volatile unsigned char t ;
volatile unsigned char rcindex ;
volatile unsigned char RxBuffer[BUFFER_SIZE] ;
volatile unsigned char TxBuffer[BUFFER_SIZE] ;

// #pragma config statements should precede project file includes.
// Use project enums instead of #define for ON and OFF.

// CONFIG1H
#pragma config OSC = XT    // Oscillator Selection bits (Internal oscillator block, port function on RA6 and RA7)
#pragma config FCMEN = OFF      // Fail-Safe Clock Monitor Enable bit (Fail-Safe Clock Monitor disabled)
#pragma config IESO = OFF       // Internal/External Oscillator Switchover bit (Oscillator Switchover mode disabled)

// CONFIG2L
#pragma config PWRT = OFF       // Power-up Timer Enable bit (PWRT disabled)
#pragma config BOREN = SBORDIS  // Brown-out Reset Enable bits (Brown-out Reset enabled in hardware only (SBOREN is disabled))
#pragma config BORV = 3         // Brown Out Reset Voltage bits (Minimum setting)

// CONFIG2H
#pragma config WDT = OFF        // Watchdog Timer Enable bit (WDT disabled (control is placed on the SWDTEN bit))
#pragma config WDTPS = 32768    // Watchdog Timer Postscale Select bits (1:32768)

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
void delay_TLS0101(unsigned int delayVal) // delay function
{
    unsigned int i; // define local variable int
    for (i = 0; i < delayVal; i++)
    {
        Nop();
    }
}

void init_adc(void)
{
    TRISA = 0b1111 ;
    ADCON0bits.CHS = 0;
    ADCON1bits.PCFG = 0b1011 ;
    ADCON0bits.ADON     = 0b1;  // turn on the ADC
}

uint16_t adc_convert(uint8_t channel)
{
    //ADCON0bits.CHS      = channel;  // select the given channel
    ADCON0bits.GO       = 0b1;      // start the conversion
    while(ADCON0bits.DONE);         // wait for the conversion to finish
    return (ADRESH<<8)|ADRESL;      // return the result
}

void init_USART(){
    TXSTAbits.TXEN = 1 ; // enable transmitter
    TXSTAbits.BRGH = 1 ; // high baud rate mode
    RCSTAbits.CREN = 1 ; // enable continous receiving

    // confirgure I/O pins
    TRISCbits.RC7 = 1 ; // RX pin is input
    TRISCbits.RC6 = 0 ; // TX pin is output

    SPBRG = 12 ;        // set baud rate to 9600 baud at 8MHz

    PIE1bits.RCIE =  1 ; // enable USART receive interrupt
    RCSTAbits.SPEN = 1 ; // enable USART
}

void USART_putc(unsigned char c){
    while(!TXSTAbits.TRMT); // wait until transmit shift register is empty
    TXREG = c ; // write character to TXREG and stat transmission
}

void USART_puts(unsigned char *s) {
    while(*s){
        USART_putc(*s);  // send character pointed to by s
        s++ ;           // increase pointer location to the next character
    }
}

int main(int argc, char** argv) {

    init_USART();

    USART_puts("Init complete!\n");

    INTCONbits.PEIE = 1 ; // enable peripheral interrupts
    INTCONbits.GIE = 1 ; // enable interrupts
    
    while(1)
    {
                
    }

    return 0;
}

void interrupt ISR(void){
    if (PIR1bits.RCIF)  // check if receive interrupt has fired
    {
        t = RCREG;      // read received character to buffer

        USART_putc(t) ;
        PIR1bits.RCIF = 0;      // reset receive interrupt flag
    }
}

