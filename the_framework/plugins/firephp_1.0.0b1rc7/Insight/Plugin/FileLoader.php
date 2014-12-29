<?php

class Insight_Plugin_FileLoader extends Insight_Plugin_API {

    private static $_instance = null;

    public function mapFilePath($path) {

        if (self::$_instance === null) {
            self::$_instance = $this;
        }

        return $this->_processFile($path);
    }

    /**
     * This method call is automatically inserted into the source by _processFile().
     */
    public static function _autoMapFilePath($path)
    {
        return self::$_instance->mapFilePath($path);
    }

    private function _processFile($path) {
        
        $cachedPath = dirname($path) . '/' . '.patched-' . basename($path) . '~' . filemtime($path) . '~';

        if (file_exists($cachedPath)) {
            return $cachedPath;
        }

        if(!file_exists(dirname($cachedPath))) {
            if(!mkdir(dirname($cachedPath), 0775, true)) {
                throw new Exception('Error creating cache path at: ' . $cachedPath);
            }
        }

        $source = file_get_contents($path);

        $tokens = token_get_all($source);      
        $statements = array();
        for( $i=0,$c=sizeof($tokens) ; $i<$c ; $i++ ) {
            if (
                $tokens[$i][0] === T_REQUIRE_ONCE || 
                $tokens[$i][0] === T_REQUIRE ||
                $tokens[$i][0] === T_INCLUDE_ONCE ||
                $tokens[$i][0] === T_INCLUDE
            )
            {
                $statements[] = $this->_processFile__determineStatement($tokens, $i);
            }
        }
        
        $statements = array_values(array_unique($statements));
        
        for( $i=0,$c=sizeof($statements) ; $i<$c ; $i++ ) {
                        
            // Match the statement against the source so we have the exact string we can replace.
            // This is a sanity test as the statement is assumed to be exact!
            if (!preg_match_all("/" . preg_quote($statements[$i], "/") . "/", $source, $sourceMatch)) {
                throw new Exception('Unable to replace file loading statement "' + $statements[$i] + '" in file "' + $path + '"');
            }

            $newStatement = array();
            preg_match("/^(\w*\s*)(\(?)(.*?)(\)?)(;?)$/", $sourceMatch[0][0], $m);
            if ($m[4] === ')' && $m[2] === '') $m[3] .= ')';
            if ($m[2] === '') $m[2] = '(';
            if ($m[4] === '') $m[4] = ')';
            $m[3] = '\\' . __CLASS__ . '::_autoMapFilePath(' . $m[3] . ')';
 
            $source = str_replace($sourceMatch[0][0], implode("", array_slice($m, 1)), $source);
        }

        file_put_contents($cachedPath, $source);

        return $cachedPath;
    }


    /**
     * Given a bunch of PHP language tokens determine the file loading statement.
     */
    private function _processFile__determineStatement(&$tokens, $index)
    {
        $statement = array();
        $brackets = null;
        $token = null;
        for ( $i=$index,$c=$index+20 ; $i<$c ; $i++ )
        {
            if (is_array($tokens[$i])) {
                $token = $tokens[$i][1];

                if ($i >= $index+1 && $tokens[$i][0] !== T_WHITESPACE && $brackets === null) {
                    $brackets = false;
                }

            } else {
                $token = $tokens[$i];
                
            }
            
            if ($brackets !== false) {
                if ($token === "(") {
                    if ($brackets === null) {
                        $brackets = 0;
                    }
                    $brackets += 1;
                } else
                if ($token === ")") {
                    $brackets -= 1;
                }
            }
            
            $statement[] = $token;
            
            if ($brackets === false && ($tokens[$i] === ";" || $tokens[$i] === "\n")) {
                break;
            } else
            if ($brackets === 0) {
                break;
            }
            
        }

        return implode("", $statement);
    }
}
