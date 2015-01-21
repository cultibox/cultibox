# Ce script permet de vérifier le statut du bouton 
# Il faut que Wiring Pi soit installé : http://wiringpi.com/download-and-install/

# Ce script est divisé en deux parties
# Les trente premières secondes permettent de réinitialiser le RPi
# Ensuite l'appui sur le bouton permet de vider la conf réseau

# Initialisation : 
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

        # L'état de la LED correspond au nb de seconde modulo 2
        gpio -g write 22 [expr [clock seconds] % 2]
        
        # On regarde l'état de du switch
        set pinValue [exec gpio -g read 27]
        
        if {$pinValue == 1} {
           
            incr ::compteur
            
            # On attend un appui de trois secondes au minimum
            # La boucle est rappelé toutes les 50ms
            # 3000 / 50 --> 60
            if {$::compteur > 60} {
               
                set ::compteur 0
           
                # RAZ du RPi:
            

                # On la rappel la procédure au bout de 10 secondes pour éviter un double effacage:
                after 10000 firstLoop
            } else {
                # On la rappel toute les 50ms si le bouton a été appuyé:
                after 50 firstLoop
            }
            
        } else {
            set ::compteur 0
            # On la rappel toute les 50ms si le bouton n'a pas été appuyé:
            after 50 firstLoop
        }

    } else {
        # On casse la première boucle
        set ::firstLoopFinish 1
    
    }

}

vwait firstLoopFinish

set compteur 0

# Procédure utilisée pour vérifier l'état de la pin et faire les MAJ si nécéssaire:
proc checkAndUpdate {} {

    # Si cultipi nous dit de rebooter, on reboot:
    if { [file exists /tmp/cultipi_restart] == 1} {               
        exec reboot
    }

    # L'état de la LED correspond au nb de seconde / 2 modulo 2
    gpio -g write 22 [expr ([clock seconds] / 2) % 2]

    # On lit la valeur de la pin
    set pinValue [exec gpio -g read 27]
   
    # Si la valeur est 1 , alors on modifie les fichier de conf
    if {$pinValue == 1} {
       
        incr ::compteur
       
        if {$::compteur > 40} {
           
            set ::compteur 0
       
            # RAZ de la configuration réseau:
            exec cp /etc/network/interfaces.BASE /etc/network/interfaces
            exec /etc/init.d/networking restart
           
            # Remise en place du mot de passe d'origine:
            if { [file exists /etc/lighttpd/.passwd.BASE] == 1} {
                exec cp /etc/lighttpd/.passwd.BASE /etc/lighttpd/.passwd
                exec /etc/init.d/lighttpd force-reload
            }
        

            # On la rappel la procédure au bout de 10 secondes pour éviter un double effacage:
            after 10000 checkAndUpdate
        } else {
            # On la rappel toute les 50ms si le bouton a été appuyé:
            after 50 checkAndUpdate
        }
    } else {
        set ::compteur 0
        # On la rappel toute les 50ms si le bouton n'a pas été appuyé
        after 50 checkAndUpdate
    }
}

checkAndUpdate

# On attend indéfiniment
vwait forever
