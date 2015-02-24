# Ce script permet de vérifier le statut du bouton 
# Il faut que Wiring Pi soit installé : http://wiringpi.com/download-and-install/

# Ce script est divisé en deux parties
# Les trente premières secondes permettent de réinitialiser le RPi
# Ensuite l'appui sur le bouton permet de vider la conf réseau

# Initialisation : 
puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Initialisation des pins du GPIO"
# Le bouton est branché sur la pin 13 sur GPIO. Wirring Pi l'appel 2, le BCM 27
# La pin est en entrée (la commande suivante marche aussi : gpio mode 2 in)
exec gpio -g mode 27 in

# La LED est branchée sur la pin 15 du GPIO. Wirring pi l'appel 3, le BCM 22
# La pin est en sortie
exec gpio -g mode 22 out

set compteur 0

# Les trente premières seconde
# On fait clignoter la LED toute les secondes pour indiquer cette état
set startTime [clock seconds]
set ::endTime   [expr $startTime + 30]
set firstLoopFinish 0

proc firstLoop {} {

    if {[clock seconds] < $::endTime} {


        # On regarde l'état de du switch
        set pinValue [exec gpio -g read 27]
        
        if {$pinValue == 1} {
           
            # On force la LED à être allumée
            exec gpio -g write 22 0
           
            incr ::compteur
            
            # On attend un appui de trois secondes au minimum
            # La boucle est rappelé toutes les 50ms
            # 3000 / 50 --> 60
            if {$::compteur > 60} {
            
                puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : RAZ du RPi demandé"
               
                # On fait clignoter la LED 10 fois
                for {set i 0} {$i < 10} {incr i} {
                    exec gpio -g write 22 0
                    after 100
                    exec gpio -g write 22 1
                    after 100
                    update
                }
                
               
                set ::compteur 0
           
                # RAZ de la configuration réseau:
                set RC [catch {exec cp /etc/network/interfaces.BASE /etc/network/interfaces} msg]
                if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Error cp : $msg"}

                # Remise en place du mot de passe d'origine:
                set RC [catch {
                    if { [file exists /etc/lighttpd/.passwd.BASE] == 1} {
                        exec cp /etc/lighttpd/.passwd.BASE /etc/lighttpd/.passwd
                    }
                } msg]
                if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Error Lighttpd password reset : $msg"}
                
                set RC [catch {
                    exec dpkg-reconfigure cultibox 
                    exec dpkg-reconfigure cultipi 
                    exec dpkg-reconfigure cultitime
                    exec dpkg-reconfigure culticonf
                } msg]
                if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Error dpkg-reconfigure : $msg"}


                #On redémare les services:
                set RC [catch {
                    exec /sbin/modprobe -r rt2800usb
                    after 2000 exec /sbin/modprobe rt2800usb
                    after 7000 exec /usr/sbin/invoke-rc.d networking force-reload
                    after 2000 exec /etc/rc.local
                } msg]
                if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Error during network configuration : $msg"}

                # On fait clignoter la LED 5 fois
                for {set i 0} {$i < 5} {incr i} {
                    exec gpio -g write 22 0
                    after 100
                    exec gpio -g write 22 1
                    after 100
                    update
                }


                # On fait clignoter la LED 5 fois
                for {set i 0} {$i < 5} {incr i} {
                    exec gpio -g write 22 0
                    after 100
                    exec gpio -g write 22 1
                    after 100
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

puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Vérification de l'effacement du du Cultipi"
# firstLoop

# vwait firstLoopFinish

set compteur 0

puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Boucle de vérification de l'effacement de la conf réseau"
# Procédure utilisée pour vérifier l'état de la pin et faire les MAJ si nécéssaire:
proc checkAndUpdate {} {

    # Si cultipi nous dit de rebooter, on reboot:
    if { [file exists /tmp/cultipi_restart] == 1} {               
        exec reboot
    }


    # On lit la valeur de la pin
    set pinValue [exec gpio -g read 27]
   
    # Si la valeur est 1 , alors on modifie les fichier de conf
    if {$pinValue == 1} {
       
        incr ::compteur
       
        # On force la LED à être allumée
        exec gpio -g write 22 0
       
        if {$::compteur > 40} {
           
            puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Network configuration reset asked..."
           
            # On fait clignoter la LED 10 fois
            for {set i 0} {$i < 10} {incr i} {
                exec gpio -g write 22 0
                after 100
                exec gpio -g write 22 1
                after 100
                update
            }
           
            set ::compteur 0
       
            # RAZ de la configuration réseau:
            set RC [catch {exec /bin/cp /etc/network/interfaces.BASE /etc/network/interfaces} msg]
            if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Error cp : $msg"}

            # Remise en place du mot de passe d'origine:
            set RC [catch {
                if { [file exists /etc/lighttpd/.passwd.BASE] == 1} {
                    exec /bin/cp /etc/lighttpd/.passwd.BASE /etc/lighttpd/.passwd
                }
            } msg]
            if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Error Lighttpd password reset : $msg"}


            #On redémare les services:
            set RC [catch {
                exec /sbin/modprobe -r rt2800usb
                after 2000 exec /sbin/modprobe rt2800usb
                after 7000 exec /usr/sbin/invoke-rc.d networking force-reload
                after 2000 exec /etc/rc.local
            } msg]
            if {$RC != 0} {puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Error during network configuration : $msg"}

            # On fait clignoter la LED 5 fois
            for {set i 0} {$i < 5} {incr i} {
                exec gpio -g write 22 0
                after 100
                exec gpio -g write 22 1
                after 100
                update
            }

            # On la rappel la procédure au bout de 10 secondes pour éviter un double effacage:
            after 10000 checkAndUpdate
        } else {
            # On la rappel toute les 50ms si le bouton a été appuyé:
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

checkAndUpdate

# On attend indéfiniment
vwait forever
