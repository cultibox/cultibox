# Ce script permet de v�rifier le statut du bouton 
# Il faut que Wiring Pi soit install� : http://wiringpi.com/download-and-install/

# Ce script est divis� en deux parties
# Les trente premi�res secondes permettent de r�initialiser le RPi
# Ensuite l'appui sur le bouton permet de vider la conf r�seau

# Initialisation : 
puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Initialisation des pins du GPIO"
# Le bouton est branch� sur la pin 13 sur GPIO. Wirring Pi l'appel 2, le BCM 27
# La pin est en entr�e (la commande suivante marche aussi : gpio mode 2 in)
exec gpio -g mode 27 in

# La LED est branch�e sur la pin 15 du GPIO. Wirring pi l'appel 3, le BCM 22
# La pin est en sortie
exec gpio -g mode 22 out

set compteur 0

# Les trente premi�res seconde
# On fait clignoter la LED toute les secondes pour indiquer cette �tat
set startTime [clock seconds]
set ::endTime   [expr $startTime + 30]
set firstLoopFinish 0

proc firstLoop {} {

    if {[clock seconds] < $::endTime} {


        # On regarde l'�tat de du switch
        set pinValue [exec gpio -g read 27]
        
        if {$pinValue == 1} {
           
            # On force la LED � �tre allum�e
            exec gpio -g write 22 0
           
            incr ::compteur
            
            # On attend un appui de trois secondes au minimum
            # La boucle est rappel� toutes les 50ms
            # 3000 / 50 --> 60
            if {$::compteur > 60} {
            
                puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : RAZ du RPi demand�"
               
                # On fait clignoter la LED 10 fois
                for {set i 0} {$i < 10} {incr i} {
                    exec gpio -g write 22 0
                    after 100
                    exec gpio -g write 22 1
                    after 100
                    update
                }
                
               
                set ::compteur 0
           
                # RAZ de la configuration r�seau:
                exec cp /etc/network/interfaces.BASE /etc/network/interfaces
                exec update-rc.d isc-dhcp-server defaults
                exec update-rc.d dnsmasq defaults
                exec /etc/init.d/networking restart
                exec /etc/init.d/dnsmasq force-reload
                exec /etc/init.d/isc-dhcp-server force-reload

                # Remise en place du mot de passe d'origine:
                if { [file exists /etc/lighttpd/.passwd.BASE] == 1} {
                    exec cp /etc/lighttpd/.passwd.BASE /etc/lighttpd/.passwd
                    exec /etc/init.d/lighttpd force-reload
                }


                exec dpkg --purge cultibox 
                exec dpkg --purge cultiraz 
                exec dpkg --purge cultipi 
                exec dpkg --purge cultitime
                exec dpkg --purge culticonf

                exec dpkg -i /home/cultipi/cultipi*.deb
                exec dpkg -i /home/cultipi/cultibox*.deb
                exec dpkg -i /home/cultipi/cultitime*.deb
                exec dpkg -i /home/cultipi/cultiraz*.deb
                exec dpkg -i /home/cultipi/culticonf*.deb


                # On rappel la proc�dure au bout de 10 secondes pour �viter un double effacage:
                after 10000 firstLoop
            } else {
                # On rappel toute les 50ms si le bouton a �t� appuy�:
                after 50 firstLoop
            }
            
        } else {
            set ::compteur 0
            
            # L'�tat de la LED correspond au nb de seconde modulo 2
            exec gpio -g write 22 [expr [clock seconds] % 2]
            
            # On la rappel toute les 50ms si le bouton n'a pas �t� appuy�:
            after 50 firstLoop
        }

    } else {
        # On casse la premi�re boucle
        set ::firstLoopFinish 1
    
    }

}

puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : V�rification de l'effacement du du Cultipi"
firstLoop

vwait firstLoopFinish

set compteur 0

puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : Boucle de v�rification de l'effacement de la conf r�seau"
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
       
        # On force la LED � �tre allum�e
        exec gpio -g write 22 0
       
        if {$::compteur > 40} {
           
            puts  "[clock format [clock seconds] -format "%b %d %H:%M:%S"] : cultiRAZ : RAZ de la conf r�seau demand�"
           
            # On fait clignoter la LED 10 fois
            for {set i 0} {$i < 10} {incr i} {
                exec gpio -g write 22 0
                after 100
                exec gpio -g write 22 1
                after 100
                update
            }
           
            set ::compteur 0
       
            # RAZ de la configuration r�seau:
            exec cp /etc/network/interfaces.BASE /etc/network/interfaces
            exec update-rc.d isc-dhcp-server defaults
            exec update-rc.d dnsmasq defaults
            exec /etc/init.d/networking restart
            exec /etc/init.d/dnsmasq force-reload
            exec /etc/init.d/isc-dhcp-server force-reload

            # Remise en place du mot de passe d'origine:
            if { [file exists /etc/lighttpd/.passwd.BASE] == 1} {
                exec cp /etc/lighttpd/.passwd.BASE /etc/lighttpd/.passwd
                exec /etc/init.d/lighttpd force-reload
            }
        

            # On la rappel la proc�dure au bout de 10 secondes pour �viter un double effacage:
            after 10000 checkAndUpdate
        } else {
            # On la rappel toute les 50ms si le bouton a �t� appuy�:
            after 50 checkAndUpdate
        }
    } else {
        set ::compteur 0
        
        # L'�tat de la LED correspond au nb de seconde / 2 modulo 2
        exec gpio -g write 22 [expr ([clock seconds] / 2) % 2]
        
        # On la rappel toute les 50ms si le bouton n'a pas �t� appuy�
        after 50 checkAndUpdate
    }
}

checkAndUpdate

# On attend ind�finiment
vwait forever
