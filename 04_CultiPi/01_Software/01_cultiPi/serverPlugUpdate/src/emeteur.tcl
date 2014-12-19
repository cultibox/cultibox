set ::EMETEUR_NB_PLUG_MAX 16
set ::EMETEUR_OFF_VALUE 0
set ::EMETEUR_ON_VALUE 999

proc emeteur_init {} {

    set ::emeteur_actualDay ""
    set ::c8_emeteurPlugFileName "plugv"
    set ::nextTimeToChange 0
    set ::uc8_regulationIsDone 0
    set ::uc8_alarm 0
    set ::actualProgramm ""

}

proc load_plugXX {} {

    # On efface l'ancien vecteur s'il existe
    if {[array exists ::programm]} {
        array unset ::programm
    }

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

proc getsProgramm {rtc_readSecondsOfTheDay {updateNextTimeToChange 0}} {
    set prg ""
    set lastProgramm ""

    foreach timeS [lsort -integer [array names ::programm]] {
    
        # On cherche l'élément le dernier élément inférieur à rtc_readSecondsOfTheDay
        if {$timeS > $rtc_readSecondsOfTheDay} {
            
            set prg $lastProgramm

            # Si besoin, on sauvegarde le prochain élément à envoyer
            if {$updateNextTimeToChange != 0} {
                set ::nextTimeToChange $timeS
            }
            
            break

        }
        
        set lastProgramm $::programm($timeS)
        
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
        set ::actualProgramm $programmeToSend

        for {set i 1} {$i <= $::EMETEUR_NB_PLUG_MAX} {incr i} {
            updatePlug $i
        }

        ::piLog::log [clock milliseconds] "info" "init emetor next change $::nextTimeToChange"
        
        # register day
        set ::emeteur_actualDay [rtc_readDay]

    } elseif {$uc24_seconds >= $::nextTimeToChange && $uc24_seconds != 86399} {

        set programmeToSend [getsProgramm $uc24_seconds "updatenextTimeToChange"]
        set ::actualProgramm $programmeToSend

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
                set plgPrgm [lindex $::actualProgramm [expr $i - 1]]
                if {$plgPrgm != "on" && $plgPrgm != "off"} {
                    emeteur_regulation $i $plgPrgm
                }
            }
            set ::uc8_regulationIsDone 1
        }

    } else {
        set ::uc8_regulationIsDone 0
    }

    after 500 emeteur_update_loop
    
    return 0
    
}


proc updatePlug {plugNumber} {

    # retourne l'ensemble du programme pour toutes les prises
    set programmeToSend $::actualProgramm
    
    # On cherche le programme de la prise (attention les prises démarre à 1 !)
    set plgPrgm [lindex $programmeToSend [expr $plugNumber - 1]]

    if {$plgPrgm == ""} {
        ::piLog::log [clock milliseconds] "error" "Plug $plugNumber programme is empty"
    } elseif {$plgPrgm != "off" && $plgPrgm != "on"} {
        # Si c'est de la régulation
        emeteur_regulation $plugNumber $plgPrgm 
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

proc savePlugSendValue {plug value} {
    
    # On enregistre l'état de la prise
    set ::plug($plug,value)  $value
    
    # On ajoute à la liste des valeurs mise à jour
    # Si seulement il n'y a pas déjà une valeur
    if {[lsearch $::plug(updated) $plug] == -1} {
        lappend ::plug(updated)  $plug
    }
    
}

proc emeteur_subscriptionEvenement {} {

    set ThereAreSomeClient 0

    if {$::plug(updated) != ""} {
    
        # Pour chaque prise mise à jour
        foreach plugNb $::plug(updated) {
        
            if {[array names ::plug -exact subscription,$plugNb] == ""} {
                set ::plug(subscription,$plugNb) ""
            }
        
            # On envoi à tous les client qui on un abonnement événementiel
            foreach client $::plug(subscription,$plugNb) {
                
                ::piServer::sendToServer $client "$client [incr ::TrameIndex] _subscriptionEvenement ::plug($plugNb,value) $::plug($plugNb,value) [clock milliseconds]"
                
                set ThereAreSomeClient 1
            }
        
        }
    
        # On efface la liste si on a envoyé quelque chose
        if {$ThereAreSomeClient != 0} {
            set ::plug(updated) ""
        }
    }
    
    after 200 emeteur_subscriptionEvenement

}
