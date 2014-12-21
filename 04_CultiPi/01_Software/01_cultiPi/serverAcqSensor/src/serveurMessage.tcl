
proc messageGestion {message} {

    # Trame standard : [FROM] [INDEX] [commande] [argument]
    set serverForResponse   [::piTools::lindexRobust $message 0]
    set indexForResponse    [::piTools::lindexRobust $message 1]
    set commande            [::piTools::lindexRobust $message 2]

    switch ${commande} {
        "stop" {
            ::piLog::log [clock milliseconds] "info" "Asked stop"
            stopIt
        }
        "pid" {
            ::piLog::log [clock milliseconds] "info" "Asked pid"
            ::piServer::sendToServer $serverForResponse "$::port(serverAcqSensor) $indexForResponse pid serverAcqSensor [pid]"
        }
        "getRepere" {
            # La variable est le nom de la variable à lire
            set variable  [::piTools::lindexRobust $message 3]

            ::piLog::log [clock milliseconds] "info" "Asked getRepere $variable"
            # Les parametres d'un repere : nom Valeur 
            
            if {[info exists ::$variable] == 1} {
            
                eval set returnValue $$variable
            
                ::piLog::log [clock milliseconds] "info" "response : $serverForResponse $indexForResponse getRepere $returnValue"
                ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse getRepere $returnValue"
            } else {
                ::piLog::log [clock milliseconds] "error" "Asked variable $variable - variable doesnot exists"
            }
        }
        "subscription" {
            # Le repère est l'index des capteurs
            set repere [::piTools::lindexRobust $message 3]
            set frequency [::piTools::lindexRobust $message 4]
            if {$frequency == 0} {set frequency 1000}
            set BandeMorteAcquisition [::piTools::lindexRobust $message 5]
            if {$BandeMorteAcquisition == ""} {set BandeMorteAcquisition 0}
            
            ::piLog::log [clock milliseconds] "info" "Subscription of $repere by $serverForResponse frequency $frequency"

            set ::subscriptionVariable($::SubscriptionIndex) ""
            
            # On cré la proc associée
            proc subscription${::SubscriptionIndex} {repere frequency SubscriptionIndex serverForResponse BandeMorteAcquisition} {

                set reponse $::sensor($repere)
                if {$reponse == ""} {
                    set reponse "DEFCOM"
                }
                
                set time [clock milliseconds]
                if {[array name ::sensor -exact $repere,time] != ""} {
                    set time    $::sensor($repere,time)
                }
            
                # On envoi la nouvelle valeur uniquement si la valeur a changée
                if {$::subscriptionVariable($SubscriptionIndex) != $reponse} {
                
                    # Dans le cas d'un double, on vérifie la bande morte
                    if {[string is double $reponse] == 1} {
                        # Reponse doit être > à l'ancienne valeur + BMA ou < à l'ancienne valeur - BMA
                        if {$reponse > [expr $::subscriptionVariable($SubscriptionIndex) + $BandeMorteAcquisition] || $reponse < [expr $::subscriptionVariable($SubscriptionIndex) - $BandeMorteAcquisition]} {
                            
                            ::piServer::sendToServer $serverForResponse "$serverForResponse [incr ::TrameIndex] _subscription ::sensor($repere) $reponse $time"
                            set ::subscriptionVariable($SubscriptionIndex) $reponse
                        }
                    } else {
                        ::piServer::sendToServer $serverForResponse "$serverForResponse [incr ::TrameIndex] _subscription ::sensor($repere) $reponse $time"
                        set ::subscriptionVariable($SubscriptionIndex) $reponse
                    }
                }
                
                after $frequency "subscription${SubscriptionIndex} $repere $frequency $SubscriptionIndex $serverForResponse $BandeMorteAcquisition"
            }
            
            # on la lance
            subscription${::SubscriptionIndex} $repere $frequency $::SubscriptionIndex $serverForResponse $BandeMorteAcquisition
            
            incr ::SubscriptionIndex
        }
        "_subscription" -
        "_subscriptionEvenement" {
            # On parse le retour de la commande
            set variable  [::piTools::lindexRobust $message 3]
            set valeur [::piTools::lindexRobust $message 4]
            
            # On enregistre le retour de l'abonnement
            set ::${variable} $valeur
            
            # ::piLog::log [clock milliseconds] "debug" "subscription response : variable $variable valeur -$valeur-"
        }
        default {
            # Si on reçoit le retour d'une commande, le nom du serveur est le notre
            if {$serverForResponse == $::port(serverAcqSensor)} {
            
                if {[array names ::TrameSended -exact $indexForResponse] != ""} {
                    
                    switch [lindex $::TrameSended($indexForResponse) 0] {
                        default {
                            ::piLog::log [clock milliseconds] "error" "Not recognize keyword response -${message}-"
                        }                    
                    }
                    
                } else {
                    ::piLog::log [clock milliseconds] "error" "Not requested response -${message}-"
                }
            
                
            } else {
                ::piLog::log [clock milliseconds] "error" "Received -${message}- but not interpreted"
            }
        }
    }
}
