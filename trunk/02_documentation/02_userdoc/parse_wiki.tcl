set path [file dirname [info script]]

# Exe path of sam2p.exe
set sam2p {/usr/bin/sam2p}

# Input directories
set wikiPath [file join $path wiki]
set imgWikiPath [file join $path wiki img]

# Output directories
set texPath [file join $path wiki_tex]
set imgTexPath [file join $path wiki_tex img]

# Define rules for special cars
set CaracSpeciaux [list]
lappend CaracSpeciaux "°C" "\\textdegree{}C"
lappend CaracSpeciaux "#" "\\#"
lappend CaracSpeciaux "_" "\\_"
lappend CaracSpeciaux "\\" "\\textbackslash{}"
lappend CaracSpeciaux "&" "\\&"
lappend CaracSpeciaux "%" "\\%"
lappend CaracSpeciaux "<" "\\textless{}"
lappend CaracSpeciaux ">" "\\textgreater{}"

set CaracSpeciauxEnd [list]
lappend CaracSpeciauxEnd "*" ""

set largeurTable 12
set ::PageActualyParse ""
set inCode 0

proc removeDiatric {st} {
 return [string map {
        "Ą" "A"  "Ł" "L"  "Ľ" "L"  "Ś" "S"  "Š" "S"  "Ş" "S"  "Ť" "T"  "Ź" "Z"
        "Ž" "Z"  "Ż" "Z"  "ą" "a"  "ł" "l"  "ľ" "l"  "ś" "s"  "š" "s"  "ş" "s"
        "ť" "t"  "ź" "z"  "ž" "z"  "ż" "z"  "Ŕ" "R"  "Á" "A"  "A" "A"  "Ă" "A"
        "Ä" "A"  "Ĺ" "L"  "Ć" "C"  "Ç" "C"  "Č" "C"  "É" "E"  "Ę" "E"  "Ë" "E"
        "Ě" "E"  "Í" "I"  "Î" "I"  "Ď" "D"  "Đ" "D"  "Ń" "N"  "Ň" "N"  "Ó" "O"
        "Ô" "O"  "Ő" "O"  "Ö" "O"  "×" "x"  "Ř" "R"  "Ů" "U"  "Ú" "U"  "Ű" "U"
        "Ü" "U"  "Ý" "Y"  "Ţ" "T"  "ß" "s"  "ŕ" "r"  "á" "a"  "â" "a"  "ă" "a"
        "ä" "a"  "ĺ" "l"  "ć" "c"  "ç" "c"  "č" "c"  "é" "e"  "ę" "e"  "ë" "e"
        "ě" "e"  "í" "i"  "î" "i"  "ď" "d"  "đ" "d"  "ń" "n"  "ň" "n"  "ó" "o"
        "ô" "o"  "ő" "o"  "ö" "o"  "ř" "r"  "ů" "u"  "ú" "u"  "ű" "u"  "ü" "u"
        "ý" "y"  "ţ" "t"  "à" "a"  "û" "u"  "œ" "oe" "è" "e"  "ê" "e"
    } $st]
}


proc parseTitle {line level} {

   if {[string match "*=*Sommaire*=*" $line]} {
      return ""
   } elseif {[string match "*====*====*" $line]} {
      incr level 4
   } elseif {[string match "*===*===*" $line]} {
      incr level 3
   } elseif {[string match "*==*==*" $line]} {
      incr level 2
   } elseif {[string match "*=*=*" $line]} {
      incr level 1
   } else {
      return $line
   }
   
   set summary [string trim [string map {"=" "" {_} " "} $line]]
   set Sous [removeDiatric [string map {" " "_"} ${summary}]]
   if {$Sous != ""} {set Sous "_$Sous"}
   set label "\\label\{[file rootname ${::PageActualyParse}]${Sous}\}"
   
   switch -- ${level} {
      "-1" {
         return "\\part\{${summary}\}  $label"
      }
      "0" {
         return "\\chapter\{${summary}\}  $label"
      }
      "1" {
         return "\\section\{${summary}\}  $label"
      }
      "2" {
         return "\\subsection\{${summary}\}  $label"
      }
      "3" {
         return "\\subsubsection\{${summary}\}  $label"
      }
      "4" {
         return "\\paragraph\{${summary}\}  $label"
      }
      "5" {
         return "\\subparagraph\{${summary}\}  $label"
      }                        
   }
   
   return $line
}

proc searchSumary {file} {
   puts "Search summary $file"; update
   set summary ""
   set fid [open $file r]

   # Ajout du nom du fichier
   while {[eof $fid] != 1} {
      gets $fid line

      if {[string first {#summary} $line] != -1} {
         set summary [string map {{#summary} ""} $line];
      }

   
   }
   
   close $fid
   return $summary

}

set inTab 0
proc parseTab {line} {

   if {[string first "||" $line] != -1} {
       set lineSplitt ""
       foreach elem [split $line "||" ] {
         if {$elem != ""} {
            lappend lineSplitt $elem
         }
       }
       set nbCol [llength $lineSplitt]
   
      set line "\\hline\n[join $lineSplitt " & "] \\tabularnewline"
   
      if {$::inTab == 0} {
         set line "\n\\begin{tabular}\{|*\{${nbCol}\}\{p\{[expr $::largeurTable / ${nbCol} ]cm\}|\}\}\n$line"
      }
      
      set ::inTab 1

   } else {
      if {$::inTab == 1} {
         set line "\\hline\n\\end{tabular}\n$line"
         set ::inTab 0
      }
   }
   return $line
}

set inListe 0
proc parseListe {line} {

   if {[string first "*" [string trim $line]] == 0 && $::inCode == 0} {

      set line "\\item [string map {"*" ""} $line]"
   
      if {$::inListe == 0} {
         #set line "\n\\begin{itemize}\n\\renewcommand\{\\labelitemi\}\{\$\\bullet\$\}\n$line"
         set line "\n\\begin{itemize}\n\\renewcommand\{\\labelitemi\}\{\$\\bullet\$\}\n$line"
      }
      
      set ::inListe 1

   } else {
      if {$::inListe == 1} {
         set line "\\end{itemize}\n$line"
         set ::inListe 0
      }
   }
   return $line
}

proc parseLink {line} {

    if {[string first "\[" $line] != -1} {
    
        set startLink [string first "\[" $line]
        set endLink   [string first "\]" $line]
        set textBefore [string range $line 0 [expr $startLink - 1]]
        set textAfter [string range $line [expr $endLink + 1] end]
        set textLink  [string map {"\[" "" "\]" ""} [string range $line $startLink $endLink]]

        if {[llength $textLink] >= 2} {
            if {[string first "code.google.com" $line] == -1 && [string first "http" $line] != -1} {
                set line "${textBefore}\\begin\{bfseries\}\\href\{[lindex $textLink 0]\}\{[lrange $textLink 1 end]\}\\end\{bfseries\}${textAfter}"
            } else {
                set TempLink [string map {"#" "_"} [removeDiatric [lindex [split [lindex $textLink 0] "/"] end]]]
                set line "${textBefore}[lrange $textLink 1 end] (\\S \\ref\{${TempLink}\}\{\})${textAfter}"
                #set line "${textBefore}${textAfter}"
            }
        }
    }

    return $line
}

proc parseCode {line} {

    if {[string first "\{\{\{" $line] != -1} {
        set line "\\begin\{center\} \\colorbox\{gray\}\{  \\begin\{minipage\}\[c\]\{0.7\\textwidth\} \\begin\{itshape\}"
        set ::inCode 1
    }
    if {[string first "\}\}\}" $line] != -1} {
        set line "\\end\{itshape\} \\end\{minipage\}  \} \\end\{center\}"
        set ::inCode 0
    }

    return $line
}

#
proc parse {inFileName outFileName level} {
   puts "Parsing $inFileName"; update
   set ::PageActualyParse [file tail $inFileName]
   
   # Recherche du titre de la page
   set summary [searchSumary $inFileName]
   
   set fid [open $inFileName r]
   set out [open $outFileName w+]
   
   # Ajout du nom du fichier
   set labelT "\\label\{[file rootname ${::PageActualyParse}]\}"
   switch -- ${level} {
      "-1" {
         puts $out "\\part\{${summary}\}  $labelT"
      }
      "0" {
         puts $out "\\chapter\{${summary}\}  $labelT"
      }
      "1" {
         puts $out "\\section\{${summary}\}  $labelT"
      }
      "2" {
         puts $out "\\subsection\{${summary}\}  $labelT"
      }
      "3" {
         puts $out "\\subsubsection\{${summary}\}  $labelT"
      }                  
   }

   
   set inComment 0
   while {[eof $fid] != 1} {
      gets $fid line
      
      # Remplacement des caractere spéciaux
      if {[string first {http://cultibox.googlecode.com/svn/wiki/img/} $line] != -1} {
         set line [string map {{http://cultibox.googlecode.com/svn/wiki/img/} "\\scalegraphics\{./wiki/img/" {.jpg} ".jpg\}" {.png} ".png\}" {.PNG} ".PNG\}"} $line]     
      } elseif {$inComment == 0} {
         set line [string map $::CaracSpeciaux $line]
      }
      
      if {$::inCode == 0} {set line [parseLink $line]}
      
      set line [parseCode $line]     
      
      set line [parseListe $line]
      
      set line [parseCode $line]

      set line [string map $::CaracSpeciauxEnd $line]
        
      if {$::inCode == 0} {set line [parseTitle $line $level]}
      
      if {$::inCode == 0} {set line [parseTab $line]}
      
      

      if {[string first {#summary} $line] != -1} {
         set line "";
      }
      if {[string first {wiki:toc} $line] != -1} {
         set line "";
      } 
      if {[string first {wiki:comment} $line] != -1} {
         set line "";
         if {$inComment == 0} {
            set inComment 1
         } else {
            set inComment 0
         }
      } 

      puts $out $line
   }
   
   if {$::inTab == 1} {
      set ::inTab 0
      puts $out "\\hline\n\\end\{tabular\}"
   }
   if {$::inListe== 1} {
      set ::inListe 0
      puts $out "\\end\{itemize\}"
   }   
   close $fid
   close $out

}

# On génére le fichier tex
set fid [open [file join $path documentation.tex] w+]
puts $fid {\documentclass[11pt]{report}}

puts $fid {\usepackage[utf8]{inputenc} % set input encoding (not needed with XeLaTeX) }

puts $fid {%%% PAGE DIMENSIONS                                                        }
puts $fid {\usepackage{geometry} % to change the page dimensions                     }
puts $fid {\geometry{a4paper} % or letterpaper (US) or a5paper or....                 }

puts $fid {\usepackage{graphicx} % support the \includegraphics command and options   }

puts $fid {% \usepackage[parfill]{parskip} % Activate to begin paragraphs with an empty line rather than an indent  }

puts $fid {%%% PACKAGES                                                    }
puts $fid {\usepackage{booktabs} % for much better looking tables           }
puts $fid {\usepackage{array} % for better arrays (eg matrices) in maths    }
puts $fid {\usepackage{paralist} % very flexible & customisable lists (eg. enumerate/itemize, etc.)               }
puts $fid {\usepackage{verbatim} % adds environment for commenting out blocks of text & for better verbatim        }
puts $fid {\usepackage{subfig} % make it possible to include more than one captioned figure/table in a single float}
puts $fid {\usepackage[francais]{babel}  }
puts $fid {\usepackage{textcomp}         }
puts $fid {\usepackage{hyperref}         }
puts $fid {\usepackage{lscape}         }
puts $fid {\usepackage{calc}}
puts $fid {\usepackage{xcolor}}
puts $fid {% These packages are all incorporated in the memoir class to one degree or another...}

puts $fid {%%% HEADERS & FOOTERS                                                         }
puts $fid {\usepackage{fancyhdr} % This should be set AFTER setting up the page geometry }
puts $fid {\pagestyle{fancy} % options: empty , plain , fancy           }
puts $fid {\renewcommand{\headrulewidth}{1pt} % customise the layout... } 
puts $fid {\fancyhead[L]{\leftmark}}
puts $fid {\fancyhead[R]{Manuel Cultibox}}

puts $fid {\renewcommand{\footrulewidth}{1pt}}
puts $fid {\fancyfoot[L]{}}
puts $fid {\fancyfoot[C]{}}
puts $fid {\fancyfoot[R]{\textbf{page \thepage}}}

puts $fid {%%% Command}
puts $fid {\newlength{\imgwidth}}
puts $fid "\\newcommand\\scalegraphics\[1\]\{%"   
puts $fid {    \settowidth{\imgwidth}{\includegraphics{#1}}%}
puts $fid {    \setlength{\imgwidth}{\minof{\imgwidth}{0.9\textwidth}}%}
puts $fid {    \includegraphics[width=\imgwidth]{#1}%}
puts $fid "\}"

puts $fid {%%% SECTION TITLE APPEARANCE    }
puts $fid {\usepackage{sectsty}            }
puts $fid {\allsectionsfont{\sffamily\mdseries\upshape} % (See the fntguide.pdf for font help)  }
puts $fid {% (This matches ConTeXt defaults)   }

puts $fid {%%% ToC (table of contents) APPEARANCE }
puts $fid {\usepackage[nottoc,notlof,notlot]{tocbibind} % Put the bibliography in the ToC }
puts $fid {\usepackage[titles,subfigure]{tocloft} % Alter the style of the Table of Contents}
puts $fid {\renewcommand{\cftsecfont}{\rmfamily\mdseries\upshape}}
puts $fid {\renewcommand{\cftsecpagefont}{\rmfamily\mdseries\upshape} % No bold!}

puts $fid {%%% END Article customizations}

puts $fid {%%% The "real" document content comes below...}

puts $fid {\title{Manuel d'utilisation de la Cultibox}}
puts $fid {\author{Cultibox}}
puts $fid {\begin{document}}
puts $fid {\begin{titlepage}             }
puts $fid {\includegraphics{./wiki/img/box_3d_1.png}      }
puts $fid {\begin{center}                     }
puts $fid {\Huge                               }
puts $fid {Manuel d'utilisation de la Cultibox\\} 
puts $fid {\date\today    }
puts $fid {\end{center}    }
puts $fid {\end{titlepage} }

puts $fid {\maketitle}
puts $fid {}
puts $fid {\newpage}
puts $fid {\tableofcontents }


set fid2 [open [file join $wikiPath Sommaire.wiki] r]
while {[eof $fid2] != 1} {
   gets $fid2 line
   
   # Work only on valid file
   if {$line != "" && [string first "#summary" $line] == -1} {
   
      # Compute level
      set level [string first "*" $line]
      if {$level != -1} {
         set level [expr ($level - 4) / 2]
      }
      
      # Search file name
      set FileName [lindex [string trim [string map {"*" "" "\[" "" "\]" ""} $line]] 0]
      
      set File [file join $wikiPath ${FileName}.wiki]
      
      # parse file
      parse $File [file join $texPath [string map {.wiki .tex} [file tail $File]]] $level  

      puts $fid "\\input\{./wiki_tex/${FileName}\}"

      
   }

}
close $fid2

puts $fid {\end{document}}
puts $fid {}

close $fid
