<?php

/**
 * GenericAPIResponse
 *
 * Response wrapper. Can attempt to automatically decode the response as
 * JSON or XML, and return as an array or object.
 *
 */

namespace GenericAPI;

class GenericAPIResponse {

    protected $response;

	/**
	 * Constructor
	 *
	 * @param string - API response
	 */
    public function __construct($response) {
        $this->response = $response;
    }

    /**
     * decode() - attempts to determine the format of the response and decode it
     *
     * @return mixed - if JSON or XML, returns object, string otherwise (original response)
     */
    public function decode() {
        if (!empty($this->response)) {
            // try to JSON decode it
            if ($json = json_decode($this->response)) {
                return $json;
            // try to parse it as XML (supress the error in case it's not XML)
            } else if ($xml = @simplexml_load_string($this->response)) {
                return $xml;
            }
        }

        // just return the resopnse as is
        return $this->response;
    }

    /**
     * __toString() - returns the original response
     *
     * @return string
     */
    public function __toString() {
        return $this->response;
    }

    /**
     * asString() - returns the original response
     *
     * @return string
     */
    public function asString() {
        return $this->__toString();
    }

    /**
     * asObject() - decodes the reponse and casts it to an object
     *
     * @return object
     */
    public function asObject() {
        return (object)$this->decode();
    }

    /**
     * asArray() - decodes the response and casts it to an array
     *
     * @return string
     */
    public function asArray() {
        return (array)$this->decode();
    }

}