
proc messageGestion {message} {

    # Trame standard : [FROM] [INDEX] [commande] [argument]
    set serverForResponse   [::piTools::lindexRobust $message 0]
    set indexForResponse    [::piTools::lindexRobust $message 1]
    set commande            [::piTools::lindexRobust $message 2]

    switch ${commande} {
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
            ::piServer::sendToServer $serverForResponse "$serverForResponse $indexForResponse getPort $module $::confStart($module,port)"
        }
        default {
            ::piLog::log [clock milliseconds] "erreur" "Received -${message}- but not interpreted"
        }
    }

    
}
