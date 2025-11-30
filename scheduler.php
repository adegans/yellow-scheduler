<?php
// Scheduler extension, https://github.com/adegans/yellow-scheduler

class YellowScheduler {
	const VERSION = "1.0";
	public $yellow;		 // access to API
	public $store;		 // Cache file

	// Handle initialisation
	public function onLoad($yellow) {
		$this->yellow = $yellow;
		$this->store = "system/workers/scheduler.ini";
        $this->yellow->system->setDefault("SchedulerPublishInterval", "6");

		if($this->yellow->extension->isExisting("blog") AND $this->yellow->extension->isExisting("draft")) {
			$StartLocation = $this->yellow->system->get("blogStartLocation");
			$blogStart = $this->find_blog_page($StartLocation);
			if(!is_null($blogStart)) {
				$timer = trim($this->yellow->toolbox->readFile($this->store));
                $interval = $this->yellow->system->get("SchedulerPublishInterval");
                $interval = (is_numeric($interval)) ? $interval : 6;
				$nextrun = time() - (3600 * $interval);

				if(empty($timer)) {
					$timer = time();

					if(!$this->yellow->toolbox->writeFile($this->store, $timer)) {
						$this->yellow->toolbox->log("error", "Scheduler: Couldn't create config file '".$this->store."'!");
					}
				}

				if($timer < $nextrun) {
					$pages = $this->find_blog_drafts($blogStart);
					foreach($pages as $draft) {
						$publishdate = explode(" ", $draft->get("published"));
						$date = explode("-", $publishdate[0]);
						$time = (!empty($publishdate[1])) ? explode(":", $publishdate[1]) : array(0, 0, 0);
						$publishdate = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]);

						$fileData = $fileDataNew = $this->yellow->toolbox->readFile($draft->fileName);
						if($publishdate < time()) {
							$fileDataNew = str_replace("Status: draft", "Status: public", $fileDataNew);
							if($fileData != $fileDataNew AND !$this->yellow->toolbox->writeFile($draft->fileName, $fileDataNew)) {
								$this->yellow->toolbox->log("error", "Scheduler: Couldn't publish blog post '".$draft->fileName."'!");
							}
						}

						unset($publishdate, $date, $time, $fileData, $fileDataNew);
					}

					if(!$this->yellow->toolbox->writeFile($this->store, time())) {
						$this->yellow->toolbox->log("error", "Scheduler: Couldn't update cache file '".$this->store."'!");
					}
				}
			}
		} else {
			$this->yellow->toolbox->log("error", "Scheduler: requires the Blog and Draft extension to be installed!");
		}
	}

	// Return blog start page, null if not found
	private function find_blog_page($StartLocation) {
		if ($StartLocation == "auto") {
			$blogStart = null;
			foreach($this->yellow->content->top(true, false) as $pageTop) {
				if($pageTop->get("layout") == "blog-start") {
					$blogStart = $pageTop;
					break;
				}
			}
		} else {
			$blogStart = $this->yellow->content->find($StartLocation);
		}

		return $blogStart;
	}

	// Return blog drafts
	private function find_blog_drafts($page) {
		if($this->yellow->system->get("blogStartLocation") == "auto") {
			$pages = $page->getChildren(1);
		} else {
			$pages = $this->yellow->content->index();
		}
		$pages->filter("layout", "blog");
		$pages->filter("status", "draft");

		return $pages;
	}
}
