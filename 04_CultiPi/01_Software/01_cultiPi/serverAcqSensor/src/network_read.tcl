# Pour que ça marche, ajouter dans la conf :
#  <item name="network_read,1,ip" ip="192.178.0.10" />
#  <item name="network_read,1,sensor" sensor="2" />

namespace eval ::network_read {
    variable periodeAcq  [expr 1000 * 5]
    variable bandeMorteAcq 0.01
    variable hostPlug
}

# Cette proc est utilisée pour initialiser les variables
proc ::network_read::init {nb_maxSensor} {
    variable periodeAcq
    variable bandeMorteAcq
    variable hostPlug
    
    for {set i 1} {$i <= $nb_maxSensor} {incr i} {
        if {[array get ::configXML network_read,$i,ip] != "" && [array get ::configXML network_read,$i,sensor] != ""} {
        
            # Les valeurs ont été demandés par l'utilisateur
            
            set ip $::configXML(network_read,$i,ip)
            set sensorNb $::configXML(network_read,$i,sensor)
            
            # On prend un abonnement
            ::piServer::sendToServer $::port(serverAcqSensor) "$::port(serverAcqSensor) [incr ::TrameIndex] subscription ${sensorNb},value $periodeAcq $bandeMorteAcq" $ip
            ::piServer::sendToServer $::port(serverAcqSensor) "$::port(serverAcqSensor) [incr ::TrameIndex] subscription ${sensorNb},type $periodeAcq" $ip
            
            # On enregistre le lien 
            set hostPlug($ip,${sensorNb}) $i

        }
    }
}

proc ::network_read::getSensor {networkHost networkSensor} {
    variable hostPlug
    if {[array get hostPlug $networkHost,${networkSensor}] != ""} {
        return $hostPlug($networkHost,${networkSensor})
    } else {
        ::piLog::log [clock milliseconds] "error" "::network_read::getSensor default No plug associated to $networkHost,${networkSensor}"
        return "NA"
    }

}
