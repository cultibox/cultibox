@ECHO OFF
If exist "./backup/backup.sql" (
    for %%R in (./backup/backup.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM jqcalendar"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM logs"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM configuration"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM plugs"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM power"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM programs"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM informations"

            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/backup.sql
        )
    )
)

If exist "./backup/jqcalendar.sql" (
    for %%R in (./backup/jqcalendar.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM jqcalendar"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/jqcalendar.sql
        )
    )
)

If exist "./backup/logs.sql" (
    for %%R in (./backup/logs.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM logs"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/logs.sql
        )
    )
)


If exist "./backup/configuration.sql" (
    for %%R in (./backup/configuration.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM configuration"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/configuration.sql
        )
    )
)


If exist "./backup/plugs.sql" (
    for %%R in (./backup/plugs.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM plugs"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/plugs.sql
        )
    )
)


If exist "./backup/power.sql" (
    for %%R in (./backup/power.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM power"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/power.sql
        )
    )
)


If exist "./backup/programs.sql" (
    for %%R in (./backup/programs.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM programs"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/programs.sql
        )
    )
)


If exist "./backup/informations.sql" (
    for %%R in (./backup/informations.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM informations"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/informations.sql
        )
    )
)

If exist "./backup/historic.sql" (
    for %%R in (./backup/historic.sql) do (
        if not %%~zR equ 0 (
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox -e "DELETE FROM historic"
            xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox cultibox < ./backup/historic.sql
        )
    )
)
