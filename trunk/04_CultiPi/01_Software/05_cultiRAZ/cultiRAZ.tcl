# Ce script permet de vérifier le statut du bouton 

# Il faut que Wiring Pi soit installé : http://wiringpi.com/download-and-install/

# Initialisation : 
# Le bouton est branché sur la pin 13 sur GPIO. Wirring Pi l'appel 2

# La pin est en entrée (la commande suivante marche aussi : gpio mode 2 in)
exec gpio -g mode 27 in

# La procédure utilisée pour vérifier l'état de la pin et faire les MAJ si nécéssaire
proc checkAndUpdate {} {

    # On lit la valeur de la pin
    set pinValue [exec gpio -g read 27]
    
    # Si la valeur est 1 , alors on modifie les fichier de conf
    if {$pinValue == 1} {
        
        # Changement de la conf réseau
        # file copy -force /neNomDeLaConfParDefaut /LeCHeminDestination
        
        # Changement des droits utilisateurs
        # exec sudo ....
        

        # On la rappel la procédure au bout de 30 secondes pour éviter un double effacage
        after 30000 checkAndUpdate
        
    } else {

        # On la rappel toute les 50ms si le bouton n'a pas été appuyé
        after 50 checkAndUpdate
    }

}

checkAndUpdate

# On attend indéfiniment
vwait forever