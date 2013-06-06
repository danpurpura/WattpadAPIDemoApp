<?php

/**
 * AbstractGenericAPI
 *
 * Minimal base class to handle sending requests to a RESTful API.
 * Supports GET or POST requests.
 *
 * Default URL scheme: baseURL/function?param1=X&param2=Y
 *
 * @abstract
 */

namespace GenericAPI;

abstract class AbstractGenericAPI {

    protected $curlHandle;

	/**
	 * getBaseURL() - returns the URL to connect to
     *
     * @abstract
	 * @return string
	 */
    abstract public function getBaseURL();

	/**
	 * responseClass() - returns the name of the class to use to encapsulate the response
     *
	 * @return string
	 */
    public function responseClass() {
        return 'GenericAPI\GenericAPIResponse';
    }

	/**
	 * getCurlHandle() - returns the curl handle
     *
     * Initializes the handler if necessary.
     *
	 * @return resource
	 */
	public function getCurlHandle() {
		if (!isset($this->curlHandle)) {
			$this->curlHandle = curl_init();
            $this->setDefaultCurlOptions();
		}

		return $this->curlHandle;
	}

    /**
     * closeCurlHandle() - closes the curl handle
     */
    public function closeCurlHandle() {
        curl_close($this->getCurlHandle());
    }

    /**
     * setDefaultCurlOptions() - sets our default curl options
     *
     * Called automatically when the handler is initialized.
     */
    public function setDefaultCurlOptions() {
		curl_setopt($this->curlHandle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * call() - sends a request to the API
     *
     * @param string - function to call
     * @param array - parameters to send
     * @param bool - use POST
     */
    public function call($function, $params = array(), $post = false) {
        $url = $this->createURL($function);

        // for GET, append our params to the URL
        if (!$post && !empty($params)) {
            $url = $url .= '?' . http_build_query($params);
        }

		curl_setopt($this->getCurlHandle(), CURLOPT_URL, $url);
		curl_setopt($this->getCurlHandle(), CURLOPT_POST, $post);

        // for POST, make sure we set the params
        if ($post) {
            curl_setopt($this->getCurlHandle(), CURLOPT_POSTFIELDS, $params);
        }

		return $this->toResponse(curl_exec($this->getCurlHandle()));
    }

    /**
     * createURL() - returns a URL to connect to the API
     *
     * Override to support alternative URL formatting.
     *
     * @param string - function to call
     * @param string - URL; default format is baseURL/function
    */
    public function createURL($function) {
        return $this->getBaseURL() . '/' . $function;
    }

    /**
     * toResponse() - wraps the response from call() in our response class
     *
     * @param string - call response
     * @return object - instance of response class
     */
    public function toResponse($response) {
        $class = $this->responseClass();
        return new $class($response);
    }

}