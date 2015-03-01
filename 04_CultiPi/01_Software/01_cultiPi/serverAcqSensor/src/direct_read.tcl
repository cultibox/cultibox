# Pour que ça marche, ajouter dans la conf :
#  <item name="direct_read,1,input" input="1" />
#  <item name="direct_read,1,value" value="1" />
#  <item name="direct_read,1,input2" input="2" />
#  <item name="direct_read,1,value2" value="2" />
#  <item name="direct_read,1,type" type="CUVE" />

namespace eval ::direct_read {
    variable pin
    set pin(1,GPIO) 16
    set pin(1,init) 0
    set pin(2,GPIO) 20
    set pin(2,init) 0
    set pin(3,GPIO) 21
    set pin(3,init) 0
    set pin(4,GPIO) 26
    set pin(4,init) 0
    set pin(5,GPIO) 19
    set pin(5,init) 0
    set pin(6,GPIO) 13
    set pin(6,init) 0
    set pin(7,GPIO) 6
    set pin(7,init) 0
    set pin(8,GPIO) 5
    set pin(8,init) 0

}

# Cette proc est utilisée pour initialiser les variables
proc ::direct_read::init {nb_maxSensor} {

    for {set i 1} {$i <= $nb_maxSensor} {incr i} {
        if {[array get ::configXML direct_read,$i,input] == ""} {
            set ::configXML(direct_read,$i,input) "NA"
        }
        if {[array get ::configXML direct_read,$i,value] == ""} {
            set ::configXML(direct_read,$i,value) "NA"
        }
        if {[array get ::configXML direct_read,$i,input2] == ""} {
            set ::configXML(direct_read,$i,input2) "NA"
        }
        if {[array get ::configXML direct_read,$i,value2] == ""} {
            set ::configXML(direct_read,$i,value2) "NA"
        }
        if {[array get ::configXML direct_read,$i,type] == ""} {
            set ::configXML(direct_read,$i,type) "NA"
        }
    }

}

# Cette proc est utlisée pour initialiser la pin en sortie
proc ::direct_read::initPin {pinIndex} {
    variable pin

    # On définit la pin en sortie
    set RC [catch {
        exec gpio -g mode $pin($pinIndex,GPIO) in
    } msg]
    if {$RC != 0} {::piLog::log [clock milliseconds] "error" "::direct_read::initPin Not able to defined pin $pin($pinIndex,GPIO) as input -$msg-"}
    
    set pin($pinIndex,init) 1
    
}


proc ::direct_read::read_value {input} {
    variable pin 

    
    # S'il elle n'est pas initialisée, on le fait
    if {$pin($input,init) == 0} {
        initPin $input
    }

    set value "NA"
    set RC [catch {
        set value [exec gpio -g read $pin($input,GPIO)]
    } msg]

    if {$RC != 0} {
        ::piLog::log [clock milliseconds] "error" "::direct_read::read_value default when reading value of input $input (GPIO : $pin($input,GPIO)) message:-$msg-"
    } else {
        ::piLog::log [clock milliseconds] "debug" "::direct_read::read_value input $input (GPIO : $pin($input,GPIO)) value $value"
    }
    
    return $value
}
