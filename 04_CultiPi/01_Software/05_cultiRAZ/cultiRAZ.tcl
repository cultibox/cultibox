# Ce script permet de vérifier le statut du bouton 
# Il faut que Wiring Pi soit installé : http://wiringpi.com/download-and-install/

# Initialisation : 
# Le bouton est branché sur la pin 13 sur GPIO. Wirring Pi l'appel 2

# La pin est en entrée (la commande suivante marche aussi : gpio mode 2 in)
exec gpio -g mode 27 in

set compteur 0

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
       
        if {$::compteur > 40} {
           
            set ::compteur 0
       
            # RAZ de la configuration réseau:
            exec cp /etc/network/interfaces.BASE /etc/network/interfaces
            exec /etc/init.d/networking restart
           
            # Changement des droits utilisateurs
            # exec sudo ....

            # On la rappel la procédure au bout de 10 secondes pour éviter un double effacage:
            after 10000 checkAndUpdate
        } else {
            # On la rappel toute les 50ms si le bouton n'a pas été appuyé:
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
