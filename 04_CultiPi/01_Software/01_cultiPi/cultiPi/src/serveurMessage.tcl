
proc messageGestion {message networkhost} {

    global statusInitialisation

    # Trame standard : [FROM] [INDEX] [commande] [argument]
    set serverForResponse   [::piTools::lindexRobust $message 0]
    set indexForResponse    [::piTools::lindexRobust $message 1]
    set commande            [::piTools::lindexRobust $message 2]

    switch ${commande} {
        "restartSlave" {
            ::piLog::log [clock milliseconds] "info" "Demande de redémarrage"
            restartSlave "log"
        }
        "stop" {
            ::piLog::log [clock milliseconds] "info" "Demande Arret de Culti Pi"
            stopCultiPi
        }
        "pid" {
            set module [::piTools::lindexRobust $message 3]
            set pid [::piTools::lindexRobust $message 4]
            ::piLog::log [clock milliseconds] "info" "Received pid $pid of $module"
            set ::confStart($module,pid) $pid
        }
        "getPort" {
            set module [::piTools::lindexRobust $message 3]
            ::piLog::log [clock milliseconds] "info" "Asked port of $module"
            # Comme c'est une réponse, le nom du serveur est celui de celui qui a demandé
            ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse _getPort $module $::confStart($module,port)" $networkhost
        }
        "getRepere" {
        
            # Pour toutes les variables demandées
            set indexVar 3
            set returnList ""
            while {[set variable [::piTools::lindexRobust $message $indexVar]] != ""} {
                # La variable est le nom de la variable à lire
                
                ::piLog::log [clock milliseconds] "info" "Asked getRepere $variable by $networkhost"
                
                if {[info exists ::$variable] == 1} {
                
                    eval set returnValue $$variable

                    lappend returnList $returnValue
                } else {
                    ::piLog::log [clock milliseconds] "error" "Asked variable $variable by $networkhost - variable doesnot exists"
                }
                
                incr indexVar
                
                # On evite le localhost final
                if {[::piTools::lindexRobust $message [expr $indexVar + 1]] == ""} {
                    break;
                }
                
            }

            ::piLog::log [clock milliseconds] "info" "response : $serverForResponse $indexForResponse getRepere - $returnList - to $networkhost"
            ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse getRepere $returnList" $networkhost

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
            ::piLog::log [clock milliseconds] "error" "Received -${message}- but not interpreted"
        }
    }

    
}
