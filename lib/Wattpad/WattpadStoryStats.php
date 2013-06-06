<?php

/**
 * WattpadCategoryStats
 *
 * Compiles states based on the results from a WattpadAPI::getStories() call.
 *
 * Final results are:
 *     // for each type: parts, pages, reads, votes, comments
 *     array(
 *        TYPE => array(
 *            'count' => int,
 *            'total' => int,
 *            'min'   => int,
 *            'max'   => int,
 *            'avg'   => int
 *        )
 *     );
 * 
 */

namespace Wattpad;

class WattpadStoryStats {

    protected $data;
    protected $results = array();

    /**
     * Constructor
     *
     * @param string - results from  WattpadAPI::getStories()
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * process() - collects stats from our data
     */
    public function process() {
        // do we have data? does our data have items?
        if (is_array($this->data) && isset($this->data['items'])) {
            // loop over the items, and log the data we care about
            foreach($this->data['items'] as $story) {
                $this->log('parts', count($story->parts));
                $this->log('pages', $story->pagecount);
                $this->log('reads', $story->readcount);
                $this->log('votes', $story->votes);
                $this->log('comments', $story->commentcount);
            }
            // calculate our aggregates
            $this->calculateAggregates();
        }
    }

    /**
     * createLog() - initializes log data for the specified type
     *
     * @param string - type
     */
    public function createLog($type) {
        $this->results[$type] = array(
            'count' => 0,
            'total' => 0,
            'min' => null,
            'max' => null,
            'avg' => 0,
            'mean' => array() // it'll be an int later
        );
    }

    /**
     * log() - logs the specified type and value
     */
    public function log($type, $value) {
        // if we haven't logged this type yet, create it
        if (!isset($this->results[$type])) {
            $this->createLog($type);
        }

        // log our total
        $this->results[$type]['total'] += $value;

        // increment our count
        $this->results[$type]['count'] ++;

        // update the min
        if (!isset($this->results[$type]['min']) || $value < $this->results[$type]['min']) {
            $this->results[$type]['min'] = $value;
        }

        // update the max
        if (!isset($this->results[$type]['max']) || $value > $this->results[$type]['max']) {
            $this->results[$type]['max'] = $value;
        }

        // stick the value in our mean
        $this->results[$type]['mean'][] = $value;
    }

    /**
     * calculateAggregates() - calculates the average and mean for all data
     */
    public function calculateAggregates() {
        foreach($this->results as $type => $values) {
            if ($this->results[$type]['count'] > 0) {
                // calculate our average
                $this->results[$type]['avg'] = round($this->results[$type]['total'] / $this->results[$type]['count']);

                // find our mean
                sort($this->results[$type]['mean']);
                $this->results[$type]['mean'] = $this->results[$type]['mean'][ceil($this->results[$type]['count'] / 2)];                
            }
        }
    }

    /**
     * getResults() - returns the results of our processing
     *
     * @param array()
     */
    public function getResults() {
        return $this->results;
    }
}