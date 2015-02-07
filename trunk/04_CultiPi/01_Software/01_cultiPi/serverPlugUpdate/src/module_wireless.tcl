
namespace eval ::wireless {
    variable debug 0
    variable moduleAdress 0x55
    variable bootloadStateAdress 0x62          ;# b
    variable bootloadJumpToAppAdress 0x53 ;# S
    variable minorVersionAdress 0x76 ;# v
    variable majorVersionAdress 0x56 ;# V
    variable enableOutputAdress 0x00 ; #define EM_PARA_ADR        0x00
}

# Cette proc est utlis�e pour d�marrer le module de l'�metteur
proc ::wireless::outFromBootloader {} {
    variable moduleAdress 
    variable bootloadStateAdress
    variable bootloadJumpToAppAdress
    variable minorVersionAdress
    variable majorVersionAdress
    
    # Gestion des erreurs de d�marrage
    # On essaye d'�crire le registre bootloadStateAdress
    # -> Si �a ne marche pas, il faut eteindre et red�marrer
    # -> Si �a marche , on essaye de lire la version
    # -> Si �a marche pas, on r�-�crit le registre bootloadStateAdress
    
    # On essaye d'�crire le registre bootloadStateAdress
    set RC [catch {
        exec i2cset -y 1 $moduleAdress $bootloadStateAdress
    } msg]
    if {$RC != 0} {
        set ::plug(etat,bootload) "DEFCOM"
        ::piLog::log [clock milliseconds] "error_critic" "We need to restart module:$msg "
        return "restart_needed"
    } else {
        ::piLog::log [clock milliseconds] "debug" "write bootloadStateAdress OK"
    }
    after 20
    
    # On lit la version
    set started ""
    set RC [catch {
        set bootloadState [exec i2cget -y 1 $moduleAdress]
    } msg]
    
    if {$RC != 0} {
        set ::plug(etat,bootload) "DEFCOM"
        ::piLog::log [clock milliseconds] "error" "error during reading bootload state error:$msg "
        return "retry_needed"
    }
    
    ::piLog::log [clock milliseconds] "debug" "emetteur bootload state : $bootloadState"
    
    # SI ce n'est pas le cas, on le d�marre
    if {$bootloadState == 0} {
        set ::plug(etat,bootload) "0"
    } else {
        set RC [catch {
            exec i2cset -y 1 $moduleAdress $bootloadJumpToAppAdress
        } msg]
        
        if {$RC != 0} {
            set ::plug(etat,bootload) "DEFCOM"
            ::piLog::log [clock milliseconds] "error" "error during jump to main app error : $msg "
        }
        
        # On lui laisse un peu de temps pour d�marrer
        after 10
    }
    
    # On enregistre les infos de versions
    # Num�ro majeur
    set RC [catch {
        exec i2cset -y 1 $moduleAdress $majorVersionAdress
        set major [exec i2cget -y 1 $moduleAdress]
    } msg]
    
    if {$RC != 0} {
        ::piLog::log [clock milliseconds] "error" "Not able to get major version : $msg "
    } else {
        set ::plug(etat,majorVersion) $major
        ::piLog::log [clock milliseconds] "debug" "emetteur major version : $major"
    }
    
    # Num�ro mineur
    set RC [catch {
        exec i2cset -y 1 $moduleAdress $minorVersionAdress
        set minor [exec i2cget -y 1 $moduleAdress]
    } msg]
    
    if {$RC != 0} {
        ::piLog::log [clock milliseconds] "error" "Not able to get minor version : $msg "
    } else {
        set ::plug(etat,minorVersion) $minor
        ::piLog::log [clock milliseconds] "debug" "emetteur major version : $minor"
    }
    
}

# Cette proc est utilis�e pour d�marrer le chip de l'�metteur
proc ::wireless::start {} {
    variable enableOutputAdress
    variable moduleAdress
    
    # On indique � l'�metteur qu'il peut d�marrer
    set RC [catch {
        exec i2cset -y 1 $moduleAdress $enableOutputAdress 0
    } msg]
    if {$RC != 0} {
        set ::plug(etat,bootload) "DEFCOM"
        ::piLog::log [clock milliseconds] "error" "Can not enable output of emettor erreur : $msg "
        return -1
    } else {
        ::piLog::log [clock milliseconds] "debug" "Emettor output enable"
    }
    return 0
}

# Cette proc est utlis�e pour d�finir l'adresse d'une prise dans le module sans fils
proc ::wireless::setAdress {plugNumber adress} {
    variable moduleAdress 
    
    # On demande l'�criture dans le registre des adresses
    #0x20 + 2 * i, emeteur_plug_adress[i]
    # L'adresse du registre
    # - Prise 1 --> 32
    # - Prise 2 --> 34
    set register [expr ($plugNumber - 1) * 2 + 32]
    
    set RC [catch {
        exec i2cset -y 1 $moduleAdress $register $::plug($plugNumber,adress)
    } msg]

    if {$RC != 0} {
        set ::plug($plugNumber,updateStatus) "DEFCOM"
        set ::plug($plugNumber,updateStatusComment) ${msg}
        ::piLog::log [clock milliseconds] "error" "default when updating adress of plug $plugNumber (adress module : $moduleAdress - register $register) message:-$msg-"
    } else {
        set ::plug($plugNumber,updateStatus) "OK"
        set ::plug($plugNumber,updateStatusComment) [clock milliseconds]
        ::piLog::log [clock milliseconds] "debug" "plug $plugNumber (adress module : $moduleAdress - register $register)  is updated with value $::plug($plugNumber,adress)"
    }
}

# Cette proc est utilis�e pour piloter une prise dans le module sans fils
proc ::wireless::setValue {plugNumber value address} {
    variable moduleAdress 

    # On demande l'�criture dans le registre des adresses
    # reg_adress = 0x20 + 2 * plugNumber + 1;
    # L'adresse du registre
    # - Prise 1 --> 33
    # - Prise 2 --> 35
    set register [expr ($plugNumber - 1) * 2 + 33]

    # On sauvegarde l'�tat de la prise
    ::savePlugSendValue $plugNumber $value
    
    if {$value == "on"} {
        set value 1 
    } else {
        set value 0
    }    
    
    set RC [catch {
        exec i2cset -y 1 $moduleAdress $register $value
    } msg]

    if {$RC != 0} {
        set ::plug($plugNumber,updateStatus) "DEFCOM"
        set ::plug($plugNumber,updateStatusComment) ${msg}
        ::piLog::log [clock milliseconds] "error" "default when updating value of plug $plugNumber (adress module : $moduleAdress - register $register) message:-$msg-"
    } else {
        set ::plug($plugNumber,updateStatus) "OK"
        set ::plug($plugNumber,updateStatusComment) [clock milliseconds]
        ::piLog::log [clock milliseconds] "debug" "plug $plugNumber (adress module : $moduleAdress - register $register)  is updated with value $value"
    }
}