# Ce script permet de v�rifier le statut du bouton 
# Il faut que Wiring Pi soit install� : http://wiringpi.com/download-and-install/

# Initialisation : 
# Le bouton est branch� sur la pin 13 sur GPIO. Wirring Pi l'appel 2

# La pin est en entr�e (la commande suivante marche aussi : gpio mode 2 in)
exec gpio -g mode 27 in

set compteur 0

# Proc�dure utilis�e pour v�rifier l'�tat de la pin et faire les MAJ si n�c�ssaire:
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
       
            # RAZ de la configuration r�seau:
            exec cp /etc/network/interfaces.BASE /etc/network/interfaces
            exec /etc/init.d/networking restart
           
            # Changement des droits utilisateurs
            # exec sudo ....

            # On la rappel la proc�dure au bout de 10 secondes pour �viter un double effacage:
            after 10000 checkAndUpdate
        } else {
            # On la rappel toute les 50ms si le bouton n'a pas �t� appuy�:
            after 50 checkAndUpdate
        }
    } else {
        set ::compteur 0
        # On la rappel toute les 50ms si le bouton n'a pas �t� appuy�
        after 50 checkAndUpdate
    }
}

checkAndUpdate

# On attend ind�finiment
vwait forever
