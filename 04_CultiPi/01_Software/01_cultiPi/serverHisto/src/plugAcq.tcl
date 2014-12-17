
namespace eval ::plugAcq {
}

# Utiliser pour initialiser la partie sensor
proc ::plugAcq::init {} {

    set ::port(serverPlugUpdate)   ""

    # On demande le numéro de port du lecteur de capteur
    ::piLog::log [clock milliseconds] "info" "ask getPort serverPlugUpdate"
    ::piServer::sendToServer $::port(serverCultiPi) "$::port(serverHisto) [incr ::TrameIndex] getPort serverPlugUpdate"
    
    for {set i 1} {$i < 17} {incr i} {
        set ::plug(${i},value) ""
    }
    
    set ::subscriptionRunned(plugAcq) 0
}


proc ::plugAcq::loop {} {

    variable periodeAcq
    variable bandeMorteAcq
    
    # On vérifie si le numéro de port est disponible
    if {$::port(serverPlugUpdate) != ""} {
    
        # Le numéro du port est disponible
        # On lui demande les repères nécessaires (les 16 premiers) par abonnement
        for {set i 1} {$i < 17} {incr i} {
            ::piServer::sendToServer $::port(serverPlugUpdate) "$::port(serverHisto) [incr ::TrameIndex] subscriptionEvenement plug ${i},value"
        }

        set ::subscriptionRunned(plugAcq) 1
    
    } else {
        ::piLog::log [clock milliseconds] "debug" "port of serverAcqSensor is not defined"
    }

    # On tue la boucle si les souscriptions sont lancés
    if {$::subscriptionRunned(plugAcq) == 0} {
        after 1500 ::plugAcq::loop
    }
}