<?php

/**
 * TestSuite.php
 *
 * This middleware will intercept traffic from visitors to
 * your website.
 *
 * PHP version 7.2
 *
 * @category Core
 * @package  RedboxTestSuite
 * @author   Johnny Mast <mastjohnny@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/johnnymast/redbox-testsuite
 * @since    GIT:1.0
 */

namespace Redbox\Testsuite;

class TestSuite
{
    /**
     * Storage to contain the
     * tests.
     *
     * @var \SplObjectStorage
     */
    protected ?\SplObjectStorage $tests = null;
    
    /**
     * Test Score counter.
     *
     * @var int|double
     */
    protected $score = 0;
    
    /**
     * TestSuite constructor.
     */
    public function __construct()
    {
        $this->tests = new \SplObjectStorage;
    }
    
    /**
     * Reset the score and rewind
     * the tests storage.
     *
     * @return void
     */
    public function reset()
    {
        $this->score = 0;
    }
    
    /**
     * Count all max scores.
     *
     * @return mixed
     */
    protected function countMaxScore(): mixed
    {
        $maxScore = 0;
        
        if (count($this->tests) > 0) {
            foreach ($this->tests as $test) {
                $maxScore += $test->maxScore();
            }
        }
        
        return $maxScore;
    }
    
    /**
     * Attach a Test or an array of
     * Tests to the TestSuite.
     *
     * @param $info The Test to attach.
     *
     * @return void
     */
    public function attach($info)
    {
        if (is_array($info) === true) {
            foreach ($info as $test) {
                $this->attach($test);
            }
        } else {
            $test = $info;
            
            // TODO: Allow array of class names and instantiate here
            /*
            if (is_string($info) === true && class_exists($info) === true) {
                $test = new $info();
            }
            */
            
            if (is_subclass_of($test, Test::class) === false) {
                throw new \InvalidArgumentException('Type not implementation Test interface.');
            }
            
            $this->tests->attach($test);
        }
    }
    
    /**
     * Detach a given test from the
     * TestSuite.
     *
     * @param Test $test The test to detach.
     *
     * @return void
     */
    public function detach(Test $test)
    {
        $this->tests->detach($test);
    }
    
    /**
     * Check to see if the TestSuite has a given
     * test inside.
     *
     * @param Test $test The Test to check for.
     *
     * @return bool
     */
    public function has(Test $test)
    {
        return $this->tests->contains($test);
    }
    
    /**
     * Return the score of tests
     * in this TestSuite.
     *
     * @return int
     */
    public function score(): int
    {
        return $this->score;
    }
    
    /**
     * Return the avarage score over the whole suite of tests.
     *
     * @return mixed
     */
    public function average()
    {
        return ($this->score / $this->countMaxScore()) * 100;
    }
    
    /**
     * Run the tests
     *
     * @return int The number of tests that ran.
     */
    public function run(): int
    {
        $tests_run = 0;
        
        /**
         * Reset the test results
         */
        $this->reset();
        
        foreach ($this->tests as $test) {
            if ($test->run()) {
                $this->score += $test->score->getScore();
                $tests_run++;
            }
        }
        
        return $tests_run;
    }
    
    /**
     * Return the test suite score.
     *
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }
}
