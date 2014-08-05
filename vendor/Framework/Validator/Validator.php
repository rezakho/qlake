<?php

class Validator
{
    private $errors = [];
    private $messages = [
        'required'    => ' field is required',
        'email'       => ' field must be an valid address',
        'password'    => ' field must be an valid address',
        'min'         => ' filed is not have minimum lenght',
        'max'         => '',
        'bool'        => '',
        'float'       => ' filed not an float ',
        'int'         => ' field not an Integer',
        'ip'          => ' filed not an valid ip address',
        'url'         => ' filed not an valid url address',
        'num'         => ' filed not an numeric ',
        'between'     => ' filed not match between',
        'time'        => ' field must be an valid time',
        'alpha'       => ' filed not an alpha characters',
        'alphaNum'    => '',
        'alphaDash'   => '',
        'strLength'   => '',
        'digit'       => ' field not an digit',
        'minInt'      => ' field not an minimum integer',
        'maxInt'      => ' field not an maximum integer',
        'equalInt'    => ' field not an equal integer',
        'equalString' => ' field not an equal string',
    ];
    private $validators = [
        'required',    // required field
        'email',       // validate email
        'password',    // validate strong password
        'min',         // minimal integer
        'bool',        // boolean field
        'float',       // float field
        'int',         // int field
        'ip',          // ip field
        'url',         // url field
        'num',         // numeric field
        'between',     // between two integer fields
        'time',        // time field
        'alpha',       // alpha field
        'alphaNum',    // alphaNum field
        'alphaDash',   // alphaDash field
        'strLength',   // string length field
    ];

    public function validate($data = [], $rules = [])
    {
        $validator = true;

        foreach ($rules as $item => $rol) {

            $rol = explode('|', $rol);

            foreach ($rol as $rule) {

                $pos = strpos($rule, ":");

                if ($pos !== false) {

                    $parameter = substr($rule, $pos + 1);

                    $rule = substr($rule, 0, $pos);

                } else {

                    $parameter = '';

                }

                $methodName = 'validate' . ucfirst($rule);

                $value = $data[$item];

                if (method_exists($this, $methodName))

                    $result = $this->$methodName($value, $parameter);

                $validator = $this->getPass($item, $rule, $result);

            }
        }
        return $validator;
    }

    /**
     * Match return result to pass errors
     *
     * @param  bool $item
     * @param  bool $rule
     * @param  bool $result
     * @return bool
     */
    public function getPass($item, $rule, $result)
    {

        if (!$result) {

            $this->errors[$item][] = 'The ' . $item . $this->messages[$rule];
            return false;

        }

        return true;
    }

    /**
     * Return an error messages
     * @param none
     * @return strung
     */
    public function getErrors()
    {

        return $this->errors;

    }

    /**
     * Validate that an attribute is an array
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    protected function validateArray($value, $parameter)
    {

        return is_array($value) ? false : true;

    }

    /**
     * Validate that an attribute is an int
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    protected function validateInt($value, $parameter)
    {

        return !filter_var($value, FILTER_VALIDATE_INT) ? false : true;

    }

    /**
     * Validate that an attribute is an required field
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateRequired($value, $parameter)
    {

        $value = trim($value);

        return ($value === '' || $value === NULL || is_null($value)) ? false : true;

    }

    /**
     * Validate that an attribute is an active email
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateEmail($value, $parameter)
    {

        return !filter_var($value, FILTER_VALIDATE_EMAIL) ? false : true;

    }

    /**
     * Validate that an attribute is an min string length
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateMinStr($value, $parameter)
    {

        return (strlen($value) >= $parameter === false) ? false : true;

    }

    /**
     * Validate that an attribute is an min string length
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateMinInt($value, $parameter)
    {

        return ! ( $value <= $parameter) ? false : true;

    }

    /**
     * Validate that an attribute is an max string length
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateMaxStr($value, $parameter)
    {

        return (strlen($value) <= $parameter === false) ? false : true;

    }

    /**
     * Validate that an attribute is an max string length
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateMaxInt($value, $parameter)
    {

        return ! ( $value >= $parameter) ? false : true;

    }

    /**
     * Validate that an attribute is an max string length
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateEqualInt($value, $parameter)
    {

        return ! ( $value === $parameter) ? false : true;

    }

    /**
     * Validate that an attribute is an max string length
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateEqualString($value, $parameter)
    {

        return ! ( $value === $parameter) ? false : true;

    }

    /**
     * Validate that an attribute is an bool
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateBool($value, $parameter)
    {

        return ! filter_var($value, FILTER_VALIDATE_BOOLEAN) ? false : true;

    }

    /**
     * Validate that an attribute is an float
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateFloat($value, $parameter)
    {

        return ! filter_var($value, FILTER_VALIDATE_FLOAT) ? false : true;

    }

    /**
     * Validate that an attribute is an ip
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateIp($value, $parameter)
    {

        return ! filter_var($value, FILTER_VALIDATE_IP) ? false : true;

    }

    /**
     * Validate that an attribute is an url
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateUrl($value, $parameter)
    {

        return !filter_var($value, FILTER_VALIDATE_URL) ? false : true;

    }

    /**
     * Validate that an attribute is an numeric
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateNum($value, $parameter)
    {

        return !is_numeric($value) ? false : true;

    }

    /**
     * Validate that an attribute is an between 2 numeric
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateBetween($value, $parameter)
    {

        list($min, $max) = explode(',', $parameter);

        return !($value >= $min && $value <= $max) ? false : true;

    }

    /**
     * Validate that an attribute is an validate time
     *

     * @param  time $time
     * @param  string $parameter
     * @return bool
     */
    private function validateTime($time, $parameter)
    {

        $pattern = '/^([0-9]|0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]|[0-5][0-9]:[0-5][0-9])$/';

        return preg_match($pattern, $time) ? false : true;

    }

    /**
     * Validate that an attribute is an active URL.
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateAlpha($value, $parameter)
    {

        return ! ctype_alpha($value) ? false : true;

    }

    /**
     * Validate that an attribute is an Alpha Numeric
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateAlphaNum($value, $parameter)
    {

        return ! ctype_alnum($value) ? false : true;

    }

    /**
     * Validate that an attribute is an Alpha Dash.
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateAlphaDash($value, $parameter = null)
    {

        return ! preg_match('/^[\pL\pM\pN_-]+$/u', $value) ? false : true;

    }

    /**
     * Validate that an attribute is an Digit.
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateDigit($value, $parameter =  null)
    {

        return ctype_digit($value)? false : true;

    }

    /**
     * Validate that an attribute is in parameter array
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateIn($value, $parameter)
    {

        $paramArray = explode(',', $parameter);
        return ! ( in_array( $value , $paramArray) )? false : true;

    }

    /**
     * Validate that an attribute is in parameter array
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateNotIn($value, $parameter)
    {

        $paramArray = explode(',', $parameter);
        return ( in_array( $value , $paramArray) )? false : true;

    }


    /**
     * Validate that an attribute is in valid image
     * @param  mixed $value
     * @param  string $parameter
     * @return bool
     */
    private function validateImage($value, $parameter)
    {

        $imageMime = [
            'gif' ,'IMAGETYPE_GIF',
            'jpg' ,'IMAGETYPE_JPEG',
            'png' ,'IMAGETYPE_PNG',
            'swf' ,'IMAGETYPE_SWF',
            'psd' ,'IMAGETYPE_PSD',
            'bmp' ,'IMAGETYPE_BMP',
            'tiff','IMAGETYPE_TIFF_II',
            'tiff','IMAGETYPE_TIFF_MM',
            'jpc' ,'IMAGETYPE_JPC',
            'jp2' ,'IMAGETYPE_JP2',
            'jpx' ,'IMAGETYPE_JPX',
            'jb2' ,'IMAGETYPE_JB2',
            'swc' ,'IMAGETYPE_SWC',
            'iff' ,'IMAGETYPE_IFF',
            'wbmp','IMAGETYPE_WBMP',
            'xbm' ,'IMAGETYPE_XBM',
            'ico' ,'IMAGETYPE_ICO',
        ];

        return (exif_imagetype( strtolower($value)) != $imageMime["$parameter"]) ? false : true;

    }


}


//echo "<pre>";print_r( $item.' '.$value );
