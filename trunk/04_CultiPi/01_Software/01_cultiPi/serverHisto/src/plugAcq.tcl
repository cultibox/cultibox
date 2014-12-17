
namespace eval ::plugAcq {
}

# Utiliser pour initialiser la partie sensor
proc ::plugAcq::init {} {

    set ::port(serverPlugUpdate)   ""

    # On demande le num�ro de port du lecteur de capteur
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
    
    # On v�rifie si le num�ro de port est disponible
    if {$::port(serverPlugUpdate) != ""} {
    
        # Le num�ro du port est disponible
        # On lui demande les rep�res n�cessaires (les 16 premiers) par abonnement
        for {set i 1} {$i < 17} {incr i} {
            ::piServer::sendToServer $::port(serverPlugUpdate) "$::port(serverHisto) [incr ::TrameIndex] subscriptionEvenement plug ${i},value"
        }

        set ::subscriptionRunned(plugAcq) 1
    
    } else {
        ::piLog::log [clock milliseconds] "debug" "port of serverAcqSensor is not defined"
    }

    # On tue la boucle si les souscriptions sont lanc�s
    if {$::subscriptionRunned(plugAcq) == 0} {
        after 1500 ::plugAcq::loop
    }
}