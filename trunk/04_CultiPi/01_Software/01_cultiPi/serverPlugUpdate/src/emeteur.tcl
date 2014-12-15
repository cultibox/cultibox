set ::EMETEUR_NB_PLUG_MAX 16
set ::EMETEUR_OFF_VALUE 0
set ::EMETEUR_ON_VALUE 999

proc load_plugXX {} {

    set plugVFileName "plugv"
    set fid [open [file join $::confPath prg plgidx] r]
    while {[eof $fid] != 1 } {
        gets $fid UneLigne
        if {[string range $UneLigne 0 3] == "[rtc_readDay][rtc_readMonth]"} {
            set plugVFileName "plu[string range $UneLigne 4 5]"
            break
        }
    }
    close $fid

    ::piLog::log [clock milliseconds] "info" "plugv filename : $plugVFileName"

    set fid [open [file join $::confPath prg $plugVFileName] r]
    
    # On ne mit pas la première ligne qui ne sert à rien
    gets $fid UneLigne
    
    while {[eof $fid] != 1 } {
        gets $fid UneLigne
        if {$UneLigne != ""} {
            set time [expr [string range $UneLigne 0 4] * 1]

            set list ""
            for {set i 5} {$i < [string length $UneLigne]} {incr i 3} {
                set programmeCourt [string range $UneLigne [expr $i] [expr $i + 2]]
                switch $programmeCourt {
                    "000" {
                        lappend list "off"
                    }
                    "999" {
                        lappend list "on"
                    }
                    default {
                        lappend list [expr [string trimleft $programmeCourt 0] / 10.0]
                    }
                }
            }
            
            set ::programm($time) $list
        } else {
            break
        }
    }
    close $fid
}

proc rtc_readSecondsOfTheDay {} {

    set time [clock seconds] 

    set sec [string trimleft [clock format $time -format %S] "0"]
    if {$sec == ""} {set sec 0}
    set min [string trimleft [clock format $time -format %M] "0"]
    if {$min == ""} {set min 0}
    set hour [string trimleft [clock format $time -format %H] "0"]
    if {$hour == ""} {set hour 0}

    return [expr $sec + $min * 60 + $hour * 3600]
}

proc rtc_readDay {} {

    return [clock format [clock seconds] -format %d]
}

proc rtc_readMonth {} {

    return [clock format [clock seconds] -format %m]
}

proc emeteur_init {} {

    set ::emeteur_actualDay ""
    set ::c8_emeteurPlugFileName "plugv"
    set ::nextTimeToChange 0
    set ::uc8_regulationIsDone 0
    set ::uc8_alarm 0
    
}

proc getsProgramm {rtc_readSecondsOfTheDay {updateNextTimeToChange 0}} {
    set prg ""
    set updateNextTimeToChangeIsUpdated 0
    ::piLog::log [clock milliseconds] "info" "changes [lsort -integer [array names ::programm]]"
    foreach timeS [lsort -integer [array names ::programm]] {
        if {$rtc_readSecondsOfTheDay >= $timeS && $prg == ""} {
            set prg $::programm($timeS)
        } elseif {$prg != "" && $updateNextTimeToChangeIsUpdated == 0 && $updateNextTimeToChange != 0} {
            set updateNextTimeToChangeIsUpdated 1
            set ::nextTimeToChange $timeS
            break
        }
    }
    
    return $prg
}

proc emeteur_update_loop {} {
        
    # Read actual hour
    set uc24_seconds [rtc_readSecondsOfTheDay]
               
    # If system is in alarm state
    if {$::uc8_alarm == 1}  {
        for {set i 0} {$i < $::EMETEUR_NB_PLUG_MAX} {incr i} {
            # Save value
            set emeteur_regulation_value($i) $::EMETEUR_OFF_VALUE
            # update plug value
            emeteur_update_plug_value $i $::EMETEUR_OFF_VALUE
        }

        # Save it on log.txt
        ::piLog::log [clock milliseconds] "info" "IN ALARME"

        set ::uc8_alarm 2
      
        after 1000 emeteur_update_loop
      
        return 0
    }

    # If first evaluation of values {plug adress are send and plug value not send} are not done
    if {[rtc_readDay] != $::emeteur_actualDay } {

        # Load plugV
        load_plugXX

        set programmeToSend [getsProgramm $uc24_seconds "updatenextTimeToChange"]

        for {set i 1} {$i <= $::EMETEUR_NB_PLUG_MAX} {incr i} {
            updatePlug $i
        }

        ::piLog::log [clock milliseconds] "info" "init emetor next change $::nextTimeToChange"
        
        # register day
        set ::emeteur_actualDay [rtc_readDay]

    } elseif {$uc24_seconds >= $::nextTimeToChange} {

        set programmeToSend [getsProgramm $uc24_seconds "updatenextTimeToChange"]

        for {set i 1} {$i <= $::EMETEUR_NB_PLUG_MAX} {incr i} {
            updatePlug $i
        }
        
        ::piLog::log [clock milliseconds] "info" "next change $::nextTimeToChange"
        
    } elseif {[expr $uc24_seconds % 5] == 0} { 
    

        # La régulation doit être faite
        if {$::uc8_regulationIsDone == 0} \
        {

            # update plug
            for {set i 1} {$i <= $::EMETEUR_NB_PLUG_MAX} {incr i} \
            {
                emeteur_regulation $i
            }
            set ::uc8_regulationIsDone 1
        }

    } else {
        set ::uc8_regulationIsDone 0
    }

    after 500 emeteur_update_loop
    
    return 0
    
}

proc emeteur_regulation {plugNumber} {

    set programmeToSend [getsProgramm [rtc_readSecondsOfTheDay]]

    # On cherche le programme de la prise (attention les prises démarre à 1 !)
    set plgPrgm [lindex $programmeToSend [expr $plugNumber - 1]]
    
    if {$plgPrgm == ""} {
        ::piLog::log [clock milliseconds] "error" "Plug $plugNumber programme is empty"
    } elseif {$plgPrgm != "off" && $plgPrgm != "on"} {
        ::piLog::log [clock milliseconds] "debug" "regulation plug $plugNumber programme $plgPrgm"

        # On envoi la commande au module
        #::wireless::setValue $plugNumber $plgPrgm
 
   }

}

proc updatePlug {plugNumber} {

    # retourne l'ensemble du programme pour toutes les prises
    set programmeToSend [getsProgramm [rtc_readSecondsOfTheDay]]
    
    # On cherche le programme de la prise (attention les prises démarre à 1 !)
    set plgPrgm [lindex $programmeToSend [expr $plugNumber - 1]]

    if {$plgPrgm == ""} {
        ::piLog::log [clock milliseconds] "error" "Plug $plugNumber programme is empty"
    } elseif {$plgPrgm != "off" && $plgPrgm != "on"} {
        # Si c'est de la régulation
        emeteur_regulation $plugNumber 
    } else {
        ::piLog::log [clock milliseconds] "info" "update plug $plugNumber with programm $plgPrgm"
        # On traduit on et off
        if {$plgPrgm == "on"} {
            set plgPrgm [expr 1]
        } else {
            set plgPrgm [expr 0]
        }
        
        # On envoi la commande au module
        ::wireless::setValue $plugNumber $plgPrgm
    }
}

# cette procédure est utilisée pour lire la valeur des capteurs
proc readSensors {} {
    ::piLog::log [clock milliseconds] "error" "proc readSensors to write"
    
    after 1000 readSensors
}
