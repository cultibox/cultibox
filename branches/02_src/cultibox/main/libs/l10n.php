<?php
$toroot = isset($toroot) ? $toroot : '.';

// Example: locale/en_US.utf8.po => templates_c/l10n.en_US.ser



define ('APP', $toroot);
define ('CACHE', $toroot);
define ('__TRANSLATIONS_MAX_LINE_LENGTH', 8192);
define ('__TRANSLATIONS_CACHE_FILEPATH', '%s/main/templates_c/l10n.%s.ser');
define ('__TRANSLATIONS_PO_FILEPATH', '%s/main/locale/%s.utf8.po');


if(isset($_COOKIE['LANG'])) {
    define ('__TRANSLATIONS_CACHE_FILEPATH_MODULE', dirname(__FILE__) . '/../templates_c/l10n.'.$_COOKIE['LANG'].'.ser');
    define ('__TRANSLATIONS_PO_FILEPATH_MODULE', dirname(__FILE__) . '/../locale/'.$_COOKIE['LANG'].'.utf8.po');
}

function __translations_check_lang($lang) {
	if (empty($lang)) {
		die("no lang specified");
	}
	if (strlen($lang) != 5) {
		die("incorrect lang code : 5 letters (ex: fr_FR)");
	}
	if ($lang[2] != '_') {
		die("incorrect lang code : the 3rd letter must be a '_' (ex: fr_FR)");
	}
}

function __translations_parse_po_file($lang) {
	__translations_check_lang($lang);
	$locale_po_file = sprintf(__TRANSLATIONS_PO_FILEPATH, APP, $lang);
	if (!file_exists($locale_po_file)) {
               if(isset($_COOKIE['LANG'])) {
                  $locale_po_file = sprintf(__TRANSLATIONS_PO_FILEPATH_MODULE, APP, $lang);
               }
               if (!file_exists($locale_po_file)) {
			         die("'$locale_po_file' does not exist");
               }
	}

	$handle = fopen($locale_po_file, "r");
	if ($handle === false) {
		die("cannot open '$locale_po_file' in read mode");
	}
	
	$translations = null;
	$line = "";
	$lineno = 1;
	$current_msgid = "";
	$errors = "";

	// Skip first line
	$line = fgets($handle, __TRANSLATIONS_MAX_LINE_LENGTH);

	while (!feof($handle)) {
		$line = fgets($handle, __TRANSLATIONS_MAX_LINE_LENGTH);
		if (preg_match("/^\s*#/", $line, $matches)) {	
			// skip comment line
		} elseif (preg_match("/^\s*msgid\s+\"(.+)\"\s*$/", $line, $matches)) {
			$current_msgid = $matches[1];
			if (isset($translations["$current_msgid"])) {
				$errors .= "Content: msgid '$current_msgid' already defined - see L$lineno<br>";
				$lineno++;
				continue;
			}
		} elseif (preg_match("/^\s*msgstr\s+\"(.*)\"\s*$/", $line, $matches)) {
			if (!$current_msgid) {
				$errors .= "Syntax: no appliable msgid found - see L$lineno<br>";
				$lineno++;
				continue;
			}
			//$translations["$current_msgid"] = $matches[1];
			$translations["$current_msgid"] = html_entity_decode(htmlentities($matches[1], ENT_COMPAT, "UTF-8"));
			$current_msgid = "";
		} elseif (preg_match("/^\s*$/", $line)) {
			// skip empty line
		} else {
			if (!$current_msgid) {
				$errors .= "Syntax: unexpected content L$lineno<br>";
				$lineno++;
				continue;
			}
		}
		$lineno++;
	}

	if (!empty($errors)) {
		die("Errors ocurred while parsing '$locale_po_file':<br>{$errors}<br>");
	}

	fclose($handle);
	return $translations;
}

function __translations_cache_lang($lang, $translations) {
	__translations_check_lang($lang);
	$cache_file = sprintf(__TRANSLATIONS_CACHE_FILEPATH, CACHE, $lang);
	$tmp_cache_file = $cache_file . uniqid(rand(), true);

	$ret = file_put_contents($tmp_cache_file, serialize($translations));
	if ($ret === false) {
        // Modification by Alliaume : Remove die because it's blocking interface
		//die("Cannot write to '$cache_file'");
	}

	// Renames are atomic
	$ret = rename($tmp_cache_file, $cache_file);
	if ($ret === false) {
        // Modification by Alliaume : Remove die because it's blocking interface
		//die("rename from '$tmp_cache_file' to '$cache_file' failed");
	}
}

function __translations_read_from_cache($lang) {
	__translations_check_lang($lang);
	$cache_file = sprintf(__TRANSLATIONS_CACHE_FILEPATH, CACHE, $lang);

	if (!file_exists($cache_file)) {
      if(isset($_COOKIE['LANG'])) {
         $cache_file = sprintf(__TRANSLATIONS_CACHE_FILEPATH_MODULE, CACHE, $lang);
      }
      if (!file_exists($cache_file)) {
		   return false;
      }
	}

	$data = file_get_contents($cache_file);
	if ($data === false) {
		die("cannot read cache file");
	}

	$ret = unserialize($data);
	if (empty($ret)) {
		die("empty data");
	}
	return $ret;
}

function __translations_get($lang) {
	__translations_check_lang($lang);

	$po_file = sprintf(__TRANSLATIONS_PO_FILEPATH, APP, $lang);
	$cache_file = sprintf(__TRANSLATIONS_CACHE_FILEPATH, CACHE, $lang);

	if (!file_exists($po_file)) {
               if(isset($_COOKIE['LANG'])) {
                  $po_file = sprintf(__TRANSLATIONS_PO_FILEPATH_MODULE, APP, $lang);
                  $cache_file = sprintf(__TRANSLATIONS_CACHE_FILEPATH_MODULE, CACHE, $lang);
               }

	            if (!file_exists($po_file)) {
			         @unlink($cache_file);
			         die("'$po_file' does not exist");
               }
	}

	$translations = null;

	if (file_exists($cache_file) && (filemtime($po_file) < filemtime($cache_file)) ) {
		// Cache exists AND is up-to-date - read from it
		$translations = __translations_read_from_cache($lang);
	} else {
		// Cache does not exist OR is not up-to-date - update it
		$translations = __translations_parse_po_file($lang);
		__translations_cache_lang($lang, $translations);
	}

	if (empty($translations)) {
		die("problem with setting up translation table");
	}
	return $translations;
}

?>
