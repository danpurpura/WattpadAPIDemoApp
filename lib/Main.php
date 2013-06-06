<?php

/**
 * Simple router / driver for the front end
 */

use Wattpad\WattpadAPI;
use Wattpad\WattpadStoryStats;

class Main {

    /**
     * route($params) - attempts to route the given params
     *
     * @static
     * @param array
     * @return object
     */
    public static function route($params) {
        $router = new self();
        return $router->call($params);
    }

    /**
     * supportedActions() - list of actions we support
     *
     * @return array - key => action name, value => function to call
     */
    public function supportedActions() {
        return array(
            'categories' => 'getCategories',
            'stories' => 'getStats'
        );
    }

    /**
     * getFunction()
     *
     * @param string - action name
     * @return string - function name
     */
    public function getFunction($action) {
        $actions = $this->supportedActions();

        // do we support it?
        if (isset($actions[$action])) {
            return $actions[$action];
        }

        // not found
        return null;
    }

    /**
     * call() - attempts to call an internal function from the given params
     *
     * @param array
     * @return object
     */
    public function call($params) {
        // make sure we have params
        if (empty($params)) {
            $this->unsupportedAction();
        }

        // extract our action
        $action = key($params);
        array_shift($params);

        $function = $this->getFunction($action);

        if (empty($function)) {
            $this->unsupportedAction();
        }

        return $this->$function($params);
    }

    /**
     * getCategories() - returns the list of avaialble story categories
     *
     * @return string - JSON encoded string
     */
    public function getCategories($params = null) {
        // instantiate our API
        $api = new WattpadAPI();

        // get the categories
        $categories = $api->getCategories();

        // sort them
        asort($categories);
        
        // rebuild the array, we'll lose the order othwerise
        $sorted_categories = array();
        foreach($categories as $key => $value) {
            $sorted_categories[] = array('id' => $key, 'descr' => $value);
        }

        // return them
        return json_encode($sorted_categories);
    }


    /**
     * getStats() - returns the story stats details
     *
     * @param array - must supply the category id: array(id) => int
     * @return string - JSON encoded string: array(id => int, stats => object)
     */
    public function getStats($params) {
        // make sure we have a category id
        if (!isset($params['id'])) {
            $this->unsupportedAction();
        }

        // instantiate our API
        $api = new WattpadAPI();

        // grab the first 50 hot stories in the category
        $stories = $api->getStoriesInCategory($params['id'], 'hot', 50);

        // load our stats engine
        $stats = new WattpadStoryStats($stories);

        // process the results
        $stats->process();

        // return the results
        return json_encode(array(
            'id' => $params['id'],
            'stats' => $stats->getResults()
        ));
    }

    public function unsupportedAction() {
        throw new Exception('Invalid action');
    }
}