
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
    set ::updateOfEndOfTheDay 0
    set ::updateAtStartOfTheDay 0
    
}


proc ::plugAcq::loop {} {

    variable periodeAcq
    variable bandeMorteAcq
    
    # On v�rifie si le num�ro de port est disponible (et qu'on l'a pas demand�)
    if {$::port(serverPlugUpdate) != "" && $::subscriptionRunned(plugAcq) == 0} {
    
        # Le num�ro du port est disponible
        # On lui demande les rep�res n�cessaires (les 16 premiers) par abonnement
        for {set i 1} {$i < 17} {incr i} {
            ::piServer::sendToServer $::port(serverPlugUpdate) "$::port(serverHisto) [incr ::TrameIndex] subscriptionEvenement plug ${i},value"
        }

        set ::subscriptionRunned(plugAcq) 1
        
        # On lui demande une mise � jour des valeurs
        # ::piServer::sendToServer $::port(serverPlugUpdate) "$::port(serverHisto) [incr ::TrameIndex] updateSubscriptionEvenement"
        
    
    } elseif {$::subscriptionRunned(plugAcq) == 0} {
        ::piLog::log [clock milliseconds] "debug" "port of serverAcqSensor is not defined"
    }
    
    # En fin de journ�e, on demande une mise � jour des valeurs
    if {[::piTime::readSecondsOfTheDay] > 86397} {
        if {$::updateOfEndOfTheDay == 0 && $::port(serverPlugUpdate) != ""} {
            set ::updateOfEndOfTheDay 1
            
            # On lui demande une mise � jour des valeurs
            ::piServer::sendToServer $::port(serverPlugUpdate) "$::port(serverHisto) [incr ::TrameIndex] updateSubscriptionEvenement"
            
        }
    } else {
        set ::updateOfEndOfTheDay 0
    }
    
    # En d�but de journ�e aussi !
    if {[::piTime::readSecondsOfTheDay] < 5 && [::piTime::readSecondsOfTheDay] > 2} {
        if {$::updateAtStartOfTheDay == 0 && $::port(serverPlugUpdate) != ""} {
            set ::updateAtStartOfTheDay 1
            
            # On lui demande une mise � jour des valeurs
            ::piServer::sendToServer $::port(serverPlugUpdate) "$::port(serverHisto) [incr ::TrameIndex] updateSubscriptionEvenement"
            
        }
    } else {
        set ::updateAtStartOfTheDay 0
    }

    # On relance la boucle toutes les 500 millisecondes
    after 500 ::plugAcq::loop
}