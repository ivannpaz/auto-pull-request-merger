<?php
namespace Command;

use \Library\GitHub;
use \Config;
use \App;
use Library\System;


/**
 * this is the basic class used to check our pull requests
 */
class Merge
{


    public function __construct()
    {
        $this->_client = new \Library\GitHub\GitHubApi(new  \Library\GitHub\GitHubCurl());
        $this->gitHubAdapter = new \Library\GitHub\GitHubAdapter(
            App::config()
        );
        $this->gitHubAdapter->addDependency(
            "gitHubApi",
            new \Library\GitHub\GitHubApi(new \Library\GitHub\GitHubCurl())
        );

    }


    /**
     * Execution
     *
     * @return int|void
     */
    public function pullRequest()
    {

        $startTime = microtime(true);


        $requestsList = $this->gitHubAdapter->openPullRequests();
        for ($i = count($requestsList) - 1; $i >= 0; $i--) {
            $pullRequest = new \Library\GitHub\PullRequest($this->gitHubAdapter, $requestsList[$i]);
            App::log("--");
            if ($pullRequest->canBeMerged()) {
                $pullRequest->merge();
            } else {
                App::log("Pull request ". $pullRequest->getId() ." cannot be merged");
            }

        }
        $endTime = microtime(true);
        $time = sprintf("%0.2f", $endTime - $startTime);
        App::log("Process finished: Parsed " . count($requestsList) . " open pull requests in $time seconds\n");
    }


}
