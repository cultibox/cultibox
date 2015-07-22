# Ce script permet de vérifier le statut du bouton Reset 
# Il faut que Wiring Pi soit installé : http://wiringpi.com/download-and-install/

# Ce script est divisé en deux parties
# Les 10 premières secondes permettent de réinitialiser le RPi et de revenir en mode usine
# Passé ce delais l'appuie sur le bouton permet de faire un Reset de la configuration uniquement (pas des paquets)

# Initialisation : 
puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Initialisation des pins du GPIO"
# Le bouton est branché sur la pin 13 sur GPIO. Wirring Pi l'appel 2, le BCM 27
# La pin est en entrée (la commande suivante marche aussi : gpio mode 2 in)
exec gpio -g mode 27 in

# La LED est branchée sur la pin 15 du GPIO. Wirring pi l'appel 3, le BCM 22
# La pin est en sortie
exec gpio -g mode 22 out
set compteur 0

# Les 10 premières secondes on fait clignoter la LED toutes les secondes pour 
# indiquer cet état
set ::startTime [clock seconds]
set ::endTime   [expr $startTime + 10]
set firstLoopFinish 0
set endReset 0




#======== PROCEDURES ========= #

# Procédures firstLoop permet de vérifier si le retour en usise doit être appliqué
# et l'applique le cas échéant
proc firstLoop {} {
    if { $::startTime < $::endTime} {
        set ::startTime [clock seconds]

		#Si on est dans les 10 premières secondes
        # On regarde l'état du switch
        set pinValue [exec gpio -g read 27]
        
        if {$pinValue == 1} {
            # On force la LED à être allumée
            exec gpio -g write 22 0
           
            incr ::compteur
            
            # On attend un appui de trois secondes au minimum
            # La boucle est rappelé toutes les 50ms
            # 3000 / 50 --> 60
            if {$::compteur > 60} {        
                puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : RAZ usine du Cultipi demandée"
               
                # On fait clignoter la LED 10 fois
                for {set i 0} {$i < 10} {incr i} {
                    exec gpio -g write 22 0
                    after 200
                    exec gpio -g write 22 1
                    after 200
                    update
                }
                
               
                set ::compteur 0
                resetConf
                vwait endReset  
                set endReset 0
                        
                # On fait clignoter la LED 10 fois
                for {set i 0} {$i < 10} {incr i} {
                    exec gpio -g write 22 0
                    after 200
                    exec gpio -g write 22 1
                    after 200
                    update
                }

                # On rappel la procédure au bout de 10 secondes pour éviter un double effacage:
                after 10000 firstLoop
            } else {
                # On rappel toute les 50ms si le bouton a été appuyé:
                after 50 firstLoop
            }           
        } else {
            set ::compteur 0
            
            # L'état de la LED correspond au nb de seconde modulo 2
            exec gpio -g write 22 [expr [clock seconds] % 2]
            
            # On la rappel toute les 50ms si le bouton n'a pas été appuyé:
            after 50 firstLoop
        }
    } else {
        # On casse la première boucle
        set ::firstLoopFinish 1
    
    }
}



proc checkAndUpdate {} {
    # On lit la valeur de la pin
    set pinValue [exec gpio -g read 27]
   
    # Si la valeur est 1 , alors on modifie les fichier de conf
    if {$pinValue == 1} {
       
        incr ::compteur
       
        # On force la LED à être allumée
        exec gpio -g write 22 0
       
        if {$::compteur > 40} {
            puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : RAZ de la configuration du Cultipi demandée"
           
            # On fait clignoter la LED 5 fois
            for {set i 0} {$i < 5} {incr i} {
                exec gpio -g write 22 0
                after 200
                exec gpio -g write 22 1
                after 200
                update
            }
         
            set ::compteur 0  
			resetConf
            
            puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : fin du RAZ de la configuration du Cultipi demandée"

            # On fait clignoter la LED 5 fois
            
            for {set i 0} {$i < 5} {incr i} {
                exec gpio -g write 22 0
                after 200
                exec gpio -g write 22 1
                after 200
                update
            }

            # On rappel la procédure au bout de 10 secondes pour éviter un double effacage:
            after 10000 checkAndUpdate
        } else {
            # On rappel la procédure toute les 50ms si le bouton a été appuyé:
            after 50 checkAndUpdate
        }
    } else {
        set ::compteur 0
        
        # L'état de la LED correspond au nb de seconde / 2 modulo 2
        exec gpio -g write 22 [expr ([clock seconds] / 2) % 2]
        
        # On la rappel toute les 50ms si le bouton n'a pas été appuyé
        after 50 checkAndUpdate
    }
}


proc resetConf {} {
    #RAZ de la configuration réseau:
    set RC [catch {exec /bin/cp /etc/network/interfaces.BASE /etc/network/interfaces} msg]
    if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Erreur lors du RAZ de la configuration réseau : $msg"}

    # Remise en place du mot de passe d'origine pour lighttpd:
    set RC [catch {
        if { [file exists /etc/lighttpd/lighttpd.conf.base] == 1} {
            exec mv /etc/lighttpd/lighttpd.conf /etc/lighttpd/lighttpd.conf.https
            exec mv /etc/lighttpd/lighttpd.conf.base /etc/lighttpd/lighttpd.conf
        }
    } msg]

    if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Erreur lors du RAZ de la configuration de lighttpd: $msg"}

    set RC [catch {
        if { [file exists /etc/lighttpd/.passwd.BASE] == 1} {
            exec /bin/cp /etc/lighttpd/.passwd.BASE /etc/lighttpd/.passwd
        }
    } msg]

    if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Erreur lors du RAZ du mot de passe lighttpd : $msg"}

    #On redémare les services:
    set RC [catch {
        exec /sbin/modprobe -r rt2800usb
        after 2000 exec /sbin/modprobe rt2800usb
    } msg]


    if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Erreur lors du RAZ des services réseaux : $msg"}

    set RC [catch {
        after 5000 exec /usr/sbin/invoke-rc.d networking force-reload; echo ""
    } msg]

    set RC [catch {
        after 5000 exec /usr/sbin/invoke-rc.d lighttpd force-reload
    } msg]

    set endReset 1
}
    

proc resetPackages {} {

    #set RC [catch {
   #    exec dpkg-reconfigure cultibox
   #    exec dpkg-reconfigure cultipi
   #    exec dpkg-reconfigure cultitime
   #    exec dpkg-reconfigure culticonf
   #} msg]

   #if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Error dpkg-reconfigure : $msg"}
}
#================================#


puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Vérification de la remise en état usine du Cultipi"
firstLoop
vwait firstLoopFinish
set compteur 0


puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Vérification de l'effacement de la configuration du Cultipi"

# Procédure utilisée pour vérifier l'état de la pin et faire les MAJ si nécéssaire:
checkAndUpdate

# On attend indéfiniment
vwait forever





