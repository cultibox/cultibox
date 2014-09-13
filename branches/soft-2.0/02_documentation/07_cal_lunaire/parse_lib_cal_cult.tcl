# Cette feuille contient toutes les procédures pour le calendrier des cultures

#==============================================================================
# \brief Commande pour ajouter un jour fruit dans le calendrier
# \param fid pointeur sur le fichier de sortie
# \param mois Numéro du mois
# \param jour Numéro du jour
#==============================================================================
proc addFruit {fid mois jour} {
puts $fid {
   <entry>
      <title>Jour fruit</title>
      <summary>Jour fruit</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "fruit" label = "fruit" />
      <content type = "text">Jour fruit</content>
      <cbx_symbol>0xB1</cbx_symbol>
      <duration>0</duration>}
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
puts $fid {      <icon>fruit.png</icon>
      <color>#DF0174</color>
   </entry>
}
}

#==============================================================================
# \brief Commande pour ajouter un jour racine dans le calendrier
# \param fid pointeur sur le fichier de sortie
# \param mois Numéro du mois
# \param jour Numéro du jour
#==============================================================================
proc addRacine {fid mois jour} {
puts $fid {
   <entry>
      <title>Jour racine</title>
      <summary>Jour racine</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "racine" label = "racine" />
      <content type = "text">Jour racine</content>
      <cbx_symbol>0xB2</cbx_symbol>
      <duration>0</duration>}
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
puts $fid {      <icon>carotte.png</icon>
      <color>#610B0B</color>
   </entry>
}
}

#==============================================================================
# \brief Commande pour ajouter un jour fleur dans le calendrier
# \param fid pointeur sur le fichier de sortie
# \param mois Numéro du mois
# \param jour Numéro du jour
#==============================================================================
proc addFleur {fid mois jour} {
puts $fid {
   <entry>
      <title>Jour fleur</title>
      <summary>Jour fleur</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "fleur" label = "fleur" />
      <content type = "text">Jour fleur</content>
      <cbx_symbol>0xB3</cbx_symbol>
      <duration>0</duration>}
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
puts $fid {      <icon>fleur.png</icon>
      <color>#00BFFF</color>
   </entry>
}
}

#==============================================================================
# \brief Commande pour ajouter un jour feuille dans le calendrier
# \param fid pointeur sur le fichier de sortie
# \param mois Numéro du mois
# \param jour Numéro du jour
#==============================================================================
proc addFeuille {fid mois jour} {
puts $fid {
   <entry>
      <title>Jour feuille</title>
      <summary>Jour feuille</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "feuille" label = "feuille" />
      <content type = "text">Jour feuille</content>
      <cbx_symbol>0xB4</cbx_symbol>
      <duration>0</duration>}
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
puts $fid {      <icon>feuille.png</icon>
      <color>#5FB404</color>
   </entry>
}
}