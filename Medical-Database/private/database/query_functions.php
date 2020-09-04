<?php
    function find_all($table_name, $pk) {
        global $db;

        $sql = "SELECT * FROM " .$table_name ." ";
        $sql .= "ORDER BY " .$pk ." asc" .";";
        $result = mysqli_query($db, $sql);
        confirm_result_set($result, $table_name);
        return $result;
    }

    function find_record_by_pk($table_name, $pk, $pk_val) {
        global $db;


        if ($table_name == "patient_treatment") {
            $sql = "SELECT * FROM " .$table_name ." ";
            $sql .= "WHERE " .$pk['tdate'] ." = \"" .db_escape($db, $pk_val['tdate']) ."\" and ";
            $sql .= $pk['tfreq'] ." = \"" .db_escape($db, $pk_val['tfreq']) ."\" and ";
            $sql .= $pk['pid'] ." = " .db_escape($db, $pk_val['pid']) ." and ";
            $sql .= $pk['tid'] ." = " .db_escape($db, $pk_val['tid']) ." and ";
            $sql .= $pk['phid'] ." = " .db_escape($db, $pk_val['phid']) .";";
        } else {
            $sql = "SELECT * FROM " .$table_name ." ";
            $sql .= "WHERE " .$pk ." = \"" .db_escape($db, $pk_val) ."\";";
        }

        $result = mysqli_query($db, $sql);
        confirm_result_set($result, $pk);
        $record = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        return $record; // assoc. arry
    }
    
    function validate_string($string, $maxlen, $not_skip, $strname) {
        $errors = [];

        $indice = 0;
        foreach ($string as $str) {
            if (!is_string($str)) {
                $errors[] = $strname[$indice] ." is required.";
            } else if (!(ctype_alpha(str_replace(" ", "", $str)) || 
                        is_numeric(str_replace("-", "", $str))) && $not_skip) {
                $errors[] = $strname[$indice] ." is required and should only consist of letters/numbers";
            } else if (strlen($str) > $maxlen[$indice]) {
                $errors[] = $strname[$indice] ." can't be longer than " .$maxlen[$indice] ." letters/numbers.";
            }

            $indice++;
        }

        return $errors;
    }

    function validate_number($number, $maxnum, $numname) {
        $errors = [];

        foreach ($number as $num) {
            if (!is_int($num)) {
                $errors[] = $numname[$indice] ." is required and should be an integer.";
            }
            if ($num < 0 || $num > $maxnum[$indice]) {
                $errors[] = $numname[$indice] ." is a number between 0 and " .maxnum[$indice];
            }
        }
    }

    function validate_record($record, $table_name) {
        $string = [];
        $maxlen = [];
        $strname = [];
        
        $number = [];
        $maxnum = [];

        $numname = [];
        $not_skip = true;

        switch ($table_name) {
            case 'department':
                $string[] = $record['dname'];
                $maxlen[] = 20;
                $strname[] = ucfirst($table_name) ."'s name";
                $string[] = $record['dtel'];
                $maxlen[] = 13;
                $strname[] = ucfirst($table_name) ."'s tel";

                break;

            case 'disease':
                $string[] = $record['dename'];
                $maxlen[] = 30;
                $strname[] = ucfirst($table_name) ."'s name";
                
                break;

            case 'patient':
                $string[] = $record['pfname'];
                $maxlen[] = 30;
                $strname[] = ucfirst($table_name) ."'s first name";

                $string[] = $record['plname'];
                $maxlen[] = 20; 
                $strname[] = ucfirst($table_name) ."'s last name";

                $string[] = $record['prace'];
                $maxlen[] = 20;
                $strname[] = ucfirst($table_name) ."'s race";

                break;

            case 'physician':
                $string[] = $record['phfname'];
                $maxlen[] = 30;
                $strname[] = ucfirst($table_name) ."'s first name";
                
                $string[] = $record['phtel'];
                $maxlen[] = 13;
                $strname[] = ucfirst($table_name) ."'s tel";

                $string[] = $record['phspl'];
                $maxlen[] = 30;
                $strname[] = ucfirst($table_name) ."'s field";

                break;

            case 'treatment':
                $string[] = $record['tname'];
                $maxlen[] = 50;
                $strname[] = ucfirst($table_name) ."'s name";
                $not_skip = false;

                break;

            case 'users':
                $string[] = $record['ufname'];
                $maxlen[] = 20;
                $strname[] = ucfirst($table_name) ."'s first name";
                $string[] = $record['ulname'];
                $maxlen[] = 20;
                $strname[] = ucfirst($table_name) ."'s last name";
                $string[] = $record['urole'];
                $maxlen[] = 20;
                $strname[] = ucfirst($table_name) ."'s role";

                break;
            
            default:
            
                break;
        }
        
        if (!empty($string)) {
            $str_errors = validate_string($string, $maxlen, $not_skip, $strname);
        }
        if (!empty($number)) {
            $num_errors = validate_number($number, $maxnum);
        }

        return array_merge($str_errors??[], $num_errors??[]);
    }

    function insert_record($record, $table_name) {
        global $db;
        
        $errors = validate_record($record, $table_name);
        if (!empty($errors)) {
            return $errors;
        }

        switch ($table_name) {
            case 'department':
                $sql = "INSERT INTO " .$table_name ." VALUES (NULL, ";
                $sql .= "upper(\"" .db_escape($db, $record['dname']) ."\"), ";
                $sql .= "\"" .db_escape($db, $record['dtel']) ."\", ";
                $sql .= db_escape($db, $record['hid']) .");";
               
                break;
            
            case 'disease':
                $sql = "INSERT INTO " .$table_name ." VALUES (NULL, ";
                $sql .= "upper(\"" .db_escape($db, $record['dename']) ."\"));";
                
                break;

            case 'hospital':
                $sql = "INSERT INTO " .$table_name ." VALUES (NULL, ";
                $sql .= "\"" .db_escape($db, $record['hname']) ."\", ";
                $sql .= "\"" .db_escape($db, $record['hst_address']) ."\", ";
                $sql .= "\"" .db_escape($db, $record['hst_city']) ."\", ";
                $sql .= "\"" .db_escape($db, $record['hstate']) ."\", ";
                $sql .= db_escape($db, $record['hzip']) .");";

                break;

            case 'patient':
                $sql = "INSERT INTO " .$table_name ." VALUES (NULL, ";
                $sql .= "upper(\"" .db_escape($db, $record['pfname']) ."\"), ";
                $sql .= "upper(\"" .db_escape($db, $record['plname']) ."\"), ";
                $sql .= "upper(\"" .db_escape($db, $record['pgender']) ."\"), ";
                $sql .= "date(\"" .db_escape($db, $record['pbd']) ." 11:11:11\") , \"";
                $sql .= db_escape($db, $record['prace']) ."\", ";
                $sql .= "upper(\"" .db_escape($db, $record['pstatus']) ."\"));";

                break;

            case 'patient_treatment':
                $sql = "INSERT INTO " .$table_name ." VALUES (";
                $sql .= "date(\"" .db_escape($db, $record['tdate']) ." 11:11:11\"), ";
                $sql .= db_escape($db, $record['tfreq']) .", ";
                if ($record['tstatus'] == "") {
                    $sql .= "NULL, ";
                } else {
                    $sql .= "\"" .db_escape($db, $record['tstatus']) ."\", ";
                }
                $sql .= db_escape($db, $record['pid']) .", ";
                $sql .= db_escape($db, $record['tid']) .", ";
                $sql .= db_escape($db, $record['phid']) .");";

                break;

            case 'physician':
                $sql = "INSERT INTO " .$table_name ." VALUES (NULL, ";
                $sql .= "upper(\"" .db_escape($db, $record['phfname']) ."\"), ";
                $sql .= "\"" .db_escape($db, $record['phtel']) ."\", ";
                $sql .= "upper(\"" .db_escape($db, $record['phspl']) ."\"), ";
                $sql .= db_escape($db, $record['hid']) .");";

                break;

            case 'treatment':
                $sql = "INSERT INTO " .$table_name ." VALUES (NULL, ";
                $sql .= "\"" .ucfirst(db_escape($db, $record['tname'])) ."\", ";
                $sql .= "upper(\"" .db_escape($db, $record['ttype']) ."\"), ";
                $sql .= db_escape($db, $record['deid']) .");";

                break;
            
            case 'users':
                $sql = "INSERT INTO " .$table_name ." VALUES (NULL, ";
                $sql .= "upper(\"" .db_escape($db, $record['ufname']) ."\"), ";
                $sql .= "upper(\"" .db_escape($db, $record['ulname']) ."\"), ";
                $sql .= "upper(\"" .db_escape($db, $record['urole']) ."\"), ";
                $sql .= db_escape($db, $record['did']) .");";
 
                break;
            
            default:
                
                break;
        }

        $result = mysqli_query($db, $sql);

        if($result) {
            // true
            return true;
        } else {
            // false
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }


    function update_record($record, $table_name, $pk, $pk_val) {
        global $db;

        $errors = validate_record($record, $table_name);
        if (!empty($errors)) {
            return $errors;
        }
        switch ($table_name) {
            case 'department':
                $sql = "UPDATE department SET ";
                $sql .= "dname = upper(\"" .db_escape($db, $record['dname']) ."\"), ";
                $sql .= "dtel = \"" .db_escape($db, $record['dtel']) ."\", ";
                $sql .= "hid = " .db_escape($db, $record['hid']) ." ";
                $sql .= "WHERE did = " .db_escape($db, $record['did']) ." ";
                $sql .= "LIMIT 1;";

                break;

            case 'disease':
                $sql = "UPDATE disease SET ";
                $sql .= "dename = upper(\"" .db_escape($db, $record['dename']) ."\"), ";
                $sql .= "WHERE deid = " .db_escape($db, $record['deid']) ." ";
                $sql .= "LIMIT 1;";

                break;

            case 'patient':
                $sql = "UPDATE patient SET ";
                $sql .= "pfname = upper(\"" .db_escape($db, $record['pfname']) ."\"), ";
                $sql .= "plname = upper(\"" .db_escape($db, $record['plname']) ."\"), ";
                $sql .= "pgender = upper(\"" .db_escape($db, $record['pgender']) ."\"), ";
                $sql .= "pbd = date(\"" .db_escape($db, $record['pbd']) ." 11:11:11\"), ";
                $sql .= "prace = \"" .db_escape($db, $record['prace']) ."\", ";
                $sql .= "pstatus = \"" .db_escape($db, $record['pstatus']) ."\" ";
                $sql .= "WHERE pid = " .db_escape($db, $record['pid']) ." ";
                $sql .= "LIMIT 1;";

                break;

            case 'patient_treatment':
                $original_record = find_record_by_pk($table_name, $pk, $pk_val);

                $sql = "UPDATE patient_treatment SET ";
                $sql .= "tstatus = \"" .db_escape($db, $record['tstatus']) ."\", ";
                $sql .= "tdate = date(\"" .db_escape($db, $record['tdate']) ." 11:11:11\"), ";
                $sql .= "tfreq = " .db_escape($db, $record['tfreq']) .", ";
                $sql .= "pid = " .db_escape($db, $record['pid']) .", ";
                $sql .= "tid = " .db_escape($db, $record['tid']) .", ";
                $sql .= "phid = " .db_escape($db, $record['phid']) ." ";
                $sql .= "WHERE ";
                $sql .= "tdate = \"" .db_escape($db, $original_record['tdate']) ."\" and ";
                $sql .= "tfreq = " .db_escape($db, $original_record['tfreq']) ." and ";
                $sql .= "pid = " .db_escape($db, $original_record['pid']) ." and ";
                $sql .= "tid = " .db_escape($db, $original_record['tid']) ." and ";
                $sql .= "phid = " .db_escape($db, $original_record['phid']) ." ";
                $sql .= "LIMIT 1;"; 

                break;
            case 'physician':
                $sql = "UPDATE physician SET ";
                $sql .= "phfname = upper(\"" .db_escape($db, $record['phfname']) ."\"), ";
                $sql .= "phtel = \"" .db_escape($db, $record['phtel']) ."\", ";
                $sql .= "phspl = upper(\"" .db_escape($db, $record['phspl']) ."\"), ";
                $sql .= "hid = " .db_escape($db, $record['hid']) ." ";
                $sql .= "WHERE phid = " .db_escape($db, $record['phid']) ." ";
                $sql .= "LIMIT 1;";
                
                break;

            case 'treatment':
                $sql = "UPDATE treatment SET ";
                $sql .= "tname = \"" .ucfirst(db_escape($db, $record['tname'])) ."\", ";
                $sql .= "ttype = upper(\"" .db_escape($db, $record['ttype']) ."\"), ";
                $sql .= "deid = " .db_escape($db, $record['deid']) ." ";
                $sql .= "WHERE tid = " .db_escape($db, $record['tid']) ." ";
                $sql .= "LIMIT 1;";

                break;

            case 'users':
                $sql = "UPDATE users SET ";
                $sql .= "ufname = upper(\"" . db_escape($db, $record['ufname']) ."\"), ";
                $sql .= "ulname = upper(\"" . db_escape($db, $record['ulname']) ."\"), ";
                $sql .= "urole = \"" . db_escape($db, $record['urole']) ."\", ";
                $sql .= "did = " . db_escape($db, $record['did']) ." ";
                $sql .= "WHERE UID = " .db_escape($db, $record['UID']) ." ";
                $sql .= "LIMIT 1;";

                break;
            
            default:
     
                break;
        }

        $result = mysqli_query($db, $sql);
        if ($result) {
            // true
            return true;
        } else {
            // false
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

    function delete_record($table_name, $pk, $pk_val) {
        global $db;

        if ($table_name == "patient_treatment") {
            $sql = "DELETE FROM " .$table_name ." ";
            $sql .= "WHERE " .$pk['tdate'] ." = \"" .db_escape($db, $pk_val['tdate']) ."\" and ";
            $sql .= $pk['tfreq'] ." = " .db_escape($db, $pk_val['tfreq']) ." and ";
            $sql .= $pk['pid'] ." = " .db_escape($db, $pk_val['pid']) ." and ";
            $sql .= $pk['tid'] ." = " .db_escape($db, $pk_val['tid']) ." and ";
            $sql .= $pk['phid'] ." = " .db_escape($db, $pk_val['phid']) ." ";
            $sql .= "LIMIT 1;";
        } else {
            $sql = "DELETE FROM " .$table_name ." ";
            $sql .= "WHERE " .$pk ." = " . db_escape($db, $pk_val) ." ";
            $sql .= "LIMIT 1;";
        }

        $result = mysqli_query($db, $sql);
        if ($result) {
            // true
            return true;
        } else {
            // false
            echo mysqli_error($db);
            db_disconnect($db);
            exit;
        }
    }

?>