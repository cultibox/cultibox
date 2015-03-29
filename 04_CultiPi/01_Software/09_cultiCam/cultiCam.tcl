# Ce script permet de piloter les webcam

# Source files
set rootDir [file dirname [info script]]

# On ajoute le path des outils
if {$::tcl_platform(os) == "Windows NT"} {
    lappend auto_path [file join $rootDir .. 01_cultiPi lib tcl]
    puts [file join $rootDir .. 01_cultiPi lib tcl]
} else {
    lappend auto_path [file join $rootDir .. cultipi lib tcl]
}

package require piXML

set confXML  [lindex $argv 0]

# Démarrage
puts  "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Démarrage"
set dailyPhotoIsTake 0

# On charge le XML
# On initialise la conf XML
array set configXML {
    verbose                 debug
    timeBeforeTwoSnapshots  5
    dailySnapshotHour       12
    dailySnapshotMin        0
    nbWebcam                2
}
set RC [catch {
    array set configXML [::piXML::convertXMLToArray $confXML]
} msg]
if {$RC != 0} {
    puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during loading XML , error : $msg"
}

# On prend une photo toutes les X secondes
proc takePhoto {webcamIndex} {

    puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Take Snapshot webcam ${webcamIndex}" ; update

    # On prend un image
    set RC [catch {
        exec sudo fswebcam --no-banner /var/www/cultibox/tmp/webcam_${webcamIndex}_temp.jpg 
    } msg]
    if {$RC != 0} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during taking snapshot webcam ${webcamIndex} , error : $msg"
    }
    
    # On la sauvegarde
    set RC [catch {
        file copy -force /var/www/cultibox/tmp/webcam_${webcamIndex}_temp.jpg  /var/www/cultibox/tmp/webcam_${webcamIndex}.jpg 
    } msg]
    if {$RC != 0} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during copying snapshot webcam ${webcamIndex} , error : $msg"
    }
    
    
    # On la sauvegarde dans les photos journalière une fois par jour
    set Hour [string trimleft [clock format [clock seconds] -format "%H"] 0]
    if {$Hour == ""} {set Hour 0}
    set Minute [string trimleft [clock format [clock seconds] -format "%M"] 0]
    if {$Minute == ""} {set Minute 0}
    set date [string trimleft [clock format [clock seconds] -format "%y%m%d"] 0]
    if {$Hour > $::configXML(dailySnapshotHour) && 
        $Minute > $::configXML(dailySnapshotMin) && 
        $::dailyPhotoIsTake != $date} {
        
        set RC [catch {
            file copy -force /var/www/cultibox/tmp/webcam_${webcamIndex}_temp.jpg  /var/www/cultibox/tmp/webcam_${webcamIndex}_${date}.jpg 
        } msg]
        if {$RC != 0} {
            puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during saving daily snapshot webcam ${webcamIndex} , error : $msg"
        }
        
        
        set ::dailyPhotoIsTake ${date}
    }

    after [expr 1000 * $::configXML(timeBeforeTwoSnapshots)] takePhoto $webcamIndex
}

# Pour chaque webcam, on lance la boucle d'acquisition
for {set i 0} {$i < $::configXML(nbWebcam)} {incr i} {
    after [expr 1000 * $i] takePhoto $i
}


# On attend indéfiniment
vwait forever

# tclsh "C:\cultibox\04_CultiPi\01_Software\09_cultiCam\cultiCam.tcl" "C:\cultibox\04_CultiPi\01_Software\09_cultiCam\conf.xml"
# tclsh /opt/culticam/cultiCam.tcl /etc/culticam/conf.xml