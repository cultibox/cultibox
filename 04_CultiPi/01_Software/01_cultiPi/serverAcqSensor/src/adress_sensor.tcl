# Définition des registres standard
set SENSOR_GENERIC_HP_ADR 0x20
set SENSOR_GENERIC_LP_ADR 0x21
set SENSOR_GENERIC_HP2_ADR 0x22
set SENSOR_GENERIC_LP2_ADR 0x23
set SLAVE_REG_MINOR_VERSION 0x76 ;#define SLAVE_REG_MINOR_VERSION 'v'
set SLAVE_REG_MAJOR_VERSION 0x56 ;#define SLAVE_REG_MAJOR_VERSION 'V'
set SLAVE_REG_CHIP_VERSION 0x43 ;#define SLAVE_REG_CHIP_VERSION 'C'

# Définition des adresses des capteurs
set sensor(SHT,1,adress) 0x01 ;#define SLAVE_SHT_ADRESS_1 0x02
set sensor(SHT,2,adress) 0x02 ;#define SLAVE_SHT_ADRESS_2 0x04
set sensor(SHT,3,adress) 0x03 ;#define SLAVE_SHT_ADRESS_3 0x06
set sensor(SHT,4,adress) 0x04 ;#define SLAVE_SHT_ADRESS_4 0x08

set sensor(DS18B20,2,adress) 0x05 ;#define SLAVE_DS18B20_ADRESS_2 0x10
set sensor(DS18B20,3,adress) 0x06 ;#define SLAVE_DS18B20_ADRESS_3 0x12
set sensor(DS18B20,4,adress) 0x07 ;#define SLAVE_DS18B20_ADRESS_4 0x14
set sensor(DS18B20,5,adress) 0x2F ;#define SLAVE_DS18B20_ADRESS_5 0x4E 0100 1110 --> 0010 0111  | Old : 0x4E New 0x5E
set sensor(DS18B20,6,adress) 0x30 ;#define SLAVE_DS18B20_ADRESS_6 0x50 0101 0000 --> 0010 1000  | Old : 0x50 New 0x60

set sensor(WATER_LEVEL,5,adress) 0x0B ;#define SLAVE_WATER_LEVEL_ADRESS_5 0x16
set sensor(WATER_LEVEL,6,adress) 0x0C ;#define SLAVE_WATER_LEVEL_ADRESS_6 0x18

set sensor(PH,2,adress) 0x13 ;#define SLAVE_PH_ADRESS_2 0x26
set sensor(PH,3,adress) 0x14 ;#define SLAVE_PH_ADRESS_3 0x28
set sensor(PH,4,adress) 0x15 ;#define SLAVE_PH_ADRESS_4 0x2A
set sensor(PH,5,adress) 0x16 ;#define SLAVE_PH_ADRESS_5 0x2C
set sensor(PH,6,adress) 0x17 ;#define SLAVE_PH_ADRESS_6 0x2E

set sensor(EC,2,adress) 0x18 ;#define SLAVE_EC_ADRESS_2 0x30
set sensor(EC,3,adress) 0x19 ;#define SLAVE_EC_ADRESS_3 0x32
set sensor(EC,4,adress) 0x1A ;#define SLAVE_EC_ADRESS_4 0x34
set sensor(EC,5,adress) 0x1B ;#define SLAVE_EC_ADRESS_5 0x36
set sensor(EC,6,adress) 0x1C ;#define SLAVE_EC_ADRESS_6 0x38

set sensor(ORP,2,adress) 0x1D ;#define SLAVE_ORP_ADRESS_2 0x3A
set sensor(ORP,3,adress) 0x1E ;#define SLAVE_ORP_ADRESS_3 0x3C
set sensor(ORP,4,adress) 0x1F ;#define SLAVE_ORP_ADRESS_4 0x3E
set sensor(ORP,5,adress) 0x28 ;#define SLAVE_ORP_ADRESS_5 0x40 | Old : 0x40 New 0x50
set sensor(ORP,6,adress) 0x29 ;#define SLAVE_ORP_ADRESS_6 0x42 | Old : 0x42 New 0x52

set sensor(OD,2,adress) 0x2A ;#define SLAVE_OD_ADRESS_2 0x44 | Old : 0x44 New 0x54
set sensor(OD,3,adress) 0x2B ;#define SLAVE_OD_ADRESS_3 0x46 | Old : 0x46 New 0x55
set sensor(OD,4,adress) 0x2C ;#define SLAVE_OD_ADRESS_4 0x48 | Old : 0x48 New 0x58
set sensor(OD,5,adress) 0x2D ;#define SLAVE_OD_ADRESS_5 0x4A | Old : 0x4A New 0x5A
set sensor(OD,6,adress) 0x2E ;#define SLAVE_OD_ADRESS_6 0x4C | Old : 0x4C New 0x5C