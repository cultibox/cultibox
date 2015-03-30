
proc stopCultiPi {} {
    ::piLog::log [clock milliseconds] "info" "Debut arret Culti Pi"
    
    ::piLog::log [clock milliseconds] "info" "Extinction des alimentations"
    set RC [catch {
        exec gpio -g write 18 0
    } msg]
    if {$RC != 0} {
        ::piLog::log [clock milliseconds] "error" "restartSlave : stopCultiPi : error $msg"
    }


    # Arret de tous les modules
    foreach moduleXML $::confStart(start) {
        set moduleName [::piXML::searchOptionInElement name $moduleXML]
        
        # SI le numéro du port est vide ,c'est qu'on a pas encore lancé le module
        if {$moduleName != "serverLog" && $::confStart($moduleName,port) != ""} {
            ::piLog::log [clock milliseconds] "info" "Demande arret $moduleName"
            # Arret du module
            ::piServer::sendToServer $::confStart($moduleName,port) "[clock milliseconds] 000 stop"
            
            #on attend 200 ms
            after 200
            
            # try to kill
            catch {
                ::piLog::log [clock milliseconds] "info" "Try to kill $moduleName pid $::confStart($moduleName,pid)"
                # Kill for windows
                if {$::tcl_platform(platform) == "windows"} {
                    exec exec [auto_execok taskkill] /PID $::confStart($moduleName,pid)
                }
                # Kill for linux
                if {$::tcl_platform(platform) == "unix"} {
                    exec kill $::confStart($moduleName,pid)
                }
            }
        }
    }

    ::piLog::log [clock milliseconds] "info" "Fin arret Culti Pi"
    
    # Arrêt du serveur de log (forcement en dernier)
    ::piServer::sendToServer $::confStart(serverLog,port) "<[clock milliseconds]><cultipi><debug><stop>"
    ::piLog::closeLog
    
    after 500 {
        puts "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiPi : Bye Bye ! "
        set ::forever 1
        exit
    }
}

# La variable outPutStyle permet de définir comment les logs doivent être sortis
# outPutStyle : puts ou log
proc restartSlave {outPutStyle} {

    # On eteint les esclaves
    set RC [catch {
        exec gpio -g mode 18 out
        exec gpio -g write 18 0
    } msg]
    if {$RC != 0} {
        if {$outPutStyle == "puts"} {
            puts "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : CultiPi : GPIO : error $msg"
        } else {
            ::piLog::log [clock milliseconds] "error" "restartSlave : GPIO 1 : error $msg"
        }
    }

    after 200
    
    # On alimente les esclaves
    set RC [catch {
        exec gpio -g write 18 1

        # On pilote le fil vers les esclaves
        exec gpio -g mode 17 out
        exec gpio -g write 17 1
    } msg]
    if {$RC != 0} {
        if {$outPutStyle == "puts"} {
            puts "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : CultiPi : GPIO : error $msg"
        } else {
            ::piLog::log [clock milliseconds] "error" "restartSlave : GPIO 2 : error $msg"
        }
        
    }

    # On attend 5 seconds
    set ::statusInitialisation "wait_20s"
    set nbSeconds 5
    if {$outPutStyle == "puts"} {
        puts "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : CultiPi : Waiting $nbSeconds seconds"
    } else {
         ::piLog::log [clock milliseconds] "info" "restartSlave : Waiting $nbSeconds seconds"
    }
    
    for {set i 0} {$i < [expr $nbSeconds * 10]} {incr i} {
        if {[expr $i % 10] == 0} {
            
            if {$outPutStyle == "puts"} {
                puts "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : CultiPi : [expr $nbSeconds - $i / 10] seconds remaining"
            } else {
                ::piLog::log [clock milliseconds] "info" "restartSlave : [expr $nbSeconds - $i / 10] seconds remaining"
            }

        }
        after 100
        update
    }

    if {$outPutStyle == "puts"} {
        puts "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : CultiPi : End of restart slave"
    } else {
        ::piLog::log [clock milliseconds] "info" "restartSlave : End of restart slave"
    }
}