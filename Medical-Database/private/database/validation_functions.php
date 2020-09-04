<?php
    function is_blank($value) {
        if (!isset($value)) {
            return !isset($value) || trim($value) === '';
        }
    }
?>