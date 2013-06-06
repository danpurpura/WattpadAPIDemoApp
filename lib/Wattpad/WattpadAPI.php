<?php

/**
 * WattpadAPI
 *
 * Partial implementation of the Wattpad Developer API
 *
 * See: http://www.wattpad.com/api_doc
 *
 */

namespace Wattpad;

use GenericAPI\AbstractGenericAPI;

class WattpadAPI extends AbstractGenericAPI {

    protected $categories;

	/**
	 * getBaseURL() - returns the URL to connect to
     *
     * @abstract
	 * @return string
	 */
    public function getBaseURL() {
        return 'http://wattpad.com/apiv2/';
    }

    /**
     * getCategories() - return the avaiable story categories
     *
     * @return array - list of categories array(category_id => category_descr, ...)
     */
    public function getCategories() {
        if (!$this->categories) {
            $this->categories = $this->call('getcategories')->asArray();
        }

        return $this->categories;
    }

    /**
     * getStories() - returns a list of story details
     *
     * All params are optional.
     *
     * @param string - section: 'hot', 'new', 'undiscovered', 'complete' (API defaults to 'hot')
     * @param int - category ID
     * @param int - language ID (API defaults to 1)
     * @param int - results offset
     * @param int - max number of results to return
     */
    public function getStories($section = null, $categoryID = null, $language = null, $offset = null, $limit = null) {
        return $this->call('storylist', array(
            'search' => $section,
            'category' => $categoryID,
            'language' => $language,
            'offset' => $offset,
            'limit' => $limit
        ))->asArray();
    }

    /**
     * getStoriesInCategory - returns a list of story details for the given category
     *
     * @param int - category ID
     * @param string - section ('hot', 'new', 'undiscovered')
     * @param int - max number of results
     */
    public function getStoriesInCategory($categoryID, $section = null, $limit = null) {
        return $this->getStories($section, $categoryID, null, null, $limit);
    }

}