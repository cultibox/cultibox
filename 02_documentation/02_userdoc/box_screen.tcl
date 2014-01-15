package require Img

# Définition des caracteres
set chars {
    { 0x00 0x00 0x00 0x00 0x00 }  
    { 0x00 0x00 0x7a 0x00 0x00 }   
    { 0x00 0x70 0x00 0x70 0x00 }   
    { 0x14 0x7f 0x14 0x7f 0x14 }   
    { 0x24 0x52 0x7f 0x52 0x12 }   
    { 0x31 0x32 0x04 0x0b 0x13 }   
    { 0x05 0x22 0x55 0x49 0x36 }   
    { 0x00 0x00 0x60 0x50 0x00 }   
    { 0x00 0x41 0x22 0x64 0x00 }   
    { 0x00 0x64 0x22 0x41 0x00 }   
    { 0x14 0x08 0x3e 0x08 0x14 }   
    { 0x08 0x08 0x3e 0x08 0x08 }   
    { 0x00 0x06 0x05 0x00 0x00 }   
    { 0x04 0x04 0x04 0x04 0x04 }   
    { 0x00 0x00 0x03 0x03 0x00 }   
    { 0x20 0x10 0x08 0x04 0x02 }   
    { 0x3e 0x51 0x49 0x45 0x3e }   
    { 0x00 0x01 0x7f 0x21 0x00 }   
    { 0x31 0x49 0x45 0x43 0x21 }   
    { 0x46 0x69 0x51 0x41 0x42 }   
    { 0x04 0x7f 0x24 0x14 0x0c }   
    { 0x4e 0x51 0x51 0x51 0x72 }   
    { 0x06 0x49 0x49 0x29 0x1e }   
    { 0x60 0x50 0x48 0x47 0x40 }   
    { 0x36 0x49 0x49 0x49 0x36 }   
    { 0x3c 0x4a 0x49 0x49 0x30 }   
    { 0x00 0x00 0x36 0x36 0x00 }   
    { 0x00 0x00 0x36 0x35 0x00 }   
    { 0x00 0x41 0x22 0x14 0x08 }   
    { 0x14 0x14 0x14 0x14 0x14 }   
    { 0x08 0x14 0x22 0x41 0x00 }   
    { 0x30 0x48 0x45 0x40 0x20 }   
    { 0x3e 0x45 0x4d 0x49 0x26 }   
    { 0x3f 0x44 0x44 0x44 0x3f }   
    { 0x36 0x49 0x49 0x49 0x7f }   
    { 0x22 0x41 0x41 0x41 0x3e }   
    { 0x1c 0x22 0x41 0x41 0x7f }   
    { 0x41 0x49 0x49 0x49 0x7f }   
    { 0x40 0x48 0x48 0x48 0x7f }   
    { 0x2f 0x49 0x49 0x41 0x3e }   
    { 0x7f 0x08 0x08 0x08 0x7f }   
    { 0x00 0x41 0x7f 0x41 0x00 }   
    { 0x40 0x7e 0x41 0x01 0x02 }   
    { 0x41 0x22 0x14 0x08 0x7f }   
    { 0x01 0x01 0x01 0x01 0x7f }   
    { 0x7f 0x20 0x18 0x20 0x7f }   
    { 0x7f 0x04 0x08 0x10 0x7f }   
    { 0x3e 0x41 0x41 0x41 0x3e }   
    { 0x30 0x48 0x48 0x48 0x7f }   
    { 0x3d 0x42 0x45 0x41 0x3e }   
    { 0x31 0x4a 0x4c 0x48 0x7f }   
    { 0x46 0x49 0x49 0x49 0x31 }   
    { 0x40 0x40 0x7f 0x40 0x40 }   
    { 0x7e 0x01 0x01 0x01 0x7e }   
    { 0x7c 0x02 0x01 0x02 0x7c }   
    { 0x7e 0x01 0x0e 0x01 0x7e }   
    { 0x63 0x14 0x08 0x14 0x63 }   
    { 0x70 0x08 0x07 0x08 0x70 }   
    { 0x61 0x51 0x49 0x45 0x43 }   
    { 0x00 0x41 0x41 0x7f 0x00 }   
    { 0x55 0x2a 0x55 0x2a 0x55 }   
    { 0x00 0x7f 0x41 0x41 0x00 }   
    { 0x10 0x20 0x40 0x20 0x10 }   
    { 0x01 0x01 0x01 0x01 0x01 }   
    { 0x00 0x10 0x20 0x40 0x00 }   
    { 0x0f 0x15 0x15 0x15 0x02 }   
    { 0x0e 0x11 0x11 0x09 0x7f }   
    { 0x02 0x11 0x11 0x11 0x0e }   
    { 0x7f 0x09 0x11 0x11 0x0e }   
    { 0x0c 0x15 0x15 0x15 0x0e }   
    { 0x20 0x40 0x48 0x3f 0x08 }   
    { 0x3e 0x25 0x25 0x25 0x18 }   
    { 0x0f 0x10 0x10 0x08 0x7f }   
    { 0x00 0x01 0x5f 0x11 0x00 }   
    { 0x00 0x5e 0x11 0x01 0x02 }   
    { 0x00 0x11 0x0a 0x04 0x7f }   
    { 0x00 0x01 0x7f 0x41 0x00 }   
    { 0x0f 0x10 0x0c 0x10 0x1f }   
    { 0x0f 0x10 0x10 0x08 0x1f }   
    { 0x0e 0x11 0x11 0x11 0x0e }   
    { 0x08 0x14 0x14 0x14 0x1f }   
    { 0x1f 0x0c 0x14 0x14 0x08 }   
    { 0x08 0x10 0x10 0x08 0x1f }   
    { 0x02 0x15 0x15 0x15 0x09 }   
    { 0x02 0x01 0x11 0x7e 0x10 }   
    { 0x1f 0x02 0x01 0x01 0x1e }   
    { 0x1c 0x02 0x01 0x02 0x1c }   
    { 0x1e 0x01 0x06 0x01 0x1e }   
    { 0x11 0x0a 0x04 0x0a 0x11 }   
    { 0x1e 0x05 0x05 0x05 0x18 }   
    { 0x11 0x19 0x15 0x13 0x11 }   
    { 0x00 0x18 0x24 0x24 0x18 }
}

lappend listeTexte {
  {--Parametres--}
  {1. Cultibox}
  {2. Emetteur}
  {3. Capteur}
  {4. Dimmer}
  {5. Retour}
}
lappend listFileName {lcd_menu_param.png}

lappend listeTexte {
  {---Cultibox--}
  {1. Horloge}
  {2. Registre}
  {3. Alarme}
  {4. Prise}
  {5. Retour}
}
lappend listFileName {lcd_menu_param_cultibox.png}

lappend listeTexte {
  {---Emetteur---}
  {1. Parametres}
  {2. Reg. Prises}
  {3. Retour}
}
lappend listFileName {lcd_menu_param_emetteur.png}

lappend listeTexte {
  {--Parametres--}
  {Chip:001}
  {Firmw:001.004}
  {Time :00h10m02}
  {Act.0:00h00m00}
  {Act.1:00h00m00}
  {Act.2:00h00m00}
  {Act.3:00h00m00}
  {Act.4:00h00m00}
  {Day saved:012}
  {Bootloader:000}
}
lappend listFileName {lcd_menu_param_emetteur_param.png}

lappend listeTexte {
  {P01 A004V001}
  {P02 A247V000}
  {P03 A222V001}
  {P04 A219V000}
  {P05 A215V000}
  {P06 A207V000}
  {P07 A252V001}
  {P08 A250V000}
  {P09 A246V001}
  {P10 A238V000}
  {P11 A187V000}
  {P12 A183V000}
  {P13 A189V001}
  {P14 A125V000}
  {P15 A123V000}
  {P16 A119V000}
  {R00 000}
  {R01 000}
}
lappend listFileName {lcd_menu_param_emetteur_reg_prises.png}

lappend listeTexte {
  {Reg: 00: 001}
  {Reg: 01: 000}
  {Reg: 02: 000}
  {Reg: 03: 000}
  {Reg: 04: 255}
  {Reg: 05: 255}
}
lappend listFileName {lcd_menu_param_emetteur_reg_autres.png}

lappend listeTexte {
  {---Registre---}
  {Contrast:018}
  {Firmw.:002.000}
  {Freq Plug :000}
  {Log Freq :300}
  {Pwr Freq :060}
  {RTC Off.:000}
  {Reset Min:0000}
  {Id: 00045}
}
lappend listFileName {lcd_menu_param_cultibox_reg.png}

lappend listeTexte {
  {---Alarme---}
  {Active: 0}
  {Value: 15.00}
  {Sensor: T}
  {Sens: +}
}
lappend listFileName {lcd_menu_param_cultibox_alarme.png}

lappend listeTexte {
  {----Prise-----}
  {1. Prise 1}
  {2. Prise 2}
  {3. Prise 3}
  {4. Prise 4}
  {5. Prise 5}
  {6. Prise 6}
  {7. Prise 7}
  {8. Prise 8}
  {9. Prise 9}
  {10. Prise 10}
}
lappend listFileName {lcd_menu_param_cultibox_prise.png}

lappend listeTexte {
  {--Parametres--}
  {Regul Primaire}
  {Type : N}
  {Sens : +}
  {Tol : 00,00}
}
lappend listFileName {lcd_menu_param_cultibox_prise_1.png}

lappend listeTexte {
  {--Parametres--}
  {Regul Secondai}
  {Type : N}
  {Sens : +}
  {Etat Actif: 0}
  {Valeur:00,00}
}
lappend listFileName {lcd_menu_param_cultibox_prise_2.png}

lappend listeTexte {
  {--Parametres--}
  {Tol. : 00,00}
  {Calcul Valeur}
  {Type : Moyenne}
  {Capteur 1: OUI}
  {Capteur 2: OUI}
  {Capteur 3: OUI}
  {Capteur 4: OUI}
  {Capteur 5: OUI}
  {Capteur 6: OUI}
}
lappend listFileName {lcd_menu_param_cultibox_prise_3.png}

lappend listeTexte {
  {--Parametres--}
  {Val Pri:00,00}
  {Val Sec:00,00}
  {Seuil:00,00}
  {Val Reg:00,00}
  {Etat Prise:000}
}
lappend listFileName {lcd_menu_param_cultibox_prise_5.png}

lappend listeTexte {
  {--Parametres--}
  {Type : NO}
  {Firm : 000.000}
  {Reg: 00: 000}
  {Reg: 01: 000}
  {Reg: 02: 000}
}
lappend listFileName {lcd_menu_param_sensor_reg.png}

lappend listeTexte {
  {---Capteur----}
  {1. Capteur 1}
  {2. Capteur 2}
  {3. Capteur 3}
  {4. Capteur 4}
  {5. Capteur 5}
  {6. Capteur 6}
  {7. Retour}
}
lappend listFileName {lcd_menu_param_capteur.png}


lappend listeTexte {
  {   Horloge}
  {line}
  {Date: 01/02/13}
  {Heure:   04:05}
  {line}
  {   Valider}
}
lappend listFileName {lcd_menu_param_cultibox_hour.png}

lappend listeTexte {
  {21 Mars}
  {line}
  {Engrais}
  {3 ml/L}
  {Floraison}
}
lappend listFileName {lcd_events.png}

# Pour chaque image à traiter
foreach name $listFileName texte $listeTexte {

    # Définition de la largeur
    set width 180
    
    # Définition de la hauteur
    set heigth [expr 8 + 21 * [llength $texte]]
    if {$heigth < 136} {set heigth 136}
    
    #console show
    image create photo screen -width $width -height $heigth
    
    # Mise en place du fond
    for {set i 0} {$i < $width} {incr i} {
        for {set j 0} {$j < $heigth} {incr j} {
            screen put #808080 -to $i $j
        }
    }

    # Mise en place du cadre
    for {set i 4} {$i < [expr $width - 4]} {incr i} {
        for {set j 4} {$j < [expr $heigth - 4]} {incr j} {
            screen put #CCFFCC -to $i $j
        }
    }


    # Ecriture du texte
    set y 0
    set x 4
    foreach line $texte {
        if {$line != "line"} {
            # Du texte à afficher
            for {set c 0} {$c < [string length $line]} {incr c} {
                scan [string index $line $c] %c ascii
                set pixels [lindex $chars [expr $ascii - 32]]
                foreach pixel [lreverse $pixels] {
                    puts $pixel
                    for {set l 0} {$l < 8} {incr l} {
                        if {[expr int(pow(2,$l)) & $pixel] != 0} {
                            screen put #808080 -to [expr 2 * $x + 0] [expr 2 * ($y * 10 + 8 - $l + 2) + 0]
                            screen put #808080 -to [expr 2 * $x + 1] [expr 2 * ($y * 10 + 8 - $l + 2) + 0]
                            screen put #808080 -to [expr 2 * $x + 0] [expr 2 * ($y * 10 + 8 - $l + 2) + 1]
                            screen put #808080 -to [expr 2 * $x + 1] [expr 2 * ($y * 10 + 8 - $l + 2) + 1]
                        }
                    }
                    incr x
                }
                incr x
            }
        } else {
            #Une ligne a afficher
            for {set l 0} {$l < $width} {incr l} {
                screen put #808080 -to [expr $l] [expr 2 * ($y * 10 + 8 + 2) - 1]
                screen put #808080 -to [expr $l] [expr 2 * ($y * 10 + 8 + 2) + 0]
            }
        }
        incr y
        set x 4
    }

screen write [file join [file dirname [info script]] wiki img $name] -format PNG
}

exit