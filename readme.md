# BISTRO

### News and Updates
1.5 - PHP7 + MySQLi

###TODO
- vythnout bokem nastaveni DB 
- vytrhnout bokem session management
- vytrhnout bokem menu 
- header a footer samostatne
- loop?
- runtime rework
- sprava obrazku a priloh
    - zobrazovat nahledy obrazku
    - obrazky zobrazovat v novem okne, namisto stahovani
- wikimedia
    - audit bokem (audit, unauthorized)
    - vycisteni html tagu z textu v db (css styly, id, <big><big><big>...) + konverze b>strong i>em - povodlene strong, em, quote, blockquote, p, a, hr
    - backup samostatne


### OTAZKY
- proc je login prez dve tabulky? session management s tim uplne nepracuje... krom toho jsou tam zbytecne duplucity
- jak doprdele funguje UNREAD - nw_unread nema zadny index, pritom se hleda podle uzivatele

### NOTES
   $file_extension = strtolower(substr(strrchr($filename,"."),1));
            switch ($file_extension) {
                case "pdf": $ctype="application/pdf"; break;
                case "exe": $ctype="application/octet-stream"; break;
                case "zip": $ctype="application/zip"; break;
                case "doc": $ctype="application/msword"; break;
                case "xls": $ctype="application/vnd.ms-excel"; break;
                case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpe": case "jpeg":
                case "jpg": $ctype="image/jpg"; break;
                default: $ctype="application/force-download";
            }