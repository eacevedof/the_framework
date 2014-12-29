<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @version 1.1.0
 * @name constants
 * @file boot_constants.php 
 * @date 05-06-2014 09:34 (SPAIN)
 * @observations: 
 */

define("IS_DEBUG_ALLOWED",
        (
            TFW_DEBUG_ISON 
            ||(
                (TFW_DEBUG_ISREMOTE && $_SERVER["REMOTE_ADDR"]==TFW_DEBUG_REMOTEIP) 
                || (TFW_DEBUG_ISREMOTE && $_SERVER["REMOTE_ADDR"]=="127.0.0.1")
              ) 
            || key_exists(TFW_DEBUG_GET_KEY,$_GET)
        )
       );
