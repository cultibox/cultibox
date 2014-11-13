@echo OFF


If exist C:\cultibox\run\get_version.bat (
    for /f "delims=" %%i in ('C:\cultibox\run\get_version.bat') do (
		set version=%%~i
		setlocal EnableDelayedExpansion
		If not "!version:~0,6!" == "" (
			set ver_tmp=!version:~0,6!
			set softv=!ver_tmp:.=!
			If !softv! gtr 0 (
				for %%B in (C:\cultibox\xampp\sql_install\update_sql-*) do ( 
					set Name=%%~nxB
					set file_name=!Name:~11!
					set file_name=!file_name:~0,6!
					set filev=!file_name:.=!
					If !filev! geq !softv! (
						echo   * Updating Cultibox database with file:  %%B
						C:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --force -h 127.0.0.1 --port=3891 < %%B
					)
				)
			) else (
				echo ==== can't get software version, exiting... ====
				pause
				exit 1
			)
		)
	)

    If exist C:\cultibox\xampp\sql_install\update_sql.sql (
		echo   * Updating Cultibox database with file: C:\cultibox\xampp\sql_install\update_sql.sql
		C:\cultibox\xampp\mysql\bin\mysql.exe --defaults-extra-file="C:\cultibox\xampp\mysql\bin\my-extra.cnf" --force -h 127.0.0.1 --port=3891 < C:\cultibox\xampp\sql_install\update_sql.sql
	)
) else (
    echo ==== Error, missing get_version.bat script, exiting ====
    pause
    exit 1
)
