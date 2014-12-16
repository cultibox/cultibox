
namespace eval ::sensor {

}

# Utiliser pour initialiser la partie sensor
proc ::sensor::init {} {

    set ::port(serverAcqSensor)   ""

    # On demande le numéro de port du lecteur de capteur
    ::piLog::log [clock milliseconds] "info" "ask getPort serverAcqSensor"
    ::piServer::sendToServer $::port(serverCultiPi) "$::port(serverPlugUpdate) [incr ::TrameIndex] getPort serverAcqSensor"

    for {set i 1} {$i < 7} {incr i} {
        set ::sensor(${i},value,1) ""
        set ::sensor(${i},value,2) ""
    }
    
    set ::subscriptionRunned 0
}


proc ::sensor::loop {} {

    # On vérifie si le numéro de port est disponible
    if {$::port(serverAcqSensor) != ""} {
    
        # Le numéro du port est disponible
        # On lui demande les repères nécessaires (les 6 premiers) par abonnement
        for {set i 1} {$i < 7} {incr i} {
            ::piServer::sendToServer $::port(serverAcqSensor) "$::port(serverPlugUpdate) [incr ::TrameIndex] subscription ${i},value,1 2000"
            ::piServer::sendToServer $::port(serverAcqSensor) "$::port(serverPlugUpdate) [incr ::TrameIndex] subscription ${i},value,2 2000"
            
            # Les lignes sivantes marchent aussi !
            #::piServer::sendToServer $::port(serverAcqSensor) "$::port(serverPlugUpdate) [incr ::TrameIndex] getRepere ${i},value,1"
            #::piServer::sendToServer $::port(serverAcqSensor) "$::port(serverPlugUpdate) [incr ::TrameIndex] getRepere ${i},value,2"
        }

        set ::subscriptionRunned 1
    
    } else {
        ::piLog::log [clock milliseconds] "debug" "port of serverAcqSensor is not defined"
    }

    # On tue la boucle si les souscriptions sont lancés
    if {$::subscriptionRunned == 0} {
        after 1500 ::sensor::loop
    }
}