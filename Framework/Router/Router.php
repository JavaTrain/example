<?php
/**
 * Created by PhpStorm.
 * User: mindkicker
 * Date: 13.07.15
 * Time: 13:36
 */

namespace Framework\Router;

class Router
{

    const SEPARATORS = '/';
    const REGEX_DELIMITER = '~';

    private $routes;


    public function __construct($routes)
    {
        $this->routes = $routes;
    }


    public function find($uri)
    {

        $res = array();

        foreach ($this->routes as $item => $row) {

            $tokens    = array();
            $variables = array();
            $pattern   = $row['pattern'];

            preg_match_all('~\{\w+\}~', $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

            $pos = 0;
            foreach ($matches as $match) {
                $varName = substr($match[0][0], 1, -1);
                // get all static text preceding the current variable
                $precedingText = substr($pattern, $pos, $match[0][1] - $pos);

                $pos = $match[0][1] + strlen($match[0][0]);

                $precedingChar = strlen($precedingText) > 0?substr($precedingText, -1):'';

                $isSeparator = '' !== $precedingChar && false !== strpos(self::SEPARATORS, $precedingChar);

                if ($isSeparator && strlen($precedingText) > 1) {
                    $tokens[] = array('text', substr($precedingText, 0, -1));
                } elseif (!$isSeparator && strlen($precedingText) > 0) {
                    $tokens[] = array('text', $precedingText);
                }

                if (!empty($row['_requirements'][$varName])) {
                    $regexp = $row['_requirements'][$varName];
                } else {
                    $regexp = null;
                }

                if (null === $regexp) {
                    $regexp = '[\w]+';
                }

                $tokens[] = array('variable', $isSeparator?$precedingChar:'', $regexp);

                $variables[] = $varName;
            }

            if ($pos < strlen($pattern)) {
                $tokens[] = array('text', substr($pattern, $pos));
            }

            $regexp = '';

            for ($i = 0, $nbToken = count($tokens);$i < $nbToken;$i++) {
                $regexp .= $this->computeRegexp($tokens, $i);
            }

            $reg = self::REGEX_DELIMITER.'^'.$regexp.'$'.self::REGEX_DELIMITER.'s';

            if (preg_match($reg, $uri, $match)) {
                array_shift($match);
                $res['params']     = array_combine($variables, $match);
                $res['controller'] = $row['controller'];
                $res['action']     = $row['action'];

                if(!empty($row['_requirements']['_method']))
                    $res['_method'] = $row['_requirements']['_method'];

                if(!empty($row['security']))
                    $res['security'] = $row['security'];

                //                $res['pattern'] = $row['pattern'];
                //                $res['regexp'] = $reg;
                //                $res['uri'] = $uri;

                return $res;
            }
        }
        return false;
    }

    function computeRegexp(array $tokens, $index)
    {
        $token = $tokens[$index];
        if ('text' === $token[0]) {
            // Text tokens
            return preg_quote($token[1], self::REGEX_DELIMITER);
        } else {
            $regexp = sprintf('%s(%s)', preg_quote($token[1], self::REGEX_DELIMITER), $token[2]);
            return $regexp;
        }
    }
}